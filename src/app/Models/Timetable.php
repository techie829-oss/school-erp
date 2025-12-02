<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'class_id',
        'section_id',
        'academic_year',
        'term',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'class_id' => 'integer',
        'section_id' => 'integer',
        'created_by' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function periods()
    {
        return $this->hasMany(TimetablePeriod::class, 'timetable_id');
    }

    public function periodsByDay($day)
    {
        return $this->periods()->where('day', $day)->orderBy('period_number');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeForAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }
}

