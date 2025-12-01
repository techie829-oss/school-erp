<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'exam_id',
        'student_id',
        'class_id',
        'section_id',
        'total_marks',
        'max_total_marks',
        'overall_percentage',
        'overall_grade',
        'overall_gpa',
        'class_rank',
        'section_rank',
        'overall_status',
        'subjects_passed',
        'subjects_failed',
        'subjects_absent',
        'class_teacher_remarks',
        'principal_remarks',
        'attendance_percentage',
        'generated_by',
        'generated_at',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'max_total_marks' => 'decimal:2',
        'overall_percentage' => 'decimal:2',
        'overall_gpa' => 'decimal:2',
        'class_rank' => 'integer',
        'section_rank' => 'integer',
        'subjects_passed' => 'integer',
        'subjects_failed' => 'integer',
        'subjects_absent' => 'integer',
        'attendance_percentage' => 'decimal:2',
        'generated_at' => 'datetime',
        'published_at' => 'date',
        'is_published' => 'boolean',
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

    public function examResults()
    {
        return ExamResult::forTenant($this->tenant_id)
            ->where('exam_id', $this->exam_id)
            ->where('student_id', $this->student_id)
            ->get();
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

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
}

