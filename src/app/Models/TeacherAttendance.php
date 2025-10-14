<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeacherAttendance extends Model
{
    use HasFactory, ForTenant;

    protected $table = 'teacher_attendance';

    protected $fillable = [
        'tenant_id',
        'teacher_id',
        'attendance_date',
        'status',
        'check_in_time',
        'check_out_time',
        'total_hours',
        'working_hours',
        'leave_type',
        'leave_id',
        'leave_reason',
        'remarks',
        'marked_by',
        'marked_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'total_hours' => 'decimal:2',
        'working_hours' => 'decimal:2',
        'marked_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
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
     * Accessors
     */
    public function getTotalHoursAttribute($value): ?float
    {
        if ($value) {
            return (float) $value;
        }

        if ($this->check_in_time && $this->check_out_time) {
            $checkIn = Carbon::parse($this->check_in_time);
            $checkOut = Carbon::parse($this->check_out_time);
            return round($checkIn->diffInHours($checkOut, true), 2);
        }

        return null;
    }

    /**
     * Scopes
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
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

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Check if teacher was present
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

