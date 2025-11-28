<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\MenuItem;

class SyncLocalToProduction extends Command
{
    protected $signature = 'db:sync-local-to-prod 
                            {--force : Force sync without confirmation}
                            {--tables= : Comma-separated list of tables to sync (default: all)}
                            {--skip-data : Only sync schema, skip data}
                            {--skip-migrations : Skip running migrations}';

    protected $description = 'Sync local SQLite database to production PostgreSQL (schema + data)';

    public function handle()
    {
        // Check production database config
        $prodDbUrl = env('PROD_DB_URL');
        
        if (!$prodDbUrl) {
            $this->error('❌ PROD_DB_URL not set in .env!');
            $this->line('Please add: PROD_DB_URL=postgresql://user:pass@host:port/database');
            return 1;
        }

        // Parse production URL
        $normalizedUrl = str_replace('postgresql://', 'postgres://', $prodDbUrl);
        $parsed = parse_url($normalizedUrl);
        
        if (!$parsed || !isset($parsed['host'])) {
            $this->error('❌ Invalid PROD_DB_URL format!');
            return 1;
        }

        // Configure production connection
        config(['database.connections.production' => [
            'driver' => 'pgsql',
            'host' => $parsed['host'],
            'port' => $parsed['port'] ?? 5432,
            'database' => ltrim($parsed['path'] ?? '', '/'),
            'username' => $parsed['user'],
            'password' => $parsed['pass'],
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ]]);

        $this->info('🔄 Syncing local database to production...');
        $this->newLine();

        // Test connections
        $this->info('🔌 Testing connections...');
        try {
            DB::connection('sqlite')->getPdo();
            $this->line('  ✓ Local (SQLite) connection OK');
        } catch (\Exception $e) {
            $this->error('  ❌ Local connection failed: ' . $e->getMessage());
            return 1;
        }

        try {
            DB::connection('production')->getPdo();
            $this->line('  ✓ Production (PostgreSQL) connection OK');
        } catch (\Exception $e) {
            $this->error('  ❌ Production connection failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Step 1: Run migrations
        if (!$this->option('skip-migrations')) {
            $this->info('📦 Step 1: Running migrations on production...');
            $this->call('migrate:prod', ['--force' => true]);
            $this->newLine();
        }

        // Step 2: Compare schemas
        $this->info('📊 Step 2: Comparing schemas...');
        $localTables = $this->getLocalTables();
        $prodTables = $this->getProductionTables();
        
        $missingTables = array_diff($localTables, $prodTables);
        $extraTables = array_diff($prodTables, $localTables);
        
        if (!empty($missingTables)) {
            $this->warn('  ⚠️  Missing tables in production: ' . implode(', ', $missingTables));
        }
        if (!empty($extraTables)) {
            $this->warn('  ⚠️  Extra tables in production: ' . implode(', ', $extraTables));
        }
        if (empty($missingTables) && empty($extraTables)) {
            $this->line('  ✓ All tables exist in production');
        }
        $this->newLine();

        // Step 3: Sync data
        if (!$this->option('skip-data')) {
            $tablesToSync = $this->option('tables') 
                ? explode(',', $this->option('tables'))
                : $localTables;

            // Filter out system tables
            $tablesToSync = array_filter($tablesToSync, function($table) {
                return !in_array($table, ['migrations', 'sqlite_sequence']);
            });

            if (!$this->option('force') && !$this->option('no-interaction')) {
                $this->warn('⚠️  This will overwrite production data!');
                if (!$this->confirm('Continue?', false)) {
                    $this->info('Cancelled.');
                    return 0;
                }
            }

            $this->info('📤 Step 3: Syncing data...');
            $this->newLine();

            $bar = $this->output->createProgressBar(count($tablesToSync));
            $bar->start();

            foreach ($tablesToSync as $table) {
                try {
                    $this->syncTable($table);
                    $bar->advance();
                } catch (\Exception $e) {
                    $bar->finish();
                    $this->newLine();
                    $this->error("  ❌ Error syncing table '{$table}': " . $e->getMessage());
                    // Continue with other tables
                }
            }

            $bar->finish();
            $this->newLine();
            $this->newLine();
        }

        // Step 4: Verify
        $this->info('✅ Step 4: Verification...');
        $this->verifySync();
        
        $this->newLine();
        $this->info('✅ Sync completed successfully!');
        
        return 0;
    }

    protected function getLocalTables(): array
    {
        $tables = DB::connection('sqlite')
            ->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        
        return array_map(fn($row) => $row->name, $tables);
    }

    protected function getProductionTables(): array
    {
        $tables = DB::connection('production')
            ->select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        
        return array_map(fn($row) => $row->tablename, $tables);
    }

    protected function syncTable(string $table): void
    {
        // Get all data from local
        $localData = DB::connection('sqlite')->table($table)->get()->toArray();
        
        if (empty($localData)) {
            // Table is empty, just truncate production
            DB::connection('production')->table($table)->truncate();
            return;
        }

        // Get column types from production to handle JSON columns
        $prodColumns = $this->getProductionColumnTypes($table);

        // Convert to array format
        $data = array_map(fn($row) => (array) $row, $localData);

        // Truncate production table
        DB::connection('production')->table($table)->truncate();

        // Insert data in chunks
        $chunks = array_chunk($data, 50); // Smaller chunks for better error handling
        $inserted = 0;
        $failed = 0;
        
        foreach ($chunks as $chunk) {
            // Process each row
            $processedChunk = array_map(function($row) use ($prodColumns) {
                foreach ($row as $key => $value) {
                    // Skip null values (keep as null)
                    if ($value === null) {
                        continue;
                    }
                    
                    // Convert SQLite boolean strings to proper booleans
                    if ($value === '1' || $value === 1) {
                        $row[$key] = true;
                    } elseif ($value === '0' || $value === 0) {
                        $row[$key] = false;
                    }
                    // Handle JSON columns - check if column is JSON type in production
                    elseif (isset($prodColumns[$key]) && $prodColumns[$key] === 'json') {
                        // If already a string, try to decode first
                        if (is_string($value)) {
                            $trimmed = trim($value);
                            if (($trimmed[0] === '{' && substr($trimmed, -1) === '}') || 
                                ($trimmed[0] === '[' && substr($trimmed, -1) === ']')) {
                                $decoded = json_decode($value, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    // Keep as array for PostgreSQL JSON column
                                    $row[$key] = $decoded;
                                } else {
                                    // Invalid JSON, set to null
                                    $row[$key] = null;
                                }
                            } else {
                                // Not JSON string, set to null
                                $row[$key] = null;
                            }
                        }
                        // If already array/object, keep as is (PostgreSQL will handle it)
                        elseif (is_array($value) || is_object($value)) {
                            // Already in correct format
                        }
                    }
                }
                return $row;
            }, $chunk);

                // Try bulk insert first
                try {
                    // Use DB::table()->insert() which handles JSON automatically
                    DB::connection('production')->table($table)->insert($processedChunk);
                    $inserted += count($processedChunk);
                } catch (\Exception $e) {
                    // If bulk insert fails, try one by one with better error reporting
                    foreach ($processedChunk as $index => $singleRow) {
                        try {
                            // Clean up the row - ensure JSON columns are properly formatted
                            $cleanRow = $this->cleanRowForInsert($singleRow, $prodColumns);
                            
                            // For JSON columns, ensure they're arrays (Laravel will encode)
                            foreach ($cleanRow as $key => $value) {
                                if (isset($prodColumns[$key]) && ($prodColumns[$key] === 'json' || $prodColumns[$key] === 'jsonb')) {
                                    if (is_string($value)) {
                                        $decoded = json_decode($value, true);
                                        if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                                            $cleanRow[$key] = $decoded;
                                        } else {
                                            $cleanRow[$key] = null;
                                        }
                                    } elseif (is_array($value) || is_object($value)) {
                                        // Already correct format
                                    } elseif ($value === null) {
                                        // Keep null
                                    } else {
                                        $cleanRow[$key] = null;
                                    }
                                }
                            }
                            
                            // For JSON columns, ensure they're properly formatted as JSON strings
                            // PostgreSQL JSON columns can accept JSON strings directly
                            foreach ($cleanRow as $key => $value) {
                                if (isset($prodColumns[$key]) && ($prodColumns[$key] === 'json' || $prodColumns[$key] === 'jsonb')) {
                                    if (is_string($value)) {
                                        // Validate JSON string
                                        $decoded = json_decode($value, true);
                                        if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                                            // Keep as JSON string - PostgreSQL will accept it
                                            $cleanRow[$key] = $value;
                                        } else {
                                            $cleanRow[$key] = null;
                                        }
                                    } elseif (is_array($value) || is_object($value)) {
                                        // Convert array/object to JSON string
                                        $cleanRow[$key] = json_encode($value);
                                    } elseif ($value === null) {
                                        // Keep null
                                        $cleanRow[$key] = null;
                                    } else {
                                        // Invalid type, set to null
                                        $cleanRow[$key] = null;
                                    }
                                }
                            }
                            
                            // Use DB::connection()->table()->insert() with JSON strings
                            DB::connection('production')->table($table)->insert($cleanRow);
                            $inserted++;
                        } catch (\Exception $e2) {
                            $failed++;
                            // Log the error but continue
                            if ($this->output->isVerbose()) {
                                $this->warn("    Row {$index} failed: " . $e2->getMessage());
                            }
                            continue;
                        }
                    }
                }
        }
        
        if ($failed > 0) {
            $this->warn("  ⚠️  {$table}: {$failed} rows failed to insert");
        }
    }

