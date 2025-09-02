@extends('school.layout')

@section('title', 'Welcome')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-50 to-primary-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                Welcome to <span class="text-primary-600">{{ $tenant['name'] ?? 'Our School' }}</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                {{ $tenant['description'] ?? 'Excellence in Education' }} - Empowering students to achieve their full potential through innovative learning and character development.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('tenant.admission', ['tenant' => $tenantSubdomain]) }}" class="inline-flex items-center px-8 py-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors shadow-lg hover:shadow-xl">
                    Apply for Admission
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                <a href="{{ route('tenant.about', ['tenant' => $tenantSubdomain]) }}" class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-50 transition-colors border-2 border-primary-600">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</section>

<!-- School Stats -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div class="p-6">
                <div class="text-4xl font-bold text-primary-600 mb-2">{{ $tenant['student_count'] ?? '500+' }}</div>
                <div class="text-gray-600">Students</div>
            </div>
            <div class="p-6">
                <div class="text-4xl font-bold text-primary-600 mb-2">25+</div>
                <div class="text-gray-600">Years Experience</div>
            </div>
            <div class="p-6">
                <div class="text-4xl font-bold text-primary-600 mb-2">95%</div>
                <div class="text-gray-600">Success Rate</div>
            </div>
            <div class="p-6">
                <div class="text-4xl font-bold text-primary-600 mb-2">{{ ucfirst($tenant['database_strategy'] ?? 'shared') }}</div>
                <div class="text-gray-600">Database Strategy</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose {{ $tenant['name'] ?? 'Our School' }}?</h2>
            <p class="text-xl text-gray-600">Discover what makes us the preferred choice for quality education</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-primary-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Academic Excellence</h3>
                <p class="text-gray-600">Comprehensive curriculum designed to challenge and inspire students to reach their highest potential.</p>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-primary-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Experienced Faculty</h3>
                <p class="text-gray-600">Our dedicated teachers bring years of experience and passion for education to every classroom.</p>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-primary-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Modern Facilities</h3>
                <p class="text-gray-600">State-of-the-art classrooms, laboratories, and recreational facilities to support holistic development.</p>
            </div>
        </div>
    </div>
</section>

<!-- Programs Preview -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Programs</h2>
            <p class="text-xl text-gray-600">Comprehensive educational programs for all age groups</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 p-6 rounded-lg text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Primary School</h3>
                <p class="text-gray-600 text-sm">Grades 1-5</p>
            </div>

            <div class="bg-gradient-to-br from-secondary-50 to-secondary-100 p-6 rounded-lg text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Middle School</h3>
                <p class="text-gray-600 text-sm">Grades 6-8</p>
            </div>

            <div class="bg-gradient-to-br from-accent-50 to-accent-100 p-6 rounded-lg text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">High School</h3>
                <p class="text-gray-600 text-sm">Grades 9-12</p>
            </div>

            <div class="bg-gradient-to-br from-primary-50 to-primary-100 p-6 rounded-lg text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Special Programs</h3>
                <p class="text-gray-600 text-sm">Arts, Sports, STEM</p>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('tenant.programs', ['tenant' => $tenantSubdomain]) }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                View All Programs
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-primary-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to Join {{ $tenant['name'] ?? 'Our School' }}?</h2>
        <p class="text-xl text-primary-100 mb-8">Take the first step towards your child's bright future</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('tenant.admission', ['tenant' => $tenantSubdomain]) }}" class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                Start Application
            </a>
            <a href="{{ route('tenant.contact', ['tenant' => $tenantSubdomain]) }}" class="inline-flex items-center px-8 py-4 bg-transparent text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors border-2 border-white">
                Contact Us
            </a>
        </div>
    </div>
</section>
@endsection
