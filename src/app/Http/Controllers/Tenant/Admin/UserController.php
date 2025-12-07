<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = User::forTenant($tenant->id);

        // Filter by user type
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get counts for stats
        $totalUsers = User::forTenant($tenant->id)->count();
        $activeUsers = User::forTenant($tenant->id)->where('is_active', true)->count();
        $teachersCount = User::forTenant($tenant->id)->where('user_type', 'teacher')->count();
        $staffCount = User::forTenant($tenant->id)->where('user_type', 'staff')->count();
        $studentsCount = User::forTenant($tenant->id)->where('user_type', 'student')->count();
        $adminsCount = User::forTenant($tenant->id)->where('user_type', 'school_admin')->count();

        return view('tenant.admin.users.index', compact(
            'users',
            'tenant',
            'totalUsers',
            'activeUsers',
            'teachersCount',
            'staffCount',
            'studentsCount',
            'adminsCount'
        ));
    }

    /**
     * Show the form for creating a new user
     */
    public function create(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return view('tenant.admin.users.create', compact('tenant'));
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where('tenant_id', $tenant->id),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type' => 'required|in:school_admin,teacher,staff,student',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id,
                'user_type' => $request->user_type,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
            ]);

            DB::commit();

            return redirect()
                ->to(url('/admin/users/' . $user->id))
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, $userId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $user = User::forTenant($tenant->id)->findOrFail($userId);

        return view('tenant.admin.users.show', compact('user', 'tenant'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(Request $request, $userId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $user = User::forTenant($tenant->id)->findOrFail($userId);

        return view('tenant.admin.users.edit', compact('user', 'tenant'));
    }

    /**
     * Update the specified user in storage
     */
    public function update(Request $request, $userId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $user = User::forTenant($tenant->id)->findOrFail($userId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where('tenant_id', $tenant->id)->ignore($user->id),
            ],
            'password' => 'nullable|confirmed|min:8',
            'user_type' => 'required|in:school_admin,teacher,staff,student',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'user_type' => $request->user_type,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            DB::commit();

            return redirect()
                ->to(url('/admin/users/' . $user->id))
                ->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(Request $request, $userId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $user = User::forTenant($tenant->id)->findOrFail($userId);

        // Prevent deleting the current logged-in user
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            DB::beginTransaction();

            $user->delete();

            DB::commit();

            return redirect()
                ->to(url('/admin/users'))
                ->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
