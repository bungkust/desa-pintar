<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncDatabaseToProduction extends Command
{
    protected $signature = 'db:sync-to-prod 
                            {--dry-run : Show what would be synced without actually syncing}
                            {--tables=* : Specific tables to sync (default: all)}';

    protected $description = 'Sync database from local SQLite to production PostgreSQL';

    public function handle()
    {
        // Check if we're in production
        if (app()->environment('production')) {
            $this->error('This command should only be run from local environment!');
            return 1;
        }

        // Check if local is using SQLite
        if (config('database.default') !== 'sqlite') {
            $this->error('Local database must be SQLite to use this command!');
            return 1;
        }

        $this->info('🔄 Starting database sync from local to production...');
        $this->newLine();

        // Get all tables
        $tables = $this->option('tables');
        if (empty($tables)) {
            $tables = $this->getAllTables();
        }

        $this->info('📋 Tables to sync: ' . implode(', ', $tables));
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No data will be synced');
            $this->newLine();
        }

        // Confirm before proceeding
        if (!$this->option('dry-run') && !$this->confirm('⚠️  This will overwrite production data. Continue?', false)) {
            $this->info('❌ Sync cancelled.');
            return 0;
        }

        $this->info('📤 Exporting data from local SQLite...');
        
        $exportedData = [];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $this->warn("⚠️  Table '{$table}' does not exist, skipping...");
                continue;
            }

            $data = DB::table($table)->get()->toArray();
            $exportedData[$table] = $data;
            
            $count = count($data);
            $this->info("  ✓ {$table}: {$count} records");
        }

        $this->newLine();
        $this->info('📥 Instructions for importing to production:');
        $this->newLine();
        $this->line('1. Copy the exported data file to production');
        $this->line('2. Run: php artisan db:import-from-local [file]');
        $this->line('3. Or manually import via PostgreSQL client');
        $this->newLine();

        // Save to JSON file
        $filename = 'database_export_' . date('Y-m-d_His') . '.json';
        $filepath = storage_path('app/' . $filename);
        file_put_contents($filepath, json_encode($exportedData, JSON_PRETTY_PRINT));

        $this->info("✅ Data exported to: {$filepath}");
        $this->info("📊 Total tables: " . count($exportedData));
        $this->info("📦 Total records: " . array_sum(array_map('count', $exportedData)));

        return 0;
    }

    protected function getAllTables(): array
    {
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        return array_map(fn($table) => $table->name, $tables);
    }
}

