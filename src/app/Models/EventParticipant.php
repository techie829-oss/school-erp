<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'participant_type',
        'participant_id',
        'status',
        'notes',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        // Polymorphic relationship based on participant_type
        if ($this->participant_type === 'student') {
            return $this->belongsTo(Student::class, 'participant_id');
        } elseif ($this->participant_type === 'teacher') {
            return $this->belongsTo(Teacher::class, 'participant_id');
        } elseif ($this->participant_type === 'class') {
            return $this->belongsTo(SchoolClass::class, 'participant_id');
        } elseif ($this->participant_type === 'section') {
            return $this->belongsTo(Section::class, 'participant_id');
        } elseif ($this->participant_type === 'department') {
            return $this->belongsTo(Department::class, 'participant_id');
        }
        return null;
    }
}

