<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'category_id',
        'event_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'organizer_id',
        'status',
        'is_all_day',
        'reminder_settings',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_all_day' => 'boolean',
        'reminder_settings' => 'array',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        $today = Carbon::today();
        return $query->where('start_date', '>=', $today)
            ->orWhere(function($q) use ($today) {
                $q->where('start_date', $today)
                  ->whereNotNull('start_time')
                  ->where('start_time', '>=', now()->format('H:i:s'));
            });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where(function($q3) use ($endDate) {
                         $q3->whereNull('end_date')
                            ->orWhere('end_date', '>=', $endDate);
                     });
              });
        });
    }

    // Accessors
    public function getIsUpcomingAttribute()
    {
        $today = Carbon::today();
        if ($this->start_date->isFuture()) {
            return true;
        }
        if ($this->start_date->isToday() && $this->start_time) {
            return Carbon::parse($this->start_time)->isFuture();
        }
        return false;
    }

    public function getIsPastAttribute()
    {
        if ($this->end_date) {
            return $this->end_date->isPast();
        }
        if ($this->start_date->isPast()) {
            if ($this->end_time) {
                return Carbon::parse($this->end_time)->isPast();
            }
            return true;
        }
        return false;
    }

    public function getFormattedDateRangeAttribute()
    {
        if ($this->is_all_day) {
            if ($this->start_date->equalTo($this->end_date ?? $this->start_date)) {
                return $this->start_date->format('M d, Y');
            }
            return $this->start_date->format('M d') . ' - ' . ($this->end_date ? $this->end_date->format('M d, Y') : $this->start_date->format('M d, Y'));
        }

        $start = $this->start_date->format('M d, Y');
        if ($this->start_time) {
            $start .= ' ' . Carbon::parse($this->start_time)->format('h:i A');
        }

        if ($this->end_date && !$this->end_date->equalTo($this->start_date)) {
            $end = $this->end_date->format('M d, Y');
        } else {
            $end = '';
        }

        if ($this->end_time) {
            $end .= ($end ? ' ' : $this->start_date->format('M d, Y') . ' ') . Carbon::parse($this->end_time)->format('h:i A');
        }

        return $end ? $start . ' - ' . $end : $start;
    }
}

