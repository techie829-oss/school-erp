<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('tenant.admin.grades.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tenant.admin.grades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('tenant.admin.grades.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Grades management feature coming soon!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        return view('tenant.admin.grades.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        return view('tenant.admin.grades.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('tenant.admin.grades.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Grades management feature coming soon!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        return redirect()->route('tenant.admin.grades.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Grades management feature coming soon!');
    }

    /**
     * Show student grades.
     */
    public function studentGrades(string $student): View
    {
        return view('tenant.admin.grades.student', compact('student'));
    }

    /**
     * Show class grades.
     */
    public function classGrades(string $class): View
    {
        return view('tenant.admin.grades.class', compact('class'));
    }
}
