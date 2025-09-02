@extends('landing.layout')

@section('title', 'Features')
@section('description', 'Comprehensive features of our School ERP system')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-secondary-900 mb-4">School ERP Features</h1>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto">
                Discover the comprehensive features that make our School ERP the perfect solution for modern educational institutions.
            </p>
        </div>

        <!-- Core Modules -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <!-- Student Management -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Student Management</h3>
                <ul class="text-secondary-600 space-y-2 text-sm">
                    <li>• Student registration & profiles</li>
                    <li>• Attendance tracking</li>
                    <li>• Academic records</li>
                    <li>• Parent communication</li>
                </ul>
            </div>

            <!-- Academic Management -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Academic Management</h3>
                <ul class="text-secondary-600 space-y-2 text-sm">
                    <li>• Class & subject management</li>
                    <li>• Timetable creation</li>
                    <li>• Assignment tracking</li>
                    <li>• Grade management</li>
                </ul>
            </div>

            <!-- Financial Management -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Financial Management</h3>
                <ul class="text-secondary-600 space-y-2 text-sm">
                    <li>• Fee collection</li>
                    <li>• Expense tracking</li>
                    <li>• Financial reporting</li>
                    <li>• Online payments</li>
                </ul>
            </div>

            <!-- HR & Payroll -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">HR & Payroll</h3>
                <ul class="text-secondary-600 space-y-2 text-sm">
                    <li>• Employee management</li>
                    <li>• Payroll processing</li>
                    <li>• Leave management</li>
                    <li>• Performance tracking</li>
                </ul>
            </div>

            <!-- Library Management -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Library Management</h3>
                <ul class="text-secondary-600 space-y-2 text-sm">
                    <li>• Book cataloging</li>
                    <li>• Issue & return tracking</li>
                    <li>• Fine management</li>
                    <li>• Digital library support</li>
                </ul>
            </div>

            <!-- Communication -->
            <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-secondary-900 mb-3">Communication</h3>
                <ul class="text-secondary-600 space-y-2 text-sm">
                    <li>• SMS notifications</li>
                    <li>• Email alerts</li>
                    <li>• Internal messaging</li>
                    <li>• Parent portal</li>
                </ul>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-secondary-900 mb-6">Ready to Get Started?</h2>
            <p class="text-xl text-secondary-600 mb-8">
                Experience the power of our comprehensive School ERP system.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('landing.contact') }}" class="btn-primary px-8 py-4 rounded-xl text-lg font-semibold">
                    Contact Us
                </a>
                <a href="{{ route('landing.contact') }}" class="btn-secondary px-8 py-4 rounded-xl text-lg font-semibold">
                    Contact Sales
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
