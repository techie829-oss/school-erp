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

    protected $casts = [
        'id' => 'string',
        'data' => 'array',
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
}
