<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'teacher_id',
        'subject_id',
        'class_id',
        'is_primary',
        'years_teaching',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'years_teaching' => 'decimal:1',
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

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Scopes
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
}

