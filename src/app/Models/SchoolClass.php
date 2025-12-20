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
        'has_sections',
        'description',
        'capacity',
        'room_number',
        'class_teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_sections' => 'boolean',
        'class_numeric' => 'integer',
        'capacity' => 'integer',
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
     * Get the class teacher
     */
    public function classTeacher()
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
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

    /**
     * Get subjects assigned to this class
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
            ->withPivot('is_active', 'tenant_id')
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    /**
     * Get all subjects (including inactive)
     */
    public function allSubjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Accessor for 'name' (alias for class_name)
     * Makes it easier to use in relationships: $class->name
     */
    public function getNameAttribute()
    {
        return $this->class_name;
    }
}
