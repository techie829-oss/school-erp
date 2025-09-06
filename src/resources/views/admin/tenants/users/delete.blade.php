@extends('layouts.admin')

@section('title', 'Delete User')
@section('page-title', 'Delete User')
@section('page-description', 'Permanently delete user account')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.tenants.users.index', $tenant) }}"
                   class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>

        <!-- Warning Card -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-red-900">Danger Zone</h3>
                    <p class="mt-2 text-red-800">
                        This action cannot be undone. This will permanently delete the user account and remove all associated data.
                    </p>
                </div>
            </div>
        </div>

        <!-- User Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">User Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Name</label>
                    <p class="text-sm text-gray-900 font-medium">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Email</label>
                    <p class="text-sm text-gray-900 font-mono">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Admin Type</label>
                    <p class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $user->admin_type)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Created</label>
                    <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Last Login</label>
                    <p class="text-sm text-gray-900">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y H:i') : 'Never' }}</p>
                </div>
            </div>
        </div>

        <!-- Confirmation Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Confirm Deletion</h3>

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.tenants.users.destroy', [$tenant, $user->id]) }}" class="space-y-6">
                @csrf
                @method('DELETE')

                <!-- Email Confirmation -->
                <div>
                    <label for="confirmation_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Type the user's email to confirm deletion
                    </label>
                    <input type="email"
                           id="confirmation_email"
                           name="confirmation_email"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('confirmation_email') border-red-500 @enderror"
                           placeholder="Enter user's email address"
                           required>
                    @error('confirmation_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Text Confirmation -->
                <div>
                    <label for="confirmation_text" class="block text-sm font-medium text-gray-700 mb-2">
                        Type <strong>DELETE</strong> to confirm
                    </label>
                    <input type="text"
                           id="confirmation_text"
                           name="confirmation_text"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('confirmation_text') border-red-500 @enderror"
                           placeholder="Type DELETE"
                           required>
                    @error('confirmation_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warning Text -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">What will be deleted:</h4>
                            <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                                <li>User account and login credentials</li>
                                <li>All user data and preferences</li>
                                <li>User activity logs and history</li>
                                <li>Any associated permissions or roles</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.tenants.users.index', $tenant) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                            id="delete-button"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Permanently Delete User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('confirmation_email');
    const textInput = document.getElementById('confirmation_text');
    const deleteButton = document.getElementById('delete-button');
    const userEmail = '{{ $user->email }}';

    function validateForm() {
        const emailMatches = emailInput.value === userEmail;
        const textMatches = textInput.value === 'DELETE';

        deleteButton.disabled = !(emailMatches && textMatches);

        if (emailMatches && textMatches) {
            deleteButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            deleteButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    emailInput.addEventListener('input', validateForm);
    textInput.addEventListener('input', validateForm);

    // Add visual feedback for email matching
    emailInput.addEventListener('input', function() {
        if (this.value === userEmail) {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        } else {
            this.classList.remove('border-green-500');
            this.classList.add('border-red-500');
        }
    });

    // Add visual feedback for text matching
    textInput.addEventListener('input', function() {
        if (this.value === 'DELETE') {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        } else {
            this.classList.remove('border-green-500');
            this.classList.add('border-red-500');
        }
    });

    // Prevent accidental form submission
    deleteButton.addEventListener('click', function(e) {
        if (this.disabled) {
            e.preventDefault();
            return false;
        }

        if (!confirm('Are you absolutely sure you want to permanently delete this user? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection
