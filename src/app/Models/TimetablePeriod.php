<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetablePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'timetable_id',
        'day',
        'period_number',
        'start_time',
        'end_time',
        'subject_id',
        'teacher_id',
        'room',
        'notes',
    ];

    protected $casts = [
        'timetable_id' => 'integer',
        'period_number' => 'integer',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'subject_id' => 'integer',
        'teacher_id' => 'integer',
    ];

    // Relationships
    public function timetable()
    {
        return $this->belongsTo(Timetable::class, 'timetable_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // Scopes
    public function scopeForDay($query, $day)
    {
        return $query->where('day', $day);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('period_number');
    }
}

