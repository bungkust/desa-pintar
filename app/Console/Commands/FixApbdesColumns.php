<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixApbdesColumns extends Command
{
    protected $signature = 'db:fix-apbdes-columns 
                            {--prod : Run on production database}
                            {--force : Force the operation}';

    protected $description = 'Add realisasi and anggaran columns to apbdes table if missing';

    public function handle()
    {
        $connection = $this->option('prod') ? 'production' : 'default';
        
        // Set production connection if needed
        if ($this->option('prod')) {
            $prodDbUrl = env('PROD_DB_URL');
            
            if ($prodDbUrl) {
                $normalizedUrl = str_replace('postgresql://', 'postgres://', $prodDbUrl);
                $parsed = parse_url($normalizedUrl);
                
                if (!$parsed || !isset($parsed['host'])) {
                    $this->error('❌ Invalid PROD_DB_URL format!');
                    return 1;
                }
                
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
            } else {
                $this->error('❌ PROD_DB_URL not set!');
                return 1;
            }
        }
        
        $this->info('🔧 Checking apbdes table columns...');
        
        $hasRealisasi = Schema::connection($connection)->hasColumn('apbdes', 'realisasi');
        $hasAnggaran = Schema::connection($connection)->hasColumn('apbdes', 'anggaran');
        
        $this->line('  realisasi: ' . ($hasRealisasi ? '✓ exists' : '✗ missing'));
        $this->line('  anggaran: ' . ($hasAnggaran ? '✓ exists' : '✗ missing'));
        
        if ($hasRealisasi && $hasAnggaran) {
            $this->info('✅ All columns already exist!');
            return 0;
        }
        
        if (!$this->option('force') && !$this->option('no-interaction')) {
            if (!$this->confirm('Add missing columns?', true)) {
                $this->info('Cancelled.');
                return 0;
            }
        }
        
        $this->info('📤 Adding missing columns...');
        
        try {
            DB::connection($connection)->beginTransaction();
            
            if (!$hasRealisasi) {
                DB::connection($connection)->statement('ALTER TABLE apbdes ADD COLUMN realisasi BIGINT DEFAULT 0');
                $this->info('  ✓ Added realisasi column');
            }
            
            if (!$hasAnggaran) {
                DB::connection($connection)->statement('ALTER TABLE apbdes ADD COLUMN anggaran BIGINT DEFAULT 0');
                $this->info('  ✓ Added anggaran column');
            }
            
            // Make amount nullable if it exists
            if (Schema::connection($connection)->hasColumn('apbdes', 'amount')) {
                try {
                    DB::connection($connection)->statement('ALTER TABLE apbdes ALTER COLUMN amount DROP NOT NULL');
                    $this->info('  ✓ Made amount column nullable');
                } catch (\Exception $e) {
                    $this->warn('  ⚠️  Could not make amount nullable: ' . $e->getMessage());
                }
            }
            
            DB::connection($connection)->commit();
            
            $this->newLine();
            $this->info('✅ Columns added successfully!');
            
        } catch (\Exception $e) {
            DB::connection($connection)->rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}

