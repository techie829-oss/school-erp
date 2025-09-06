<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #ffffff 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-6">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <!-- Logo -->
            <div class="mx-auto w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            
            <!-- School Name -->
            <h1 class="text-2xl font-bold text-blue-900 mb-2">
                School Management System
            </h1>
            
            <!-- Subtitle -->
            <p class="text-gray-600 text-sm mb-3">Admin Portal</p>
            
            <!-- Admin Badge -->
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 border border-blue-200">
                <svg class="w-3 h-3 text-blue-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-blue-800 text-xs font-medium">
                    Admin Access
                </span>
            </div>
        </div>

        <!-- Login Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if (session('tenant_redirect'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                {{ session('tenant_redirect.message') }}
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Please login at: <strong>{{ session('tenant_redirect.tenant_name') }}</strong></p>
                                <p class="mt-1">
                                    <a href="{{ session('tenant_redirect.login_url') }}" 
                                       class="font-medium underline text-blue-600 hover:text-blue-500">
                                        {{ session('tenant_redirect.tenant_domain') }}/login
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Welcome Message -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Welcome Back</h2>
                <p class="text-gray-600 text-sm">Sign in to your account to continue</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                               placeholder="admin@myschool.test"
                               value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               autocomplete="current-password" 
                               required 
                               class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror" 
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input id="remember" 
                           name="remember" 
                           type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <!-- Sign In Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    Sign In
                </button>
            </form>
        </div>
    </div>

    <script>
    // Check for tenant user on email input
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const adminDomain = '{{ config("all.domains.admin") }}';
        const currentHost = window.location.hostname;
        
        if (currentHost === adminDomain) {
            emailInput.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    checkTenantUser(this.value);
                }
            });
        }
    });

    function checkTenantUser(email) {
        fetch('{{ route("check.tenant.user") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                // Show redirect message
                showTenantRedirectMessage(data);
            }
        })
        .catch(error => {
            console.log('Tenant check failed:', error);
        });
    }

    function showTenantRedirectMessage(data) {
        // Remove existing message
        const existingMessage = document.querySelector('.tenant-redirect-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = 'tenant-redirect-message mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg';
        messageDiv.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        ${data.message}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Please login at: <strong>${data.tenant_name}</strong></p>
                        <p class="mt-1">
                            <a href="${data.login_url}" 
                               class="font-medium underline text-blue-600 hover:text-blue-500">
                                ${data.tenant_domain}/login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        `;
        
        // Insert before the form
        const form = document.querySelector('form');
        form.parentNode.insertBefore(messageDiv, form);
    }
    </script>
</body>
</html>
