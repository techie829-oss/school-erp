<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Assignment extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'course_id',
        'topic_id',
        'title',
        'description',
        'total_marks',
        'due_date',
        'file_path',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function topic()
    {
        return $this->belongsTo(CourseTopic::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }
}
