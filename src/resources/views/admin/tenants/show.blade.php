@extends('layouts.admin')

@section('title', 'View Tenant')
@section('page-title', 'Tenant Details')
@section('page-description', 'View tenant information and settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $tenant->data['name'] ?? 'Unnamed Tenant' }}</h1>
            <p class="text-gray-600">Tenant ID: {{ $tenant->id }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.tenants.edit', $tenant) }}"
               class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Edit Tenant
            </a>
            <a href="{{ route('admin.tenants.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Back to List
            </a>
        </div>
    </div>

    <!-- Tenant Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Basic Information -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Tenant Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $tenant->data['name'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Email Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $tenant->data['email'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Institution Type</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($tenant->data['type'] ?? 'N/A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Domain</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if(isset($tenant->data['full_domain']))
                            {{ $tenant->data['full_domain'] }}
                        @else
                            {{ $tenant->id }}.{{ config('all.domains.primary') }}
                        @endif
                    </p>
                    @if(isset($tenant->data['domain_type']))
                        <p class="mt-1 text-xs text-gray-500">
                            Type: {{ ucfirst($tenant->data['domain_type']) }}
                            @if($tenant->data['domain_type'] === 'subdomain' && isset($tenant->data['subdomain']))
                                ({{ $tenant->data['subdomain'] }})
                            @elseif($tenant->data['domain_type'] === 'custom' && isset($tenant->data['custom_domain']))
                                ({{ $tenant->data['custom_domain'] }})
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Database Strategy</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($tenant->data['database_strategy'] ?? 'N/A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Status</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ ($tenant->data['active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ($tenant->data['active'] ?? false) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Created At</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $tenant->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $tenant->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="http://{{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}"
               target="_blank"
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Visit Tenant Site</h4>
                    <p class="text-sm text-gray-600">Open tenant website</p>
                </div>
            </a>

            <a href="http://{{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}/admin"
               target="_blank"
               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Admin Panel</h4>
                    <p class="text-sm text-gray-600">Access tenant admin</p>
                </div>
            </a>

            <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}"
                  class="flex items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors cursor-pointer"
                  onsubmit="return confirm('Are you sure you want to delete this tenant? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center w-full">
                    <div class="p-2 bg-red-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h4 class="font-medium text-gray-900">Delete Tenant</h4>
                        <p class="text-sm text-gray-600">Remove tenant permanently</p>
                    </div>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
