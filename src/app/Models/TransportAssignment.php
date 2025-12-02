<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportAssignment extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'route_id',
        'vehicle_id',
        'pickup_stop_id',
        'drop_stop_id',
        'start_date',
        'end_date',
        'booking_date',
        'booking_status',
        'monthly_fare',
        'notes',
        'status',
        'assigned_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'booking_date' => 'date',
        'monthly_fare' => 'decimal:2',
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

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function pickupStop()
    {
        return $this->belongsTo(RouteStop::class, 'pickup_stop_id');
    }

    public function dropStop()
    {
        return $this->belongsTo(RouteStop::class, 'drop_stop_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function bills()
    {
        return $this->hasMany(TransportBill::class, 'assignment_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('booking_status', 'confirmed');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByRoute($query, $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' &&
               $this->booking_status === 'active' &&
               (!$this->end_date || $this->end_date >= now());
    }

    public function getFareAttribute()
    {
        if ($this->pickupStop && $this->dropStop) {
            return abs($this->dropStop->fare_from_start - $this->pickupStop->fare_from_start);
        }
        return $this->route->base_fare ?? 0;
    }
}
