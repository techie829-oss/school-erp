<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_card_id',
        'fee_component_id',
        'original_amount',
        'discount_amount',
        'discount_reason',
        'net_amount',
        'due_date',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function studentFeeCard()
    {
        return $this->belongsTo(StudentFeeCard::class);
    }

    public function feeComponent()
    {
        return $this->belongsTo(FeeComponent::class);
    }

    /**
     * Update payment status
     */
    public function updateStatus()
    {
        if ($this->paid_amount >= $this->net_amount) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
        // Don't call updateBalance() here - let the controller handle it after all items are updated
    }
}
