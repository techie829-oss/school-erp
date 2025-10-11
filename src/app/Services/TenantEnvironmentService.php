<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Dotenv\Dotenv;

class TenantEnvironmentService
{
    protected static array $loadedEnvFiles = [];

    /**
     * Load domain-specific environment file if exists
     */
    public function loadTenantEnvironment(Tenant $tenant): array
    {
        $subdomain = $tenant->data['subdomain'] ?? null;

        if (!$subdomain) {
            Log::warning('Tenant has no subdomain defined', ['tenant_id' => $tenant->id]);
            return $this->getDefaultTenantConfig($tenant);
        }

        // Build full domain
        $primaryDomain = config('all.domains.primary');
        $fullDomain = "{$subdomain}.{$primaryDomain}";

        // Check if we already loaded this env file
        if (isset(self::$loadedEnvFiles[$fullDomain])) {
            return self::$loadedEnvFiles[$fullDomain];
        }

        $basePath = base_path();
        $envFilePath = $basePath . "/.env.{$fullDomain}";

        Log::info('Loading domain-specific environment', [
            'tenant_id' => $tenant->id,
            'subdomain' => $subdomain,
            'full_domain' => $fullDomain,
            'env_file' => $envFilePath,
            'file_exists' => File::exists($envFilePath)
        ]);

        // If domain-specific env file exists, load it
        if (File::exists($envFilePath)) {
            try {
                $config = $this->parseEnvFile($envFilePath);
                self::$loadedEnvFiles[$fullDomain] = $config;

                Log::info('Domain environment loaded successfully', [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $subdomain,
                    'full_domain' => $fullDomain,
                    'config_keys' => array_keys($config)
                ]);

                return $config;
            } catch (\Exception $e) {
                Log::error('Failed to load domain environment file', [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $subdomain,
                    'full_domain' => $fullDomain,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback to tenant model database configuration
        return $this->getDefaultTenantConfig($tenant);
    }

    /**
     * Parse environment file and return configuration array
     */
    protected function parseEnvFile(string $filePath): array
    {
        $config = [];
        $lines = File::lines($filePath);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Parse KEY=VALUE
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                $value = trim($value, '"\'');

                $config[$key] = $value;
            }
        }

        return $config;
    }

    /**
     * Get default tenant configuration from model
     */
    protected function getDefaultTenantConfig(Tenant $tenant): array
    {
        if (!$tenant->usesSeparateDatabase()) {
            return [];
        }

        return [
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $tenant->database_host ?? $tenant->data['database_host'] ?? '127.0.0.1',
            'DB_PORT' => $tenant->database_port ?? $tenant->data['database_port'] ?? '3306',
            'DB_DATABASE' => $tenant->database_name ?? $tenant->data['database_name'] ?? '',
            'DB_USERNAME' => $tenant->database_username ?? $tenant->data['database_username'] ?? 'root',
            'DB_PASSWORD' => $tenant->database_password ?? $tenant->data['database_password'] ?? '',
            'DB_CHARSET' => $tenant->database_charset ?? $tenant->data['database_charset'] ?? 'utf8mb4',
            'DB_COLLATION' => $tenant->database_collation ?? $tenant->data['database_collation'] ?? 'utf8mb4_unicode_ci',
        ];
    }

    /**
     * Build database configuration array from tenant environment
     */
    public function buildDatabaseConfig(array $tenantEnv): array
    {
        return [
            'driver' => $tenantEnv['DB_CONNECTION'] ?? 'mysql',
            'host' => $tenantEnv['DB_HOST'] ?? '127.0.0.1',
            'port' => (int)($tenantEnv['DB_PORT'] ?? 3306),
            'database' => $tenantEnv['DB_DATABASE'] ?? '',
            'username' => $tenantEnv['DB_USERNAME'] ?? 'root',
            'password' => $tenantEnv['DB_PASSWORD'] ?? '',
            'unix_socket' => '', // Force TCP/IP connection
            'charset' => $tenantEnv['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => $tenantEnv['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ];
    }

    /**
     * Create domain-specific environment file (full copy of main .env with DB changes)
     */
    public function createTenantEnvironmentFile(Tenant $tenant, array $config): bool
    {
        $subdomain = $tenant->data['subdomain'] ?? null;

        if (!$subdomain) {
            Log::error('Cannot create domain env file without subdomain', ['tenant_id' => $tenant->id]);
            return false;
        }

        // Build full domain
        $primaryDomain = config('all.domains.primary');
        $fullDomain = "{$subdomain}.{$primaryDomain}";

        $basePath = base_path();
        $mainEnvPath = $basePath . '/.env';
        $domainEnvPath = $basePath . "/.env.{$fullDomain}";

        try {
            // Read the main .env file
            if (!File::exists($mainEnvPath)) {
                Log::error('Main .env file not found', ['path' => $mainEnvPath]);
                return false;
            }

            $mainEnvContent = File::get($mainEnvPath);

            // Add header comment
            $content = "# ============================================================================\n";
            $content .= "# Domain-Specific Environment Configuration\n";
            $content .= "# Domain: {$fullDomain}\n";
            $content .= "# Generated at: " . now()->toDateTimeString() . "\n";
            $content .= "# This is a full copy of .env with modified database settings\n";
            $content .= "# Auto-loaded when accessing: http://{$fullDomain}\n";
            $content .= "# ============================================================================\n\n";

            // Process each line from main .env
            $lines = explode("\n", $mainEnvContent);

            foreach ($lines as $line) {
                $trimmedLine = trim($line);

                // Check if this line is a database configuration
                if (preg_match('/^DB_CONNECTION\s*=/', $trimmedLine)) {
                    $content .= "DB_CONNECTION=" . ($config['DB_CONNECTION'] ?? 'mysql') . "\n";
                } elseif (preg_match('/^DB_HOST\s*=/', $trimmedLine)) {
                    // Force 127.0.0.1 instead of localhost for TCP/IP
                    $host = $config['DB_HOST'] ?? '127.0.0.1';
                    if ($host === 'localhost') {
                        $host = '127.0.0.1';
                    }
                    $content .= "DB_HOST=" . $host . "\n";
                } elseif (preg_match('/^DB_PORT\s*=/', $trimmedLine)) {
                    $content .= "DB_PORT=" . ($config['DB_PORT'] ?? '3306') . "\n";
                } elseif (preg_match('/^DB_DATABASE\s*=/', $trimmedLine)) {
                    $content .= "DB_DATABASE=" . ($config['DB_DATABASE'] ?? '') . "\n";
                } elseif (preg_match('/^DB_USERNAME\s*=/', $trimmedLine)) {
                    $content .= "DB_USERNAME=" . ($config['DB_USERNAME'] ?? 'root') . "\n";
                } elseif (preg_match('/^DB_PASSWORD\s*=/', $trimmedLine)) {
                    $password = $config['DB_PASSWORD'] ?? '';
                    // Add quotes if password contains spaces
                    if (str_contains($password, ' ')) {
                        $password = "\"{$password}\"";
                    }
                    $content .= "DB_PASSWORD=" . $password . "\n";
                } elseif (preg_match('/^DB_SOCKET\s*=/', $trimmedLine) || preg_match('/^unix_socket\s*=/', $trimmedLine)) {
                    // Force empty socket to use TCP/IP
                    $content .= "DB_SOCKET=\n";
                } else {
                    // Keep all other lines as-is from original .env
                    $content .= $line . "\n";
                }
            }

            // Write the domain-specific environment file
            File::put($domainEnvPath, $content);

            Log::info('Domain environment file created (full copy)', [
                'tenant_id' => $tenant->id,
                'subdomain' => $subdomain,
                'full_domain' => $fullDomain,
                'file_path' => $domainEnvPath,
                'source' => $mainEnvPath,
                'database' => $config['DB_DATABASE'] ?? 'unknown'
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create domain environment file', [
                'tenant_id' => $tenant->id,
                'subdomain' => $subdomain,
                'full_domain' => $fullDomain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Delete domain-specific environment file
     */
    public function deleteTenantEnvironmentFile(Tenant $tenant): bool
    {
        $subdomain = $tenant->data['subdomain'] ?? null;

        if (!$subdomain) {
            return false;
        }

        // Build full domain
        $primaryDomain = config('all.domains.primary');
        $fullDomain = "{$subdomain}.{$primaryDomain}";

        $basePath = base_path();
        $envFilePath = $basePath . "/.env.{$fullDomain}";

        if (File::exists($envFilePath)) {
            try {
                File::delete($envFilePath);
                unset(self::$loadedEnvFiles[$fullDomain]);

                Log::info('Domain environment file deleted', [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $subdomain,
                    'full_domain' => $fullDomain
                ]);

                return true;
            } catch (\Exception $e) {
                Log::error('Failed to delete domain environment file', [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $subdomain,
                    'full_domain' => $fullDomain,
                    'error' => $e->getMessage()
                ]);

                return false;
            }
        }

        return true;
    }

    /**
     * Check if tenant has custom environment file
     */
    public function hasTenantEnvironmentFile(Tenant $tenant): bool
    {
        $subdomain = $tenant->data['subdomain'] ?? null;

        if (!$subdomain) {
            return false;
        }

        // Build full domain
        $primaryDomain = config('all.domains.primary');
        $fullDomain = "{$subdomain}.{$primaryDomain}";

        $basePath = base_path();
        $envFilePath = $basePath . "/.env.{$fullDomain}";

        return File::exists($envFilePath);
    }

    /**
     * Clear loaded environment cache
     */
    public static function clearCache(): void
    {
        self::$loadedEnvFiles = [];
    }
}

