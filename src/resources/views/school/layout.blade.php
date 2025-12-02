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
        <footer class="bg-gray-900 text-white mt-auto">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-lg font-semibold mb-4 text-white">
                            {{ $cmsSiteName ?? $tenant['name'] ?? 'School ERP' }}
                        </h3>
                        <p class="text-gray-300 mb-4">
                            {{ $cmsSiteTagline ?? $tenant['description'] ?? 'Excellence in Education' }}
                        </p>
                        @if($cmsFooterText ?? null)
                            <p class="text-gray-300 mb-4">{{ $cmsFooterText }}</p>
                        @endif
                        <div class="text-gray-400 text-sm space-y-1">
                            @if($cmsContactAddress ?? null)
                            <p><strong>Address:</strong> {{ $cmsContactAddress }}</p>
                            @elseif(isset($tenant['location']) && $tenant['location'])
                            <p><strong>Location:</strong> {{ $tenant['location'] }}</p>
                            @endif
                            @if($cmsContactPhone ?? null)
                            <p><strong>Phone:</strong> {{ $cmsContactPhone }}</p>
                            @endif
                            @if($cmsContactEmail ?? null)
                            <p><strong>Email:</strong> {{ $cmsContactEmail }}</p>
                            @endif
                            @if(isset($tenant['student_count']) && $tenant['student_count'])
                            <p><strong>Students:</strong> {{ number_format($tenant['student_count']) }}</p>
                            @endif
                            <p><strong>Type:</strong> {{ ucfirst($tenant['type'] ?? 'School') }}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-semibold mb-4 text-white">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ url('/about') }}" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
                            <li><a href="{{ url('/programs') }}" class="text-gray-300 hover:text-white transition-colors">Programs</a></li>
                            <li><a href="{{ url('/admission') }}" class="text-gray-300 hover:text-white transition-colors">Admission</a></li>
                            <li><a href="{{ url('/contact') }}" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-md font-semibold mb-4 text-white">Connect</h4>
                        <ul class="space-y-2">
                            @auth
                                <li><a href="{{ url('/admin/dashboard') }}" class="text-gray-300 hover:text-white transition-colors">Dashboard</a></li>
                            @else
                                <li><a href="{{ url('/login') }}" class="text-gray-300 hover:text-white transition-colors">Parent Portal</a></li>
                            @endauth
                            <li><a href="{{ url('/contact') }}" class="text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Newsletter</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ $cmsSiteName ?? $tenant['name'] ?? 'School ERP' }}. All rights reserved.</p>
                    <p class="text-sm mt-2">Powered by School ERP System</p>
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
