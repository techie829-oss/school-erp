<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePlan extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'academic_year',
        'class_id',
        'term',
        'effective_from',
        'effective_to',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function feePlanItems()
    {
        return $this->hasMany(FeePlanItem::class);
    }

    public function studentFeeCards()
    {
        return $this->hasMany(StudentFeeCard::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Accessors
     */
    public function getTotalAmountAttribute()
    {
        return $this->feePlanItems()->sum('amount');
    }
}
