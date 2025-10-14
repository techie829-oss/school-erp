<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassEnrollment extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'student_id',
        'tenant_id',
        'class_id',
        'section_id',
        'roll_number',
        'academic_year',
        'enrollment_date',
        'start_date',
        'end_date',
        'enrollment_status',
        'is_current',
        'result',
        'percentage',
        'grade',
        'remarks',
        'promoted_to_class_id',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'percentage' => 'decimal:2',
    ];

    /**
     * Boot method to ensure only one current enrollment per student
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($enrollment) {
            // If this is being marked as current, unmark others
            if ($enrollment->is_current) {
                static::where('student_id', $enrollment->student_id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }
        });

        static::updating(function ($enrollment) {
            // If this is being marked as current, unmark others
            if ($enrollment->is_current && $enrollment->isDirty('is_current')) {
                static::where('student_id', $enrollment->student_id)
                    ->where('id', '!=', $enrollment->id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }
        });
    }

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function promotedToClass()
    {
        return $this->belongsTo(SchoolClass::class, 'promoted_to_class_id');
    }

    /**
     * Scopes
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_current', false)->whereNotNull('end_date');
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeInClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Helper methods
     */
    public function isCurrent()
    {
        return $this->is_current;
    }

    public function isCompleted()
    {
        return !$this->is_current && $this->end_date !== null;
    }

    public function markAsCompleted($result, $percentage = null, $grade = null, $remarks = null, $promotedToClassId = null)
    {
        $this->update([
            'end_date' => now(),
            'is_current' => false,
            'result' => $result,
            'percentage' => $percentage,
            'grade' => $grade,
            'remarks' => $remarks,
            'promoted_to_class_id' => $promotedToClassId,
            'enrollment_status' => $result,
        ]);
    }
}
