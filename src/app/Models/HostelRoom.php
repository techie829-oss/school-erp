<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'room_number',
        'room_type',
        'capacity',
        'available_beds',
        'floor',
        'facilities',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'available_beds' => 'integer',
        'facilities' => 'array',
    ];

    // Relationships
    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function allocations()
    {
        return $this->hasMany(HostelAllocation::class, 'room_id');
    }

    public function activeAllocations()
    {
        return $this->hasMany(HostelAllocation::class, 'room_id')
            ->where('status', 'active');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('available_beds', '>', 0);
    }

    public function scopeForHostel($query, $hostelId)
    {
        return $query->where('hostel_id', $hostelId);
    }

    // Accessors
    public function getOccupiedBedsAttribute()
    {
        return $this->capacity - $this->available_beds;
    }

    public function getIsFullAttribute()
    {
        return $this->available_beds <= 0;
    }
}

