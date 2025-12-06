@extends('school.layout')

@section('title', 'Contact')

@section('content')
@php
    $tenantId = $tenant['id'] ?? null;
    if (!$tenantId && isset($tenant) && is_object($tenant)) {
        $tenantId = $tenant->id ?? null;
    }
@endphp
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-teal-50 via-white to-cyan-50 py-24 lg:py-32 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23000000\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <div class="mb-6">
                <span class="inline-block px-4 py-2 bg-teal-100 text-teal-700 rounded-full text-sm font-semibold mb-4">
                    {{ cms_field('contact', 'hero_badge', 'We\'re Here to Help', $tenantId) }}
                </span>
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                @php
                    $heroHeading = cms_field('contact', 'hero_heading', 'Contact Us', $tenantId);
                    // Split "Contact Us" to highlight "Us" if it contains "Contact"
                    if (strpos($heroHeading, 'Contact') !== false) {
                        $parts = explode('Contact', $heroHeading, 2);
                        echo 'Contact <span class="text-primary-600 relative inline-block">' . trim($parts[1] ?? 'Us') . '<span class="absolute bottom-0 left-0 right-0 h-3 bg-primary-200 opacity-30 -z-10 transform -rotate-1"></span></span>';
                    } else {
                        echo $heroHeading;
                    }
                @endphp
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                {{ cms_field('contact', 'hero_description', 'Get in touch with us for any questions, inquiries, or to schedule a campus visit.', $tenantId) }}
            </p>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-teal-200 rounded-full opacity-20 blur-xl"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-cyan-300 rounded-full opacity-20 blur-2xl"></div>
</section>

