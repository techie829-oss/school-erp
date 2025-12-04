<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $cmsSiteName ?? $tenant['name'] ?? 'School ERP' }} - @yield('title', 'School Management System')</title>
    <meta name="description" content="@yield('description', $cmsSiteTagline ?? $tenant['description'] ?? 'Excellence in Education')">

    @if($cmsFavicon ?? null)
        <link rel="icon" type="image/x-icon" href="{{ $cmsFavicon }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        .footer {
            margin-top: auto;
        }
    </style>

    <!-- Custom CSS with Color Palette - Loaded after Tailwind to ensure proper override -->
    @php
        try {
            $colorCSS = app(\App\Services\ColorPaletteService::class)->generateInlineCSS(request());
        } catch (\Exception $e) {
            $colorCSS = '<style>
                :root {
                    --color-primary-600: #3b82f6;
                    --color-primary-700: #1d4ed8;
                    --color-secondary-600: #475569;
                    --color-secondary-100: #f1f5f9;
                    --color-secondary-200: #e2e8f0;
                }
            </style>';
        }
    @endphp
    {!! $colorCSS !!}

    <!-- Additional CSS to ensure colors are visible -->
    <style>
        /* Force colors to be visible */
        .text-primary-600,
        .hover\:text-primary-600:hover {
            color: var(--color-primary-600) !important;
        }

        .bg-primary-600,
        .hover\:bg-primary-700:hover {
            background-color: var(--color-primary-600) !important;
        }

        .text-secondary-600 {
            color: var(--color-secondary-600) !important;
        }

        .bg-secondary-100,
        .hover\:bg-secondary-200:hover {
            background-color: var(--color-secondary-100) !important;
        }

        .border-primary-600 {
            border-color: var(--color-primary-600) !important;
        }

        .bg-primary-50 {
            background-color: var(--color-primary-50) !important;
        }

        /* Debug: Make sure colors are visible */
        .debug-color {
            border: 2px solid var(--color-primary-600) !important;
        }
    </style>

    <!-- Debug: Show CSS variables -->
    @if(config('app.debug'))
    <script>
        console.log('CSS Variables Debug:');
        console.log('Primary 600:', getComputedStyle(document.documentElement).getPropertyValue('--color-primary-600'));
        console.log('Secondary 600:', getComputedStyle(document.documentElement).getPropertyValue('--color-secondary-600'));
    </script>
    @endif
