<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request, Course $course)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($course->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'topic_id' => 'nullable|exists:course_topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|integer|min:0',
            'due_date' => 'nullable|date',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignments', 'public');
        }

        Assignment::create([
            'tenant_id' => $tenant->id,
            'course_id' => $course->id,
            'topic_id' => $validated['topic_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'total_marks' => $validated['total_marks'],
            'due_date' => $validated['due_date'],
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Assignment created successfully.');
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($assignment->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|integer|min:0',
            'due_date' => 'nullable|date',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            $assignment->file_path = $request->file('file')->store('assignments', 'public');
        }

        $assignment->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'total_marks' => $validated['total_marks'],
            'due_date' => $validated['due_date'],
        ]);

        return back()->with('success', 'Assignment updated successfully.');
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy(Request $request, Assignment $assignment)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($assignment->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }

        $assignment->delete();

        return back()->with('success', 'Assignment deleted successfully.');
    }
}
