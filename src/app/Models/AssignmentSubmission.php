<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmission extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'assignment_id',
        'student_id',
        'submission_text',
        'file_path',
        'obtained_marks',
        'grade',
        'feedback',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }
}
