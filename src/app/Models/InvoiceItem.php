<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'fee_component_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function feeComponent()
    {
        return $this->belongsTo(FeeComponent::class);
    }
}
