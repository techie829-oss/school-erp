<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeComponent extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function feePlanItems()
    {
        return $this->hasMany(FeePlanItem::class);
    }

    public function studentFeeItems()
    {
        return $this->hasMany(StudentFeeItem::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRecurring($query)
    {
        return $query->where('type', 'recurring');
    }

    public function scopeOneTime($query)
    {
        return $query->where('type', 'one_time');
    }
}
