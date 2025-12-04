@extends('school.layout')

@section('title', 'Facilities')

@section('content')
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
                Our <span class="text-primary-600 relative inline-block">
                    Facilities
                    <span class="absolute bottom-0 left-0 right-0 h-3 bg-primary-200 opacity-30 -z-10 transform -rotate-1"></span>
                </span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                Explore our state-of-the-art facilities designed to enhance learning, creativity, and overall student development.
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
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Campus Facilities</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Modern infrastructure supporting excellence in education</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Facility Card 1 - Library -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Modern Library</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Extensive collection of books, digital resources, and quiet study spaces for research and learning.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>50,000+ books collection</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Digital reading resources</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Quiet study zones</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Facility Card 2 - Science Labs -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Science Laboratories</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Fully equipped labs for Physics, Chemistry, and Biology with modern equipment and safety measures.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Physics, Chemistry, Biology labs</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Modern equipment & tools</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Safety-certified facilities</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Facility Card 3 - Computer Lab -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Computer Labs</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        High-tech computer laboratories with latest hardware and software for digital learning and programming.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Latest hardware & software</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>High-speed internet</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Programming & coding tools</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Facility Card 4 - Sports Complex -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Sports Complex</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Comprehensive sports facilities including indoor and outdoor courts, fields, and fitness centers.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Basketball & volleyball courts</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Football & cricket fields</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Fitness & gymnasium</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Facility Card 5 - Auditorium -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Auditorium</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Spacious auditorium with modern audio-visual equipment for assemblies, performances, and events.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>500+ seating capacity</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Advanced sound system</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Stage & lighting setup</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Facility Card 6 - Cafeteria -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-yellow-400 to-orange-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Cafeteria</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Clean and spacious dining area serving healthy, nutritious meals prepared in hygienic conditions.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Healthy meal options</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Hygienic food preparation</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Comfortable seating area</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Facility Card 7 - Art Room -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Art & Craft Room</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Dedicated space for creative expression with art supplies, tools, and display areas for student artwork.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-pink-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Complete art supplies</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-pink-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Pottery & sculpture tools</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-pink-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Artwork display gallery</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Facility Card 8 - Music Room -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Music Room</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Soundproof music room equipped with various instruments and audio equipment for music education.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Various musical instruments</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Soundproof environment</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Recording facilities</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Facility Card 9 - Medical Room -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Medical Room</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Well-equipped medical facility with trained staff to handle emergencies and provide first aid.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-cyan-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Qualified medical staff</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-cyan-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>First aid equipment</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-cyan-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>24/7 emergency support</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Facilities -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">More Facilities</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Additional Amenities</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Supporting facilities that enhance the learning experience</p>
        </div>

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

            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow text-center border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Security System</h3>
                <p class="text-gray-600 text-sm">24/7 surveillance and access control</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow text-center border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Wi-Fi Campus</h3>
                <p class="text-gray-600 text-sm">High-speed internet throughout the campus</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow text-center border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Transportation</h3>
                <p class="text-gray-600 text-sm">Safe and reliable school bus service</p>
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
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Experience Our Facilities</h2>
        <p class="text-xl md:text-2xl text-primary-100 mb-10 max-w-2xl mx-auto">Schedule a campus tour to see our world-class facilities in person</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/contact') }}" class="group inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                Schedule a Tour
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
