<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'route_number',
        'start_location',
        'end_location',
        'distance',
        'base_fare',
        'description',
        'status',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'base_fare' => 'decimal:2',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function stops()
    {
        return $this->hasMany(RouteStop::class, 'route_id')->orderBy('stop_order');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'route_id');
    }

    public function assignments()
    {
        return $this->hasMany(TransportAssignment::class, 'route_id');
    }

    public function activeAssignments()
    {
        return $this->hasMany(TransportAssignment::class, 'route_id')
            ->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getTotalStopsAttribute()
    {
        return $this->stops()->count();
    }

    public function getTotalStudentsAttribute()
    {
        return $this->activeAssignments()->count();
    }
}
