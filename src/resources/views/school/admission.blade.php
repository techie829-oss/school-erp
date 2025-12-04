@extends('school.layout')

@section('title', 'Admission')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-24 lg:py-32 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23000000\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <div class="mb-6">
                <span class="inline-block px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold mb-4">
                    Join Our Community
                </span>
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                Admission <span class="text-primary-600 relative inline-block">
                    Information
                    <span class="absolute bottom-0 left-0 right-0 h-3 bg-primary-200 opacity-30 -z-10 transform -rotate-1"></span>
                </span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                Learn about our admission process and requirements for joining our school community.
            </p>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-indigo-200 rounded-full opacity-20 blur-xl"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-purple-300 rounded-full opacity-20 blur-2xl"></div>
</section>

<!-- Admission Process -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">How to Apply</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Admission Process</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Simple steps to join our school community</p>
        </div>

        <div class="relative">
            <!-- Timeline Line -->
            <div class="hidden lg:block absolute top-0 left-1/2 transform -translate-x-1/2 w-1 h-full bg-gradient-to-b from-primary-200 via-primary-400 to-primary-200"></div>

            <div class="space-y-12 lg:space-y-0">
                <!-- Step 1 -->
                <div class="relative flex flex-col lg:flex-row items-center">
                    <div class="lg:w-1/2 lg:pr-12 mb-8 lg:mb-0">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 border border-blue-200">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-xl">1</span>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">Inquiry & Application</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                Submit an online inquiry form or visit our campus to learn more about our programs. Complete the admission application form with all required details.
                            </p>
                        </div>
                    </div>
                    <div class="lg:w-1/2 lg:pl-12 lg:text-left text-center">
                        <div class="inline-block">
                            <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative flex flex-col lg:flex-row-reverse items-center">
                    <div class="lg:w-1/2 lg:pl-12 mb-8 lg:mb-0">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 border border-green-200">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-xl">2</span>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">Document Submission</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                Submit all required documents including birth certificate, previous school records, medical reports, and passport-sized photographs.
                            </p>
                        </div>
                    </div>
                    <div class="lg:w-1/2 lg:pr-12 lg:text-right text-center">
                        <div class="inline-block">
                            <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative flex flex-col lg:flex-row items-center">
                    <div class="lg:w-1/2 lg:pr-12 mb-8 lg:mb-0">
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 border border-purple-200">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-xl">3</span>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">Assessment & Interview</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                Students may be required to take an assessment test. Parents and students will have an interview session with the admission committee.
                            </p>
                        </div>
                    </div>
                    <div class="lg:w-1/2 lg:pl-12 lg:text-left text-center">
                        <div class="inline-block">
                            <div class="w-20 h-20 bg-purple-600 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative flex flex-col lg:flex-row-reverse items-center">
                    <div class="lg:w-1/2 lg:pl-12 mb-8 lg:mb-0">
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-8 border border-orange-200">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-xl">4</span>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">Admission Decision</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                After reviewing all applications, successful candidates will receive an admission offer letter with details about enrollment and fees.
                            </p>
                        </div>
                    </div>
                    <div class="lg:w-1/2 lg:pr-12 lg:text-right text-center">
                        <div class="inline-block">
                            <div class="w-20 h-20 bg-orange-600 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="relative flex flex-col lg:flex-row items-center">
                    <div class="lg:w-1/2 lg:pr-12 mb-8 lg:mb-0">
                        <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-2xl p-8 border border-teal-200">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-xl">5</span>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">Enrollment & Orientation</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                Complete the enrollment process by paying fees and submitting final documents. Attend the orientation program to get familiar with the school.
                            </p>
                        </div>
                    </div>
                    <div class="lg:w-1/2 lg:pl-12 lg:text-left text-center">
                        <div class="inline-block">
                            <div class="w-20 h-20 bg-teal-600 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Requirements Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">What You Need</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Admission Requirements</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Documents and information needed for admission</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Academic Records</h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Previous school transcripts</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Report cards (last 2 years)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Transfer certificate</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Personal Documents</h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Birth certificate</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Passport-sized photos (4 copies)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>ID proof (Aadhaar/Passport)</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Medical Records</h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Medical fitness certificate</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Vaccination records</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Blood group certificate</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Important Dates -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">Timeline</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Important Dates</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Key dates for the admission process</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                <div class="text-blue-600 font-bold text-sm mb-2">Application Opens</div>
                <div class="text-2xl font-bold text-gray-900 mb-2">January 1</div>
                <div class="text-gray-700 text-sm">Start submitting your applications</div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                <div class="text-green-600 font-bold text-sm mb-2">Last Date</div>
                <div class="text-2xl font-bold text-gray-900 mb-2">March 31</div>
                <div class="text-gray-700 text-sm">Final deadline for applications</div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                <div class="text-purple-600 font-bold text-sm mb-2">Assessments</div>
                <div class="text-2xl font-bold text-gray-900 mb-2">April 15</div>
                <div class="text-gray-700 text-sm">Assessment tests scheduled</div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
                <div class="text-orange-600 font-bold text-sm mb-2">Results</div>
                <div class="text-2xl font-bold text-gray-900 mb-2">May 1</div>
                <div class="text-gray-700 text-sm">Admission results announced</div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">Got Questions?</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-xl text-gray-600">Common questions about our admission process</p>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <button class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(this)">
                    <span class="text-lg font-semibold text-gray-900">What is the age requirement for admission?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="hidden px-6 pb-5 text-gray-700">
                    Age requirements vary by grade level. Generally, students should be age-appropriate for their grade. Please contact our admission office for specific age requirements for each grade.
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <button class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(this)">
                    <span class="text-lg font-semibold text-gray-900">Is there an entrance exam?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="hidden px-6 pb-5 text-gray-700">
                    Yes, students may be required to take an assessment test depending on the grade level. The test evaluates basic academic skills and helps us understand the student's learning needs.
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <button class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(this)">
                    <span class="text-lg font-semibold text-gray-900">What are the fee structures?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="hidden px-6 pb-5 text-gray-700">
                    Fee structures vary by grade level and program. Detailed fee information is provided during the admission process. We also offer scholarships and financial aid for eligible students.
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <button class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(this)">
                    <span class="text-lg font-semibold text-gray-900">Can I visit the campus before applying?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="hidden px-6 pb-5 text-gray-700">
                    Absolutely! We encourage prospective families to visit our campus. You can schedule a campus tour by contacting our admission office. We also organize open house events throughout the year.
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
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Ready to Begin Your Journey?</h2>
        <p class="text-xl md:text-2xl text-primary-100 mb-10 max-w-2xl mx-auto">Start your admission process today and join our vibrant learning community</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/contact') }}" class="group inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                Contact Admission Office
                <svg class="ml-2 w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
            <a href="{{ url('/') }}" class="inline-flex items-center px-8 py-4 bg-transparent text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-300 border-2 border-white shadow-lg hover:shadow-xl">
                Learn More About Us
            </a>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full opacity-10 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full opacity-10 blur-3xl"></div>
</section>

<script>
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('svg');
    const isHidden = content.classList.contains('hidden');

    // Close all other FAQs
    document.querySelectorAll('[onclick*="toggleFAQ"]').forEach(btn => {
        if (btn !== button) {
            btn.nextElementSibling.classList.add('hidden');
            btn.querySelector('svg').classList.remove('rotate-180');
        }
    });

    // Toggle current FAQ
    if (isHidden) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
@endsection
