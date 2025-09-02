@extends('landing.layout')

@section('title', 'About Us')
@section('description', 'Learn about our mission to transform school management')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-secondary-900 mb-4">About School ERP</h1>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto">
                We're on a mission to transform how schools manage their operations, 
                making education administration more efficient, transparent, and student-focused.
            </p>
        </div>

        <!-- Mission & Vision -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <div class="bg-primary-50 rounded-2xl p-8">
                <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-primary-900 mb-4">Our Mission</h2>
                <p class="text-primary-700 leading-relaxed">
                    To empower educational institutions with innovative technology solutions that streamline 
                    administrative processes, enhance communication, and ultimately improve student outcomes. 
                    We believe that when schools run efficiently, teachers can focus on what matters most: teaching.
                </p>
            </div>

            <div class="bg-accent-50 rounded-2xl p-8">
                <div class="w-16 h-16 bg-accent-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-accent-900 mb-4">Our Vision</h2>
                <p class="text-accent-700 leading-relaxed">
                    To become the leading School ERP solution globally, recognized for our commitment to 
                    innovation, reliability, and customer success. We envision a future where every school, 
                    regardless of size, can access enterprise-grade management tools.
                </p>
            </div>
        </div>

        <!-- Company Story -->
        <div class="bg-secondary-50 rounded-2xl p-8 mb-16">
            <h2 class="text-3xl font-bold text-secondary-900 mb-8 text-center">Our Story</h2>
            
            <div class="max-w-4xl mx-auto space-y-8">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-primary-600 font-bold text-lg">2018</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-secondary-900 mb-2">The Beginning</h3>
                        <p class="text-secondary-600">
                            Founded by a team of educators and technologists who experienced firsthand the 
                            challenges of managing school operations with outdated systems. We started with 
                            a simple goal: make school management easier.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-primary-600 font-bold text-lg">2020</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-secondary-900 mb-2">First Major Release</h3>
                        <p class="text-secondary-600">
                            Launched our comprehensive School ERP system, serving our first 50 schools. 
                            The feedback was overwhelming, and we knew we were on the right track.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-primary-600 font-bold text-lg">2022</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-secondary-900 mb-2">Multi-Tenancy Innovation</h3>
                        <p class="text-secondary-600">
                            Introduced our revolutionary multi-tenancy architecture, allowing schools to 
                            choose between shared and separate database strategies based on their needs.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-primary-600 font-bold text-lg">2024</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-secondary-900 mb-2">Today & Beyond</h3>
                        <p class="text-secondary-600">
                            Serving over 500+ schools across India and expanding globally. We continue to 
                            innovate, always keeping our customers' needs at the heart of everything we do.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Core Values -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-secondary-900 mb-8 text-center">Our Core Values</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Excellence</h3>
                    <p class="text-secondary-600 text-sm">
                        We strive for excellence in every product we build and every service we provide.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-accent-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Collaboration</h3>
                    <p class="text-secondary-600 text-sm">
                        We believe in the power of collaboration with our customers, partners, and team.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Innovation</h3>
                    <p class="text-secondary-600 text-sm">
                        We continuously innovate to stay ahead of the curve and meet evolving needs.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-accent-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Passion</h3>
                    <p class="text-secondary-600 text-sm">
                        We're passionate about education and committed to making a positive impact.
                    </p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="bg-white rounded-2xl p-8 border border-secondary-200 mb-16">
            <h2 class="text-3xl font-bold text-secondary-900 mb-8 text-center">Our Leadership Team</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-24 h-24 bg-primary-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-2">Rajesh Kumar</h3>
                    <p class="text-primary-600 font-medium mb-2">CEO & Founder</p>
                    <p class="text-secondary-600 text-sm">
                        Former school principal with 15+ years in education technology.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 bg-accent-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-2">Priya Sharma</h3>
                    <p class="text-accent-600 font-medium mb-2">CTO</p>
                    <p class="text-secondary-600 text-sm">
                        Technology leader with expertise in scalable software architecture.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 bg-primary-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-2">Amit Patel</h3>
                    <p class="text-primary-600 font-medium mb-2">Head of Customer Success</p>
                    <p class="text-secondary-600 text-sm">
                        Dedicated to ensuring every school achieves success with our platform.
                    </p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-secondary-900 mb-6">Join Us in Transforming Education</h2>
            <p class="text-xl text-secondary-600 mb-8">
                Be part of the revolution in school management technology.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('landing.contact') }}" class="btn-primary px-8 py-4 rounded-xl text-lg font-semibold">
                    Get in Touch
                </a>
                <a href="{{ route('landing.features') }}" class="btn-secondary px-8 py-4 rounded-xl text-lg font-semibold">
                    Explore Features
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
