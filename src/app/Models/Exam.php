<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'exam_name',
        'exam_type',
        'academic_year',
        'class_id',
        'start_date',
        'end_date',
        'description',
        'status',
        'is_published',
        'publish_date',
        'created_by',
        'max_exams_per_day',
        'shift_selection_mode',
        'default_shift_id',
        'skip_weekends',
        'default_max_marks',
        'default_passing_marks',
        'default_duration_minutes',
        'scheduling_preferences',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'publish_date' => 'date',
        'is_published' => 'boolean',
        'skip_weekends' => 'boolean',
        'scheduling_preferences' => 'array',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    public function admitCards()
    {
        return $this->hasMany(AdmitCard::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function defaultShift()
    {
        return $this->belongsTo(ExamShift::class, 'default_shift_id');
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('exam_type', $type);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accessors
     */
    public function getExamTypeLabelAttribute(): string
    {
        return match($this->exam_type) {
            'mid_term' => 'Mid-term',
            'final' => 'Final',
            'unit_test' => 'Unit Test',
            'quiz' => 'Quiz',
            'assignment' => 'Assignment',
            default => ucfirst(str_replace('_', ' ', $this->exam_type)),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'published' => 'Published',
            'archived' => 'Archived',
            default => ucfirst($this->status),
        };
    }
}

