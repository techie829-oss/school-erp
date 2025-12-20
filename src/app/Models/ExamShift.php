<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamShift extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'shift_name',
        'shift_code',
        'start_time',
        'end_time',
        'duration_minutes',
        'class_ranges',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'class_ranges' => 'array',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Accessor for start_time - convert TIME string to Carbon instance
     */
    public function getStartTimeAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof \Carbon\Carbon) {
            return $value;
        }

        // If it's a string (TIME format), parse it
        return \Carbon\Carbon::createFromTimeString($value);
    }

    /**
     * Accessor for end_time - convert TIME string to Carbon instance
     */
    public function getEndTimeAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof \Carbon\Carbon) {
            return $value;
        }

        // If it's a string (TIME format), parse it
        return \Carbon\Carbon::createFromTimeString($value);
    }

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('start_time');
    }

    /**
     * Check if a class falls within this shift's class ranges
     */
    public function includesClass($classNumeric)
    {
        if (!$this->class_ranges || empty($this->class_ranges)) {
            return true; // If no ranges specified, include all classes
        }

        foreach ($this->class_ranges as $range) {
            $min = $range['min'] ?? 0;
            $max = $range['max'] ?? 999;

            if ($classNumeric >= $min && $classNumeric <= $max) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
        $start = $this->start_time instanceof \Carbon\Carbon
            ? $this->start_time->format('H:i')
            : $this->start_time;
        $end = $this->end_time instanceof \Carbon\Carbon
            ? $this->end_time->format('H:i')
            : $this->end_time;

        return "{$start} - {$end}";
    }
}