</head>
<body class="font-sans antialiased bg-white flex flex-col min-h-screen">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Top row with logo and CTA buttons -->
                <div class="flex justify-between items-center h-16">
                    <!-- Logo and School Name -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            @if($cmsLogo ?? null)
                                <img src="{{ $cmsLogo }}" alt="{{ $cmsSiteName ?? 'Logo' }}" class="h-12 w-auto max-w-[150px] object-contain">
                            @else
                                <div class="w-12 h-12 bg-primary-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-xl">{{ strtoupper(substr($cmsSiteName ?? $tenant['name'] ?? 'S', 0, 1)) }}</span>
                                </div>
                            @endif
                            <div class="ml-4">
                                <h1 class="text-lg font-bold text-gray-900 leading-tight">
                                    <span class="hidden sm:inline">{{ $cmsSiteName ?? $tenant['name'] ?? 'School ERP' }}</span>
                                    <span class="sm:hidden">{{ $tenant['short_name'] ?? substr($cmsSiteName ?? $tenant['name'] ?? 'School', 0, 20) }}</span>
                                </h1>
                                <p class="text-xs text-gray-500 leading-tight">{{ $cmsSiteTagline ?? $tenant['location'] ?? 'Location' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="hidden md:flex items-center space-x-3">
                        @auth
                            <a href="{{ url('/admin/dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-secondary-600 bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ url('/login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-secondary-600 bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 transition-colors">
                                Parent Login
                            </a>
                        @endauth
                        <a href="{{ url('/contact') }}" class="px-6 py-2 rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                            Contact Us
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button type="button" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 rounded-md p-2" onclick="toggleMobileMenu()">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Navigation row -->
                <div class="hidden md:flex justify-center border-t border-gray-100 py-3">
                    <div class="flex items-center space-x-8">
                        <a href="{{ url('/') }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('/') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Home
                        </a>
                        <a href="{{ url('/about') }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('about') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            About
                        </a>
                        <a href="{{ url('/programs') }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('programs') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Programs
                        </a>
                        <a href="{{ url('/facilities') }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('facilities') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Facilities
                        </a>
                        <a href="{{ url('/admission') }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('admission') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Admission
                        </a>
                        <a href="{{ url('/contact') }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('contact') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Contact
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->is('/') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Home
                    </a>
                    <a href="{{ url('/about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->is('about') ? 'text-primary-600 bg-primary-50' : '' }}">
                        About
                    </a>
                    <a href="{{ url('/programs') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->is('programs') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Programs
                    </a>
                    <a href="{{ url('/facilities') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->is('facilities') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Facilities
                    </a>
                    <a href="{{ url('/admission') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->is('admission') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Admission
                    </a>
                    <a href="{{ url('/contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->is('contact') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Contact
                    </a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200 px-2 sm:px-3 space-y-3">
                    @auth
                        <a href="{{ url('/admin/dashboard') }}" class="block w-full text-center px-4 py-3 text-base font-medium text-secondary-600 bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 transition-colors rounded-lg">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ url('/login') }}" class="block w-full text-center px-4 py-3 text-base font-medium text-secondary-600 bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 transition-colors rounded-lg">
                            Parent Login
                        </a>
                    @endauth
                    <a href="{{ url('/contact') }}" class="block w-full text-center px-6 py-3 text-base font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors rounded-lg">
                        Contact Us
                    </a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gradient-to-b from-gray-900 to-gray-800 text-white mt-auto">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                    <!-- About Section -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center mb-6">
                            @if($cmsLogo ?? null)
                                <img src="{{ $cmsLogo }}" alt="{{ $cmsSiteName ?? 'Logo' }}" class="h-12 w-auto max-w-[150px] object-contain mr-4">
                            @else
                                <div class="w-12 h-12 bg-primary-600 rounded-lg flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-xl">{{ strtoupper(substr($cmsSiteName ?? $tenant['name'] ?? 'S', 0, 1)) }}</span>
                                </div>
                            @endif
                            <h3 class="text-2xl font-bold text-white">
                                {{ $cmsSiteName ?? $tenant['name'] ?? 'School ERP' }}
                            </h3>
                        </div>
                        <p class="text-gray-300 mb-6 leading-relaxed max-w-md">
                            {{ $cmsSiteTagline ?? $tenant['description'] ?? 'Excellence in Education' }}
                        </p>
                        @if($cmsFooterText ?? null)
                            <p class="text-gray-300 mb-6 leading-relaxed">{{ $cmsFooterText }}</p>
                        @endif

                        <!-- Contact Info -->
                        <div class="space-y-3 text-gray-300 text-sm">
                            @if($cmsContactAddress ?? null)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-primary-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $cmsContactAddress }}</span>
                            </div>
                            @elseif(isset($tenant['location']) && $tenant['location'])
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-primary-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $tenant['location'] }}</span>
                            </div>
                            @endif
                            @if($cmsContactPhone ?? null)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <a href="tel:{{ $cmsContactPhone }}" class="hover:text-primary-400 transition-colors">{{ $cmsContactPhone }}</a>
                            </div>
                            @endif
                            @if($cmsContactEmail ?? null)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $cmsContactEmail }}" class="hover:text-primary-400 transition-colors">{{ $cmsContactEmail }}</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-lg font-bold mb-6 text-white">Quick Links</h4>
                        <ul class="space-y-3">
                            <li><a href="{{ url('/') }}" class="text-gray-300 hover:text-primary-400 transition-colors flex items-center group">
                                <svg class="w-4 h-4 mr-2 text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                Home
                            </a></li>
                            <li><a href="{{ url('/about') }}" class="text-gray-300 hover:text-primary-400 transition-colors flex items-center group">
                                <svg class="w-4 h-4 mr-2 text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                About Us
                            </a></li>
                            <li><a href="{{ url('/programs') }}" class="text-gray-300 hover:text-primary-400 transition-colors flex items-center group">
                                <svg class="w-4 h-4 mr-2 text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                Programs
                            </a></li>
                            <li><a href="{{ url('/facilities') }}" class="text-gray-300 hover:text-primary-400 transition-colors flex items-center group">
                                <svg class="w-4 h-4 mr-2 text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                Facilities
                            </a></li>
                            <li><a href="{{ url('/admission') }}" class="text-gray-300 hover:text-primary-400 transition-colors flex items-center group">
                                <svg class="w-4 h-4 mr-2 text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                Admission
                            </a></li>
                            <li><a href="{{ url('/contact') }}" class="text-gray-300 hover:text-primary-400 transition-colors flex items-center group">
                                <svg class="w-4 h-4 mr-2 text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                Contact
                            </a></li>
                        </ul>
                    </div>

                    <!-- Connect Section -->
                    <div>
                        <h4 class="text-lg font-bold mb-6 text-white">Connect</h4>
                        <ul class="space-y-3 mb-6">
                            @auth
                                <li><a href="{{ url('/admin/dashboard') }}" class="text-gray-300 hover:text-primary-400 transition-colors flex items-center group">
                                    <svg class="w-4 h-4 mr-2 text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    Dashboard
                                </a></li>
                            @else
                                <li><a href="{{ url('/login') }}" class="text-gray-300 hover:text-primary-400 transition-colors flex items-center group">
                                    <svg class="w-4 h-4 mr-2 text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    Parent Portal
                                </a></li>
                            @endauth
                        </ul>

                        <!-- Social Media Links -->
                        @if(!empty($cmsSocialMedia) && (isset($cmsSocialMedia['facebook']) || isset($cmsSocialMedia['twitter']) || isset($cmsSocialMedia['instagram']) || isset($cmsSocialMedia['linkedin']) || isset($cmsSocialMedia['youtube'])))
                        <div>
                            <h5 class="text-sm font-semibold mb-4 text-gray-300 uppercase tracking-wider">Follow Us</h5>
                            <div class="flex space-x-3">
                                @if(!empty($cmsSocialMedia['facebook']))
                                <a href="{{ $cmsSocialMedia['facebook'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors group">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                @endif
                                @if(!empty($cmsSocialMedia['twitter']))
                                <a href="{{ $cmsSocialMedia['twitter'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center hover:bg-sky-500 transition-colors group">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                </a>
                                @endif
                                @if(!empty($cmsSocialMedia['instagram']))
                                <a href="{{ $cmsSocialMedia['instagram'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center hover:bg-gradient-to-br hover:from-purple-500 hover:to-pink-500 transition-colors group">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
                                @endif
                                @if(!empty($cmsSocialMedia['linkedin']))
                                <a href="{{ $cmsSocialMedia['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors group">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                                @endif
                                @if(!empty($cmsSocialMedia['youtube']))
                                <a href="{{ $cmsSocialMedia['youtube'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center hover:bg-red-600 transition-colors group">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-700">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <p class="text-gray-400 text-sm text-center md:text-left">
                            &copy; {{ date('Y') }} {{ $cmsSiteName ?? $tenant['name'] ?? 'School ERP' }}. All rights reserved.
                        </p>
                        <p class="text-gray-500 text-sm text-center md:text-right">
                            Powered by <span class="text-primary-400">School ERP System</span>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
