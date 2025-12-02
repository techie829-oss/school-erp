<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'vehicle_number',
        'vehicle_type',
        'make',
        'model',
        'manufacturing_year',
        'capacity',
        'color',
        'registration_number',
        'registration_date',
        'insurance_expiry',
        'permit_expiry',
        'fitness_expiry',
        'driver_id',
        'route_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'manufacturing_year' => 'integer',
        'capacity' => 'integer',
        'registration_date' => 'date',
        'insurance_expiry' => 'date',
        'permit_expiry' => 'date',
        'fitness_expiry' => 'date',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function assignments()
    {
        return $this->hasMany(TransportAssignment::class, 'vehicle_id');
    }

    public function activeAssignments()
    {
        return $this->hasMany(TransportAssignment::class, 'vehicle_id')
            ->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    // Accessors
    public function getOccupiedSeatsAttribute()
    {
        return $this->activeAssignments()->count();
    }

    public function getAvailableSeatsAttribute()
    {
        return max(0, $this->capacity - $this->occupied_seats);
    }

    public function getIsFullAttribute()
    {
        return $this->available_seats <= 0;
    }

    public function getIsInsuranceExpiredAttribute()
    {
        return $this->insurance_expiry && $this->insurance_expiry < now();
    }

    public function getIsPermitExpiredAttribute()
    {
        return $this->permit_expiry && $this->permit_expiry < now();
    }

    public function getIsFitnessExpiredAttribute()
    {
        return $this->fitness_expiry && $this->fitness_expiry < now();
    }
}
