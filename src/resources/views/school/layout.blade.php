<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $tenant['name'] ?? 'School ERP' }} - @yield('title', 'Welcome')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Dynamic Color Palette -->
    {!! app(\App\Services\ColorPaletteService::class)->generateInlineCSS(request()) !!}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and School Name -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">S</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-xl font-bold text-gray-900">{{ $tenant['name'] ?? 'School ERP' }}</h1>
                                <p class="text-sm text-gray-500">{{ $tenant['location'] ?? 'Location' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('tenant.home', ['tenant' => $tenantSubdomain]) }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('tenant.home') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Home
                        </a>
                        <a href="{{ route('tenant.about', ['tenant' => $tenantSubdomain]) }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('tenant.about') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            About
                        </a>
                        <a href="{{ route('tenant.programs', ['tenant' => $tenantSubdomain]) }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('tenant.programs') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Programs
                        </a>
                        <a href="{{ route('tenant.facilities', ['tenant' => $tenantSubdomain]) }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('tenant.facilities') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Facilities
                        </a>
                        <a href="{{ route('tenant.admission', ['tenant' => $tenantSubdomain]) }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('tenant.admission') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Admission
                        </a>
                        <a href="{{ route('tenant.contact', ['tenant' => $tenantSubdomain]) }}" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('tenant.contact') ? 'text-primary-600 border-b-2 border-primary-600 pb-1' : '' }}">
                            Contact
                        </a>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="hidden md:flex items-center space-x-4">
                                            <a href="{{ route('tenant.login', ['tenant' => $tenantSubdomain]) }}" class="px-4 py-2 rounded-lg text-sm font-medium text-secondary-600 bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 transition-colors">
                        Parent Login
                    </a>
                    <a href="{{ route('tenant.register', ['tenant' => $tenantSubdomain]) }}" class="px-6 py-2 rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                        Apply Now
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
            </div>

            <!-- Mobile Navigation -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('tenant.home', ['tenant' => $tenantSubdomain]) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->routeIs('tenant.home') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('tenant.about', ['tenant' => $tenantSubdomain]) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->routeIs('tenant.about') ? 'text-primary-600 bg-primary-50' : '' }}">
                        About
                    </a>
                    <a href="{{ route('tenant.programs', ['tenant' => $tenantSubdomain]) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->routeIs('tenant.programs') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Programs
                    </a>
                    <a href="{{ route('tenant.facilities', ['tenant' => $tenantSubdomain]) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->routeIs('tenant.facilities') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Facilities
                    </a>
                    <a href="{{ route('tenant.admission', ['tenant' => $tenantSubdomain]) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->routeIs('tenant.admission') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Admission
                    </a>
                    <a href="{{ route('tenant.contact', ['tenant' => $tenantSubdomain]) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors {{ request()->routeIs('tenant.contact') ? 'text-primary-600 bg-primary-50' : '' }}">
                        Contact
                    </a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200 px-2 sm:px-3 space-y-3">
                    <a href="{{ route('tenant.login', ['tenant' => $tenantSubdomain]) }}" class="block w-full text-center px-4 py-3 text-base font-medium text-secondary-600 bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 transition-colors rounded-lg">
                        Parent Login
                    </a>
                    <a href="{{ route('tenant.register', ['tenant' => $tenantSubdomain]) }}" class="block w-full text-center px-6 py-3 text-base font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors rounded-lg">
                        Apply Now
                    </a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-lg font-semibold mb-4">{{ $tenant['name'] ?? 'School ERP' }}</h3>
                        <p class="text-gray-300 mb-4">{{ $tenant['description'] ?? 'Excellence in Education' }}</p>
                        <p class="text-gray-400 text-sm">
                            <strong>Location:</strong> {{ $tenant['location'] ?? 'Location' }}<br>
                            <strong>Students:</strong> {{ $tenant['student_count'] ?? 'N/A' }}<br>
                            <strong>Database:</strong> {{ ucfirst($tenant['database_strategy'] ?? 'shared') }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('tenant.about', ['tenant' => $tenantSubdomain]) }}" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
                            <li><a href="{{ route('tenant.programs', ['tenant' => $tenantSubdomain]) }}" class="text-gray-300 hover:text-white transition-colors">Programs</a></li>
                            <li><a href="{{ route('tenant.admission', ['tenant' => $tenantSubdomain]) }}" class="text-gray-300 hover:text-white transition-colors">Admission</a></li>
                            <li><a href="{{ route('tenant.contact', ['tenant' => $tenantSubdomain]) }}" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold mb-4">Connect</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('tenant.login', ['tenant' => $tenantSubdomain]) }}" class="text-gray-300 hover:text-white transition-colors">Parent Portal</a></li>
                            <li><a href="{{ route('tenant.register', ['tenant' => $tenantSubdomain]) }}" class="text-gray-300 hover:text-white transition-colors">Apply Online</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Newsletter</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ $tenant['name'] ?? 'School ERP' }}. All rights reserved.</p>
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
