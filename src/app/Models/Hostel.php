<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'capacity',
        'available_beds',
        'warden_id',
        'contact_number',
        'description',
        'gender',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'available_beds' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function warden()
    {
        return $this->belongsTo(Teacher::class, 'warden_id');
    }

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class, 'hostel_id');
    }

    public function allocations()
    {
        return $this->hasMany(HostelAllocation::class, 'hostel_id');
    }

    public function activeAllocations()
    {
        return $this->hasMany(HostelAllocation::class, 'hostel_id')
            ->where('status', 'active');
    }

    public function fees()
    {
        return $this->hasMany(HostelFee::class, 'hostel_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getOccupiedBedsAttribute()
    {
        return $this->capacity - $this->available_beds;
    }

    public function getOccupancyRateAttribute()
    {
        if ($this->capacity == 0) return 0;
        return round(($this->occupied_beds / $this->capacity) * 100, 2);
    }
}

