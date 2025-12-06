<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Teacher extends Model
{
    use HasFactory, SoftDeletes, ForTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'gender',
        'date_of_birth',
        'blood_group',
        'nationality',
        'religion',
        'category',
        'email',
        'phone',
        'alternate_phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'current_address',
        'permanent_address',
        'department_id',
        'designation',
        'employment_type',
        'date_of_joining',
        'date_of_leaving',
        'highest_qualification',
        'experience_years',
        'salary_amount',
        'bank_name',
        'bank_account_number',
        'bank_ifsc_code',
        'pan_number',
        'aadhar_number',
        'photo',
        'is_active',
        'status',
        'status_remarks',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'date_of_leaving' => 'date',
        'current_address' => 'array',
        'permanent_address' => 'array',
        'experience_years' => 'decimal:1',
        'salary_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = ['age', 'photo_url', 'years_of_service'];

    /**
     * Boot method - auto-generate full name
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($teacher) {
            $teacher->full_name = trim(
                $teacher->first_name . ' ' .
                ($teacher->middle_name ? $teacher->middle_name . ' ' : '') .
                $teacher->last_name
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function qualifications()
    {
        return $this->hasMany(TeacherQualification::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects')
            ->withPivot('class_id', 'is_primary', 'years_teaching')
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(TeacherDocument::class);
    }

    // Class Teacher assignments - sections where this teacher is class teacher
    public function classesTaught()
    {
        return $this->hasMany(Section::class, 'class_teacher_id');
    }

    /**
     * Accessors
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return \Carbon\Carbon::parse($this->date_of_birth)->age;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }

    public function getYearsOfServiceAttribute(): float
    {
        if (!$this->date_of_joining) {
            return 0;
        }

        /** @var \Carbon\Carbon $joiningDate */
        $joiningDate = $this->date_of_joining;
        $end = $this->date_of_leaving ?? now();
        return round($joiningDate->diffInYears($end), 1);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
              ->orWhere('employee_id', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('designation', 'like', "%{$search}%");
        });
    }

    /**
     * Helper Methods
     */
    public function isActive()
    {
        return $this->is_active && $this->status === 'active';
    }

    /**
     * Generate employee ID
     */
    /**
     * Generate employee ID (tenant-scoped)
     */
    public static function generateEmployeeId($tenantId, $year = null)
    {
        $year = $year ?? now()->year;

        // Get the highest number used for this tenant and year
        $lastTeacher = static::where('tenant_id', $tenantId)
            ->where('employee_id', 'like', "TCH-{$year}-%")
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastTeacher) {
            // Extract the number from the last employee ID (e.g., "TCH-2025-001" -> 1)
            preg_match('/TCH-\d+-(\d+)/', $lastTeacher->employee_id, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
        } else {
            $lastNumber = 0;
        }

        return sprintf('TCH-%d-%03d', $year, $lastNumber + 1);
    }
}

