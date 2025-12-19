<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'class_id',
        'section_name',
        'capacity',
        'room_number',
        'class_teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Get the tenant that owns the section
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the class that this section belongs to
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the class teacher
     */
    public function classTeacher()
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    /**
     * Get all enrollments for this section
     */
    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class, 'section_id');
    }

    /**
     * Get current enrollments (active students)
     */
    public function currentEnrollments()
    {
        return $this->enrollments()->where('is_current', true);
    }

    /**
     * Get all students in this section (via current enrollments)
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            ClassEnrollment::class,
            'section_id',
            'id',
            'id',
            'student_id'
        )->where('class_enrollments.is_current', true);
    }

    /**
     * Get students count
     */
    public function getStudentsCountAttribute()
    {
        return $this->currentEnrollments()->count();
    }

    /**
     * Check if section is full
     */
    public function isFull()
    {
        return $this->capacity && $this->students_count >= $this->capacity;
    }

    /**
     * Get available seats
     */
    public function getAvailableSeatsAttribute()
    {
        if (!$this->capacity) {
            return null;
        }
        return max(0, $this->capacity - $this->students_count);
    }

    /**
     * Scope to filter active sections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by class
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Get subjects assigned to this section
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'section_subjects', 'section_id', 'subject_id')
            ->withPivot('is_active')
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    /**
     * Get all subjects (including inactive)
     */
    public function allSubjects()
    {
        return $this->belongsToMany(Subject::class, 'section_subjects', 'section_id', 'subject_id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Accessor for 'name' (alias for section_name)
     * Makes it easier to use in relationships: $section->name
     */
    public function getNameAttribute()
    {
        return $this->section_name;
    }
}
