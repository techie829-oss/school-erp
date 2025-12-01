<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTopic extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'chapter_id',
        'topic_name',
        'topic_number',
        'description',
        'content',
        'video_url',
        'order',
    ];

    // Relationships
    public function chapter()
    {
        return $this->belongsTo(CourseChapter::class, 'chapter_id');
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class, 'topic_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'topic_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'topic_id');
    }
}
