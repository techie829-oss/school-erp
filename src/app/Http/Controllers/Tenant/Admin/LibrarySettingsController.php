<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibrarySetting;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LibrarySettingsController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    protected function getTenant(Request $request)
    {
        $tenant = $request->attributes->get('current_tenant');
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        return $tenant;
    }

    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);
        $settings = LibrarySetting::getForTenant($tenant->id);

        return view('tenant.admin.library.settings', compact('settings', 'tenant'));
    }

    public function update(Request $request)
    {
        $tenant = $this->getTenant($request);
        $settings = LibrarySetting::getForTenant($tenant->id);

        $validator = Validator::make($request->all(), [
            'max_books_per_student' => 'required|integer|min:1|max:10',
            'issue_duration_days' => 'required|integer|min:1|max:90',
            'fine_per_day' => 'required|numeric|min:0',
            'max_renewals' => 'required|integer|min:0|max:5',
            'renewal_duration_days' => 'required|integer|min:1|max:30',
            'book_lost_fine' => 'nullable|numeric|min:0',
            'book_damage_fine' => 'nullable|numeric|min:0',
            'allow_online_issue' => 'nullable|boolean',
            'send_overdue_notifications' => 'nullable|boolean',
            'overdue_notification_days' => 'nullable|integer|min:0',
            'library_rules' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings->update([
            'max_books_per_student' => $request->max_books_per_student,
            'issue_duration_days' => $request->issue_duration_days,
            'fine_per_day' => $request->fine_per_day,
            'max_renewals' => $request->max_renewals,
            'renewal_duration_days' => $request->renewal_duration_days,
            'book_lost_fine' => $request->book_lost_fine ?? 0,
            'book_damage_fine' => $request->book_damage_fine ?? 0,
            'allow_online_issue' => $request->has('allow_online_issue'),
            'send_overdue_notifications' => $request->has('send_overdue_notifications'),
            'overdue_notification_days' => $request->overdue_notification_days ?? 1,
            'library_rules' => $request->library_rules,
        ]);

        return redirect(url('/admin/library/settings'))->with('success', 'Library settings updated successfully.');
    }
}

