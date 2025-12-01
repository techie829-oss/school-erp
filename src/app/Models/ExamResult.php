<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'exam_id',
        'exam_schedule_id',
        'student_id',
        'subject_id',
        'class_id',
        'section_id',
        'marks_obtained',
        'max_marks',
        'passing_marks',
        'percentage',
        'grade',
        'gpa',
        'status',
        'is_absent',
        'is_re_exam',
        'original_marks',
        'moderation_reason',
        'moderated_by',
        'moderated_at',
        'entered_by',
        'entered_at',
        'remarks',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'max_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'gpa' => 'decimal:2',
        'original_marks' => 'decimal:2',
        'is_absent' => 'boolean',
        'is_re_exam' => 'boolean',
        'moderated_at' => 'datetime',
        'entered_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function moderatedBy()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Scopes
     */
    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

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
        return $query->where('status', 'absent')->orWhere('is_absent', true);
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
     * Check if passed
     */
    public function isPassed(): bool
    {
        if ($this->is_absent) {
            return false;
        }

        if ($this->passing_marks) {
            return $this->marks_obtained >= $this->passing_marks;
        }

        return $this->percentage >= 33; // Default 33% passing
    }
}

