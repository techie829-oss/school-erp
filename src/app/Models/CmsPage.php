<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    use HasFactory, ForTenant;

    protected $table = 'cms_pages';

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'template',
        'status',
        'published_at',
        'author_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured_image',
        'parent_id',
        'order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(CmsPage::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CmsPage::class, 'parent_id')->orderBy('order');
    }

    // Scopes
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where(function($q) {
                        $q->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                    });
    }

    public function scopeByTemplate($query, $template)
    {
        return $query->where('template', $template);
    }

    // Mutators
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ? Str::slug($value) : Str::slug($this->title);
    }

    // Accessors
    public function getUrlAttribute()
    {
        if ($this->template === 'home') {
            return url('/');
        }
        return url('/' . $this->slug);
    }
}

