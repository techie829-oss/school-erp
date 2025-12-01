<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\CourseTopic;
use App\Services\LmsService;
use App\Services\TenantService;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    protected $lmsService;
    protected $tenantService;

    public function __construct(LmsService $lmsService, TenantService $tenantService)
    {
        $this->lmsService = $lmsService;
        $this->tenantService = $tenantService;
    }

    /**
     * Store a newly created chapter.
     */
    public function storeChapter(Request $request, Course $course)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($course->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'chapter_name' => 'required|string|max:255',
            'chapter_number' => 'nullable|integer',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $this->lmsService->addChapter($course, $validated);

        return back()->with('success', 'Chapter added successfully.');
    }

    /**
     * Update the specified chapter.
     */
    public function updateChapter(Request $request, CourseChapter $chapter)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($chapter->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'chapter_name' => 'required|string|max:255',
            'chapter_number' => 'nullable|integer',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $chapter->update($validated);

        return back()->with('success', 'Chapter updated successfully.');
    }

    /**
     * Remove the specified chapter.
     */
    public function destroyChapter(Request $request, CourseChapter $chapter)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($chapter->tenant_id !== $tenant->id) {
            abort(403);
        }

        $chapter->delete();

        return back()->with('success', 'Chapter deleted successfully.');
    }

    /**
     * Store a newly created topic.
     */
    public function storeTopic(Request $request, CourseChapter $chapter)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($chapter->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'topic_name' => 'required|string|max:255',
            'topic_number' => 'nullable|integer',
            'description' => 'nullable|string',
            'content' => 'nullable|string', // HTML content
            'video_url' => 'nullable|url',
            'order' => 'nullable|integer',
        ]);

        $this->lmsService->addTopic($chapter, $validated);

        return back()->with('success', 'Topic added successfully.');
    }

    /**
     * Update the specified topic.
     */
    public function updateTopic(Request $request, CourseTopic $topic)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($topic->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'topic_name' => 'required|string|max:255',
            'topic_number' => 'nullable|integer',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'order' => 'nullable|integer',
        ]);

        $topic->update($validated);

        return back()->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified topic.
     */
    public function destroyTopic(Request $request, CourseTopic $topic)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($topic->tenant_id !== $tenant->id) {
            abort(403);
        }

        $topic->delete();

        return back()->with('success', 'Topic deleted successfully.');
    }
}
