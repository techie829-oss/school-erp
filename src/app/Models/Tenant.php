<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'data',
        'database_name',
        'database_host',
        'database_port',
        'database_username',
        'database_password',
        'database_charset',
        'database_collation',
    ];

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'mysql';

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'data' => 'array',
        'database_port' => 'integer',
    ];

    /**
     * Get the color palettes for this tenant.
     */
    public function colorPalettes(): HasMany
    {
        return $this->hasMany(TenantColorPalette::class, 'tenant_id', 'id');
    }

    /**
     * Get the active color palette for this tenant.
     */
    public function activeColorPalette()
    {
        return $this->colorPalettes()->where('is_active', true)->first();
    }

    /**
     * Get tenant name from data array.
     */
    public function getNameAttribute(): string
    {
        return $this->data['name'] ?? 'Unknown Tenant';
    }

    /**
     * Get tenant domain from data array.
     */
    public function getDomainAttribute(): string
    {
        return $this->data['domain'] ?? 'unknown.test';
    }

    /**
     * Get tenant status from data array.
     */
    public function getStatusAttribute(): string
    {
        return $this->data['status'] ?? 'inactive';
    }

    /**
     * Check if tenant uses separate database.
     */
    public function usesSeparateDatabase(): bool
    {
        // During console operations (seeding, commands), check if we're in a tenant management context
        // This prevents issues during database seeding but allows tenant management operations
        if (app()->runningInConsole()) {
            // Allow separate database for tenant management commands
            $argv = request()->server('argv') ?? [];
            $command = $argv[1] ?? '';

            // Allow separate database for tenant-related commands or when not in seeding context
            if (str_contains($command, 'tenant') ||
                str_contains($command, 'test:') ||
                str_contains($command, 'debug') ||
                !str_contains($command, 'seed')) {
                // Allow separate database for tenant-related commands
            } else {
                return false;
            }
        }

        // In admin context (admin domain), allow separate database operations
        // but the model itself will use shared database (handled in getConnection methods)
        try {
            if (request() && request()->getHost() === config('all.domains.admin')) {
                // Allow separate database operations in admin context
                // The actual database switching will be handled by the service layer
            }
        } catch (\Exception $e) {
            // If request is not available, continue with normal logic
        }

        // Check if database strategy is separate and has required configuration
        $strategy = $this->data['database_strategy'] ?? 'shared';
        if ($strategy !== 'separate') {
            return false;
        }

        // Check if we have database configuration (either in columns or data)
        $dbName = $this->database_name ?? $this->data['database_name'] ?? null;
        $dbHost = $this->database_host ?? $this->data['database_host'] ?? null;

        return !empty($dbName) && !empty($dbHost);
    }

    /**
     * Get database strategy from data array.
     */
    public function getDatabaseStrategyAttribute(): string
    {
        return $this->data['database_strategy'] ?? 'shared';
    }

    /**
     * Get database configuration array.
     */
    public function getDatabaseConfig(): array
    {
        if (!$this->usesSeparateDatabase()) {
            return [];
        }

        return [
            'driver' => 'mysql',
            'host' => $this->database_host ?? $this->data['database_host'] ?? '127.0.0.1',
            'port' => $this->database_port ?? $this->data['database_port'] ?? 3306,
            'database' => $this->database_name ?? $this->data['database_name'] ?? '',
            'username' => $this->database_username ?? $this->data['database_username'] ?? 'root',
            'password' => $this->database_password ?? $this->data['database_password'] ?? '',
            'unix_socket' => '', // Force TCP/IP connection instead of socket
            'charset' => $this->database_charset ?? $this->data['database_charset'] ?? 'utf8mb4',
            'collation' => $this->database_collation ?? $this->data['database_collation'] ?? 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ];
    }

    /**
     * Get connection name for this tenant.
     */
    public function getConnectionName(): string
    {
        // Check if we have complete database configuration for separate database
        if ($this->usesSeparateDatabase()) {
            // Double-check that we have valid database configuration
            $config = $this->getDatabaseConfig();
            if (empty($config) || !isset($config['database']) || empty($config['database'])) {
                return 'mysql';
            }

            return "tenant_{$this->id}";
        }

        return 'mysql';
    }

    /**
     * Get the database connection for the model.
     */
    public function getConnection()
    {
        // In admin context, always use shared database
        try {
            if (request() && request()->getHost() === config('all.domains.admin')) {
                return $this->resolveConnection('mysql');
            }
        } catch (\Exception $e) {
            // If request is not available, continue with normal logic
        }

        // Check if we should use separate database and if it's properly configured
        if ($this->usesSeparateDatabase()) {
            $config = $this->getDatabaseConfig();
            if (empty($config) || !isset($config['database']) || empty($config['database'])) {
                // If database configuration is incomplete, use shared database
                return $this->resolveConnection('mysql');
            }

            // Check if the tenant connection is already registered
            $connectionName = "tenant_{$this->id}";
            if (!\Config::has("database.connections.{$connectionName}")) {
                // If connection is not registered, use shared database
                return $this->resolveConnection('mysql');
            }
        }

        return parent::getConnection();
    }
}
