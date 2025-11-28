<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateProduction extends Command
{
    protected $signature = 'migrate:prod 
                            {--force : Force the operation to run when in production}
                            {--pretend : Dump the SQL queries that would be run}
                            {--step : Force the migrations to be run so they can be rolled back individually}';

    protected $description = 'Run migrations on production database from local environment';

    public function handle()
    {
        // Check if production database config exists
        // Support both direct config and External Database URL
        $prodDbUrl = env('PROD_DB_URL');
        
        if ($prodDbUrl) {
            // Parse External Database URL
            // Format: postgresql://user:pass@host:port/dbname or postgres://user:pass@host:port/dbname
            // Normalize postgresql:// to postgres:// for parse_url
            $normalizedUrl = str_replace('postgresql://', 'postgres://', $prodDbUrl);
            $parsed = parse_url($normalizedUrl);
            
            if (!$parsed || !isset($parsed['host'])) {
                $this->error('❌ Invalid PROD_DB_URL format!');
                $this->line('Expected format: postgresql://user:pass@host:port/dbname');
                return 1;
            }
            
            $prodConfig = [
                'host' => $parsed['host'] ?? null,
                'port' => $parsed['port'] ?? 5432,
                'database' => ltrim($parsed['path'] ?? '', '/'),
                'username' => $parsed['user'] ?? null,
                'password' => $parsed['pass'] ?? null,
            ];
        } else {
            // Use individual config values
            $prodConfig = [
                'host' => env('PROD_DB_HOST'),
                'port' => env('PROD_DB_PORT', 5432),
                'database' => env('PROD_DB_DATABASE'),
                'username' => env('PROD_DB_USERNAME'),
                'password' => env('PROD_DB_PASSWORD'),
            ];
        }

        if (empty($prodConfig['host']) || empty($prodConfig['database'])) {
            $this->error('❌ Production database configuration not found!');
            $this->newLine();
            $this->info('Please add these to your .env file:');
            $this->line('PROD_DB_HOST=your-production-db-host');
            $this->line('PROD_DB_PORT=5432');
            $this->line('PROD_DB_DATABASE=your-production-db-name');
            $this->line('PROD_DB_USERNAME=your-production-db-user');
            $this->line('PROD_DB_PASSWORD=your-production-db-password');
            return 1;
        }

        $this->info('🔄 Running migrations on production database...');
        $this->newLine();
        $this->info('📊 Production Database:');
        $this->line('  Host: ' . $prodConfig['host']);
        $this->line('  Database: ' . $prodConfig['database']);
        $this->line('  Username: ' . $prodConfig['username']);
        $this->newLine();

        // Confirm before proceeding
        if (!$this->option('force') && !$this->option('no-interaction')) {
            if (!$this->confirm('⚠️  This will run migrations on PRODUCTION database. Continue?', false)) {
                $this->info('❌ Migration cancelled.');
                return 0;
            }
        }

        // Test connection first
        $this->info('🔌 Testing connection to production database...');
        try {
            $connection = $this->createProductionConnection($prodConfig);
            $connection->getPdo();
            $this->info('  ✓ Connection successful!');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->error('  ❌ Connection failed: ' . $errorMessage);
            $this->newLine();
            
            // Check if it's a DNS resolution error (internal hostname)
            if (strpos($errorMessage, 'could not translate host name') !== false || 
                strpos($errorMessage, 'nodename nor servname provided') !== false) {
                $this->warn('⚠️  This looks like an internal Render hostname.');
                $this->newLine();
                $this->info('Render PostgreSQL has two connection types:');
                $this->line('  1. Internal hostname - only accessible from Render services');
                $this->line('  2. External hostname - accessible from outside');
                $this->newLine();
                $this->info('To connect from local, you need the External Database URL:');
                $this->line('  1. Go to Render Dashboard → PostgreSQL service');
                $this->line('  2. Find "External Database URL" (not Internal)');
                $this->line('  3. Use that URL or extract hostname from it');
                $this->newLine();
                $this->info('Alternative: Migration will run automatically on deploy via Dockerfile.');
                $this->line('If you need to run migration now, use Render Shell or update PROD_DB_HOST');
                $this->newLine();
            }
            
            // Ask if user wants to continue anyway
            if (!$this->option('force')) {
                if ($this->confirm('Continue anyway? (might fail)', false)) {
                    $this->info('Continuing with migration attempt...');
                } else {
                    return 1;
                }
            } else {
                $this->warn('Continuing with --force flag...');
            }
        }

        $this->newLine();

        // Run migrations
        $this->info('📤 Running migrations...');
        
        $options = [];
        if ($this->option('force')) {
            $options[] = '--force';
        }
        if ($this->option('pretend')) {
            $options[] = '--pretend';
        }
        if ($this->option('step')) {
            $options[] = '--step';
        }

        // Set production connection as default temporarily
        config(['database.default' => 'production']);
        
        // Run migrate command with production connection
        $exitCode = $this->call('migrate', array_merge($options, [
            '--database' => 'production',
        ]));

        // Restore default connection
        config(['database.default' => env('DB_CONNECTION', 'sqlite')]);

        if ($exitCode === 0) {
            $this->newLine();
            $this->info('✅ Migrations completed successfully!');
        }

        return $exitCode;
    }

    protected function createProductionConnection(array $config)
    {
        // Temporarily add production connection to config
        config(['database.connections.production' => [
            'driver' => 'pgsql',
            'host' => $config['host'],
            'port' => $config['port'],
            'database' => $config['database'],
            'username' => $config['username'],
            'password' => $config['password'],
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ]]);

        return DB::connection('production');
    }
}

