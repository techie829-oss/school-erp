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
        'school_start_time',
        'school_end_time',
        'late_arrival_after',
        'grace_period_minutes',
        'minimum_working_hours',
        'half_day_threshold_hours',
        'weekend_days',
        'auto_mark_absent',
        'require_remarks_for_absent',
        'allow_edit_after_days',
    ];

    protected $casts = [
        'minimum_working_hours' => 'decimal:1',
        'half_day_threshold_hours' => 'decimal:1',
        'weekend_days' => 'array',
        'auto_mark_absent' => 'boolean',
        'require_remarks_for_absent' => 'boolean',
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
                'school_start_time' => '09:00:00',
                'school_end_time' => '17:00:00',
                'late_arrival_after' => '09:15:00',
                'grace_period_minutes' => 15,
                'minimum_working_hours' => 8.0,
                'half_day_threshold_hours' => 4.0,
                'weekend_days' => ['sunday'],
                'auto_mark_absent' => false,
                'require_remarks_for_absent' => false,
                'allow_edit_after_days' => 7,
            ]
        );
    }

    /**
     * Check if a day is a weekend
     */
    public function isWeekend($dayOfWeek): bool
    {
        if (!$this->weekend_days) {
            return false;
        }

        $dayName = strtolower($dayOfWeek);
        return in_array($dayName, $this->weekend_days);
    }
}

