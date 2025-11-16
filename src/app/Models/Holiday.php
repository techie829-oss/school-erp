<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ForTenant;

class Holiday extends Model
{
    use ForTenant;

    protected $table = 'holidays';

    protected $fillable = [
        'tenant_id',
        'date',
        'title',
        'type',
        'is_full_day',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_full_day' => 'boolean',
    ];

    public function scopes()
    {
        return $this->hasMany(HolidayScope::class);
    }

    /**
     * Convenience accessors
     */
    public function getScopeLabelAttribute(): string
    {
        return match ($this->type) {
            'school' => 'Whole School',
            'students_only' => 'Students Only',
            default => ucfirst($this->type ?? 'General'),
        };
    }

    public function getDayTypeLabelAttribute(): string
    {
        return $this->is_full_day ? 'Full Day' : 'Half Day';
    }
}


