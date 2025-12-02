<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'isbn',
        'title',
        'author',
        'publisher',
        'category_id',
        'edition',
        'copies',
        'available_copies',
        'price',
        'description',
        'language',
        'publication_year',
        'rack_number',
        'barcode',
        'status',
    ];

    protected $casts = [
        'copies' => 'integer',
        'available_copies' => 'integer',
        'price' => 'decimal:2',
        'publication_year' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function issues()
    {
        return $this->hasMany(BookIssue::class, 'book_id');
    }

    public function activeIssues()
    {
        return $this->hasMany(BookIssue::class, 'book_id')
            ->whereIn('status', ['issued', 'overdue']);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('available_copies', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    // Accessors & Mutators
    public function getIsAvailableAttribute()
    {
        return $this->status === 'available' && $this->available_copies > 0;
    }

    // Methods
    public function incrementAvailable()
    {
        $this->increment('available_copies');
    }

    public function decrementAvailable()
    {
        if ($this->available_copies > 0) {
            $this->decrement('available_copies');
        }
    }
}
