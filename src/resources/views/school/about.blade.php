@extends('school.layout')

@section('title', 'About Us')

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
                    Learn More About Us
                </span>
            </div>
            @php
                $tenantId = $tenant['id'] ?? null;
                if (!$tenantId && isset($tenant) && is_object($tenant)) {
                    $tenantId = $tenant->id ?? null;
                }
            @endphp
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                {{ cms_field('about', 'hero_heading', 'About Our School', $tenantId) }}
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                {{ cms_field('about', 'hero_description', 'Learn about our mission, values, and commitment to student success.', $tenantId) }}
            </p>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-primary-200 rounded-full opacity-20 blur-xl"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-primary-300 rounded-full opacity-20 blur-2xl"></div>
</section>

<!-- About Content -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div>
                <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">Our Story</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">{{ cms_field('about', 'story_title', 'Building Excellence Since Day One', $tenantId) }}</h2>
                <div class="text-lg text-gray-600 mb-6 leading-relaxed">
                    {!! nl2br(e(cms_field('about', 'story_content', 'Founded with a vision to provide exceptional education, Our School has been at the forefront of academic excellence for over two decades. We believe in nurturing not just academic skills, but also character, creativity, and leadership qualities. Our commitment to personalized learning and holistic development has made us a preferred choice for parents who want the best for their children\'s future.', $tenantId))) !!}
                </div>
                <div class="grid grid-cols-2 gap-6 mt-8">
                    <div class="bg-gradient-to-br from-primary-50 to-primary-100 p-6 rounded-xl text-center border border-primary-200">
                        <div class="text-4xl font-bold text-primary-600 mb-2">{{ cms_field('about', 'stat1_value', '1000+', $tenantId) }}</div>
                        <div class="text-gray-600 font-medium">{{ cms_field('about', 'stat1_label', 'Happy Students', $tenantId) }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl text-center border border-green-200">
                        <div class="text-4xl font-bold text-green-600 mb-2">{{ cms_field('about', 'stat2_value', '50+', $tenantId) }}</div>
                        <div class="text-gray-600 font-medium">{{ cms_field('about', 'stat2_label', 'Expert Teachers', $tenantId) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-primary-100 via-primary-50 to-primary-200 rounded-2xl p-8 lg:p-10 shadow-xl border border-primary-200">
                <h3 class="text-3xl font-bold text-gray-900 mb-6">{{ cms_field('about', 'info_title', 'Why Choose Us', $tenantId) }}</h3>
                <div class="space-y-5">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <strong class="text-gray-900 font-semibold block mb-1">{{ cms_field('about', 'info_item1_label', 'Accredited Institution', $tenantId) }}</strong>
                            <span class="text-gray-700">{{ cms_field('about', 'info_item1_value', 'Recognized by Education Board', $tenantId) }}</span>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <strong class="text-gray-900 font-semibold block mb-1">{{ cms_field('about', 'info_item2_label', 'Modern Facilities', $tenantId) }}</strong>
                            <span class="text-gray-700">{{ cms_field('about', 'info_item2_value', 'State-of-the-art Infrastructure', $tenantId) }}</span>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <strong class="text-gray-900 font-semibold block mb-1">{{ cms_field('about', 'info_item3_label', 'Experienced Faculty', $tenantId) }}</strong>
                            <span class="text-gray-700">{{ cms_field('about', 'info_item3_value', 'Qualified & Dedicated Teachers', $tenantId) }}</span>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <strong class="text-gray-900 font-semibold block mb-1">{{ cms_field('about', 'info_item4_label', 'Holistic Development', $tenantId) }}</strong>
                            <span class="text-gray-700">{{ cms_field('about', 'info_item4_value', 'Academic & Character Building', $tenantId) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Values -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">Our Foundation</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('about', 'mission_title', 'Our Mission & Values', $tenantId) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('about', 'mission_description', 'Guiding principles that shape our educational approach and define who we are', $tenantId) }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10">
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 text-center border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ cms_field('about', 'value1_title', 'Innovation', $tenantId) }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ cms_field('about', 'value1_description', 'Embracing new technologies and teaching methodologies to enhance learning outcomes and prepare students for the future.', $tenantId) }}</p>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 text-center border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ cms_field('about', 'value2_title', 'Community', $tenantId) }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ cms_field('about', 'value2_description', 'Building strong partnerships between students, parents, teachers, and the community to create a supportive learning environment.', $tenantId) }}</p>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 text-center border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ cms_field('about', 'value3_title', 'Excellence', $tenantId) }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ cms_field('about', 'value3_description', 'Striving for the highest standards in academics, character, and personal development to ensure student success.', $tenantId) }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Vision Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="order-2 lg:order-1">
                <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl p-10 lg:p-12 text-white shadow-2xl">
                    <span class="inline-block px-4 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold mb-6">Our Vision</span>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">{{ cms_field('about', 'vision_title', 'Shaping Tomorrow\'s Leaders', $tenantId) }}</h2>
                    <div class="text-lg text-primary-100 leading-relaxed">
                        {!! nl2br(e(cms_field('about', 'vision_content', 'To be recognized as a premier educational institution that empowers students to become confident, compassionate, and capable leaders who make a positive impact on society. We envision a school where every student discovers their unique potential and is equipped with the knowledge, skills, and values needed to thrive in an ever-changing world.', $tenantId))) !!}
                    </div>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">What We Stand For</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">{{ cms_field('about', 'principles_title', 'Our Core Principles', $tenantId) }}</h2>
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ cms_field('about', 'principle1_title', 'Holistic Development', $tenantId) }}</h3>
                            <p class="text-gray-600">{{ cms_field('about', 'principle1_description', 'Nurturing academic, social, emotional, and physical growth in every student.', $tenantId) }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ cms_field('about', 'principle2_title', 'Individualized Learning', $tenantId) }}</h3>
                            <p class="text-gray-600">{{ cms_field('about', 'principle2_description', 'Recognizing and supporting each student\'s unique learning style and pace.', $tenantId) }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ cms_field('about', 'principle3_title', 'Character Building', $tenantId) }}</h3>
                            <p class="text-gray-600">{{ cms_field('about', 'principle3_description', 'Instilling integrity, respect, responsibility, and ethical values.', $tenantId) }}</p>
                        </div>
                    </div>
                </div>
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
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Join Our School Community</h2>
        <p class="text-xl md:text-2xl text-primary-100 mb-10 max-w-2xl mx-auto">Be part of an institution that values excellence, innovation, and student success</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/admission') }}" class="group inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                Apply Now
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
