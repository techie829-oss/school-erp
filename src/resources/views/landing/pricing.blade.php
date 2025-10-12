@extends('landing.layout')

@section('title', 'Pricing')
@section('description', 'Choose the perfect plan for your school')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-secondary-900 mb-4">Simple, Transparent Pricing</h1>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto">
                Choose the perfect plan for your school. All plans include our core features with no hidden costs.
            </p>
        </div>

        <!-- Pricing Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <!-- Starter Plan -->
            <div class="bg-secondary-50 rounded-2xl p-8 border-2 border-transparent hover:border-primary-200 transition-all">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-secondary-900 mb-2">Starter</h3>
                    <p class="text-secondary-600 mb-4">Perfect for small schools</p>
                    <div class="text-4xl font-bold text-primary-600 mb-2">₹2,999</div>
                    <p class="text-secondary-500">per month</p>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Up to 500 students
                    </li>
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Basic modules included
                    </li>
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Shared database
                    </li>
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Email support
                    </li>
                </ul>

                <a href="{{ route('landing.contact') }}" class="btn-secondary w-full text-center py-3 rounded-xl">
                    Contact Sales
                </a>
            </div>

            <!-- Professional Plan -->
            <div class="bg-primary-50 rounded-2xl p-8 border-2 border-primary-200 relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-primary-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                        Most Popular
                    </span>
                </div>

                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-primary-900 mb-2">Professional</h3>
                    <p class="text-primary-700 mb-4">Ideal for growing schools</p>
                    <div class="text-4xl font-bold text-primary-600 mb-2">₹5,999</div>
                    <p class="text-primary-500">per month</p>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center text-primary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Up to 2,000 students
                    </li>
                    <li class="flex items-center text-primary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        All modules included
                    </li>
                    <li class="flex items-center text-primary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Secure data isolation
                    </li>
                    <li class="flex items-center text-primary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Priority support
                    </li>
                    <li class="flex items-center text-primary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Custom branding
                    </li>
                </ul>

                <a href="{{ route('landing.contact') }}" class="btn-primary w-full text-center py-3 rounded-xl">
                    Contact Sales
                </a>
            </div>

            <!-- Enterprise Plan -->
            <div class="bg-secondary-50 rounded-2xl p-8 border-2 border-transparent hover:border-accent-200 transition-all">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-secondary-900 mb-2">Enterprise</h3>
                    <p class="text-secondary-600 mb-4">For large institutions</p>
                    <div class="text-4xl font-bold text-accent-600 mb-2">₹12,999</div>
                    <p class="text-secondary-500">per month</p>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Unlimited students
                    </li>
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        All modules + custom features
                    </li>
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Dedicated support
                    </li>
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        24/7 dedicated support
                    </li>
                    <li class="flex items-center text-secondary-700">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Custom integrations
                    </li>
                </ul>

                <a href="{{ route('landing.contact') }}" class="btn-secondary w-full text-center py-3 rounded-xl">
                    Contact Sales
                </a>
            </div>
        </div>

        <!-- Features Comparison -->
        <div class="bg-secondary-50 rounded-2xl p-8 mb-16">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-8 text-center">Feature Comparison</h2>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-secondary-200">
                            <th class="text-left py-4 px-4 font-semibold text-secondary-900">Feature</th>
                            <th class="text-center py-4 px-4 font-semibold text-secondary-900">Starter</th>
                            <th class="text-center py-4 px-4 font-semibold text-primary-900">Professional</th>
                            <th class="text-center py-4 px-4 font-semibold text-secondary-900">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-200">
                        <tr>
                            <td class="py-4 px-4 text-secondary-700">Student Management</td>
                            <td class="py-4 px-4 text-center">✅</td>
                            <td class="py-4 px-4 text-center">✅</td>
                            <td class="py-4 px-4 text-center">✅</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-secondary-700">Financial Management</td>
                            <td class="py-4 px-4 text-center">✅</td>
                            <td class="py-4 px-4 text-center">✅</td>
                            <td class="py-4 px-4 text-center">✅</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-secondary-700">HR & Payroll</td>
                            <td class="py-4 px-4 text-center">❌</td>
                            <td class="py-4 px-4 text-center">✅</td>
                            <td class="py-4 px-4 text-center">✅</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-secondary-700">Library Management</td>
                            <td class="py-4 px-4 text-center">❌</td>
                            <td class="py-4 px-4 text-center">✅</td>
                            <td class="py-4 px-4 text-center">✅</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-secondary-700">Data Isolation</td>
                            <td class="py-4 px-4 text-center">Standard</td>
                            <td class="py-4 px-4 text-center">Enhanced</td>
                            <td class="py-4 px-4 text-center">Separate</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-secondary-700">Support</td>
                            <td class="py-4 px-4 text-center">Email</td>
                            <td class="py-4 px-4 text-center">Priority</td>
                            <td class="py-4 px-4 text-center">24/7 Dedicated</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-2xl p-8 border border-secondary-200">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-8 text-center">Frequently Asked Questions</h2>

            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Can I change my plan later?</h3>
                    <p class="text-secondary-600">Yes, you can upgrade or downgrade your plan at any time. Changes take effect from the next billing cycle.</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Is there a free trial?</h3>
                    <p class="text-secondary-600">Yes, we offer a 30-day free trial for all plans. No credit card required to start.</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">What about data migration?</h3>
                    <p class="text-secondary-600">We provide free data migration assistance for Professional and Enterprise plans.</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Do you offer custom pricing?</h3>
                    <p class="text-secondary-600">Yes, for Enterprise plans we can create custom pricing based on your specific requirements.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
