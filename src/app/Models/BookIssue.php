<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookIssue extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'book_id',
        'student_id',
        'issue_date',
        'due_date',
        'return_date',
        'renewal_count',
        'fine_amount',
        'paid_fine',
        'issue_notes',
        'return_notes',
        'status',
        'issued_by',
        'returned_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'renewal_count' => 'integer',
        'fine_amount' => 'decimal:2',
        'paid_fine' => 'decimal:2',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    // Scopes
    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'issued')
                  ->where('due_date', '<', now());
            });
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByBook($query, $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->status === 'issued' && $this->due_date < now();
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->is_overdue) {
            return now()->diffInDays($this->due_date);
        }
        return 0;
    }

    public function getRemainingFineAttribute()
    {
        return max(0, $this->fine_amount - $this->paid_fine);
    }

    // Methods
    public function calculateFine($finePerDay, $maxFine = null)
    {
        if ($this->return_date && $this->return_date > $this->due_date) {
            $daysOverdue = $this->return_date->diffInDays($this->due_date);
            $fine = $daysOverdue * $finePerDay;

            if ($maxFine) {
                $fine = min($fine, $maxFine);
            }

            $this->fine_amount = $fine;
            $this->save();
        } elseif ($this->is_overdue) {
            $daysOverdue = $this->days_overdue;
            $fine = $daysOverdue * $finePerDay;

            if ($maxFine) {
                $fine = min($fine, $maxFine);
            }

            $this->fine_amount = $fine;
            if ($this->status === 'issued') {
                $this->status = 'overdue';
            }
            $this->save();
        }

        return $this->fine_amount;
    }

    public function markAsReturned($returnDate = null, $userId = null)
    {
        $this->return_date = $returnDate ?? now();
        $this->status = 'returned';
        $this->returned_by = $userId;
        $this->save();

        // Increment available copies
        $this->book->incrementAvailable();
    }

    public function renew($newDueDate, $maxRenewals = 2)
    {
        if ($this->renewal_count >= $maxRenewals) {
            return false;
        }

        $this->due_date = $newDueDate;
        $this->renewal_count++;
        $this->save();

        return true;
    }
}
