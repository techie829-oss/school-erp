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
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email', $tenant->data['email'] ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                       placeholder="Enter email address"
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

            <!-- Database Strategy -->
            <div class="mb-6">
                <label for="database_strategy" class="block text-sm font-medium text-gray-700 mb-2">
                    Database Strategy <span class="text-red-500">*</span>
                </label>
                <select id="database_strategy"
                        name="database_strategy"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('database_strategy') border-red-500 @enderror"
                        required>
                    <option value="">Select database strategy</option>
                    <option value="shared" {{ old('database_strategy', $tenant->data['database_strategy'] ?? '') == 'shared' ? 'selected' : '' }}>Shared Database</option>
                    <option value="separate" {{ old('database_strategy', $tenant->data['database_strategy'] ?? '') == 'separate' ? 'selected' : '' }}>Separate Database</option>
                </select>
                @error('database_strategy')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subdomain (Mandatory) -->
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
                           placeholder="schoola"
                           pattern="[a-z0-9-]+"
                           title="Only lowercase letters, numbers, and hyphens allowed"
                           required>
                    <span class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600 text-sm">
                        .{{ config('all.domains.primary') }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and hyphens allowed</p>
                @error('subdomain')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Custom Domain (Optional) -->
            <div class="mb-6">
                <label for="custom_domain" class="block text-sm font-medium text-gray-700 mb-2">
                    Custom Domain <span class="text-gray-400">(Optional)</span>
                </label>
                <input type="text"
                       id="custom_domain"
                       name="custom_domain"
                       value="{{ old('custom_domain', $tenant->data['custom_domain'] ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('custom_domain') border-red-500 @enderror"
                       placeholder="schoola.com"
                       pattern="[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
                       title="Enter a valid domain name">
                <p class="mt-1 text-xs text-gray-500">Enter the full domain name (e.g., schoola.com) - This is optional</p>
                @error('custom_domain')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox"
                           id="active"
                           name="active"
                           value="1"
                           {{ old('active', $tenant->data['active'] ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="active" class="ml-2 block text-sm text-gray-700">
                        Active (tenant can access the system)
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.tenants.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Update Tenant
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subdomainInput = document.getElementById('subdomain');
    const tenantId = '{{ $tenant->id }}';
    let validationTimeout;

    // Real-time subdomain validation
    subdomainInput.addEventListener('input', function() {
        const subdomain = this.value.trim();
        
        // Clear previous timeout
        if (validationTimeout) {
            clearTimeout(validationTimeout);
        }
        
        // Validate after 500ms of no typing
        validationTimeout = setTimeout(() => {
            if (subdomain) {
                validateSubdomain(subdomain);
            } else {
                clearValidation();
            }
        }, 500);
    });

    function validateSubdomain(subdomain) {
        // Show loading state
        showValidationState('checking', 'Checking availability...');
        
        fetch('{{ route("admin.tenants.check-subdomain") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                subdomain: subdomain,
                exclude_tenant_id: tenantId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                showValidationState('success', data.message);
            } else {
                showValidationState('error', data.message);
            }
        })
        .catch(error => {
            console.error('Validation error:', error);
            showValidationState('error', 'Error checking subdomain availability');
        });
    }

    function showValidationState(type, message) {
        // Remove existing validation elements
        const existingFeedback = subdomainInput.parentNode.querySelector('.validation-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Create validation feedback element
        const feedback = document.createElement('div');
        feedback.className = `validation-feedback mt-1 text-sm flex items-center`;
        
        let iconClass, textClass;
        switch (type) {
            case 'success':
                iconClass = 'text-green-500';
                textClass = 'text-green-600';
                break;
            case 'error':
                iconClass = 'text-red-500';
                textClass = 'text-red-600';
                break;
            case 'checking':
                iconClass = 'text-blue-500';
                textClass = 'text-blue-600';
                break;
        }

        feedback.innerHTML = `
            <svg class="w-4 h-4 mr-1 ${iconClass}" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'success' ? 
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>' :
                    type === 'error' ?
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>' :
                    '<path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>'
                }
            </svg>
            <span class="${textClass}">${message}</span>
        `;

        subdomainInput.parentNode.appendChild(feedback);
    }

    function clearValidation() {
        const existingFeedback = subdomainInput.parentNode.querySelector('.validation-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
    }
});
</script>
@endsection
