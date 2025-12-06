@extends('school.layout')

@section('title', 'Welcome')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-50 via-white to-primary-100 py-24 lg:py-32 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23000000\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <div class="mb-6">
                <span class="inline-block px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">
                    {{ cms_field('', 'hero_badge', null, $tenant['id'] ?? null) }}
                </span>
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                {{ cms_field('', 'hero_heading', null, $tenant['id'] ?? null) }}
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                {{ cms_field('', 'hero_description', null, $tenant['id'] ?? null) }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ cms_field('', 'hero_button_url', null, $tenant['id'] ?? null) }}" class="group inline-flex items-center px-8 py-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    {{ cms_field('', 'hero_button_text', null, $tenant['id'] ?? null) }}
                    <svg class="ml-2 w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                <a href="{{ url('/about') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-50 transition-all duration-300 border-2 border-primary-600 shadow-md hover:shadow-lg">
                    Learn More
                </a>
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-primary-200 rounded-full opacity-20 blur-xl"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-primary-300 rounded-full opacity-20 blur-2xl"></div>
</section>

<!-- School Stats -->
<section class="py-16 bg-gradient-to-br from-white to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            <div class="bg-white p-6 md:p-8 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center border border-gray-100">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-primary-600 mb-2">{{ cms_field('', 'stats_students', null, $tenant['id'] ?? null) }}</div>
                <div class="text-gray-600 font-medium">Active Students</div>
            </div>
            <div class="bg-white p-6 md:p-8 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center border border-gray-100">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-green-600 mb-2">25+</div>
                <div class="text-gray-600 font-medium">Years Experience</div>
            </div>
            <div class="bg-white p-6 md:p-8 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center border border-gray-100">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">95%</div>
                <div class="text-gray-600 font-medium">Success Rate</div>
            </div>
            <div class="bg-white p-6 md:p-8 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center border border-gray-100">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-purple-600 mb-2">{{ ucfirst($tenant['type'] ?? 'School') }}</div>
                <div class="text-gray-600 font-medium">Institution Type</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">Why Choose Us</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('', 'features_title', null, $tenant['id'] ?? null) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('', 'features_description', null, $tenant['id'] ?? null) }}</p>
        </div>

        @php
            $tenantId = $tenant['id'] ?? null;
            if (!$tenantId && isset($tenant) && is_object($tenant)) {
                $tenantId = $tenant->id ?? null;
            }
            $features = cms_components('', 'features', $tenantId);
        @endphp
        @if(count($features) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10">
            @foreach($features as $feature)
            @php
                $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($feature['color'] ?? 'primary');
            @endphp
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br {{ $colorClasses['bg_light'] }} {{ $colorClasses['bg_light_2'] }} rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 {{ $colorClasses['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $feature['title'] ?? '' }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $feature['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
        @else
        <!-- Default fallback if no components -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10">
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Academic Excellence</h3>
                <p class="text-gray-600 leading-relaxed">Comprehensive curriculum designed to challenge and inspire students to reach their highest potential through innovative teaching methods.</p>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Programs Preview -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">Our Offerings</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Our Programs</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Comprehensive educational programs designed for all age groups and learning styles</p>
        </div>

        @php
            $tenantId = $tenant['id'] ?? null;
            if (!$tenantId && isset($tenant) && is_object($tenant)) {
                $tenantId = $tenant->id ?? null;
            }
            $programs = cms_components('', 'programs', $tenantId);
        @endphp
        @if(count($programs) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($programs as $program)
            @php
                $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($program['color'] ?? 'primary');
            @endphp
            <div class="group bg-gradient-to-br {{ $colorClasses['bg_gradient_from'] }} {{ $colorClasses['bg_gradient_via'] }} {{ $colorClasses['bg_gradient_to'] }} p-8 rounded-xl text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border {{ $colorClasses['border'] }}">
                <div class="w-16 h-16 {{ $colorClasses['bg'] }} rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $program['title'] ?? '' }}</h3>
                @if($program['subtitle'] ?? null)
                <p class="text-gray-600 mb-4">{{ $program['subtitle'] }}</p>
                @endif
                @if($program['description'] ?? null)
                <p class="text-sm text-gray-500">{{ $program['description'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <!-- Default fallback -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="group bg-gradient-to-br from-primary-50 via-primary-100 to-primary-50 p-8 rounded-xl text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-primary-200">
                <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Primary School</h3>
                <p class="text-gray-600 mb-4">Grades 1-5</p>
                <p class="text-sm text-gray-500">Foundation building programs</p>
            </div>
        </div>
        @endif

        <div class="text-center mt-12">
            <a href="{{ url('/programs') }}" class="group inline-flex items-center px-8 py-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                View All Programs
                <svg class="ml-2 w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">What Parents Say</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Testimonials</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Hear from our satisfied parents and students</p>
        </div>

        @php
            $testimonials = cms_components('', 'testimonials', $tenant['id'] ?? null);
        @endphp
        @if(count($testimonials) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
            @php
                $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($testimonial['color'] ?? 'primary');
            @endphp
            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                @if($testimonial['rating'] ?? null)
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        @for($i = 0; $i < ($testimonial['rating'] ?? 5); $i++)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                </div>
                @endif
                <p class="text-gray-600 mb-6 leading-relaxed">"{{ $testimonial['description'] ?? '' }}"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 {{ $colorClasses['bg_light'] }} rounded-full flex items-center justify-center mr-4">
                        <span class="{{ $colorClasses['text'] }} font-semibold">{{ $testimonial['author_initials'] ?? substr($testimonial['author_name'] ?? 'A', 0, 2) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $testimonial['author_name'] ?? 'Anonymous' }}</p>
                        @if($testimonial['author_role'] ?? null)
                        <p class="text-sm text-gray-500">{{ $testimonial['author_role'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
                    </div>
        @else
        <!-- Default fallback -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                </div>
                <p class="text-gray-600 mb-6 leading-relaxed">"The best decision we made for our child's education."</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-primary-600 font-semibold">SM</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Sarah Mitchell</p>
                        <p class="text-sm text-gray-500">Parent</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Quick Links Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">Quick Access</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Quick Links</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Easy access to important information and resources</p>
        </div>

        @php
            $tenantId = $tenant['id'] ?? null;
            if (!$tenantId && isset($tenant) && is_object($tenant)) {
                $tenantId = $tenant->id ?? null;
            }
            $quickLinks = cms_components('', 'quick_links', $tenantId);
        @endphp
        @if(count($quickLinks) > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($quickLinks as $link)
            @php
                $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($link['color'] ?? 'primary');
            @endphp
            <a href="{{ $link['url'] ?? '#' }}" class="group bg-gradient-to-br {{ $colorClasses['bg_gradient_from'] }} {{ $colorClasses['bg_gradient_to'] }} p-6 rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 text-center border {{ $colorClasses['border'] }}">
                <div class="w-16 h-16 {{ $colorClasses['bg'] }} rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ $link['title'] ?? '' }}</h3>
                @if($link['description'] ?? null)
                <p class="text-sm text-gray-600">{{ $link['description'] }}</p>
                @endif
            </a>
            @endforeach
        </div>
        @else
        <!-- Default fallback -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="{{ url('/admission') }}" class="group bg-gradient-to-br from-primary-50 to-primary-100 p-6 rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 text-center border border-primary-200">
                <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Admission</h3>
                <p class="text-sm text-gray-600">Apply now</p>
            </a>
        </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary-600 via-primary-700 to-primary-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Ready to Join {{ $tenant['name'] ?? 'Our School' }}?</h2>
        <p class="text-xl md:text-2xl text-primary-100 mb-10 max-w-2xl mx-auto">Take the first step towards your child's bright future and academic excellence</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/admission') }}" class="group inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                Start Application
                <svg class="ml-2 w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
            <a href="{{ url('/contact') }}" class="inline-flex items-center px-8 py-4 bg-transparent text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-300 border-2 border-white shadow-lg hover:shadow-xl">
                Contact Us
            </a>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full opacity-10 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full opacity-10 blur-3xl"></div>
</section>
@endsection
