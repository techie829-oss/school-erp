@extends('layouts.admin')

@section('title', 'Create Tenant')
@section('page-title', 'Create New Tenant')
@section('page-description', 'Add a new tenant to the system')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.tenants.store') }}">
            @csrf

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
                       value="{{ old('name') }}"
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
                       value="{{ old('email') }}"
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
                    <option value="internal" {{ old('type') == 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="school" {{ old('type') == 'school' ? 'selected' : '' }}>School</option>
                    <option value="college" {{ old('type') == 'college' ? 'selected' : '' }}>College</option>
                    <option value="university" {{ old('type') == 'university' ? 'selected' : '' }}>University</option>
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
                        required
                        onchange="toggleDatabaseConfig()">
                    <option value="">Select database strategy</option>
                    <option value="shared" {{ old('database_strategy') == 'shared' ? 'selected' : '' }}>Shared Database</option>
                    <option value="separate" {{ old('database_strategy') == 'separate' ? 'selected' : '' }}>Separate Database</option>
                </select>
                @error('database_strategy')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Database Configuration (Only for Separate Database) -->
            <div id="database-config" class="mb-6 p-4 bg-gray-50 rounded-lg border" style="display: none;">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Database Configuration</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Database Name -->
                    <div class="md:col-span-2">
                        <label for="database_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Database Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="database_name"
                               name="database_name"
                               value="{{ old('database_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('database_name') border-red-500 @enderror"
                               placeholder="school_erp_tenant_name">
                        @error('database_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Database Host -->
                    <div>
                        <label for="database_host" class="block text-sm font-medium text-gray-700 mb-2">
                            Database Host <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="database_host"
                               name="database_host"
                               value="{{ old('database_host', 'localhost') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('database_host') border-red-500 @enderror"
                               placeholder="localhost">
                        @error('database_host')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Database Port -->
                    <div>
                        <label for="database_port" class="block text-sm font-medium text-gray-700 mb-2">
                            Database Port
                        </label>
                        <input type="number"
                               id="database_port"
                               name="database_port"
                               value="{{ old('database_port', '3306') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('database_port') border-red-500 @enderror"
                               placeholder="3306">
                        @error('database_port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Database Username -->
                    <div>
                        <label for="database_username" class="block text-sm font-medium text-gray-700 mb-2">
                            Database Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="database_username"
                               name="database_username"
                               value="{{ old('database_username') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('database_username') border-red-500 @enderror"
                               placeholder="root">
                        @error('database_username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Database Password -->
                    <div>
                        <label for="database_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Database Password
                        </label>
                        <input type="password"
                               id="database_password"
                               name="database_password"
                               value="{{ old('database_password') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('database_password') border-red-500 @enderror"
                               placeholder="Enter database password">
                        @error('database_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Database Charset -->
                    <div>
                        <label for="database_charset" class="block text-sm font-medium text-gray-700 mb-2">
                            Database Charset
                        </label>
                        <select id="database_charset"
                                name="database_charset"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('database_charset') border-red-500 @enderror">
                            <option value="utf8mb4" {{ old('database_charset', 'utf8mb4') == 'utf8mb4' ? 'selected' : '' }}>utf8mb4</option>
                            <option value="utf8" {{ old('database_charset') == 'utf8' ? 'selected' : '' }}>utf8</option>
                        </select>
                        @error('database_charset')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Database Collation -->
                    <div>
                        <label for="database_collation" class="block text-sm font-medium text-gray-700 mb-2">
                            Database Collation
                        </label>
                        <select id="database_collation"
                                name="database_collation"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('database_collation') border-red-500 @enderror">
                            <option value="utf8mb4_unicode_ci" {{ old('database_collation', 'utf8mb4_unicode_ci') == 'utf8mb4_unicode_ci' ? 'selected' : '' }}>utf8mb4_unicode_ci</option>
                            <option value="utf8mb4_general_ci" {{ old('database_collation') == 'utf8mb4_general_ci' ? 'selected' : '' }}>utf8mb4_general_ci</option>
                            <option value="utf8_unicode_ci" {{ old('database_collation') == 'utf8_unicode_ci' ? 'selected' : '' }}>utf8_unicode_ci</option>
                        </select>
                        @error('database_collation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
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
                           value="{{ old('subdomain') }}"
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
                       value="{{ old('custom_domain') }}"
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
                           {{ old('active', true) ? 'checked' : '' }}
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
                    Create Tenant
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subdomainInput = document.getElementById('subdomain');
    const nameInput = document.getElementById('name');
    let validationTimeout;

    // Auto-generate subdomain from name
    nameInput.addEventListener('input', function() {
        if (!subdomainInput.value) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            subdomainInput.value = slug;

            // Validate the auto-generated subdomain
            if (slug) {
                validateSubdomain(slug);
            }
        }
    });

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
                subdomain: subdomain
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

// Toggle database configuration section
function toggleDatabaseConfig() {
    const databaseStrategy = document.getElementById('database_strategy');
    const databaseConfig = document.getElementById('database-config');

    if (databaseStrategy.value === 'separate') {
        databaseConfig.style.display = 'block';

        // Auto-generate database name from tenant name
        const tenantName = document.getElementById('name').value;
        const databaseName = document.getElementById('database_name');
        if (tenantName && !databaseName.value) {
            const slug = tenantName.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '_')
                .replace(/-+/g, '_');
            databaseName.value = `school_erp_${slug}`;
        }
    } else {
        databaseConfig.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDatabaseConfig();
});
</script>
@endsection
