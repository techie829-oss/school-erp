<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'grade_name',
        'min_percentage',
        'max_percentage',
        'gpa_value',
        'description',
        'is_pass',
        'order',
        'is_active',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'gpa_value' => 'decimal:2',
        'is_pass' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePassing($query)
    {
        return $query->where('is_pass', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('min_percentage', 'desc');
    }

    /**
     * Get grade for a given percentage
     */
    public static function getGradeForPercentage($tenantId, $percentage)
    {
        return static::forTenant($tenantId)
            ->active()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();
    }
}

