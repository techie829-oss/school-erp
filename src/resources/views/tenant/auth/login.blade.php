@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-6">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <!-- Logo -->
            <div class="mx-auto w-16 h-16 bg-primary-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>

            <!-- School Name -->
            <h1 class="text-2xl font-bold text-primary-900 mb-2">
                {{ $tenant->data['name'] ?? 'School Management System' }}
            </h1>

            <!-- Subtitle -->
            <p class="text-secondary-600 text-sm mb-3">School Management System</p>

            <!-- Database Badge -->
            @if(isset($tenant->data['database_strategy']))
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-accent-50 border border-accent-600">
                    <svg class="w-3 h-3 text-accent-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-accent-600 text-xs font-medium">
                        {{ ucfirst($tenant->data['database_strategy'] ?? 'shared') }} Database
                    </span>
                </div>
            @endif
        </div>

        <!-- Login Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Welcome Message -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-secondary-900 mb-2">Welcome Back</h2>
                <p class="text-secondary-600">Sign in to your account to continue</p>
            </div>

            <form method="POST" action="{{ route('tenant.login.post', ['tenant' => request()->route('tenant')]) }}" class="space-y-6">
                @csrf
                
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-secondary-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               class="block w-full pl-10 pr-3 py-3 border border-secondary-300 rounded-xl text-secondary-900 placeholder-secondary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('email') border-error @enderror" 
                               placeholder="svps@gmail.com"
                               value="{{ old('email', 'svps@gmail.com') }}">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-error-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-secondary-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               autocomplete="current-password" 
                               required 
                               class="block w-full pl-10 pr-3 py-3 border border-secondary-300 rounded-xl text-secondary-900 placeholder-secondary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('password') border-error @enderror" 
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-error-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember" class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-secondary-300 rounded">
                        <span class="ml-2 text-sm text-secondary-700">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" 
                           class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
