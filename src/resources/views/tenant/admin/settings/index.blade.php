@extends('tenant.layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Settings</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Settings
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage your institution settings and preferences
            </p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Settings Tabs -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('general')" id="tab-general" class="tab-button border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    General Settings
                </button>
                <button onclick="showTab('academic')" id="tab-academic" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Academic Settings
                </button>
                <button onclick="showTab('attendance')" id="tab-attendance" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Attendance Settings
                </button>
                <button onclick="showTab('payment')" id="tab-payment" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Payment Settings
                </button>
            </nav>
        </div>

        <!-- General Settings Tab -->
        <div id="content-general" class="tab-content p-6">
            @include('tenant.admin.settings.general')
        </div>


        <!-- Academic Settings Tab -->
        <div id="content-academic" class="tab-content hidden p-6">
            @include('tenant.admin.settings.academic')
        </div>

        <!-- Attendance Settings Tab -->
        <div id="content-attendance" class="tab-content hidden p-6">
            @include('tenant.admin.settings.attendance')
        </div>

        <!-- Payment Settings Tab -->
        <div id="content-payment" class="tab-content hidden p-6">
            @include('tenant.admin.settings.payment')
        </div>

    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('border-primary-500', 'text-primary-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Add active class to selected button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('border-primary-500', 'text-primary-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');

    // Save active tab to localStorage
    localStorage.setItem('settings_active_tab', tabName);
}

// On page load, restore last active tab
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = localStorage.getItem('settings_active_tab');
    if (savedTab && document.getElementById('content-' + savedTab)) {
        showTab(savedTab);
    }
});
</script>
@endsection

