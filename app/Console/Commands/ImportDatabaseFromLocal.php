<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportDatabaseFromLocal extends Command
{
    protected $signature = 'db:import-from-local 
                            {file : Path to exported JSON file}
                            {--tables=* : Specific tables to import (default: all)}
                            {--truncate : Truncate tables before importing}
                            {--force : Skip confirmation prompts}
                            {--skip-migrations : Skip migrations table (recommended)}';

    protected $description = 'Import database data from local export to production PostgreSQL';

    public function handle()
    {
        // Check if we're in production (warn but allow continue)
        if (!app()->environment('production')) {
            $this->warn('⚠️  You are not in production environment!');
            $this->info('💡 This command is designed for production, but you can test it locally.');
            if (!$this->option('force') && !$this->option('no-interaction') && !$this->confirm('Continue anyway?', false)) {
                return 1;
            }
        }

        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("❌ File not found: {$file}");
            return 1;
        }

        $this->info('📥 Importing data from local export...');
        $this->newLine();

        $data = json_decode(file_get_contents($file), true);
        
        if (!$data) {
            $this->error('❌ Invalid JSON file!');
            return 1;
        }

        $tables = $this->option('tables');
        if (empty($tables)) {
            $tables = array_keys($data);
        }

        // Skip migrations table by default (recommended)
        if ($this->option('skip-migrations') || !in_array('migrations', $this->option('tables'))) {
            $tables = array_filter($tables, fn($table) => $table !== 'migrations');
        }

        $this->info('📋 Tables to import: ' . implode(', ', $tables));
        $this->newLine();

        if ($this->option('truncate')) {
            $this->warn('🗑️  Truncating tables before import...');
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->info("  ✓ Truncated: {$table}");
                }
            }
            $this->newLine();
        }

        // Confirm before proceeding (skip if --force)
        if (!$this->option('force') && !$this->option('no-interaction') && !$this->confirm('⚠️  This will import data. Continue?', false)) {
            $this->info('❌ Import cancelled.');
            return 0;
        }

        $this->info('📤 Importing data...');
        $this->newLine();

        foreach ($tables as $table) {
            if (!isset($data[$table])) {
                $this->warn("⚠️  Table '{$table}' not found in export, skipping...");
                continue;
            }

            if (!Schema::hasTable($table)) {
                $this->warn("⚠️  Table '{$table}' does not exist in database, skipping...");
                continue;
            }

            $records = $data[$table];
            $count = count($records);

            if ($count === 0) {
                $this->info("  ⚪ {$table}: No records to import");
                continue;
            }

            // Convert array of objects to array of arrays
            $recordsArray = array_map(function($record) {
                return (array) $record;
            }, $records);

            // Insert in chunks (skip migrations table as it's managed by Laravel)
            if ($table === 'migrations') {
                $this->info("  ⚪ {$table}: Skipped (managed by Laravel migrations)");
                continue;
            }

            // Insert in chunks
            $chunks = array_chunk($recordsArray, 100);
            $imported = 0;

            foreach ($chunks as $chunk) {
                try {
                    DB::table($table)->insert($chunk);
                    $imported += count($chunk);
                } catch (\Exception $e) {
                    // If duplicate key error, try update instead
                    if (str_contains($e->getMessage(), 'UNIQUE constraint') || str_contains($e->getMessage(), 'duplicate key')) {
                        $this->warn("  ⚠️  {$table}: Some records already exist, skipping duplicates...");
                        // Try insert ignore or update on duplicate
                        foreach ($chunk as $record) {
                            try {
                                DB::table($table)->insert($record);
                                $imported++;
                            } catch (\Exception $e2) {
                                // Skip duplicate records
                                continue;
                            }
                        }
                    } else {
                        $this->error("  ❌ Error importing {$table}: " . $e->getMessage());
                    }
                    continue;
                }
            }

            $this->info("  ✓ {$table}: {$imported}/{$count} records imported");
        }

        $this->newLine();
        $this->info('✅ Import completed!');

        return 0;
    }
}

