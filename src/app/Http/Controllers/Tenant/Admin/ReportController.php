<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Show attendance report.
     */
    public function attendance(): View
    {
        return view('tenant.admin.reports.attendance');
    }

    /**
     * Show grades report.
     */
    public function grades(): View
    {
        return view('tenant.admin.reports.grades');
    }

    /**
     * Show students report.
     */
    public function students(): View
    {
        return view('tenant.admin.reports.students');
    }
}
