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
        return $this->data['is_active'] ?? true;
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
