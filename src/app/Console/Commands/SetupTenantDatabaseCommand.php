<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantEnvironmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupTenantDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup-db {subdomain : The tenant subdomain}
                            {--create-db : Create the database if it doesn\'t exist}
                            {--run-migrations : Run migrations on the tenant database}
                            {--host=127.0.0.1 : Database host}
                            {--port=3306 : Database port}
                            {--username=root : Database username}
                            {--password= : Database password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a separate database for a tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subdomain = $this->argument('subdomain');

        $this->info("===========================================");
        $this->info("Setting up separate database for: {$subdomain}");
        $this->info("===========================================");
        $this->newLine();

        // Find tenant
        $this->info('1. Looking up tenant...');
        $tenant = Tenant::where('data->subdomain', $subdomain)->first();

        if (!$tenant) {
            $this->error("Tenant with subdomain '{$subdomain}' not found!");
            $this->newLine();
            $this->info('Available tenants:');
            $tenants = Tenant::all();
            foreach ($tenants as $t) {
                $sub = $t->data['subdomain'] ?? 'N/A';
                $name = $t->data['name'] ?? 'N/A';
                $this->line("  - {$sub} ({$name})");
            }
            return 1;
        }

        $this->info("   ✓ Found tenant: {$tenant->data['name']} (ID: {$tenant->id})");
        $this->newLine();

        // Get database configuration
        $host = $this->option('host');
        $port = $this->option('port');
        $username = $this->option('username');
        $password = $this->option('password');
        $database = "school_erp_{$subdomain}";

        if (empty($password)) {
            $password = $this->secret('Enter database password (leave empty for no password):');
        }

        $this->info('2. Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Host', $host],
                ['Port', $port],
                ['Database', $database],
                ['Username', $username],
                ['Password', str_repeat('*', min(strlen($password), 10))],
            ]
        );
        $this->newLine();

        // Create database if requested
        if ($this->option('create-db')) {
            $this->info('3. Creating database...');

            if (!$this->confirm('This will create the database. Continue?', true)) {
                $this->info('Cancelled.');
                return 0;
            }

            try {
                // Connect without database name
                $tempConfig = [
                    'driver' => 'mysql',
                    'host' => $host,
                    'port' => $port,
                    'username' => $username,
                    'password' => $password,
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ];

                config(['database.connections.temp_connection' => $tempConfig]);

                DB::connection('temp_connection')->statement(
                    "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
                );

                $this->info("   ✓ Database '{$database}' created successfully!");
                $this->newLine();

            } catch (\Exception $e) {
                $this->error('   ✗ Failed to create database!');
                $this->error('   Error: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('3. Skipping database creation (use --create-db to create)');
            $this->newLine();
        }

        // Create environment file
        $this->info('4. Creating tenant environment file...');

        $envService = new TenantEnvironmentService();

        $config = [
            'TENANT_DB_CONNECTION' => 'mysql',
            'TENANT_DB_HOST' => $host,
            'TENANT_DB_PORT' => $port,
            'TENANT_DB_DATABASE' => $database,
            'TENANT_DB_USERNAME' => $username,
            'TENANT_DB_PASSWORD' => $password,
            'TENANT_DB_CHARSET' => 'utf8mb4',
            'TENANT_DB_COLLATION' => 'utf8mb4_unicode_ci',
        ];

        if ($envService->hasTenantEnvironmentFile($tenant)) {
            if (!$this->confirm('Environment file already exists. Overwrite?', false)) {
                $this->info('   Skipped environment file creation');
            } else {
                $envService->createTenantEnvironmentFile($tenant, $config);
                $this->info("   ✓ Environment file created: .env.tenant.{$subdomain}");
            }
        } else {
            $envService->createTenantEnvironmentFile($tenant, $config);
            $this->info("   ✓ Environment file created: .env.tenant.{$subdomain}");
        }
        $this->newLine();

        // Update tenant model
        $this->info('5. Updating tenant model...');

        $data = $tenant->data;
        $data['database_strategy'] = 'separate';
        $tenant->data = $data;
        $tenant->database_host = $host;
        $tenant->database_port = $port;
        $tenant->database_name = $database;
        $tenant->database_username = $username;
        $tenant->database_password = $password;
        $tenant->save();

        $this->info('   ✓ Tenant model updated with separate database configuration');
        $this->newLine();

        // Run migrations if requested
        if ($this->option('run-migrations')) {
            $this->info('6. Running migrations...');

            if (!$this->confirm('This will run all migrations on the tenant database. Continue?', true)) {
                $this->info('Cancelled.');
                return 0;
            }

            try {
                $connectionName = $tenant->getConnectionName();
                $this->call('migrate', [
                    '--database' => $connectionName,
                    '--force' => true,
                ]);

                $this->info('   ✓ Migrations completed successfully!');
                $this->newLine();

            } catch (\Exception $e) {
                $this->error('   ✗ Migration failed!');
                $this->error('   Error: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('6. Skipping migrations (use --run-migrations to run)');
            $this->info('   You can run migrations later with:');
            $this->line("   php artisan migrate --database=tenant_{$tenant->id}");
            $this->newLine();
        }

        // Final steps
        $this->info('===========================================');
        $this->info('✓ Setup completed successfully!');
        $this->info('===========================================');
        $this->newLine();

        $this->info('Next steps:');
        $this->line('1. Test the connection: php artisan tenant:test-db ' . $subdomain);
        $this->line('2. Create admin users in the tenant database');
        $this->line('3. Try logging in at: http://' . $subdomain . '.' . config('all.domains.primary') . '/login');
        $this->newLine();

        return 0;
    }
}