<!-- Contact Form & Info -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div>
                <div class="mb-8">
                    <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">{{ cms_field('contact', 'form_badge', 'Send Us a Message', $tenantId) }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ cms_field('contact', 'form_title', 'Get In Touch', $tenantId) }}</h2>
                    <p class="text-gray-600">{{ cms_field('contact', 'form_description', 'Fill out the form below and we\'ll get back to you as soon as possible.', $tenantId) }}</p>
                </div>

                <form class="space-y-6" action="#" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Subject *</label>
                        <select id="subject" name="subject" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                            <option value="">Select a subject</option>
                            <option value="admission">Admission Inquiry</option>
                            <option value="general">General Information</option>
                            <option value="visit">Campus Tour</option>
                            <option value="academic">Academic Programs</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Message *</label>
                        <textarea id="message" name="message" rows="6" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white font-semibold py-4 px-6 rounded-lg hover:bg-primary-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        {{ cms_field('contact', 'form_button_text', 'Send Message', $tenantId) }}
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div>
                <div class="mb-8">
                    <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">{{ cms_field('contact', 'contact_info_badge', 'Contact Information', $tenantId) }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ cms_field('contact', 'contact_info_title', 'Visit or Reach Us', $tenantId) }}</h2>
                    <p class="text-gray-600">{{ cms_field('contact', 'contact_info_description', 'We\'re here to answer your questions and help you learn more about our school.', $tenantId) }}</p>
                </div>

                <div class="space-y-6 mb-8">
                    <!-- Address -->
                    <div class="flex items-start bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Address</h3>
                            <p class="text-gray-700">
                                {{ cms_field('contact', 'address', '123 School Street, Education City, State 12345', $tenantId) }}
                            </p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="flex items-start bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Phone</h3>
                            <p class="text-gray-700">
                                @php
                                    $phone = cms_field('contact', 'phone', '+1 (234) 567-890', $tenantId);
                                @endphp
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="hover:text-primary-600 transition-colors">
                                    {{ $phone }}
                                </a>
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Email</h3>
                            <p class="text-gray-700">
                                @php
                                    $email = cms_field('contact', 'email', 'info@school.com', $tenantId);
                                @endphp
                                <a href="mailto:{{ $email }}" class="hover:text-primary-600 transition-colors">
                                    {{ $email }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Office Hours -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ cms_field('contact', 'office_hours_title', 'Office Hours', $tenantId) }}
                    </h3>
                    <div class="space-y-2 text-gray-700">
                        <div>
                            <span class="font-medium">{{ cms_field('contact', 'office_hours_weekdays', 'Monday - Friday: 8:00 AM - 5:00 PM', $tenantId) }}</span>
                        </div>
                        <div>
                            <span class="font-medium">{{ cms_field('contact', 'office_hours_saturday', 'Saturday: 9:00 AM - 1:00 PM', $tenantId) }}</span>
                        </div>
                        <div>
                            <span class="font-medium">{{ cms_field('contact', 'office_hours_sunday', 'Sunday: Closed', $tenantId) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">{{ cms_field('contact', 'map_badge', 'Find Us', $tenantId) }}</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('contact', 'map_title', 'Location', $tenantId) }}</h2>
            <p class="text-xl text-gray-600">{{ cms_field('contact', 'map_description', 'Visit our campus or get directions', $tenantId) }}</p>
        </div>

        @php
            $mapEmbedUrl = cms_field('contact', 'map_embed_url', '', $tenantId);
        @endphp
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            @if(!empty($mapEmbedUrl))
            <div class="h-96 w-full">
                <iframe
                    src="{{ $mapEmbedUrl }}"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    class="w-full h-full">
                </iframe>
            </div>
            @else
            <div class="h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-600 font-medium">Map integration can be added here</p>
                    <p class="text-gray-500 text-sm mt-2">{{ cms_field('contact', 'address', '123 School Street, Education City, State 12345', $tenantId) }}</p>
                    <p class="text-gray-400 text-xs mt-4">Add a Google Maps embed URL in the CMS to display the map</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Social Media & Quick Links -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">{{ cms_field('contact', 'social_badge', 'Connect With Us', $tenantId) }}</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('contact', 'social_title', 'Follow Our Journey', $tenantId) }}</h2>
            <p class="text-xl text-gray-600">{{ cms_field('contact', 'social_description', 'Stay connected through our social media channels', $tenantId) }}</p>
        </div>

        <div class="flex flex-wrap justify-center gap-4 mb-12">
            @if(!empty($cmsSocialMedia['facebook']))
            <a href="{{ $cmsSocialMedia['facebook'] }}" target="_blank" rel="noopener noreferrer" class="group w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-700 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </a>
            @endif

            @if(!empty($cmsSocialMedia['twitter']))
            <a href="{{ $cmsSocialMedia['twitter'] }}" target="_blank" rel="noopener noreferrer" class="group w-16 h-16 bg-sky-500 rounded-xl flex items-center justify-center hover:bg-sky-600 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
            </a>
            @endif

            @if(!empty($cmsSocialMedia['instagram']))
            <a href="{{ $cmsSocialMedia['instagram'] }}" target="_blank" rel="noopener noreferrer" class="group w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center hover:from-purple-600 hover:to-pink-600 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
            </a>
            @endif

            @if(!empty($cmsSocialMedia['linkedin']))
            <a href="{{ $cmsSocialMedia['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="group w-16 h-16 bg-blue-700 rounded-xl flex items-center justify-center hover:bg-blue-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
            </a>
            @endif

            @if(!empty($cmsSocialMedia['youtube']))
            <a href="{{ $cmsSocialMedia['youtube'] }}" target="_blank" rel="noopener noreferrer" class="group w-16 h-16 bg-red-600 rounded-xl flex items-center justify-center hover:bg-red-700 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
            </a>
            @endif
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ url('/admission') }}" class="group bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-6 border border-primary-200 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary-600 transition-colors">Admission</h3>
                </div>
                <p class="text-gray-600 text-sm">Learn about our admission process</p>
            </a>

            <a href="{{ url('/programs') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Programs</h3>
                </div>
                <p class="text-gray-600 text-sm">Explore our educational programs</p>
            </a>

            <a href="{{ url('/facilities') }}" class="group bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-green-600 transition-colors">Facilities</h3>
                </div>
                <p class="text-gray-600 text-sm">View our campus facilities</p>
            </a>
        </div>
    </div>
</section>
@endsection
