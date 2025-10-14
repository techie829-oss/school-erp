<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    use HasFactory, ForTenant;

    protected $table = 'attendance_summary';

    protected $fillable = [
        'tenant_id',
        'attendable_type',
        'attendable_id',
        'month',
        'year',
        'total_days',
        'present_days',
        'absent_days',
        'late_days',
        'half_days',
        'leave_days',
        'holiday_days',
        'attendance_percentage',
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
    ];

    /**
     * Polymorphic relationship
     */
    public function attendable()
    {
        return $this->morphTo();
    }

    /**
     * Calculate and update summary for a given month
     */
    public static function calculateSummary($tenantId, $type, $id, $month, $year)
    {
        $model = $type === 'student' ? StudentAttendance::class : TeacherAttendance::class;
        $foreignKey = $type . '_id';

        $records = $model::where('tenant_id', $tenantId)
            ->where($foreignKey, $id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        $summary = [
            'total_days' => $records->count(),
            'present_days' => $records->where('status', 'present')->count(),
            'absent_days' => $records->where('status', 'absent')->count(),
            'late_days' => $records->where('status', 'late')->count(),
            'half_days' => $records->where('status', 'half_day')->count(),
            'leave_days' => $records->where('status', 'on_leave')->count(),
            'holiday_days' => $records->where('status', 'holiday')->count(),
        ];

        // Calculate working days (excluding holidays)
        $workingDays = $summary['present_days'] + $summary['absent_days'] +
                      $summary['late_days'] + $summary['half_days'] + $summary['leave_days'];

        // Calculate attendance percentage
        if ($workingDays > 0) {
            $effectiveDays = $summary['present_days'] + $summary['late_days'] + ($summary['half_days'] * 0.5);
            $summary['attendance_percentage'] = round(($effectiveDays / $workingDays) * 100, 2);
        } else {
            $summary['attendance_percentage'] = 0;
        }

        return static::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'attendable_type' => $type,
                'attendable_id' => $id,
                'month' => $month,
                'year' => $year,
            ],
            $summary
        );
    }

    /**
     * Get summary for student
     */
    public static function forStudent($tenantId, $studentId, $month, $year)
    {
        return static::where('tenant_id', $tenantId)
            ->where('attendable_type', 'student')
            ->where('attendable_id', $studentId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
    }

    /**
     * Get summary for teacher
     */
    public static function forTeacher($tenantId, $teacherId, $month, $year)
    {
        return static::where('tenant_id', $tenantId)
            ->where('attendable_type', 'teacher')
            ->where('attendable_id', $teacherId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
    }
}

