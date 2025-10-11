<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantEnvironmentService;
use App\Services\TenantContextService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestTenantDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:test-db {subdomain : The tenant subdomain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test tenant database connection and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subdomain = $this->argument('subdomain');

        $this->info("Testing tenant database configuration for: {$subdomain}");
        $this->newLine();

        // Find tenant
        $this->info('1. Looking up tenant...');
        $tenant = Tenant::where('data->subdomain', $subdomain)->first();

        if (!$tenant) {
            $this->error("Tenant with subdomain '{$subdomain}' not found!");
            return 1;
        }

        $this->info("   ✓ Found tenant: {$tenant->data['name']} (ID: {$tenant->id})");
        $this->newLine();

        // Check database strategy
        $this->info('2. Checking database strategy...');
        $strategy = $tenant->data['database_strategy'] ?? 'shared';
        $this->info("   Strategy: {$strategy}");

        if ($strategy !== 'separate') {
            $this->warn('   This tenant uses shared database, not separate database.');
            return 0;
        }

        $this->info('   ✓ Tenant uses separate database');
        $this->newLine();

        // Check environment file
        $this->info('3. Checking tenant environment file...');
        $envService = new TenantEnvironmentService();
        $hasEnvFile = $envService->hasTenantEnvironmentFile($tenant);

        if ($hasEnvFile) {
            $this->info('   ✓ Tenant environment file exists: .env.tenant.' . $subdomain);
        } else {
            $this->warn('   ! No tenant environment file found: .env.tenant.' . $subdomain);
            $this->warn('   Will use tenant model configuration');
        }
        $this->newLine();

        // Load configuration
        $this->info('4. Loading database configuration...');
        $tenantEnv = $envService->loadTenantEnvironment($tenant);

        if (!empty($tenantEnv)) {
            $dbConfig = $envService->buildDatabaseConfig($tenantEnv);
            $this->info('   ✓ Configuration loaded from environment file');
        } else {
            $dbConfig = $tenant->getDatabaseConfig();
            $this->info('   ✓ Configuration loaded from tenant model');
        }

        $this->table(
            ['Setting', 'Value'],
            [
                ['Host', $dbConfig['host'] ?? 'N/A'],
                ['Port', $dbConfig['port'] ?? 'N/A'],
                ['Database', $dbConfig['database'] ?? 'N/A'],
                ['Username', $dbConfig['username'] ?? 'N/A'],
                ['Password', str_repeat('*', min(strlen($dbConfig['password'] ?? ''), 10))],
                ['Charset', $dbConfig['charset'] ?? 'N/A'],
                ['Collation', $dbConfig['collation'] ?? 'N/A'],
            ]
        );
        $this->newLine();

        // Test connection
        $this->info('5. Testing database connection...');

        try {
            $contextService = new TenantContextService();
            $contextService->initializeContext($tenant);

            $connection = TenantContextService::getDatabaseConnection($tenant);
            $pdo = $connection->getPdo();

            $this->info('   ✓ Connection successful!');
            $this->info('   Driver: ' . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
            $this->info('   Server: ' . $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION));
            $this->newLine();

            // Test query
            $this->info('6. Testing database query...');

            $tables = $connection->select('SHOW TABLES');
            $tableCount = count($tables);

            $this->info("   ✓ Query successful! Found {$tableCount} tables");

            if ($tableCount > 0) {
                $this->info('   Tables:');
                foreach ($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    $this->info("     - {$tableName}");
                }
            } else {
                $this->warn('   ! No tables found. You may need to run migrations.');
                $this->info('   Run: php artisan migrate --database=tenant_' . $tenant->id);
            }

            $this->newLine();

            // Check for admin_users table
            $this->info('7. Checking admin_users table...');

            $adminUsersExists = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.tables
                 WHERE table_schema = ? AND table_name = 'admin_users'",
                [$dbConfig['database']]
            )[0]->count ?? 0;

            if ($adminUsersExists) {
                $userCount = $connection->table('admin_users')->count();
                $this->info("   ✓ admin_users table exists with {$userCount} users");

                if ($userCount > 0) {
                    $this->info('   Users:');
                    $users = $connection->table('admin_users')->select('id', 'name', 'email', 'is_active')->get();
                    foreach ($users as $user) {
                        $status = $user->is_active ? '✓ Active' : '✗ Inactive';
                        $this->info("     - {$user->name} ({$user->email}) - {$status}");
                    }
                } else {
                    $this->warn('   ! No users found in admin_users table');
                    $this->info('   You need to create users for this tenant database');
                }
            } else {
                $this->error('   ✗ admin_users table not found!');
                $this->info('   Run migrations: php artisan migrate --database=tenant_' . $tenant->id);
            }

            $this->newLine();
            $this->info('========================================');
            $this->info('✓ All tests passed successfully!');
            $this->info('========================================');

            // Reset context
            $contextService->resetContext();

            return 0;

        } catch (\Exception $e) {
            $this->error('   ✗ Connection failed!');
            $this->error('   Error: ' . $e->getMessage());
            $this->newLine();

            $this->error('Troubleshooting steps:');
            $this->line('1. Verify database exists: mysql -e "SHOW DATABASES;"');
            $this->line('2. Check database credentials in .env.tenant.' . $subdomain);
            $this->line('3. Ensure database user has proper permissions');
            $this->line('4. Check MySQL service is running');

            return 1;
        }
    }
}

