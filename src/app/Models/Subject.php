<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'subject_name',
        'subject_code',
        'subject_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects')
            ->withPivot('class_id', 'is_primary', 'years_teaching')
            ->withTimestamps();
    }

    /**
     * Get classes this subject is assigned to
     */
    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subjects', 'subject_id', 'class_id')
            ->withPivot('is_active')
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    /**
     * Get all classes (including inactive assignments)
     */
    public function allClasses()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subjects', 'subject_id', 'class_id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Get sections this subject is assigned to
     */
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_subjects', 'subject_id', 'section_id')
            ->withPivot('is_active')
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    /**
     * Get all sections (including inactive assignments)
     */
    public function allSections()
    {
        return $this->belongsToMany(Section::class, 'section_subjects', 'subject_id', 'section_id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('subject_type', $type);
    }
}

