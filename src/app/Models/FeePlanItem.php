<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_plan_id',
        'fee_component_id',
        'amount',
        'is_mandatory',
        'due_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_mandatory' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function feePlan()
    {
        return $this->belongsTo(FeePlan::class);
    }

    public function feeComponent()
    {
        return $this->belongsTo(FeeComponent::class);
    }
}
