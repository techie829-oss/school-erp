<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'department_name',
        'department_code',
        'description',
        'head_teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function headTeacher()
    {
        return $this->belongsTo(Teacher::class, 'head_teacher_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Accessors
     */
    public function getTeachersCountAttribute()
    {
        return $this->teachers()->count();
    }
}

