<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TenantContextService
{
    private static ?Tenant $currentTenant = null;
    private static bool $contextInitialized = false;
    private static array $originalConfig = [];

    /**
     * Initialize tenant context
     */
    public function initializeContext(Tenant $tenant): void
    {
        if (self::$contextInitialized && self::$currentTenant?->id === $tenant->id) {
            return; // Already initialized for this tenant
        }

        // Store original configuration
        if (!self::$contextInitialized) {
            self::$originalConfig = [
                'database.default' => Config::get('database.default'),
                'cache.default' => Config::get('cache.default'),
                'session.domain' => Config::get('session.domain'),
            ];
        }

        self::$currentTenant = $tenant;
        self::$contextInitialized = true;

        Log::info('Initializing tenant context', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->data['name'] ?? 'Unknown',
            'database_strategy' => $tenant->data['database_strategy'] ?? 'shared'
        ]);

        $this->configureDatabase($tenant);
        $this->configureCache($tenant);
        $this->configureSession($tenant);
    }

    /**
     * Configure database based on tenant strategy
     */
    private function configureDatabase(Tenant $tenant): void
    {
        if ($tenant->usesSeparateDatabase()) {
            // Separate database tenant
            $connectionName = $tenant->getConnectionName();

            // Check if we're on tenant domain (not admin domain)
            $currentHost = request()->getHost();
            $adminDomain = config('all.domains.admin');
            $isAdminContext = ($currentHost === $adminDomain);

            $databaseConfig = null;
            $envLoaded = false;

            // Only load env file if we're on tenant domain (not admin)
            if (!$isAdminContext) {
                $envService = new TenantEnvironmentService();
                $tenantEnv = $envService->loadTenantEnvironment($tenant);

                if (!empty($tenantEnv)) {
                    $databaseConfig = $envService->buildDatabaseConfig($tenantEnv);
                    $envLoaded = true;
                }
            }

            // Fallback to tenant model configuration (always used in admin context)
            if (empty($databaseConfig)) {
                $databaseConfig = $tenant->getDatabaseConfig();
            }

            // Validate database configuration
            if (empty($databaseConfig['database'])) {
                Log::error('Invalid database configuration for tenant', [
                    'tenant_id' => $tenant->id,
                    'config' => $databaseConfig,
                    'is_admin_context' => $isAdminContext
                ]);
                throw new \RuntimeException("Invalid database configuration for tenant {$tenant->id}");
            }

            // Add the connection to the database configuration
            Config::set("database.connections.{$connectionName}", $databaseConfig);

            // Set as default connection
            Config::set('database.default', $connectionName);

            // Reconnect database to apply new configuration
            DB::purge($connectionName);

            Log::info('Configured separate database', [
                'tenant_id' => $tenant->id,
                'connection' => $connectionName,
                'database' => $databaseConfig['database'] ?? 'unknown',
                'host' => $databaseConfig['host'] ?? 'unknown',
                'source' => $envLoaded ? 'env_file' : 'tenant_model',
                'is_admin_context' => $isAdminContext
            ]);

            // Don't test connection here - will be tested on actual use
            Log::info('Database connection configured (will connect on first use)');
        } else {
            // Shared database tenant - use main connection
            Config::set('database.default', 'mysql');

            Log::info('Configured shared database', [
                'tenant_id' => $tenant->id,
                'connection' => 'mysql'
            ]);
        }
    }

    /**
     * Configure cache based on tenant strategy
     */
    private function configureCache(Tenant $tenant): void
    {
        if ($tenant->usesSeparateDatabase()) {
            // For separate database tenants, use tenant-specific cache prefix
            $cachePrefix = "tenant_{$tenant->id}_";
            Config::set('cache.prefix', $cachePrefix);

            Log::info('Configured tenant-specific cache', [
                'tenant_id' => $tenant->id,
                'prefix' => $cachePrefix
            ]);
        } else {
            // For shared database tenants, use tenant_id in cache key
            $cachePrefix = "shared_tenant_{$tenant->id}_";
            Config::set('cache.prefix', $cachePrefix);

            Log::info('Configured shared tenant cache', [
                'tenant_id' => $tenant->id,
                'prefix' => $cachePrefix
            ]);
        }
    }

    /**
     * Configure session based on tenant strategy
     */
    private function configureSession(Tenant $tenant): void
    {
        // Always use main database for sessions to avoid conflicts
        Config::set('session.connection', 'mysql');

        // Add tenant context to session
        session(['tenant_context' => [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->data['name'] ?? 'Unknown',
            'database_strategy' => $tenant->data['database_strategy'] ?? 'shared',
            'subdomain' => $tenant->data['subdomain'] ?? null
        ]]);

        Log::info('Configured session for tenant', [
            'tenant_id' => $tenant->id,
            'database_strategy' => $tenant->data['database_strategy'] ?? 'shared'
        ]);
    }

    /**
     * Get current tenant
     */
    public static function getCurrentTenant(): ?Tenant
    {
        return self::$currentTenant;
    }

    /**
     * Check if context is initialized
     */
    public static function isContextInitialized(): bool
    {
        return self::$contextInitialized;
    }

    /**
     * Reset context to original state
     */
    public function resetContext(): void
    {
        if (!self::$contextInitialized) {
            return;
        }

        Log::info('Resetting tenant context', [
            'previous_tenant' => self::$currentTenant?->id ?? 'none'
        ]);

        // Restore original configuration
        foreach (self::$originalConfig as $key => $value) {
            Config::set($key, $value);
        }

        // Clear tenant context from session
        session()->forget('tenant_context');

        // Reset static properties
        self::$currentTenant = null;
        self::$contextInitialized = false;
        self::$originalConfig = [];

        // Purge tenant-specific database connections
        $this->purgeTenantConnections();
    }

    /**
     * Purge tenant-specific database connections
     */
    private function purgeTenantConnections(): void
    {
        $connections = Config::get('database.connections', []);

        foreach ($connections as $name => $config) {
            if (str_starts_with($name, 'tenant_')) {
                DB::purge($name);
                Config::set("database.connections.{$name}", null);
            }
        }
    }

    /**
     * Execute code within tenant context
     */
    public function withinContext(Tenant $tenant, callable $callback)
    {
        $this->initializeContext($tenant);

        try {
            return $callback();
        } finally {
            // Don't reset context here - let the middleware handle it
        }
    }

    /**
     * Get tenant-specific cache key
     */
    public static function getCacheKey(string $key, ?Tenant $tenant = null): string
    {
        $tenant = $tenant ?? self::$currentTenant;

        if (!$tenant) {
            return $key;
        }

        if ($tenant->usesSeparateDatabase()) {
            return "tenant_{$tenant->id}_{$key}";
        } else {
            return "shared_tenant_{$tenant->id}_{$key}";
        }
    }

    /**
     * Get tenant-specific database connection
     */
    public static function getDatabaseConnection(?Tenant $tenant = null): \Illuminate\Database\Connection
    {
        $tenant = $tenant ?? self::$currentTenant;

        if (!$tenant) {
            return DB::connection();
        }

        if ($tenant->usesSeparateDatabase()) {
            return DB::connection($tenant->getConnectionName());
        } else {
            return DB::connection('mysql');
        }
    }

    /**
     * Check if we're in a tenant context
     */
    public static function inTenantContext(): bool
    {
        return self::$contextInitialized && self::$currentTenant !== null;
    }

    /**
     * Get tenant context info
     */
    public static function getContextInfo(): array
    {
        if (!self::$contextInitialized) {
            return ['initialized' => false];
        }

        return [
            'initialized' => true,
            'tenant_id' => self::$currentTenant?->id,
            'tenant_name' => self::$currentTenant?->data['name'] ?? 'Unknown',
            'database_strategy' => self::$currentTenant?->data['database_strategy'] ?? 'shared',
            'current_connection' => DB::getDefaultConnection(),
            'cache_prefix' => Config::get('cache.prefix'),
        ];
    }
}
