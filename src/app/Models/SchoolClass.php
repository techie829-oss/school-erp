<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory, ForTenant;

    protected $table = 'classes';

    protected $fillable = [
        'tenant_id',
        'class_name',
        'class_numeric',
        'class_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'class_numeric' => 'integer',
    ];

    /**
     * Get the tenant that owns the class
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get all sections for this class
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    /**
     * Get all enrollments for this class
     */
    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class, 'class_id');
    }

    /**
     * Get current enrollments (active students)
     */
    public function currentEnrollments()
    {
        return $this->enrollments()->where('is_current', true);
    }

    /**
     * Get all students in this class (via current enrollments)
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            ClassEnrollment::class,
            'class_id',
            'id',
            'id',
            'student_id'
        )->where('class_enrollments.is_current', true);
    }

    /**
     * Get active sections only
     */
    public function activeSections()
    {
        return $this->sections()->where('is_active', true);
    }

    /**
     * Scope to filter active classes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by numeric value
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('class_numeric')->orderBy('class_name');
    }
}
