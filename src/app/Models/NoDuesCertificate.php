<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoDuesCertificate extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'class_id',
        'section_id',
        'certificate_number',
        'issue_date',
        'remarks',
        'library_clearance',
        'fee_clearance',
        'lab_clearance',
        'sports_clearance',
        'hostel_clearance',
        'clearance_remarks',
        'status',
        'rejection_reason',
        'generated_by',
        'generated_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'library_clearance' => 'boolean',
        'fee_clearance' => 'boolean',
        'lab_clearance' => 'boolean',
        'sports_clearance' => 'boolean',
        'hostel_clearance' => 'boolean',
        'generated_at' => 'datetime',
        'approved_at' => 'datetime',
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

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if all clearances are done
     */
    public function isFullyCleared()
    {
        return $this->library_clearance
            && $this->fee_clearance
            && $this->lab_clearance
            && $this->sports_clearance
            && $this->hostel_clearance;
    }
}
