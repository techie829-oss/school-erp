<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    /**
     * Show settings index.
     */
    public function index(): View
    {
        return view('tenant.admin.settings.index');
    }

    /**
     * Update school settings.
     */
    public function updateSchool(Request $request): RedirectResponse
    {
        return redirect()->route('tenant.admin.settings.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Settings management feature coming soon!');
    }

    /**
     * Update academic settings.
     */
    public function updateAcademic(Request $request): RedirectResponse
    {
        return redirect()->route('tenant.admin.settings.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Settings management feature coming soon!');
    }
}
