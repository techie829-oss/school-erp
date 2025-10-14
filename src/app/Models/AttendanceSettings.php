<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSettings extends Model
{
    use HasFactory, ForTenant;

    protected $table = 'attendance_settings';

    protected $fillable = [
        'tenant_id',
        'student_enable_period_wise',
        'student_periods_per_day',
        'student_half_day_threshold',
        'student_late_threshold_minutes',
        'teacher_working_hours_per_day',
        'teacher_half_day_threshold',
        'teacher_late_threshold_minutes',
        'teacher_enable_biometric',
        'week_start_day',
        'working_days',
        'holidays',
        'school_start_time',
        'school_end_time',
        'late_arrival_after',
        'grace_period_minutes',
        'notify_parent_on_absent',
        'notify_admin_on_teacher_absent',
        'low_attendance_threshold',
    ];

    protected $casts = [
        'student_enable_period_wise' => 'boolean',
        'student_half_day_threshold' => 'decimal:1',
        'teacher_working_hours_per_day' => 'decimal:1',
        'teacher_half_day_threshold' => 'decimal:1',
        'teacher_enable_biometric' => 'boolean',
        'working_days' => 'array',
        'holidays' => 'array',
        'notify_parent_on_absent' => 'boolean',
        'notify_admin_on_teacher_absent' => 'boolean',
        'low_attendance_threshold' => 'decimal:1',
    ];

    /**
     * Relationship
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get or create settings for tenant with defaults
     */
    public static function getForTenant($tenantId)
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            [
                'student_enable_period_wise' => false,
                'student_periods_per_day' => 1,
                'student_half_day_threshold' => 4.0,
                'student_late_threshold_minutes' => 15,
                'teacher_working_hours_per_day' => 8.0,
                'teacher_half_day_threshold' => 4.0,
                'teacher_late_threshold_minutes' => 15,
                'teacher_enable_biometric' => false,
                'week_start_day' => 'monday',
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'holidays' => [],
                'school_start_time' => '09:00:00',
                'school_end_time' => '17:00:00',
                'late_arrival_after' => '09:15:00',
                'grace_period_minutes' => 15,
                'notify_parent_on_absent' => true,
                'notify_admin_on_teacher_absent' => true,
                'low_attendance_threshold' => 75.0,
            ]
        );
    }

    /**
     * Check if a date is a holiday
     */
    public function isHoliday($date): bool
    {
        if (!$this->holidays) {
            return false;
        }

        $dateString = is_string($date) ? $date : $date->format('Y-m-d');
        return in_array($dateString, $this->holidays);
    }

    /**
     * Check if a day is a working day
     */
    public function isWorkingDay($dayOfWeek): bool
    {
        if (!$this->working_days) {
            return true;
        }

        $dayName = strtolower($dayOfWeek);
        return in_array($dayName, $this->working_days);
    }
}

