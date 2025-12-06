<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeCard extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'fee_plan_id',
        'academic_year',
        'total_amount',
        'discount_amount',
        'paid_amount',
        'balance_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feePlan()
    {
        return $this->belongsTo(FeePlan::class);
    }

    public function feeItems()
    {
        return $this->hasMany(StudentFeeItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'student_id', 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'student_id');
    }

    /**
     * Scopes
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['active', 'partial', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Update balance and status
     */
    public function updateBalance()
    {
        // Use the relationship if already loaded, otherwise query
        if ($this->relationLoaded('feeItems')) {
            $this->paid_amount = $this->feeItems->sum('paid_amount');
        } else {
            $this->paid_amount = $this->feeItems()->sum('paid_amount');
        }

        $this->balance_amount = $this->total_amount - $this->discount_amount - $this->paid_amount;

        if ($this->balance_amount <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'active';
        }

        $this->save();
    }
}
