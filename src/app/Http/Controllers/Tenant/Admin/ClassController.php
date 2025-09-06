<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // For now, return empty data until we have actual class management
        $classes = collect([]);

        return view('tenant.admin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tenant.admin.classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // For now, just redirect back with a success message
        return redirect()->route('tenant.admin.classes.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Class management feature coming soon!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // For now, return a placeholder view
        return view('tenant.admin.classes.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // For now, return a placeholder view
        return view('tenant.admin.classes.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // For now, just redirect back with a success message
        return redirect()->route('tenant.admin.classes.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Class management feature coming soon!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // For now, just redirect back with a success message
        return redirect()->route('tenant.admin.classes.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Class management feature coming soon!');
    }

    /**
     * Show class students.
     */
    public function students(string $id): View
    {
        // For now, return a placeholder view
        return view('tenant.admin.classes.students', compact('id'));
    }

    /**
     * Add student to class.
     */
    public function addStudent(Request $request, string $id): RedirectResponse
    {
        // For now, just redirect back with a success message
        return redirect()->route('tenant.admin.classes.students', ['tenant' => request()->route('tenant'), 'class' => $id])
            ->with('success', 'Class management feature coming soon!');
    }

    /**
     * Remove student from class.
     */
    public function removeStudent(string $classId, string $studentId): RedirectResponse
    {
        // For now, just redirect back with a success message
        return redirect()->route('tenant.admin.classes.students', ['tenant' => request()->route('tenant'), 'class' => $classId])
            ->with('success', 'Class management feature coming soon!');
    }
}
