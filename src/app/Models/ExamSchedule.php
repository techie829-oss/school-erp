<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'exam_id',
        'shift_id',
        'subject_id',
        'class_id',
        'section_id',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'room_number',
        'hall_ticket_prefix',
        'max_marks',
        'passing_marks',
        'instructions',
        'supervisor_id',
        'is_done',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_minutes' => 'integer',
        'max_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'is_done' => 'boolean',
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

    public function shift()
    {
        return $this->belongsTo(ExamShift::class);
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

    public function supervisor()
    {
        return $this->belongsTo(Teacher::class, 'supervisor_id');
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Scopes
     */
    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
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

    public function scopeOnDate($query, $date)
    {
        return $query->where('exam_date', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>=', now()->toDateString());
    }
}

