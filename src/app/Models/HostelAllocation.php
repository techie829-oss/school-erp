<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelAllocation extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'hostel_id',
        'room_id',
        'bed_number',
        'allocation_date',
        'release_date',
        'status',
        'notes',
        'allocated_by',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'release_date' => 'date',
        'bed_number' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    public function allocator()
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForHostel($query, $hostelId)
    {
        return $query->where('hostel_id', $hostelId);
    }

    public function scopeForRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }
}

