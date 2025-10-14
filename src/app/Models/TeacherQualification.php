<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeacherQualification extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'teacher_id',
        'qualification_type',
        'degree_name',
        'specialization',
        'institution_name',
        'university_board',
        'year_of_passing',
        'grade_percentage',
        'certificate_number',
        'certificate_document',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'year_of_passing' => 'integer',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
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

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Accessors
     */
    public function getCertificateUrlAttribute()
    {
        return $this->certificate_document ? Storage::url($this->certificate_document) : null;
    }

    /**
     * Scopes
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('qualification_type', $type);
    }
}

