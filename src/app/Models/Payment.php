<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'invoice_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'payment_type',
        'transaction_id',
        'reference_number',
        'status',
        'gateway_response',
        'notes',
        'collected_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
    ];

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }

    /**
     * Scopes
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('payment_date', $date);
    }

    /**
     * Generate payment number (globally unique)
     */
    public static function generatePaymentNumber($tenantId)
    {
        $prefix = 'PAY';
        $year = date('Y');

        // Get the last payment globally (not tenant-scoped) since the constraint is global
        $lastPayment = static::where('payment_number', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $paymentNumber = $prefix . '-' . $year . '-' . $newNumber;

        // Double-check it doesn't exist (race condition protection)
        $maxRetries = 10;
        $retryCount = 0;
        while (static::where('payment_number', $paymentNumber)->exists() && $retryCount < $maxRetries) {
            $lastNumber = (int) substr($paymentNumber, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $paymentNumber = $prefix . '-' . $year . '-' . $newNumber;
            $retryCount++;
        }

        if ($retryCount >= $maxRetries) {
            // Fallback: use timestamp-based number
            $paymentNumber = $prefix . '-' . $year . '-' . str_pad((int)substr(time(), -6), 6, '0', STR_PAD_LEFT);
        }

        return $paymentNumber;
    }
}
