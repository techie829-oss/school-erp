@extends('school.layout')

@section('title', 'Facilities')

@section('content')
@php
    $tenantId = $tenant['id'] ?? null;
    if (!$tenantId && isset($tenant) && is_object($tenant)) {
        $tenantId = $tenant->id ?? null;
    }
@endphp
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-green-50 via-white to-teal-50 py-24 lg:py-32 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23000000\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <div class="mb-6">
                <span class="inline-block px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold mb-4">
                    World-Class Infrastructure
                </span>
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                {{ cms_field('facilities', 'hero_heading', 'Our Facilities', $tenantId) }}
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                {{ cms_field('facilities', 'hero_description', 'Explore our state-of-the-art facilities designed to enhance learning, creativity, and overall student development.', $tenantId) }}
            </p>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-green-200 rounded-full opacity-20 blur-xl"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-teal-300 rounded-full opacity-20 blur-2xl"></div>
</section>

<!-- Facilities Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">What We Offer</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('facilities', 'facilities_title', 'Campus Facilities', $tenantId) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('facilities', 'facilities_description', 'Modern infrastructure supporting excellence in education', $tenantId) }}</p>
        </div>

        @php
            $facilityCards = cms_components('facilities', 'facility_cards', $tenantId);
        @endphp
        @if(count($facilityCards) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($facilityCards as $card)
            @php
                $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($card['color'] ?? 'blue');
                // Map color to gradient classes for facility cards
                $gradientMap = [
                    'blue' => 'from-blue-400 to-blue-600',
                    'purple' => 'from-purple-400 to-purple-600',
                    'indigo' => 'from-indigo-400 to-indigo-600',
                    'green' => 'from-green-400 to-green-600',
                    'red' => 'from-red-400 to-red-600',
                    'orange' => 'from-yellow-400 to-orange-600',
                    'pink' => 'from-pink-400 to-pink-600',
                    'teal' => 'from-teal-400 to-teal-600',
                    'cyan' => 'from-cyan-400 to-cyan-600',
                ];
                $gradientClass = $gradientMap[$card['color'] ?? 'blue'] ?? 'from-blue-400 to-blue-600';
            @endphp
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br {{ $gradientClass }} flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $card['title'] ?? '' }}</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $card['description'] ?? '' }}
                    </p>
                    @if(isset($card['features']) && is_array($card['features']) && count($card['features']) > 0)
                    <ul class="space-y-2 text-gray-700">
                        @foreach($card['features'] as $feature)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 {{ $colorClasses['text'] }} mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @endforeach
                </div>
        @else
        <!-- Default fallback -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Modern Library</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">Extensive collection of books and resources.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Additional Facilities -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">More Facilities</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('facilities', 'amenities_title', 'Additional Amenities', $tenantId) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('facilities', 'amenities_description', 'Supporting facilities that enhance the learning experience', $tenantId) }}</p>
        </div>

        @php
            $amenityCards = cms_components('facilities', 'amenity_cards', $tenantId);
        @endphp
        @if(count($amenityCards) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($amenityCards as $amenity)
            @php
                $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($amenity['color'] ?? 'blue');
            @endphp
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow text-center border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br {{ $colorClasses['bg_light'] }} {{ $colorClasses['bg_light_2'] }} rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 {{ $colorClasses['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $amenity['title'] ?? '' }}</h3>
                <p class="text-gray-600 text-sm">{{ $amenity['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
        @else
        <!-- Default fallback -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow text-center border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Smart Classrooms</h3>
                <p class="text-gray-600 text-sm">Interactive whiteboards and digital learning tools</p>
            </div>
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
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ cms_field('facilities', 'cta_title', 'Experience Our Facilities', $tenantId) }}</h2>
        <p class="text-xl md:text-2xl text-primary-100 mb-10 max-w-2xl mx-auto">{{ cms_field('facilities', 'cta_description', 'Schedule a campus tour to see our world-class facilities in person', $tenantId) }}</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/contact') }}" class="group inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                {{ cms_field('facilities', 'cta_button_text', 'Schedule a Tour', $tenantId) }}
                <svg class="ml-2 w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
            <a href="{{ url('/admission') }}" class="inline-flex items-center px-8 py-4 bg-transparent text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-300 border-2 border-white shadow-lg hover:shadow-xl">
                Apply Now
            </a>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full opacity-10 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full opacity-10 blur-3xl"></div>
</section>
@endsection
