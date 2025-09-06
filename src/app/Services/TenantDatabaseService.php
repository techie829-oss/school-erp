<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Connection;

class TenantDatabaseService
{
    /**
     * Switch to tenant's database connection.
     */
    public function switchToTenantDatabase(Tenant $tenant): void
    {
        if (!$tenant->usesSeparateDatabase()) {
            // Use default connection for shared database tenants
            Config::set('database.default', 'mysql');
            return;
        }

        $connectionName = $tenant->getConnectionName();
        $databaseConfig = $tenant->getDatabaseConfig();

        // Add the connection to the database configuration
        Config::set("database.connections.{$connectionName}", $databaseConfig);

        // Set as default connection
        Config::set('database.default', $connectionName);
    }

    /**
     * Get tenant's database connection.
     */
    public function getTenantConnection(Tenant $tenant): Connection
    {
        if (!$tenant->usesSeparateDatabase()) {
            return DB::connection('mysql');
        }

        $connectionName = $tenant->getConnectionName();

        // Ensure connection is configured
        if (!Config::has("database.connections.{$connectionName}")) {
            $this->switchToTenantDatabase($tenant);
        }

        return DB::connection($connectionName);
    }

    /**
     * Test tenant database connection.
     */
    public function testTenantConnection(Tenant $tenant): array
    {
        try {
            if (!$tenant->usesSeparateDatabase()) {
                return [
                    'success' => true,
                    'message' => 'Using shared database connection',
                    'connection' => 'mysql'
                ];
            }

            // Connect to MySQL server without specifying database
            $config = $tenant->getDatabaseConfig();

            // Check if we have valid database configuration
            if (empty($config) || !isset($config['database']) || empty($config['database'])) {
                return [
                    'success' => false,
                    'message' => 'Database configuration is incomplete. Please ensure all database fields are filled.',
                    'connection' => $tenant->getConnectionName(),
                    'error' => 'Invalid database configuration'
                ];
            }

            // Additional validation to prevent "Undefined array key" errors
            if (!isset($config['host']) || !isset($config['username']) || !isset($config['password'])) {
                return [
                    'success' => false,
                    'message' => 'Database configuration is missing required fields (host, username, password).',
                    'connection' => $tenant->getConnectionName(),
                    'error' => 'Incomplete database configuration'
                ];
            }

            $serverConfig = $config;
            if (isset($serverConfig['database'])) {
                unset($serverConfig['database']);
            }
            // Add a default database to prevent Laravel connector warnings
            $serverConfig['database'] = 'mysql';

            $connectionName = "temp_test_{$tenant->id}";
            Config::set("database.connections.{$connectionName}", $serverConfig);

            $connection = DB::connection($connectionName);

            // Test the server connection
            $connection->getPdo();

            // Check if the database actually exists
            $databaseName = $tenant->database_name;
            $databaseExists = $connection->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);

            // Clean up temporary connection
            DB::purge($connectionName);
            Config::set("database.connections.{$connectionName}", null);

            if (empty($databaseExists)) {
                return [
                    'success' => false,
                    'message' => "Database '{$databaseName}' not found in database. Please create the database first.",
                    'connection' => $tenant->getConnectionName(),
                    'database' => $databaseName,
                    'error' => 'Database not found'
                ];
            }

            return [
                'success' => true,
                'message' => 'Database connection successful',
                'connection' => $tenant->getConnectionName(),
                'database' => $databaseName
            ];
        } catch (\Exception $e) {
            // Log the full exception for debugging
            \Log::error('Database connection test failed', [
                'tenant_id' => $tenant->id,
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'connection' => $tenant->getConnectionName(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create tenant database if it doesn't exist.
     */
    public function createTenantDatabase(Tenant $tenant): array
    {
        if (!$tenant->usesSeparateDatabase()) {
            return [
                'success' => true,
                'message' => 'No separate database needed for shared strategy'
            ];
        }

        try {
            // Connect to MySQL server without specifying database
            $config = $tenant->getDatabaseConfig();

            // Check if we have valid database configuration
            if (empty($config) || !isset($config['database'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid database configuration. Please ensure database strategy is set to separate and all required fields are filled.',
                    'error' => 'Missing database configuration'
                ];
            }

            $serverConfig = $config;
            if (isset($serverConfig['database'])) {
                unset($serverConfig['database']);
            }
            // Add a default database to prevent Laravel connector warnings
            $serverConfig['database'] = 'mysql';

            $connectionName = "temp_{$tenant->id}";
            Config::set("database.connections.{$connectionName}", $serverConfig);

            $connection = DB::connection($connectionName);

            // Create database if it doesn't exist
            $databaseName = $tenant->database_name;
            $charset = $tenant->database_charset;
            $collation = $tenant->database_collation;

            $connection->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET {$charset} COLLATE {$collation}");

            // Clean up temporary connection
            DB::purge($connectionName);
            Config::set("database.connections.{$connectionName}", null);

            // Register the actual tenant connection now that the database exists
            $this->switchToTenantDatabase($tenant);

            return [
                'success' => true,
                'message' => "Database '{$databaseName}' created successfully"
            ];
        } catch (\Exception $e) {
            \Log::error('Database creation failed for tenant: ' . $tenant->id, [
                'tenant_id' => $tenant->id,
                'database_config' => $config,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create database: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Run migrations for tenant database.
     */
    public function runTenantMigrations(Tenant $tenant): array
    {
        if (!$tenant->usesSeparateDatabase()) {
            return [
                'success' => true,
                'message' => 'No separate migrations needed for shared strategy'
            ];
        }

        try {
            // Check if we have valid database configuration
            $config = $tenant->getDatabaseConfig();
            if (empty($config) || !isset($config['database'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid database configuration. Please ensure database strategy is set to separate and all required fields are filled.',
                    'error' => 'Missing database configuration'
                ];
            }

            $this->switchToTenantDatabase($tenant);

            // Run migrations
            \Artisan::call('migrate', [
                '--database' => $tenant->getConnectionName(),
                '--force' => true
            ]);

            return [
                'success' => true,
                'message' => 'Migrations completed successfully',
                'output' => \Artisan::output()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get tenant database tables.
     */
    public function getTenantTables(Tenant $tenant): array
    {
        try {
            $connection = $this->getTenantConnection($tenant);

            // Get tables using raw SQL query
            $tables = $connection->select("SHOW TABLES");
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables);

            return [
                'success' => true,
                'tables' => $tableNames
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get tables: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Reset to default database connection.
     */
    public function resetToDefaultConnection(): void
    {
        Config::set('database.default', 'mysql');
    }

    /**
     * Get all tenant connections.
     */
    public function getAllTenantConnections(): array
    {
        $connections = [];

        foreach (Config::get('database.connections') as $name => $config) {
            if (str_starts_with($name, 'tenant_')) {
                $connections[$name] = $config;
            }
        }

        return $connections;
    }

    /**
     * Clean up tenant connections.
     */
    public function cleanupTenantConnections(): void
    {
        $tenantConnections = $this->getAllTenantConnections();

        foreach (array_keys($tenantConnections) as $connectionName) {
            DB::purge($connectionName);
            Config::forget("database.connections.{$connectionName}");
        }
    }

    /**
     * Create primary admin user for tenant database.
     */
    public function createPrimaryAdminUser(Tenant $tenant): array
    {
        if (!$tenant->usesSeparateDatabase()) {
            return [
                'success' => true,
                'message' => 'No separate admin user needed for shared strategy'
            ];
        }

        try {
            $this->switchToTenantDatabase($tenant);

            // Check if admin user already exists
            $existingAdmin = DB::connection($tenant->getConnectionName())
                ->table('admin_users')
                ->where('email', $tenant->data['email'])
                ->first();

            if ($existingAdmin) {
                return [
                    'success' => true,
                    'message' => 'Primary admin user already exists',
                    'user' => $existingAdmin
                ];
            }

            // Create primary admin user
            $adminUser = [
                'name' => $tenant->data['name'] . ' Admin',
                'email' => $tenant->data['email'],
                'admin_type' => 'super_admin', // Primary admin should be super_admin
                'password' => Hash::make('admin123'), // Default password
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $userId = DB::connection($tenant->getConnectionName())
                ->table('admin_users')
                ->insertGetId($adminUser);

            return [
                'success' => true,
                'message' => 'Primary admin user created successfully',
                'user_id' => $userId,
                'email' => $adminUser['email'],
                'default_password' => 'admin123'
            ];

        } catch (\Exception $e) {
            \Log::error('Failed to create primary admin user for tenant: ' . $tenant->id, [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create primary admin user: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
}
