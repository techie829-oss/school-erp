<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudyMaterial extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'topic_id',
        'title',
        'file_path',
        'file_type',
        'file_size',
        'url',
    ];

    // Relationships
    public function topic()
    {
        return $this->belongsTo(CourseTopic::class, 'topic_id');
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : $this->url;
    }
}
