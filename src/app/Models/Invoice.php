<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'invoice_number',
        'academic_year',
        'invoice_date',
        'due_date',
        'total_amount',
        'discount_amount',
        'tax_amount',
        'net_amount',
        'paid_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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
        return $query->whereIn('status', ['draft', 'sent', 'partial']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Generate invoice number
     */
    public static function generateInvoiceNumber($tenantId)
    {
        $prefix = 'INV';
        $year = date('Y');
        $lastInvoice = static::forTenant($tenantId)
            ->where('invoice_number', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . '-' . $year . '-' . $newNumber;
    }

    /**
     * Update invoice amounts
     */
    public function recalculateAmounts()
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->net_amount = $this->total_amount - $this->discount_amount + $this->tax_amount;
        $this->save();
    }
}
