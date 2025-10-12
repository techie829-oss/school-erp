@extends('layouts.admin')

@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')
@section('page-description', 'Update tenant information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
            @csrf
            @method('PUT')

            <!-- General Error Display -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were errors with your submission:
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Tenant Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $tenant->data['name'] ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                       placeholder="Enter tenant name"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Contact Email <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email', $tenant->data['email'] ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                       placeholder="Enter contact email"
                       required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div class="mb-6">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Institution Type <span class="text-red-500">*</span>
                </label>
                <select id="type"
                        name="type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('type') border-red-500 @enderror"
                        required>
                    <option value="">Select institution type</option>
                    <option value="internal" {{ old('type', $tenant->data['type'] ?? '') == 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="school" {{ old('type', $tenant->data['type'] ?? '') == 'school' ? 'selected' : '' }}>School</option>
                    <option value="college" {{ old('type', $tenant->data['type'] ?? '') == 'college' ? 'selected' : '' }}>College</option>
                    <option value="university" {{ old('type', $tenant->data['type'] ?? '') == 'university' ? 'selected' : '' }}>University</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subdomain -->
            <div class="mb-6">
                <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-2">
                    Subdomain <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <input type="text"
                           id="subdomain"
                           name="subdomain"
                           value="{{ old('subdomain', $tenant->data['subdomain'] ?? '') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('subdomain') border-red-500 @enderror"
                           placeholder="subdomain"
                           required>
                    <span class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-lg bg-gray-50 text-gray-500 text-sm">
                        .{{ config('all.domains.primary') }}
                    </span>
                </div>
                @error('subdomain')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">This will be the URL for the tenant (e.g., schoolname.myschool.test)</p>
            </div>

            <!-- Custom Domain (Optional) -->
            <div class="mb-6">
                <label for="custom_domain" class="block text-sm font-medium text-gray-700 mb-2">
                    Custom Domain (Optional)
                </label>
                <input type="text"
                       id="custom_domain"
                       name="custom_domain"
                       value="{{ old('custom_domain', $tenant->data['custom_domain'] ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('custom_domain') border-red-500 @enderror"
                       placeholder="example.com">
                @error('custom_domain')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Optional custom domain for the tenant</p>
            </div>

            <!-- Active Status -->
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox"
                           id="active"
                           name="active"
                           value="1"
                           {{ old('active', $tenant->data['is_active'] ?? '1') ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="active" class="ml-2 block text-sm text-gray-900">
                        Active tenant
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Inactive tenants will not be accessible</p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.tenants.show', $tenant) }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Update Tenant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
