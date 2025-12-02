<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'class_id',
        'section_id',
        'subject_id',
        'exam_id',
        'mark_type',
        'title',
        'assessment_date',
        'marks_obtained',
        'max_marks',
        'percentage',
        'grade',
        'gpa',
        'status',
        'is_absent',
        'entered_by',
        'entered_at',
        'remarks',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'marks_obtained' => 'decimal:2',
        'max_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'gpa' => 'decimal:2',
        'is_absent' => 'boolean',
        'entered_at' => 'datetime',
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

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
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

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('mark_type', $type);
    }

    public function scopePassed($query)
    {
        return $query->where('status', 'pass');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'fail');
    }

    public function scopeAbsent($query)
    {
        return $query->where('is_absent', true);
    }

    /**
     * Calculate percentage
     */
    public function calculatePercentage(): float
    {
        if ($this->max_marks > 0) {
            return round(($this->marks_obtained / $this->max_marks) * 100, 2);
        }
        return 0;
    }

    /**
     * Calculate and assign grade based on percentage
     */
    public function calculateGrade($tenantId)
    {
        if ($this->is_absent) {
            $this->grade = null;
            $this->gpa = null;
            $this->status = 'fail';
            return;
        }

        $percentage = $this->calculatePercentage();
        $this->percentage = $percentage;

        $gradeScale = GradeScale::getGradeForPercentage($tenantId, $percentage);

        if ($gradeScale) {
            $this->grade = $gradeScale->grade_name;
            $this->gpa = $gradeScale->gpa_value;
            $this->status = $gradeScale->is_pass ? 'pass' : 'fail';
        } else {
            $this->status = $percentage >= 33 ? 'pass' : 'fail'; // Default 33% passing
        }
    }

    /**
     * Check if passed
     */
    public function isPassed(): bool
    {
        if ($this->is_absent) {
            return false;
        }

        return $this->status === 'pass';
    }
}
