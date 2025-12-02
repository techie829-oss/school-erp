<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeBook extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'class_id',
        'section_id',
        'academic_year',
        'term',
        'total_marks',
        'max_total_marks',
        'percentage',
        'overall_grade',
        'overall_gpa',
        'rank',
        'total_subjects',
        'passed_subjects',
        'failed_subjects',
        'status',
        'is_published',
        'published_at',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'max_total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'overall_gpa' => 'decimal:2',
        'is_published' => 'boolean',
        'published_at' => 'date',
        'generated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Relationship to marks (via student, class, academic_year, term)
     */
    public function marks()
    {
        return Mark::forTenant($this->tenant_id)
            ->where('student_id', $this->student_id)
            ->where('class_id', $this->class_id)
            ->when($this->section_id, function($query) {
                $query->where('section_id', $this->section_id);
            });
    }

    /**
     * Scopes
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeForAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeForTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePassed($query)
    {
        return $query->where('status', 'pass');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'fail');
    }

    /**
     * Calculate overall grade from percentage
     */
    public function calculateOverallGrade($tenantId)
    {
        $gradeScale = GradeScale::getGradeForPercentage($tenantId, $this->percentage);

        if ($gradeScale) {
            $this->overall_grade = $gradeScale->grade_name;
            $this->overall_gpa = $gradeScale->gpa_value;
            $this->status = $gradeScale->is_pass ? 'pass' : 'fail';
        } else {
            $this->status = $this->percentage >= 33 ? 'pass' : 'fail'; // Default 33% passing
        }
    }

    /**
     * Check if passed
     */
    public function isPassed(): bool
    {
        return $this->status === 'pass';
    }
}
