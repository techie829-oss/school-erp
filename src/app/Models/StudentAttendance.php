<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory, ForTenant;

    protected $table = 'student_attendance';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'class_id',
        'section_id',
        'attendance_date',
        'status',
        'period_number',
        'subject_id',
        'teacher_id',
        'leave_reason',
        'leave_approved_by',
        'remarks',
        'marked_by',
        'marked_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'marked_at' => 'datetime',
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

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Scopes
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('attendance_date', $month)
                    ->whereYear('attendance_date', $year);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Check if student was present
     */
    public function isPresent(): bool
    {
        return in_array($this->status, ['present', 'late']);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'present' => 'green',
            'absent' => 'red',
            'late' => 'yellow',
            'half_day' => 'blue',
            'on_leave' => 'purple',
            'holiday' => 'gray',
            default => 'gray',
        };
    }
}

