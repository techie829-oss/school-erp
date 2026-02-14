@extends('landing.layout')

@section('title', 'Home')
@section('description', 'Complete School Management System with Multi-Tenancy Support')

@section('content')
    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-accent-50"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60"
            xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff"
            fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="2" /%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="text-center">
                <!-- Badge -->
                <div
                    class="inline-flex items-center px-4 py-2 rounded-full bg-accent-100 text-accent-700 text-sm font-medium mb-8">
                    <x-heroicon-s-check-badge class="w-4 h-4 mr-2" />
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
                    {{ config('all.company.tagline') }}. Streamline operations, enhance learning, and manage your school
                    efficiently with our comprehensive ERP solution.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <a href="{{ route('landing.contact') }}"
                        class="btn-primary px-8 py-4 rounded-xl text-lg font-semibold shadow-lg">
                        Contact Us
                    </a>
                    <a href="{{ route('landing.features') }}"
                        class="btn-secondary px-8 py-4 rounded-xl text-lg font-semibold">
                        View Features
                    </a>
                </div>

                <!-- Access Information -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 mb-8 max-w-2xl mx-auto">
                    <h3 class="text-lg font-semibold text-secondary-900 mb-3">How to Access the System</h3>
                    <div class="text-sm text-secondary-600 space-y-2">
                        <p><strong>Main:</strong> Visit <code
                                class="bg-secondary-100 px-2 py-1 rounded">{{ config('all.domains.primary') }}</code> to
                            access your school portal</p>
                        @if (config('all.demo.enabled', false) && count(config('all.demo.tenants', [])) > 0)
                            <p><strong>Demo School:</strong>
                                @php
                                    $demoTenants = config('all.demo.tenants', []);
                                    $firstDemo = reset($demoTenants);
                                @endphp
                                <code
                                    class="bg-secondary-100 px-2 py-1 rounded">{{ $firstDemo }}.{{ config('all.domains.primary') }}</code>
                            </p>
                        @endif
                        <p class="text-xs text-secondary-500 mt-3 pt-3 border-t border-secondary-200">
                            <x-heroicon-o-information-circle class="w-4 h-4 inline mr-1 text-primary-600" />
                            <strong>Multi-Tenancy:</strong> Each school gets its own subdomain (e.g., <code
                                class="bg-secondary-100 px-1 rounded">schoolname.{{ config('all.domains.primary') }}</code>)
                            with completely isolated data and custom branding.
                        </p>
                    </div>
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-8 text-secondary-500">
                    <div class="flex items-center">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-success mr-2" />
                        <span>30-day free trial</span>
                    </div>
                    <div class="flex items-center">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-success mr-2" />
                        <span>No credit card required</span>
                    </div>
                    <div class="flex items-center">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-success mr-2" />
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
                    From student management to financial operations, our comprehensive platform covers all aspects of school
                    administration.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Student Management -->
                <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                        <x-heroicon-o-user-group class="w-6 h-6 text-primary-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Student Management</h3>
                    <p class="text-secondary-600">Complete student lifecycle management with enrollment, attendance,
                        documents, fee cards, assignments, and performance tracking.</p>
                </div>

                <!-- Fee Management -->
                <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                    <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                        <x-heroicon-o-currency-dollar class="w-6 h-6 text-accent-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Fee Management</h3>
                    <p class="text-secondary-600">Automated fee collection with custom fee plans, invoicing, payment
                        tracking, refunds, and detailed financial reports.</p>
                </div>

                <!-- Examination System -->
                <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-primary-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Examination System</h3>
                    <p class="text-secondary-600">Complete examination management with scheduling, mark entry, grade books,
                        report cards, admit cards, and ranking systems.</p>
                </div>

                <!-- Library & Hostel -->
                <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                    <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                        <x-heroicon-o-building-library class="w-6 h-6 text-accent-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Library & Hostel</h3>
                    <p class="text-secondary-600">Integrated library with book cataloging and issue tracking, plus
                        comprehensive hostel management with room allocation and fee billing.</p>
                </div>

                <!-- Transport Management -->
                <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                        <x-heroicon-o-truck class="w-6 h-6 text-primary-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Transport Management</h3>
                    <p class="text-secondary-600">Complete transport system with vehicle tracking, route management, student
                        assignments, driver records, and automated billing.</p>
                </div>

                <!-- CMS & Branding -->
                <div class="bg-secondary-50 rounded-2xl p-8 card-hover">
                    <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-6">
                        <x-heroicon-o-building-office-2 class="w-6 h-6 text-accent-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">CMS & Branding</h3>
                    <p class="text-secondary-600">Built-in CMS for custom pages, multi-tenancy with subdomain routing,
                        custom color palettes, and complete data isolation per school.</p>
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
                Join hundreds of schools already using our platform to streamline their operations and enhance learning
                outcomes.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('landing.contact') }}"
                    class="bg-white text-primary-600 px-8 py-4 rounded-xl text-lg font-semibold hover:bg-gray-50 transition-colors shadow-lg">
                    Start Free Trial
                </a>
                <a href="{{ route('landing.contact') }}"
                    class="border-2 border-white text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-white hover:text-primary-600 transition-colors">
                    Schedule Demo
                </a>
            </div>
        </div>
    </section>
@endsection
