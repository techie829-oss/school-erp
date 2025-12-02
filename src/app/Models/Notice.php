<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notice extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'content',
        'notice_type',
        'priority',
        'target_audience',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(NoticeAttachment::class);
    }

    public function reads()
    {
        return $this->hasMany(NoticeRead::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where('status', 'published')
            ->where('start_date', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
            });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('notice_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeForAudience($query, $audience)
    {
        return $query->where(function($q) use ($audience) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', $audience);
        });
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        if (!$this->end_date) {
            return false;
        }
        return Carbon::parse($this->end_date)->isPast();
    }

    public function getIsActiveAttribute()
    {
        if ($this->status !== 'published') {
            return false;
        }
        $today = Carbon::today();
        if (Carbon::parse($this->start_date)->isFuture()) {
            return false;
        }
        if ($this->end_date && Carbon::parse($this->end_date)->isPast()) {
            return false;
        }
        return true;
    }

    public function getReadCountAttribute()
    {
        return $this->reads()->count();
    }

    public function isReadBy($userId)
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}

