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
    ];


    /**
     * Get the color palettes for this tenant.
     */
    public function colorPalettes(): HasMany
    {
        return $this->hasMany(TenantColorPalette::class);
    }

    /**
     * Get the admin users for this tenant.
     */
    public function adminUsers(): HasMany
    {
        return $this->hasMany(AdminUser::class);
    }

    /**
     * Get the active color palette for this tenant.
     */
    public function activeColorPalette()
    {
        return $this->colorPalettes()->where('is_active', true)->first();
    }

    /**
     * Get tenant name from data.
     */
    public function getNameAttribute(): string
    {
        return $this->data['name'] ?? 'Unknown Tenant';
    }

    /**
     * Get tenant subdomain from data.
     */
    public function getSubdomainAttribute(): ?string
    {
        return $this->data['subdomain'] ?? null;
    }

    /**
     * Get tenant full domain from data.
     */
    public function getFullDomainAttribute(): ?string
    {
        $subdomain = $this->getSubdomainAttribute();
        if (!$subdomain) {
            return null;
        }

        $primaryDomain = config('all.domains.primary');
        return "{$subdomain}.{$primaryDomain}";
    }

    /**
     * Check if tenant is active.
     */
    public function isActive(): bool
    {
        $isActive = $this->data['is_active'] ?? true;

        // Handle both boolean and string values (from database)
        if (is_bool($isActive)) {
            return $isActive;
        }

        // Handle string values like '1', 'true', 'yes', etc.
        if (is_string($isActive)) {
            return in_array(strtolower($isActive), ['1', 'true', 'yes', 'on'], true);
        }

        // Handle numeric values
        if (is_numeric($isActive)) {
            return (int)$isActive === 1;
        }

        // Default to true if not set
        return true;
    }

    /**
     * Check if tenant uses separate database.
     * Currently all tenants use shared database.
     */
    public function usesSeparateDatabase(): bool
    {
        return ($this->data['database_strategy'] ?? 'shared') === 'separate';
    }

    /**
     * Get the database connection name for this tenant.
     */
    public function getConnectionName(): string
    {
        if ($this->usesSeparateDatabase()) {
            return 'tenant_' . $this->id;
        }
        return 'mysql';
    }

    /**
     * Get the database connection for the model.
     */
    public function getConnection()
    {
        // Always use the default MySQL connection for shared database tenants
        return $this->resolveConnection('mysql');
    }
}
