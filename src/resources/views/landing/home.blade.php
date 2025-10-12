@extends('landing.layout')

@section('title', 'Home')
@section('description', 'Complete School Management System with Multi-Tenancy Support')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-accent-50"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent-100 text-accent-700 text-sm font-medium mb-8">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Trusted by 500+ Schools
            </div>

            <!-- Main Heading -->
            <h1 class="text-4xl md:text-6xl font-bold text-secondary-900 mb-6 leading-tight">
                Complete
                <span class="text-primary-600">School Management</span>
                <br>System
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-secondary-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                {{ config('all.company.tagline') }}. Streamline operations, enhance learning, and manage your school efficiently with our comprehensive ERP solution.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                <a href="{{ route('landing.contact') }}" class="btn-primary px-8 py-4 rounded-xl text-lg font-semibold shadow-lg">
                    Contact Us
                </a>
                <a href="{{ route('landing.features') }}" class="btn-secondary px-8 py-4 rounded-xl text-lg font-semibold">
                    View Features
                </a>
            </div>

            <!-- Access Information -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 mb-8 max-w-2xl mx-auto">
                <h3 class="text-lg font-semibold text-secondary-900 mb-3">How to Access the System</h3>
                <div class="text-sm text-secondary-600 space-y-2">
                    <p><strong>For Schools:</strong> Visit <code class="bg-secondary-100 px-2 py-1 rounded">schoolname.myschool.test</code> to access your school portal</p>
                    <p><strong>For Administrators:</strong> Visit <code class="bg-secondary-100 px-2 py-1 rounded">app.myschool.test</code> for system management</p>
                    <p><strong>Demo Schools:</strong> Try <code class="bg-secondary-100 px-2 py-1 rounded">schoola.myschool.test</code>, <code class="bg-secondary-100 px-2 py-1 rounded">schoolb.myschool.test</code>, or <code class="bg-secondary-100 px-2 py-1 rounded">schoolc.myschool.test</code></p>
                </div>
            </div>

            <!-- Trust Indicators -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-8 text-secondary-500">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-success mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>30-day free trial</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-success mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>No credit card required</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-success mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>24/7 support</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Overview Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
                Everything You Need to Run Your School
            </h2>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto">
                From student management to financial operations, our comprehensive platform covers all aspects of school administration.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Student Management -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Student Management</h3>
                <p class="text-secondary-600">Comprehensive student records, attendance tracking, and academic performance monitoring.</p>
            </div>

            <!-- Financial Management -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Financial Management</h3>
                <p class="text-secondary-600">Fee collection, expense tracking, payroll management, and financial reporting.</p>
            </div>

            <!-- Learning Management -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Learning Management</h3>
                <p class="text-secondary-600">Online classes, assignments, assessments, and progress tracking for enhanced learning.</p>
            </div>

            <!-- Communication -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Communication</h3>
                <p class="text-secondary-600">SMS, email, and in-app notifications to keep everyone connected and informed.</p>
            </div>

            <!-- Reporting & Analytics -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Reporting & Analytics</h3>
                <p class="text-secondary-600">Comprehensive reports and insights to make data-driven decisions.</p>
            </div>

            <!-- Multi-Tenancy -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Multi-Tenancy</h3>
                <p class="text-secondary-600">Support for multiple institutions with custom domain mapping and complete data isolation.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 gradient-bg">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Transform Your School?
        </h2>
        <p class="text-xl text-primary-100 mb-8">
            Join hundreds of schools already using our platform to streamline their operations and enhance learning outcomes.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landing.contact') }}" class="bg-white text-primary-600 px-8 py-4 rounded-xl text-lg font-semibold hover:bg-gray-50 transition-colors shadow-lg">
                Start Free Trial
            </a>
            <a href="{{ route('landing.contact') }}" class="border-2 border-white text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-white hover:text-primary-600 transition-colors">
                Schedule Demo
            </a>
        </div>
    </div>
</section>
@endsection
