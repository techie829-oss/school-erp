<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // For now, return empty data until we have actual teacher management
        $teachers = collect([]);

        return view('tenant.admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tenant.admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // For now, just redirect back with a success message
        return redirect()->route('tenant.admin.teachers.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Teacher management feature coming soon!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // For now, return a placeholder view
        return view('tenant.admin.teachers.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // For now, return a placeholder view
        return view('tenant.admin.teachers.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // For now, just redirect back with a success message
        return redirect()->route('tenant.admin.teachers.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Teacher management feature coming soon!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // For now, just redirect back with a success message
        return redirect()->route('tenant.admin.teachers.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Teacher management feature coming soon!');
    }

    /**
     * Show teacher profile.
     */
    public function profile(string $id): View
    {
        // For now, return a placeholder view
        return view('tenant.admin.teachers.profile', compact('id'));
    }
}
