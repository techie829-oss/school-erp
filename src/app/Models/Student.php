<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory, SoftDeletes, ForTenant;

    protected $fillable = [
        'tenant_id',
        'admission_number',
        'admission_date',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'nationality',
        'religion',
        'category',
        'email',
        'phone',
        'photo',
        'current_address',
        'permanent_address',
        'same_as_current',
        'father_name',
        'father_occupation',
        'father_phone',
        'father_email',
        'mother_name',
        'mother_occupation',
        'mother_phone',
        'mother_email',
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'guardian_email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'medical_info',
        'previous_school_name',
        'previous_class',
        'tc_number',
        'overall_status',
        'is_active',
        'status_remarks',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'date_of_birth' => 'date',
        'current_address' => 'array',
        'permanent_address' => 'array',
        'medical_info' => 'array',
        'same_as_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method - auto-generate full name
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($student) {
            $student->full_name = trim(
                $student->first_name . ' ' .
                ($student->middle_name ? $student->middle_name . ' ' : '') .
                $student->last_name
            );
        });
    }

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    // Has many enrollments (all classes ever attended)
    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class)->orderBy('academic_year', 'desc');
    }

    // Has one current enrollment
    public function currentEnrollment()
    {
        return $this->hasOne(ClassEnrollment::class)->where('is_current', true);
    }

    // Get current class through current enrollment
    public function currentClass()
    {
        return $this->hasOneThrough(
            SchoolClass::class,
            ClassEnrollment::class,
            'student_id',
            'id',
            'id',
            'class_id'
        )->where('class_enrollments.is_current', true);
    }

    // Get current section through current enrollment
    public function currentSection()
    {
        return $this->hasOneThrough(
            Section::class,
            ClassEnrollment::class,
            'student_id',
            'id',
            'id',
            'section_id'
        )->where('class_enrollments.is_current', true);
    }

    // Academic history (completed enrollments)
    public function academicHistory()
    {
        return $this->enrollments()->completed();
    }

    // Documents
    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    /**
     * Accessors
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getCurrentRollNumberAttribute()
    {
        return $this->currentEnrollment?->roll_number;
    }

    /**
     * Helper Methods
     */
    public function isActive()
    {
        return $this->is_active && $this->overall_status === 'active';
    }

    /**
     * Enroll student in a class
     */
    public function enrollInClass($classId, $sectionId, $academicYear, $rollNumber = null)
    {
        // Mark any existing current enrollment as not current
        $this->enrollments()->where('is_current', true)->update(['is_current' => false]);

        // Create new enrollment
        return $this->enrollments()->create([
            'tenant_id' => $this->tenant_id,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'roll_number' => $rollNumber,
            'academic_year' => $academicYear,
            'enrollment_date' => now(),
            'start_date' => now(),
            'enrollment_status' => 'enrolled',
            'is_current' => true,
        ]);
    }

    /**
     * Promote student to next class
     */
    public function promoteToClass($toClassId, $sectionId, $academicYear, $percentage = null, $grade = null, $remarks = null, $rollNumber = null)
    {
        // Complete current enrollment
        $currentEnrollment = $this->currentEnrollment;
        if ($currentEnrollment) {
            $currentEnrollment->markAsCompleted('promoted', $percentage, $grade, $remarks, $toClassId);
        }

        // Enroll in new class
        return $this->enrollInClass($toClassId, $sectionId, $academicYear, $rollNumber);
    }

    /**
     * Generate admission number
     */
    public static function generateAdmissionNumber($tenantId, $year = null)
    {
        $year = $year ?? now()->year;
        $lastNumber = static::where('tenant_id', $tenantId)
            ->where('admission_number', 'like', "STU-{$year}-%")
            ->count();

        return sprintf('STU-%d-%03d', $year, $lastNumber + 1);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('overall_status', 'active');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeInClass($query, $classId)
    {
        return $query->whereHas('currentEnrollment', function($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }

    public function scopeInSection($query, $sectionId)
    {
        return $query->whereHas('currentEnrollment', function($q) use ($sectionId) {
            $q->where('section_id', $sectionId);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
              ->orWhere('admission_number', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhereHas('currentEnrollment', function($q2) use ($search) {
                  $q2->where('roll_number', 'like', "%{$search}%");
              });
        });
    }
}
