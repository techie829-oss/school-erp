<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantColorPalette extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'tenant_id',
        'name',
        'is_active',
        'colors',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'colors' => 'array',
    ];

    /**
     * Get the tenant that owns the color palette.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all primary colors as an array.
     */
    public function getPrimaryColorsAttribute(): array
    {
        return $this->colors['primary'] ?? [];
    }

    /**
     * Get all secondary colors as an array.
     */
    public function getSecondaryColorsAttribute(): array
    {
        return $this->colors['secondary'] ?? [];
    }

    /**
     * Get all accent colors as an array.
     */
    public function getAccentColorsAttribute(): array
    {
        return $this->colors['accent'] ?? [];
    }

    /**
     * Get all status colors as an array.
     */
    public function getStatusColorsAttribute(): array
    {
        return [
            'success' => $this->colors['success'] ?? null,
            'warning' => $this->colors['warning'] ?? null,
            'error' => $this->colors['error'] ?? null,
            'info' => $this->colors['info'] ?? null,
        ];
    }

    /**
     * Get all colors as a structured array.
     */
    public function getAllColorsAttribute(): array
    {
        return $this->colors ?? [];
    }

    /**
     * Scope to get only active color palettes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the default color palette for a tenant.
     */
    public static function getDefaultForTenant(string $tenantId): self
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Default'],
            [
                'is_active' => true,
                'colors' => [
                    'primary' => [
                        '50' => '#eff6ff',
                        '100' => '#dbeafe',
                        '500' => '#3b82f6',
                        '600' => '#2563eb',
                        '700' => '#1d4ed8',
                        '900' => '#1e3a8a',
                    ],
                    'secondary' => [
                        '50' => '#f8fafc',
                        '100' => '#f1f5f9',
                        '500' => '#64748b',
                        '600' => '#475569',
                        '700' => '#334155',
                        '900' => '#0f172a',
                    ],
                    'accent' => [
                        '50' => '#fef3c7',
                        '100' => '#fde68a',
                        '500' => '#f59e0b',
                        '600' => '#d97706',
                        '700' => '#b45309',
                        '900' => '#78350f',
                    ],
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'error' => '#ef4444',
                    'info' => '#3b82f6',
                ],
            ]
        );
    }
}
