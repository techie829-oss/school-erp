<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Services\TenantService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Store a newly created quiz.
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
            'pass_marks' => 'required|integer|min:0|lte:total_marks',
            'duration_minutes' => 'nullable|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_published' => 'boolean',
        ]);

        Quiz::create([
            'tenant_id' => $tenant->id,
            'course_id' => $course->id,
            'topic_id' => $validated['topic_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'total_marks' => $validated['total_marks'],
            'pass_marks' => $validated['pass_marks'],
            'duration_minutes' => $validated['duration_minutes'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_published' => $validated['is_published'] ?? false,
        ]);

        return back()->with('success', 'Quiz created successfully.');
    }

    /**
     * Update the specified quiz.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($quiz->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|integer|min:0',
            'pass_marks' => 'required|integer|min:0|lte:total_marks',
            'duration_minutes' => 'nullable|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_published' => 'boolean',
        ]);

        $quiz->update($validated);

        return back()->with('success', 'Quiz updated successfully.');
    }

    /**
     * Remove the specified quiz.
     */
    public function destroy(Request $request, Quiz $quiz)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($quiz->tenant_id !== $tenant->id) {
            abort(403);
        }

        $quiz->delete();

        return back()->with('success', 'Quiz deleted successfully.');
    }

    /**
     * Store a newly created question.
     */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($quiz->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,short_answer',
            'options' => 'nullable|array',
            'correct_answer' => 'required|string',
            'marks' => 'required|integer|min:1',
            'order' => 'nullable|integer',
        ]);

        QuizQuestion::create([
            'tenant_id' => $tenant->id,
            'quiz_id' => $quiz->id,
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'marks' => $validated['marks'],
            'order' => $validated['order'] ?? 0,
        ]);

        return back()->with('success', 'Question added successfully.');
    }

    /**
     * Update the specified question.
     */
    public function updateQuestion(Request $request, QuizQuestion $question)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($question->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,short_answer',
            'options' => 'nullable|array',
            'correct_answer' => 'required|string',
            'marks' => 'required|integer|min:1',
            'order' => 'nullable|integer',
        ]);

        $question->update($validated);

        return back()->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question.
     */
    public function destroyQuestion(Request $request, QuizQuestion $question)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($question->tenant_id !== $tenant->id) {
            abort(403);
        }

        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }
}
