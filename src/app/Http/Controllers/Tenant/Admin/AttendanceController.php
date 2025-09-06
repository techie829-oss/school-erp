<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('tenant.admin.attendance.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tenant.admin.attendance.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('tenant.admin.attendance.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Attendance management feature coming soon!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        return view('tenant.admin.attendance.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        return view('tenant.admin.attendance.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('tenant.admin.attendance.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Attendance management feature coming soon!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        return redirect()->route('tenant.admin.attendance.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Attendance management feature coming soon!');
    }

    /**
     * Show class attendance for specific date.
     */
    public function classAttendance(string $class, string $date): View
    {
        return view('tenant.admin.attendance.class', compact('class', 'date'));
    }

    /**
     * Mark attendance.
     */
    public function markAttendance(Request $request): RedirectResponse
    {
        return redirect()->back()
            ->with('success', 'Attendance management feature coming soon!');
    }
}
