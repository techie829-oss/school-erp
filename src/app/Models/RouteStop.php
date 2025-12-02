<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'stop_name',
        'stop_address',
        'stop_order',
        'fare_from_start',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'stop_order' => 'integer',
        'fare_from_start' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function pickupAssignments()
    {
        return $this->hasMany(TransportAssignment::class, 'pickup_stop_id');
    }

    public function dropAssignments()
    {
        return $this->hasMany(TransportAssignment::class, 'drop_stop_id');
    }
}
