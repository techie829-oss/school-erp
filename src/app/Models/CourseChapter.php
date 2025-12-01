<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseChapter extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'course_id',
        'chapter_name',
        'chapter_number',
        'description',
        'order',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function topics()
    {
        return $this->hasMany(CourseTopic::class, 'chapter_id')->orderBy('order');
    }
}
