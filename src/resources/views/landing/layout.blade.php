<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('all.company.name') }} - @yield('title', 'Complete School Management System')</title>
    <meta name="description" content="@yield('description', config('all.company.tagline'))">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WEYDBBHMZ1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-WEYDBBHMZ1');
    </script>

    <!-- Default Color Palette for Landing Page -->
    <style>
        :root {
            --color-primary-50: {{ config('all.colors.primary.50') }};
            --color-primary-100: {{ config('all.colors.primary.100') }};
            --color-primary-500: {{ config('all.colors.primary.500') }};
            --color-primary-600: {{ config('all.colors.primary.600') }};
            --color-primary-700: {{ config('all.colors.primary.700') }};
            --color-primary-900: {{ config('all.colors.primary.900') }};
            --color-secondary-50: {{ config('all.colors.secondary.50') }};
            --color-secondary-100: {{ config('all.colors.secondary.100') }};
            --color-secondary-500: {{ config('all.colors.secondary.500') }};
            --color-secondary-600: {{ config('all.colors.secondary.600') }};
            --color-secondary-700: {{ config('all.colors.secondary.700') }};
            --color-secondary-900: {{ config('all.colors.secondary.900') }};
            --color-accent-50: {{ config('all.colors.accent.50') }};
            --color-accent-100: {{ config('all.colors.accent.100') }};
            --color-accent-500: {{ config('all.colors.accent.500') }};
            --color-accent-600: {{ config('all.colors.accent.600') }};
            --color-accent-700: {{ config('all.colors.accent.700') }};
            --color-accent-900: {{ config('all.colors.accent.900') }};
            --color-success: {{ config('all.colors.success') }};
            --color-warning: {{ config('all.colors.warning') }};
            --color-error: {{ config('all.colors.error') }};
            --color-info: {{ config('all.colors.info') }};
        }
    </style>

    <style>
        .bg-primary-50 {
            background-color: var(--color-primary-50);
        }

        .bg-primary-100 {
            background-color: var(--color-primary-100);
        }

        .bg-primary-500 {
            background-color: var(--color-primary-500);
        }

        .bg-primary-600 {
            background-color: var(--color-primary-600);
        }

        .bg-primary-700 {
            background-color: var(--color-primary-700);
        }

        .bg-primary-900 {
            background-color: var(--color-primary-900);
        }

        .text-primary-500 {
            color: var(--color-primary-500);
        }

        .text-primary-600 {
            color: var(--color-primary-600);
        }

        .text-primary-700 {
            color: var(--color-primary-700);
        }

        .text-primary-900 {
            color: var(--color-primary-900);
        }

        .border-primary-500 {
            border-color: var(--color-primary-500);
        }

        .border-primary-600 {
            border-color: var(--color-primary-600);
        }

        .bg-accent-50 {
            background-color: var(--color-accent-50);
        }

        .bg-accent-100 {
            background-color: var(--color-accent-100);
        }

        .bg-accent-500 {
            background-color: var(--color-accent-500);
        }

        .text-accent-600 {
            color: var(--color-accent-600);
        }

        .bg-secondary-50 {
            background-color: var(--color-secondary-50);
        }

        .bg-secondary-100 {
            background-color: var(--color-secondary-100);
        }

        .text-secondary-600 {
            color: var(--color-secondary-600);
        }

        .text-secondary-700 {
            color: var(--color-secondary-700);
        }

        .text-secondary-900 {
            color: var(--color-secondary-900);
        }

        .text-success {
            color: var(--color-success);
        }

        .text-warning {
            color: var(--color-warning);
        }

        .text-error {
            color: var(--color-error);
        }

        .text-info {
            color: var(--color-info);
        }

        .btn-primary {
            background-color: var(--color-primary-600);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--color-primary-700);
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background-color: var(--color-secondary-100);
            color: var(--color-secondary-700);
            border: 1px solid var(--color-secondary-200);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: var(--color-secondary-200);
            transform: translateY(-1px);
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--color-primary-500) 0%, var(--color-primary-700) 100%);
            position: relative;
        }

        .gradient-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-primary-800) 100%);
            opacity: 0.9;
        }

        .gradient-bg>* {
            position: relative;
            z-index: 1;
        }

        /* Footer specific styles */
        .footer-cta {
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-primary-800) 100%);
            color: white;
        }

        .footer-cta .btn-white {
            background-color: white;
            color: var(--color-primary-600);
            border: 2px solid white;
            transition: all 0.3s ease;
        }

        .footer-cta .btn-white:hover {
            background-color: transparent;
            color: white;
        }

        .footer-cta .btn-outline {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            transition: all 0.3s ease;
        }

        .footer-cta .btn-outline:hover {
            background-color: white;
            color: var(--color-primary-600);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Navigation Link Styles */
        .nav-link {
            @apply px-2 py-2 text-sm font-bold transition-all duration-200;
            color: var(--color-secondary-600);
        }

        .nav-link:hover {
            color: var(--color-primary-600);
            transform: translateY(-1px);
        }

        .nav-active {
            color: var(--color-primary-600) !important;
            border-bottom: 2px solid var(--color-primary-600);
            padding-bottom: 0.25rem;
        }

        /* Mobile Navigation Link Styles */
        .nav-link-mobile {
            @apply block px-3 py-2 text-base font-bold transition-all duration-200;
            color: var(--color-secondary-600);
        }

        .nav-link-mobile:hover {
            color: var(--color-primary-600);
            background-color: var(--color-primary-50);
        }

        .nav-active-mobile {
            color: var(--color-primary-600) !important;
            background-color: var(--color-primary-100);
            border-left: 3px solid var(--color-primary-600);
            padding-left: 0.75rem;
        }
    </style>
</head>

<body class="font-sans antialiased bg-white">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ route('landing.home') }}" class="flex items-center">
                            @php
                                $companyLogo = config('all.company.logo');
                            @endphp
                            @if ($companyLogo)
                                <img src="{{ asset($companyLogo) }}" alt="{{ config('all.company.name') }}"
                                    class="h-10 w-auto">
                            @else
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-700 rounded-lg flex items-center justify-center shadow-md mr-3">
                                        <x-heroicon-o-academic-cap class="w-6 h-6 text-white" />
                                    </div>
                                    <span
                                        class="text-2xl font-bold text-primary-600">{{ config('all.company.name') }}</span>
                                </div>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:block">
                    <div class="ml-8 flex items-center space-x-6">
                        <a href="{{ route('landing.home') }}"
                            class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-blue-600 transition-colors {{ request()->routeIs('landing.home') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : '' }}">
                            Home
                        </a>
                        <a href="{{ route('landing.features') }}"
                            class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-blue-600 transition-colors {{ request()->routeIs('landing.features') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : '' }}">
                            Features
                        </a>
                        <a href="{{ route('landing.pricing') }}"
                            class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-blue-600 transition-colors {{ request()->routeIs('landing.pricing') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : '' }}">
                            Pricing
                        </a>
                        <a href="{{ route('landing.about') }}"
                            class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-blue-600 transition-colors {{ request()->routeIs('landing.about') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : '' }}">
                            About
                        </a>
                        <a href="{{ route('landing.contact') }}"
                            class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-blue-600 transition-colors {{ request()->routeIs('landing.contact') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : '' }}">
                            Contact
                        </a>
                        <a href="{{ route('landing.color-palette') }}"
                            class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-blue-600 transition-colors {{ request()->routeIs('landing.color-palette') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : '' }}">
                            Color Palette
                        </a>
                        <a href="{{ route('landing.multi-tenancy-demo') }}"
                            class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-blue-600 transition-colors {{ request()->routeIs('landing.multi-tenancy-demo') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : '' }}">
                            Multi-Tenancy
                        </a>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('landing.contact') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-secondary-600 bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 transition-colors">
                        Contact Us
                    </a>
                    <a href="{{ route('landing.pricing') }}"
                        class="px-6 py-2 rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                        View Pricing
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-secondary-600 hover:text-primary-600" id="mobile-menu-button">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-100">
                <a href="{{ route('landing.home') }}"
                    class="block px-3 py-2 text-base font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors {{ request()->routeIs('landing.home') ? 'text-blue-600 bg-blue-100 border-l-3 border-blue-600 pl-4' : '' }}">
                    Home
                </a>
                <a href="{{ route('landing.features') }}"
                    class="block px-3 py-2 text-base font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors {{ request()->routeIs('landing.features') ? 'text-blue-600 bg-blue-100 border-l-3 border-blue-600 pl-4' : '' }}">
                    Features
                </a>
                <a href="{{ route('landing.pricing') }}"
                    class="block px-3 py-2 text-base font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors {{ request()->routeIs('landing.pricing') ? 'text-blue-600 bg-blue-100 border-l-3 border-blue-600 pl-4' : '' }}">
                    Pricing
                </a>
                <a href="{{ route('landing.about') }}"
                    class="block px-3 py-2 text-base font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-100 transition-colors {{ request()->routeIs('landing.about') ? 'text-blue-600 bg-blue-100 border-l-3 border-blue-600 pl-4' : '' }}">
                    About
                </a>
                <a href="{{ route('landing.contact') }}"
                    class="block px-3 py-2 text-base font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors {{ request()->routeIs('landing.contact') ? 'text-blue-600 bg-blue-100 border-l-3 border-blue-600 pl-4' : '' }}">
                    Contact
                </a>
                <a href="{{ route('landing.color-palette') }}"
                    class="block px-3 py-2 text-base font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors {{ request()->routeIs('landing.color-palette') ? 'text-blue-600 bg-blue-100 border-l-3 border-blue-600 pl-4' : '' }}">
                    Color Palette
                </a>
                <a href="{{ route('landing.multi-tenancy-demo') }}"
                    class="block px-3 py-2 text-base font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors {{ request()->routeIs('landing.multi-tenancy-demo') ? 'text-blue-600 bg-blue-100 border-l-3 border-blue-600 pl-4' : '' }}">
                    Multi-Tenancy
                </a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200 px-2 sm:px-3 space-y-3">
                <a href="{{ route('landing.contact') }}"
                    class="block w-full text-center px-4 py-3 text-base font-medium text-secondary-600 bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 transition-colors rounded-lg">
                    Contact Us
                </a>
                <a href="{{ route('landing.pricing') }}"
                    class="block w-full text-center px-6 py-3 text-base font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors rounded-lg">
                    View Pricing
                </a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-secondary-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    @php
                        $companyLogo = config('all.company.logo');
                    @endphp
                    @if ($companyLogo)
                        <img src="{{ asset($companyLogo) }}" alt="{{ config('all.company.name') }}"
                            class="h-12 w-auto mb-4">
                    @else
                        <div class="flex items-center mb-4">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-700 rounded-lg flex items-center justify-center shadow-md mr-3">
                                <x-heroicon-o-academic-cap class="w-6 h-6 text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-white">{{ config('all.company.name') }}</h3>
                        </div>
                    @endif
                    @if ($companyLogo)
                        <h3 class="text-xl font-bold text-white mb-4">{{ config('all.company.name') }}</h3>
                    @endif
                    <p class="text-secondary-300 mb-4">{{ config('all.company.tagline') }}</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-secondary-300 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#" class="text-secondary-300 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                            </svg>
                        </a>
                        <a href="#" class="text-secondary-300 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('landing.features') }}"
                                class="text-secondary-300 hover:text-white transition-colors">Features</a></li>
                        <li><a href="{{ route('landing.pricing') }}"
                                class="text-secondary-300 hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="{{ route('landing.about') }}"
                                class="text-secondary-300 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ route('landing.contact') }}"
                                class="text-secondary-300 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <div class="space-y-2 text-secondary-300">
                        <p>{{ config('all.company.email') }}</p>
                        <p>{{ config('all.company.phone') }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-secondary-700 mt-8 pt-8 text-center text-secondary-300">
                <p>&copy; {{ date('Y') }} {{ config('all.company.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu JavaScript -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>

</html>
