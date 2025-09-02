@extends('school.layout')

@section('title', 'About Us')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-50 to-primary-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                About <span class="text-primary-600">{{ $tenant['name'] ?? 'Our School' }}</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                {{ $tenant['description'] ?? 'Excellence in Education' }} - Learn about our mission, values, and commitment to student success.
            </p>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Story</h2>
                <p class="text-lg text-gray-600 mb-6">
                    Founded with a vision to provide exceptional education, {{ $tenant['name'] ?? 'Our School' }} has been at the forefront of academic excellence for over two decades. We believe in nurturing not just academic skills, but also character, creativity, and leadership qualities.
                </p>
                <p class="text-lg text-gray-600 mb-6">
                    Our commitment to personalized learning and holistic development has made us a preferred choice for parents who want the best for their children's future.
                </p>
                <div class="grid grid-cols-2 gap-6 mt-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600">{{ $tenant['student_count'] ?? '500+' }}</div>
                        <div class="text-gray-600">Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600">25+</div>
                        <div class="text-gray-600">Years</div>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">School Information</h3>
                <div class="space-y-4">
                    <div>
                        <strong class="text-gray-900">Name:</strong>
                        <span class="text-gray-600 ml-2">{{ $tenant['name'] ?? 'School Name' }}</span>
                    </div>
                    <div>
                        <strong class="text-gray-900">Location:</strong>
                        <span class="text-gray-600 ml-2">{{ $tenant['location'] ?? 'Location' }}</span>
                    </div>
                    <div>
                        <strong class="text-gray-900">Database Strategy:</strong>
                        <span class="text-gray-600 ml-2">{{ ucfirst($tenant['database_strategy'] ?? 'shared') }}</span>
                    </div>
                    <div>
                        <strong class="text-gray-900">Status:</strong>
                        <span class="text-gray-600 ml-2">{{ ucfirst($tenant['status'] ?? 'active') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Values -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Mission & Values</h2>
            <p class="text-xl text-gray-600">Guiding principles that shape our educational approach</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Innovation</h3>
                <p class="text-gray-600">Embracing new technologies and teaching methodologies to enhance learning outcomes.</p>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Community</h3>
                <p class="text-gray-600">Building strong partnerships between students, parents, teachers, and the community.</p>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Excellence</h3>
                <p class="text-gray-600">Striving for the highest standards in academics, character, and personal development.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-primary-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Join Our School Community</h2>
        <p class="text-xl text-primary-100 mb-8">Be part of an institution that values excellence and innovation</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('tenant.admission', ['tenant' => $tenantSubdomain]) }}" class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                Apply Now
            </a>
            <a href="{{ route('tenant.contact', ['tenant' => $tenantSubdomain]) }}" class="inline-flex items-center px-8 py-4 bg-transparent text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors border-2 border-white">
                Contact Us
            </a>
        </div>
    </div>
</section>
@endsection
