<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'license_number',
        'license_type',
        'license_issue_date',
        'license_expiry_date',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
        'salary',
        'joining_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'license_issue_date' => 'date',
        'license_expiry_date' => 'date',
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'driver_id');
    }

    public function activeVehicle()
    {
        return $this->hasOne(Vehicle::class, 'driver_id')
            ->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getIsLicenseExpiredAttribute()
    {
        return $this->license_expiry_date && $this->license_expiry_date < now();
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }
}