    protected function getProductionColumnTypes(string $table): array
    {
        try {
            $columns = DB::connection('production')
                ->select("
                    SELECT column_name, data_type 
                    FROM information_schema.columns 
                    WHERE table_name = ? AND table_schema = 'public'
                ", [$table]);
            
            $types = [];
            foreach ($columns as $col) {
                $types[$col->column_name] = $col->data_type;
            }
            
            return $types;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function cleanRowForInsert(array $row, array $columnTypes): array
    {
        foreach ($row as $key => $value) {
            // Handle JSON columns - PostgreSQL JSON columns need JSON string or array
            // Laravel will handle array automatically, but we need to ensure it's valid
            if (isset($columnTypes[$key]) && ($columnTypes[$key] === 'jsonb' || $columnTypes[$key] === 'json')) {
                if (is_string($value)) {
                    // Try to decode to validate, then keep as string or convert to array
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                        // Valid JSON - Laravel will handle array for JSON columns
                        $row[$key] = $decoded;
                    } else {
                        // Invalid JSON string, set to null
                        $row[$key] = null;
                    }
                } elseif (is_array($value) || is_object($value)) {
                    // Already in correct format - Laravel will JSON encode automatically
                    $row[$key] = $value;
                } elseif ($value === null) {
                    // Keep null
                    $row[$key] = null;
                } else {
                    // Invalid type, set to null
                    $row[$key] = null;
                }
            }
            // Remove empty strings for nullable columns
            elseif ($value === '' && !isset($columnTypes[$key])) {
                $row[$key] = null;
            }
        }
        return $row;
    }

    protected function verifySync(): void
    {
        $tables = $this->getLocalTables();
        $tables = array_filter($tables, fn($t) => !in_array($t, ['migrations', 'sqlite_sequence']));

        $allMatch = true;
        foreach ($tables as $table) {
            $localCount = DB::connection('sqlite')->table($table)->count();
            $prodCount = DB::connection('production')->table($table)->count();
            
            if ($localCount !== $prodCount) {
                $this->warn("  ⚠️  Table '{$table}': Local={$localCount}, Prod={$prodCount}");
                $allMatch = false;
            }
        }

        if ($allMatch) {
            $this->line('  ✓ All table row counts match');
        }
    }
}

