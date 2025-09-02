@extends('landing.layout')

@section('title', 'Contact Us')
@section('description', 'Get in touch with our team for any questions or support')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-secondary-900 mb-4">Get in Touch</h1>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto">
                Have questions about our School ERP system? Need support? Want to schedule a demo? 
                We're here to help you succeed.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-secondary-50 rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-secondary-900 mb-6">Send us a Message</h2>
                
                <form action="{{ route('landing.contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-secondary-700 mb-2">
                                First Name *
                            </label>
                            <input type="text" id="first_name" name="first_name" required
                                class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                                placeholder="Enter your first name">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-secondary-700 mb-2">
                                Last Name *
                            </label>
                            <input type="text" id="last_name" name="last_name" required
                                class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                                placeholder="Enter your last name">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-secondary-700 mb-2">
                            Email Address *
                        </label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                            placeholder="Enter your email address">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-secondary-700 mb-2">
                            Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone"
                            class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                            placeholder="Enter your phone number">
                    </div>

                    <div>
                        <label for="school_name" class="block text-sm font-medium text-secondary-700 mb-2">
                            School/Institution Name *
                        </label>
                        <input type="text" id="school_name" name="school_name" required
                            class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                            placeholder="Enter your school name">
                    </div>

                    <div>
                        <label for="student_count" class="block text-sm font-medium text-secondary-700 mb-2">
                            Number of Students
                        </label>
                        <select id="student_count" name="student_count"
                            class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select student count</option>
                            <option value="1-500">1 - 500 students</option>
                            <option value="501-1000">501 - 1,000 students</option>
                            <option value="1001-2000">1,001 - 2,000 students</option>
                            <option value="2001-5000">2,001 - 5,000 students</option>
                            <option value="5000+">5,000+ students</option>
                        </select>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-secondary-700 mb-2">
                            Subject *
                        </label>
                        <select id="subject" name="subject" required
                            class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select a subject</option>
                            <option value="demo">Schedule a Demo</option>
                            <option value="pricing">Pricing Information</option>
                            <option value="support">Technical Support</option>
                            <option value="partnership">Partnership Opportunities</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-secondary-700 mb-2">
                            Message *
                        </label>
                        <textarea id="message" name="message" rows="5" required
                            class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                            placeholder="Tell us more about your needs..."></textarea>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="newsletter" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm text-secondary-600">
                                Subscribe to our newsletter for updates and tips
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary w-full py-4 rounded-xl text-lg font-semibold">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <!-- Company Info -->
                <div class="bg-primary-50 rounded-2xl p-8">
                    <h2 class="text-2xl font-bold text-primary-900 mb-6">Contact Information</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-primary-900">Phone</p>
                                <p class="text-primary-700">{{ config('all.company.phone', '+91 98765 43210') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-primary-900">Email</p>
                                <p class="text-primary-700">{{ config('all.company.email', 'info@myschool.com') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-primary-900">Address</p>
                                <p class="text-primary-700">
                                    Tech Park, Electronic City<br>
                                    Bangalore - 560100, Karnataka, India
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="bg-accent-50 rounded-2xl p-8">
                    <h2 class="text-2xl font-bold text-accent-900 mb-6">Business Hours</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-accent-700">Monday - Friday</span>
                            <span class="font-semibold text-accent-900">9:00 AM - 6:00 PM IST</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-accent-700">Saturday</span>
                            <span class="font-semibold text-accent-900">10:00 AM - 4:00 PM IST</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-accent-700">Sunday</span>
                            <span class="font-semibold text-accent-900">Closed</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-accent-100 rounded-lg">
                        <p class="text-sm text-accent-800">
                            <strong>Note:</strong> For urgent technical support, please call our 24/7 support line.
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-secondary-50 rounded-2xl p-8">
                    <h2 class="text-2xl font-bold text-secondary-900 mb-6">Quick Actions</h2>
                    
                    <div class="space-y-4">
                        <a href="{{ route('landing.features') }}" class="flex items-center p-4 bg-white rounded-lg border border-secondary-200 hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-secondary-900">Explore Features</p>
                                <p class="text-sm text-secondary-600">Learn about our comprehensive modules</p>
                            </div>
                        </a>

                        <a href="{{ route('landing.pricing') }}" class="flex items-center p-4 bg-white rounded-lg border border-secondary-200 hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-secondary-900">View Pricing</p>
                                <p class="text-sm text-secondary-600">Choose the perfect plan for your school</p>
                            </div>
                        </a>

                        <a href="{{ route('landing.multi-tenancy-demo') }}" class="flex items-center p-4 bg-white rounded-lg border border-secondary-200 hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-secondary-900">Multi-Tenancy Demo</p>
                                <p class="text-sm text-secondary-600">See our system in action</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
