<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\CourseTopic;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LmsService
{
    /**
     * Create a new course
     */
    public function createCourse(Tenant $tenant, array $data)
    {
        return DB::transaction(function () use ($tenant, $data) {
            // Handle image upload if present
            if (isset($data['course_image']) && $data['course_image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['course_image'] = $data['course_image']->store('courses/images', 'public');
            }

            $data['tenant_id'] = $tenant->id;
            
            return Course::create($data);
        });
    }

    /**
     * Update a course
     */
    public function updateCourse(Course $course, array $data)
    {
        return DB::transaction(function () use ($course, $data) {
            if (isset($data['course_image']) && $data['course_image'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old image
                if ($course->course_image) {
                    Storage::disk('public')->delete($course->course_image);
                }
                $data['course_image'] = $data['course_image']->store('courses/images', 'public');
            }

            $course->update($data);
            return $course;
        });
    }

    /**
     * Add a chapter to a course
     */
    public function addChapter(Course $course, array $data)
    {
        $data['tenant_id'] = $course->tenant_id;
        $data['course_id'] = $course->id;
        
        // Auto-calculate order if not provided
        if (!isset($data['order'])) {
            $data['order'] = $course->chapters()->max('order') + 1;
        }

        return CourseChapter::create($data);
    }

    /**
     * Add a topic to a chapter
     */
    public function addTopic(CourseChapter $chapter, array $data)
    {
        $data['tenant_id'] = $chapter->tenant_id;
        $data['chapter_id'] = $chapter->id;

        if (!isset($data['order'])) {
            $data['order'] = $chapter->topics()->max('order') + 1;
        }

        return CourseTopic::create($data);
    }

    /**
     * Get courses for a student based on their class
     */
    public function getStudentCourses($student)
    {
        $currentClassId = $student->currentClass->id ?? null;
        
        if (!$currentClassId) {
            return collect();
        }

        return Course::forTenant($student->tenant_id)
            ->where('class_id', $currentClassId)
            ->where('is_active', true)
            ->with(['teacher', 'subject'])
            ->get();
    }
}
