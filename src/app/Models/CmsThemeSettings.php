<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsThemeSettings extends Model
{
    use HasFactory, ForTenant;

    protected $table = 'cms_theme_settings';

    protected $fillable = [
        'tenant_id',
        'primary_color_50',
        'primary_color_100',
        'primary_color_500',
        'primary_color_600',
        'primary_color_700',
        'primary_color_900',
        'secondary_color_50',
        'secondary_color_100',
        'secondary_color_500',
        'secondary_color_600',
        'secondary_color_700',
        'secondary_color_900',
        'accent_color_50',
        'accent_color_100',
        'accent_color_500',
        'accent_color_600',
        'accent_color_700',
        'accent_color_900',
        'success_color',
        'warning_color',
        'error_color',
        'info_color',
        'custom_css',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    // Scopes
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // Accessors - Get colors as array
    public function getPrimaryColorsAttribute()
    {
        return [
            '50' => $this->primary_color_50,
            '100' => $this->primary_color_100,
            '500' => $this->primary_color_500,
            '600' => $this->primary_color_600,
            '700' => $this->primary_color_700,
            '900' => $this->primary_color_900,
        ];
    }

    public function getSecondaryColorsAttribute()
    {
        return [
            '50' => $this->secondary_color_50,
            '100' => $this->secondary_color_100,
            '500' => $this->secondary_color_500,
            '600' => $this->secondary_color_600,
            '700' => $this->secondary_color_700,
            '900' => $this->secondary_color_900,
        ];
    }

    public function getAccentColorsAttribute()
    {
        return [
            '50' => $this->accent_color_50,
            '100' => $this->accent_color_100,
            '500' => $this->accent_color_500,
            '600' => $this->accent_color_600,
            '700' => $this->accent_color_700,
            '900' => $this->accent_color_900,
        ];
    }
}

