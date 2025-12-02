<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelFee extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'hostel_id',
        'fee_type',
        'name',
        'amount',
        'frequency',
        'description',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
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
}

