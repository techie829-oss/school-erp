<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AdminUser;
use App\Models\User;
use App\Models\TenantSetting;
use App\Services\VhostService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|in:internal,school,college,university',
            'subdomain' => 'required|string|max:50|regex:/^[a-z0-9-]+$/|unique:tenants,data->subdomain',
            'custom_domain' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:tenants,data->custom_domain',
            'active' => 'boolean',
        ]);

        // Generate unique tenant ID
        $tenantId = $this->generateUniqueTenantId($validated['name']);

        $tenantData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'subdomain' => $validated['subdomain'],
            'is_active' => $validated['active'] ?? true,
            'created_at' => now()->toISOString(),
        ];

        // Add domain information
        $tenantData['full_domain'] = $validated['subdomain'] . '.' . config('all.domains.primary');
        if ($validated['custom_domain']) {
            $tenantData['custom_domain'] = $validated['custom_domain'];
        }

        $tenant = Tenant::create([
            'id' => $tenantId,
            'data' => $tenantData,
        ]);

        // Update Herd configuration
        $this->updateHerdConfiguration($validated['subdomain']);

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Tenant created successfully!');
    }

    public function show(Tenant $tenant)
    {
        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|in:internal,school,college,university',
            'subdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('tenants', 'data->subdomain')->ignore($tenant->id)
            ],
            'custom_domain' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('tenants', 'data->custom_domain')->ignore($tenant->id)
            ],
            'active' => 'boolean',
        ]);

        $tenantData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'subdomain' => $validated['subdomain'],
            'is_active' => $validated['active'] ?? true,
            'updated_at' => now()->toISOString(),
        ];

        // Add domain information
        $tenantData['full_domain'] = $validated['subdomain'] . '.' . config('all.domains.primary');
        if ($validated['custom_domain']) {
            $tenantData['custom_domain'] = $validated['custom_domain'];
        }

        $tenant->update([
            'data' => array_merge($tenant->data, $tenantData),
        ]);

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }

    // User Management Methods
    public function usersIndex(Tenant $tenant)
    {
        // Get school users from users table (not admin_users)
        $users = User::where('tenant_id', $tenant->id)->paginate(10);
        return view('admin.tenants.users.index', compact('tenant', 'users'));
    }

    public function usersCount(Tenant $tenant)
    {
        // Count school users from users table (not admin_users)
        $count = User::where('tenant_id', $tenant->id)->count();
        return response()->json([
            'success' => true,
                'count' => $count
            ]);
    }

    public function usersCreate(Tenant $tenant)
    {
        return view('admin.tenants.users.create', compact('tenant'));
    }

    public function usersStore(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type' => 'required|in:school_admin,teacher,staff,student',
            'is_active' => 'sometimes|boolean',
        ]);

                // Create school admin in users table (not admin_users)
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'tenant_id' => $tenant->id,
                    'user_type' => 'school_admin',
                    'is_active' => $validated['is_active'] ?? true,
                ]);

        return redirect()->route('admin.tenants.users.show', [$tenant, $user->id])
            ->with('success', 'User created successfully!');
    }

    public function usersShow(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        return view('admin.tenants.users.show', compact('tenant', 'user'));
    }

    public function usersEdit(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        return view('admin.tenants.users.edit', compact('tenant', 'user'));
    }

    public function usersUpdate(Request $request, Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => 'nullable|confirmed|min:8',
            'user_type' => 'required|in:school_admin,teacher,staff,student',
            'is_active' => 'sometimes|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_type' => $validated['user_type'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.tenants.users.show', [$tenant, $userId])
            ->with('success', 'User updated successfully!');
    }

    public function usersChangePassword(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        return view('admin.tenants.users.change-password', compact('tenant', 'user'));
    }

    public function usersUpdatePassword(Request $request, Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

            $user->update([
            'password' => Hash::make($validated['password'])
            ]);

        return redirect()->route('admin.tenants.users.show', [$tenant, $userId])
            ->with('success', 'Password updated successfully!');
    }

    public function usersDelete(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        return view('admin.tenants.users.delete', compact('tenant', 'user'));
    }

    public function usersDestroy(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        $user->delete();

        return redirect()->route('admin.tenants.users.index', $tenant)
            ->with('success', 'User deleted successfully!');
    }

    private function generateUniqueTenantId(string $name): string
    {
        $baseId = Str::slug($name);
        $counter = 1;
        $tenantId = $baseId;

        while (Tenant::where('id', $tenantId)->exists()) {
            $tenantId = $baseId . '-' . $counter;
            $counter++;
        }

        return $tenantId;
    }

    private function updateHerdConfiguration(string $subdomain): void
    {
        try {
            $vhostService = new VhostService();
            $vhostService->updateHerdConfiguration($subdomain);
        } catch (\Exception $e) {
            Log::error('Failed to update Herd configuration', [
                'subdomain' => $subdomain,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function checkSubdomain(Request $request)
    {
        $subdomain = $request->input('subdomain');
        $exists = Tenant::where('data->subdomain', $subdomain)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This subdomain is already taken' : 'Subdomain is available'
        ]);
    }

    public function cleanupHerdYml()
    {
        try {
            $vhostService = new VhostService();
            $result = $vhostService->cleanupHerdYml();

            return redirect()->route('admin.tenants.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->route('admin.tenants.index')
                ->with('error', 'Failed to cleanup Herd configuration: ' . $e->getMessage());
        }
    }

    public function syncHerdYmlWithDatabase()
    {
        try {
            $vhostService = new VhostService();
            $result = $vhostService->syncHerdYmlWithDatabase();

            return redirect()->route('admin.tenants.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->route('admin.tenants.index')
                ->with('error', 'Failed to sync Herd configuration: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenantData = $tenant->data;
        // Use 'is_active' to match the isActive() method in Tenant model
        $tenantData['is_active'] = !($tenantData['is_active'] ?? true);
        $tenant->update(['data' => $tenantData]);

        $status = $tenantData['is_active'] ? 'activated' : 'deactivated';

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', "Tenant {$status} successfully!");
    }

    /**
     * Show module settings (features) for a tenant
     */
    public function settingsFeatures(Tenant $tenant)
    {
        $featureSettings = TenantSetting::getAllForTenant($tenant->id, 'features');
        return view('admin.tenants.settings.features', compact('tenant', 'featureSettings'));
    }

    /**
     * Update module settings (features) for a tenant
     */
    public function updateSettingsFeatures(Request $request, Tenant $tenant)
    {
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

        return redirect()->route('admin.tenants.settings.features', $tenant)
            ->with('success', 'Module settings updated successfully!');
    }

    /**
     * Show SMS and Email configuration settings for a tenant
     */
    public function settingsNotifications(Tenant $tenant)
    {
        $notificationSettings = TenantSetting::getAllForTenant($tenant->id, 'notifications');

        // Decrypt sensitive fields for display (if they exist)
        if (!empty($notificationSettings['mail_password'])) {
            try {
                $notificationSettings['mail_password'] = decrypt($notificationSettings['mail_password']);
            } catch (\Exception $e) {
                $notificationSettings['mail_password'] = '';
            }
        }

        if (!empty($notificationSettings['msg91_auth_key'])) {
            try {
                $notificationSettings['msg91_auth_key'] = decrypt($notificationSettings['msg91_auth_key']);
            } catch (\Exception $e) {
                $notificationSettings['msg91_auth_key'] = '';
            }
        }

        return view('admin.tenants.settings.notifications', compact('tenant', 'notificationSettings'));
    }

    /**
     * Update SMS and Email configuration settings for a tenant
     */
    public function updateSettingsNotifications(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'email_enabled' => 'nullable|boolean',
            'mail_mailer' => 'nullable|in:smtp,sendmail,mailgun,ses',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|in:tls,ssl,',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            'sms_enabled' => 'nullable|boolean',
            'sms_provider' => 'nullable|in:msg91,twilio,textlocal',
            'msg91_auth_key' => 'nullable|string|max:255',
            'msg91_sender_id' => 'nullable|string|max:6',
            'msg91_route' => 'nullable|in:1,4',
            'msg91_dlt_template_payment_confirmation' => 'nullable|string|max:255',
            'msg91_dlt_template_payment_reminder' => 'nullable|string|max:255',
            'msg91_dlt_template_fee_due' => 'nullable|string|max:255',
            'msg91_dlt_template_attendance' => 'nullable|string|max:255',
            'notify_payment_confirmation' => 'nullable|boolean',
            'notify_payment_reminder' => 'nullable|boolean',
            'notify_fee_due' => 'nullable|boolean',
            'notify_attendance' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        // Save Email Settings
        TenantSetting::setSetting($tenant->id, 'email_enabled', $request->boolean('email_enabled'), 'boolean', 'notifications');

        if ($request->email_enabled) {
            if ($request->mail_mailer) {
                TenantSetting::setSetting($tenant->id, 'mail_mailer', $request->mail_mailer, 'string', 'notifications');
            }
            if ($request->mail_host) {
                TenantSetting::setSetting($tenant->id, 'mail_host', $request->mail_host, 'string', 'notifications');
            }
            if ($request->mail_port) {
                TenantSetting::setSetting($tenant->id, 'mail_port', $request->mail_port, 'string', 'notifications');
            }
            if ($request->has('mail_encryption')) {
                TenantSetting::setSetting($tenant->id, 'mail_encryption', $request->mail_encryption, 'string', 'notifications');
            }
            if ($request->mail_username) {
                TenantSetting::setSetting($tenant->id, 'mail_username', $request->mail_username, 'string', 'notifications');
            }
            if ($request->mail_password) {
                // Encrypt password before storing
                TenantSetting::setSetting($tenant->id, 'mail_password', encrypt($request->mail_password), 'string', 'notifications');
            }
            if ($request->mail_from_address) {
                TenantSetting::setSetting($tenant->id, 'mail_from_address', $request->mail_from_address, 'string', 'notifications');
            }
            if ($request->mail_from_name) {
                TenantSetting::setSetting($tenant->id, 'mail_from_name', $request->mail_from_name, 'string', 'notifications');
            }
        }

        // Save SMS Settings
        TenantSetting::setSetting($tenant->id, 'sms_enabled', $request->boolean('sms_enabled'), 'boolean', 'notifications');

        if ($request->sms_enabled) {
            if ($request->sms_provider) {
                TenantSetting::setSetting($tenant->id, 'sms_provider', $request->sms_provider, 'string', 'notifications');
            }

            // MSG91 Settings
            if ($request->sms_provider === 'msg91') {
                if ($request->msg91_auth_key) {
                    TenantSetting::setSetting($tenant->id, 'msg91_auth_key', encrypt($request->msg91_auth_key), 'string', 'notifications');
                }
                if ($request->msg91_sender_id) {
                    TenantSetting::setSetting($tenant->id, 'msg91_sender_id', strtoupper($request->msg91_sender_id), 'string', 'notifications');
                }
                if ($request->msg91_route) {
                    TenantSetting::setSetting($tenant->id, 'msg91_route', $request->msg91_route, 'string', 'notifications');
                }
                if ($request->msg91_dlt_template_payment_confirmation) {
                    TenantSetting::setSetting(
                        $tenant->id,
                        'msg91_dlt_template_payment_confirmation',
                        $request->msg91_dlt_template_payment_confirmation,
                        'string',
                        'notifications'
                    );
                }
                if ($request->msg91_dlt_template_payment_reminder) {
                    TenantSetting::setSetting(
                        $tenant->id,
                        'msg91_dlt_template_payment_reminder',
                        $request->msg91_dlt_template_payment_reminder,
                        'string',
                        'notifications'
                    );
                }
                if ($request->msg91_dlt_template_fee_due) {
                    TenantSetting::setSetting(
                        $tenant->id,
                        'msg91_dlt_template_fee_due',
                        $request->msg91_dlt_template_fee_due,
                        'string',
                        'notifications'
                    );
                }
                if ($request->msg91_dlt_template_attendance) {
                    TenantSetting::setSetting(
                        $tenant->id,
                        'msg91_dlt_template_attendance',
                        $request->msg91_dlt_template_attendance,
                        'string',
                        'notifications'
                    );
                }
            }
        }

        // Save Notification Preferences
        TenantSetting::setSetting($tenant->id, 'notify_payment_confirmation', $request->boolean('notify_payment_confirmation'), 'boolean', 'notifications');
        TenantSetting::setSetting($tenant->id, 'notify_payment_reminder', $request->boolean('notify_payment_reminder'), 'boolean', 'notifications');
        TenantSetting::setSetting($tenant->id, 'notify_fee_due', $request->boolean('notify_fee_due'), 'boolean', 'notifications');
        TenantSetting::setSetting($tenant->id, 'notify_attendance', $request->boolean('notify_attendance'), 'boolean', 'notifications');

        return redirect()->route('admin.tenants.settings.notifications', $tenant)
            ->with('success', 'Notification settings updated successfully!');
    }
}
