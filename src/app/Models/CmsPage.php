<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'slug',
        'title',
        'meta_description',
        'meta_keywords',
        'content',
        'settings',
        'is_published',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * Get the tenant that owns the page.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Scope a query to only include pages for a specific tenant.
     */
    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope a query to only include published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Get page by slug for tenant.
     */
    public static function getBySlug(string $tenantId, string $slug)
    {
        return static::forTenant($tenantId)->where('slug', $slug)->first();
    }
}
