<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSettings;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display settings page
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Get all settings grouped
        $generalSettings = TenantSetting::getAllForTenant($tenant->id, 'general');
        $featureSettings = TenantSetting::getAllForTenant($tenant->id, 'features');
        $academicSettings = TenantSetting::getAllForTenant($tenant->id, 'academic');
        $attendanceSettings = AttendanceSettings::getForTenant($tenant->id);
        $paymentSettings = TenantSetting::getAllForTenant($tenant->id, 'payment');

        // Get tenant data
        $tenantData = $tenant->data ?? [];

        return view('tenant.admin.settings.index', compact(
            'tenant',
            'tenantData',
            'generalSettings',
            'featureSettings',
            'academicSettings',
            'attendanceSettings',
            'paymentSettings'
        ));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'platform_type' => 'required|in:school,college,both',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        // Update tenant data
        $data = $tenant->data ?? [];
        $data['name'] = $request->name;
        $data['platform_type'] = $request->platform_type;
        $data['contact_email'] = $request->contact_email;
        $data['contact_phone'] = $request->contact_phone;
        $data['address'] = $request->address;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if (isset($data['logo']) && $data['logo']) {
                Storage::disk('public')->delete($data['logo']);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('tenant-logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Save tenant data
        $tenant->data = $data;
        $tenant->save();

        return back()->with('success', 'General settings updated successfully!');
    }

    /**
     * Update feature settings (enable/disable modules)
     */
    public function updateFeatures(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $features = [
            'students' => $request->boolean('enable_students'),
            'teachers' => $request->boolean('enable_teachers'),
            'classes' => $request->boolean('enable_classes'),
            'attendance' => $request->boolean('enable_attendance'),
            'exams' => $request->boolean('enable_exams'),
            'grades' => $request->boolean('enable_grades'),
            'fees' => $request->boolean('enable_fees'),
            'library' => $request->boolean('enable_library'),
            'transport' => $request->boolean('enable_transport'),
            'hostel' => $request->boolean('enable_hostel'),
            'assignments' => $request->boolean('enable_assignments'),
            'timetable' => $request->boolean('enable_timetable'),
            'events' => $request->boolean('enable_events'),
            'notice_board' => $request->boolean('enable_notice_board'),
            'communication' => $request->boolean('enable_communication'),
            'reports' => $request->boolean('enable_reports'),
        ];

        foreach ($features as $feature => $enabled) {
            TenantSetting::setSetting(
                $tenant->id,
                "feature_{$feature}",
                $enabled,
                'boolean',
                'features',
                "Enable/disable {$feature} module"
            );
        }

        return back()->with('success', 'Feature settings updated successfully!');
    }

    /**
     * Update academic settings
     */
    public function updateAcademic(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'academic_year_start' => 'nullable|date',
            'academic_year_end' => 'nullable|date|after:academic_year_start',
            'default_session' => 'nullable|string|max:50',
            'week_start_day' => 'nullable|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        // Save academic settings
        if ($request->academic_year_start) {
            TenantSetting::setSetting(
                $tenant->id,
                'academic_year_start',
                $request->academic_year_start,
                'string',
                'academic',
                'Academic year start date'
            );
        }

        if ($request->academic_year_end) {
            TenantSetting::setSetting(
                $tenant->id,
                'academic_year_end',
                $request->academic_year_end,
                'string',
                'academic',
                'Academic year end date'
            );
        }

        if ($request->default_session) {
            TenantSetting::setSetting(
                $tenant->id,
                'default_session',
                $request->default_session,
                'string',
                'academic',
                'Default academic session'
            );
        }

        if ($request->week_start_day) {
            TenantSetting::setSetting(
                $tenant->id,
                'week_start_day',
                $request->week_start_day,
                'string',
                'academic',
                'Week start day'
            );
        }

        return back()->with('success', 'Academic settings updated successfully!');
    }

    /**
     * Update attendance settings
     */
    public function updateAttendance(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'school_start_time' => 'nullable|date_format:H:i',
            'school_end_time' => 'nullable|date_format:H:i|after:school_start_time',
            'late_arrival_after' => 'nullable|date_format:H:i|after:school_start_time',
            'grace_period_minutes' => 'nullable|integer|min:0|max:60',
            'minimum_working_hours' => 'nullable|numeric|min:0|max:24',
            'half_day_threshold_hours' => 'nullable|numeric|min:0|max:12',
            'weekend_days' => 'nullable|array',
            'weekend_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'auto_mark_absent' => 'nullable|boolean',
            'require_remarks_for_absent' => 'nullable|boolean',
            'allow_edit_after_days' => 'nullable|integer|min:0|max:30',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        // Get or create attendance settings
        $settings = AttendanceSettings::firstOrNew([
            'tenant_id' => $tenant->id,
        ]);

        // Update settings
        if ($request->school_start_time) {
            $settings->school_start_time = $request->school_start_time . ':00';
        }
        if ($request->school_end_time) {
            $settings->school_end_time = $request->school_end_time . ':00';
        }
        if ($request->late_arrival_after) {
            $settings->late_arrival_after = $request->late_arrival_after . ':00';
        }
        if ($request->has('grace_period_minutes')) {
            $settings->grace_period_minutes = $request->grace_period_minutes;
        }
        if ($request->has('minimum_working_hours')) {
            $settings->minimum_working_hours = $request->minimum_working_hours;
        }
        if ($request->has('half_day_threshold_hours')) {
            $settings->half_day_threshold_hours = $request->half_day_threshold_hours;
        }
        if ($request->has('weekend_days')) {
            $settings->weekend_days = json_encode($request->weekend_days);
        }

        $settings->auto_mark_absent = $request->boolean('auto_mark_absent');
        $settings->require_remarks_for_absent = $request->boolean('require_remarks_for_absent');

        if ($request->has('allow_edit_after_days')) {
            $settings->allow_edit_after_days = $request->allow_edit_after_days;
        }

        $settings->save();

        return back()->with('success', 'Attendance settings updated successfully!');
    }

    /**
     * Update payment settings
     */
    public function updatePayment(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'enable_online_payments' => 'nullable|boolean',
            'payment_gateway' => 'nullable|in:razorpay,stripe,payu,phonepe',
            'razorpay_key_id' => 'nullable|string|max:255',
            'razorpay_key_secret' => 'nullable|string|max:255',
            'razorpay_webhook_secret' => 'nullable|string|max:255',
            'razorpay_test_mode' => 'nullable|boolean',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'in:cash,cheque,card,upi,net_banking,demand_draft',
            'auto_generate_receipt' => 'nullable|boolean',
            'payment_reminder_days' => 'nullable|integer|min:0|max:30',
            'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'receipt_prefix' => 'nullable|string|max:10',
            'invoice_prefix' => 'nullable|string|max:10',
            'currency_code' => 'nullable|in:INR,USD,EUR,GBP',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'email_receipts' => 'nullable|boolean',
            'sms_payment_confirmation' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        // Save payment settings
        TenantSetting::setSetting($tenant->id, 'enable_online_payments', $request->boolean('enable_online_payments'), 'boolean', 'payment');

        if ($request->payment_gateway) {
            TenantSetting::setSetting($tenant->id, 'payment_gateway', $request->payment_gateway, 'string', 'payment');
        }

        if ($request->razorpay_key_id) {
            TenantSetting::setSetting($tenant->id, 'razorpay_key_id', $request->razorpay_key_id, 'string', 'payment');
        }

        if ($request->razorpay_key_secret) {
            // In production, encrypt this
            TenantSetting::setSetting($tenant->id, 'razorpay_key_secret', encrypt($request->razorpay_key_secret), 'string', 'payment');
        }

        if ($request->razorpay_webhook_secret) {
            TenantSetting::setSetting($tenant->id, 'razorpay_webhook_secret', encrypt($request->razorpay_webhook_secret), 'string', 'payment');
        }

        TenantSetting::setSetting($tenant->id, 'razorpay_test_mode', $request->boolean('razorpay_test_mode'), 'boolean', 'payment');

        if ($request->has('payment_methods')) {
            TenantSetting::setSetting($tenant->id, 'payment_methods', json_encode($request->payment_methods), 'json', 'payment');
        }

        TenantSetting::setSetting($tenant->id, 'auto_generate_receipt', $request->boolean('auto_generate_receipt'), 'boolean', 'payment');

        if ($request->has('payment_reminder_days')) {
            TenantSetting::setSetting($tenant->id, 'payment_reminder_days', $request->payment_reminder_days, 'integer', 'payment');
        }

        if ($request->has('late_fee_percentage')) {
            TenantSetting::setSetting($tenant->id, 'late_fee_percentage', $request->late_fee_percentage, 'string', 'payment');
        }

        if ($request->receipt_prefix) {
            TenantSetting::setSetting($tenant->id, 'receipt_prefix', $request->receipt_prefix, 'string', 'payment');
        }

        if ($request->invoice_prefix) {
            TenantSetting::setSetting($tenant->id, 'invoice_prefix', $request->invoice_prefix, 'string', 'payment');
        }

        if ($request->currency_code) {
            TenantSetting::setSetting($tenant->id, 'currency_code', $request->currency_code, 'string', 'payment');
        }

        if ($request->has('tax_percentage')) {
            TenantSetting::setSetting($tenant->id, 'tax_percentage', $request->tax_percentage, 'string', 'payment');
        }

        TenantSetting::setSetting($tenant->id, 'email_receipts', $request->boolean('email_receipts'), 'boolean', 'payment');
        TenantSetting::setSetting($tenant->id, 'sms_payment_confirmation', $request->boolean('sms_payment_confirmation'), 'boolean', 'payment');

        return back()->with('success', 'Payment settings updated successfully!');
    }

    /**
     * Delete logo
     */
    public function deleteLogo(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $data = $tenant->data ?? [];

        if (isset($data['logo']) && $data['logo']) {
            Storage::disk('public')->delete($data['logo']);
            $data['logo'] = null;
            $tenant->data = $data;
            $tenant->save();
        }

        return back()->with('success', 'Logo deleted successfully!');
    }
}
