@extends('school.layout')

@section('title', 'Programs')
@section('description', 'Discover the comprehensive educational programs we offer to nurture your child\'s potential.')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-blue-50 via-white to-purple-50 py-24 lg:py-32 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23000000\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <div class="mb-6">
                <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-4">
                    Educational Excellence
                </span>
            </div>
            @php
                $tenantId = $tenant['id'] ?? null;
                if (!$tenantId && isset($tenant) && is_object($tenant)) {
                    $tenantId = $tenant->id ?? null;
                }
            @endphp
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                {{ cms_field('programs', 'hero_heading', 'Our Programs', $tenantId) }}
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                {{ cms_field('programs', 'hero_description', 'Discover comprehensive educational programs designed to nurture your child\'s potential and prepare them for a successful future.', $tenantId) }}
            </p>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-blue-200 rounded-full opacity-20 blur-xl"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-purple-300 rounded-full opacity-20 blur-2xl"></div>
</section>

<!-- Programs Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">What We Offer</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('programs', 'programs_title', 'Academic Programs', $tenantId) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('programs', 'programs_description', 'Comprehensive curriculum designed to meet diverse learning needs', $tenantId) }}</p>
        </div>

        @php
            $programCards = cms_components('programs', 'program_cards', $tenantId);
        @endphp
        @if(count($programCards) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($programCards as $card)
            @php
                $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($card['color'] ?? 'primary');
            @endphp
            <div class="bg-gradient-to-br {{ $colorClasses['bg_gradient_from'] }} {{ $colorClasses['bg_gradient_to'] }} rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border {{ $colorClasses['border'] }}">
                <div class="w-16 h-16 {{ $colorClasses['bg'] }} rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $card['title'] ?? '' }}</h3>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    {{ $card['description'] ?? '' }}
                </p>
                @if(isset($card['features']) && is_array($card['features']) && count($card['features']) > 0)
                <ul class="space-y-2 mb-6">
                    @foreach($card['features'] as $feature)
                    <li class="flex items-start text-gray-700">
                        <svg class="w-5 h-5 {{ $colorClasses['text'] }} mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
                <a href="{{ url($card['url'] ?? '/admission') }}" class="inline-flex items-center {{ $colorClasses['text'] }} font-semibold hover:{{ $colorClasses['hover_bg'] }}">
                    Learn More
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <!-- Default fallback -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-blue-200">
                <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Elementary Education</h3>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Foundational learning program for grades 1-5, focusing on building strong academic fundamentals and developing critical thinking skills.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start text-gray-700">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Age-appropriate curriculum</span>
                    </li>
                </ul>
                <a href="{{ url('/admission') }}" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700">
                    Learn More
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Program Features -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">Why Choose Us</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('programs', 'highlights_title', 'Program Highlights', $tenantId) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('programs', 'highlights_description', 'What makes our programs stand out', $tenantId) }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-primary-100 to-primary-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ cms_field('programs', 'highlight1_title', 'Expert Faculty', $tenantId) }}</h3>
                <p class="text-gray-600">{{ cms_field('programs', 'highlight1_description', 'Highly qualified and experienced teachers dedicated to student success.', $tenantId) }}</p>
            </div>

            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ cms_field('programs', 'highlight2_title', 'Modern Facilities', $tenantId) }}</h3>
                <p class="text-gray-600">{{ cms_field('programs', 'highlight2_description', 'State-of-the-art classrooms, labs, and learning spaces.', $tenantId) }}</p>
            </div>

            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ cms_field('programs', 'highlight3_title', 'Flexible Scheduling', $tenantId) }}</h3>
                <p class="text-gray-600">{{ cms_field('programs', 'highlight3_description', 'Programs designed to accommodate diverse learning needs.', $tenantId) }}</p>
            </div>

            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ cms_field('programs', 'highlight4_title', 'Certified Programs', $tenantId) }}</h3>
                <p class="text-gray-600">{{ cms_field('programs', 'highlight4_description', 'Accredited curriculum meeting national education standards.', $tenantId) }}</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary-600 via-primary-700 to-primary-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Ready to Get Started?</h2>
        <p class="text-xl md:text-2xl text-primary-100 mb-10 max-w-2xl mx-auto">Join our community and give your child the best educational experience</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/admission') }}" class="group inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                Apply for Admission
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
