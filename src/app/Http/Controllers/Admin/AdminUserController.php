<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index(Request $request)
    {
        $query = AdminUser::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('admin_type', 'like', "%{$search}%");
            });
        }

        // Filter by admin type
        if ($request->filled('admin_type')) {
            $query->where('admin_type', $request->get('admin_type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified admin user.
     */
    public function show(AdminUser $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for changing user password.
     */
    public function changePassword(AdminUser $user)
    {
        return view('admin.users.change-password', compact('user'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request, AdminUser $user)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(AdminUser $user)
    {
        $user->update([
            'active' => !$user->active,
        ]);

        $status = $user->active ? 'activated' : 'deactivated';

        return redirect()->route('admin.users.index')
            ->with('success', "User {$status} successfully!");
    }
}
