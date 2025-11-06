@extends('tenant.layouts.admin')

@section('title', 'Teacher Profile')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/teachers') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Teachers</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Profile</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center">
                @if($teacher->photo)
                    <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}" class="h-16 w-16 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center border-2 border-gray-200">
                        <span class="text-white font-medium text-xl">
                            {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                        </span>
                    </div>
                @endif
                <div class="ml-4">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {{ $teacher->full_name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $teacher->employee_id }} • {{ $teacher->designation ?? 'Teacher' }}
                        @if($teacher->department)
                            • {{ $teacher->department->department_name }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/teachers/' . $teacher->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="{{ url('/admin/teachers') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Status Banner -->
    @php
        $statusColors = [
            'active' => 'bg-green-50 border-green-200 text-green-800',
            'on_leave' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'resigned' => 'bg-gray-50 border-gray-200 text-gray-800',
            'retired' => 'bg-blue-50 border-blue-200 text-blue-800',
            'terminated' => 'bg-red-50 border-red-200 text-red-800',
        ];
        $statusColor = $statusColors[$teacher->status] ?? 'bg-gray-50 border-gray-200 text-gray-800';
    @endphp
    <div class="rounded-lg border-2 {{ $statusColor }} p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium">
                    Status: {{ ucfirst(str_replace('_', ' ', $teacher->status)) }}
                    • Employment: {{ ucfirst($teacher->employment_type) }}
                </h3>
                @if($teacher->status_remarks)
                    <p class="mt-1 text-sm opacity-75">{{ $teacher->status_remarks }}</p>
                @endif
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $teacher->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $teacher->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <dt class="text-sm font-medium text-gray-500 truncate">Age</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $teacher->age ?? 0 }} years</dd>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <dt class="text-sm font-medium text-gray-500 truncate">Years of Service</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $teacher->years_of_service ?? 0 }} years</dd>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <dt class="text-sm font-medium text-gray-500 truncate">Subjects</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $teacher->subjects->count() }}</dd>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <dt class="text-sm font-medium text-gray-500 truncate">Documents</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $teacher->documents->count() }}</dd>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white shadow rounded-lg">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTeacherTab('overview')" id="teacher-tab-overview" class="teacher-tab-button border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Overview
                </button>
                <button onclick="showTeacherTab('employment')" id="teacher-tab-employment" class="teacher-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Employment
                </button>
                <button onclick="showTeacherTab('qualifications')" id="teacher-tab-qualifications" class="teacher-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Qualifications ({{ $teacher->qualifications->count() }})
                </button>
                <button onclick="showTeacherTab('subjects')" id="teacher-tab-subjects" class="teacher-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Subjects ({{ $teacher->subjects->count() }})
                </button>
                <button onclick="showTeacherTab('classes')" id="teacher-tab-classes" class="teacher-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Classes ({{ $teacher->classesTaught->count() }})
                </button>
                <button onclick="showTeacherTab('documents')" id="teacher-tab-documents" class="teacher-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Documents ({{ $teacher->documents->count() }})
                </button>
            </nav>
        </div>

        <!-- Tab Content: Overview -->
        <div id="teacher-content-overview" class="teacher-tab-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Details -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Personal Details</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Full Name:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->full_name }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Date of Birth:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->date_of_birth?->format('d M, Y') }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Age:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->age }} years</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Gender:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ ucfirst($teacher->gender) }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Blood Group:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->blood_group ?? 'Not specified' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Nationality:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->nationality }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Religion:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->religion ?? 'Not specified' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Category:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ strtoupper($teacher->category ?? 'N/A') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Contact Details -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Contact Details</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Email:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->email ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Phone:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->phone ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Alternate Phone:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->alternate_phone ?? 'Not provided' }}</dd>
                        </div>
                    </dl>

                    <h4 class="text-sm font-medium text-gray-900 mb-3 mt-6">Emergency Contact</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Name:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->emergency_contact_name ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Phone:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->emergency_contact_phone ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Relation:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->emergency_contact_relation ?? 'Not provided' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Address Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h5 class="text-xs font-medium text-gray-700 mb-2">Current Address</h5>
                            <p class="text-sm text-gray-900">
                                @if($teacher->current_address)
                                    {{ $teacher->current_address['address'] ?? '' }}<br>
                                    {{ $teacher->current_address['city'] ?? '' }}, {{ $teacher->current_address['state'] ?? '' }} {{ $teacher->current_address['pincode'] ?? '' }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                        <div>
                            <h5 class="text-xs font-medium text-gray-700 mb-2">Permanent Address</h5>
                            <p class="text-sm text-gray-900">
                                @if($teacher->permanent_address)
                                    {{ $teacher->permanent_address['address'] ?? '' }}<br>
                                    {{ $teacher->permanent_address['city'] ?? '' }}, {{ $teacher->permanent_address['state'] ?? '' }} {{ $teacher->permanent_address['pincode'] ?? '' }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Employment -->
        <div id="teacher-content-employment" class="teacher-tab-content p-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Employment Information</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Employee ID:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->employee_id }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Department:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->department->department_name ?? 'Not assigned' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Designation:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->designation ?? 'Not specified' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Employment Type:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ ucfirst($teacher->employment_type) }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Date of Joining:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->date_of_joining?->format('d M, Y') }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Years of Service:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->years_of_service }} years</dd>
                        </div>
                        @if($teacher->date_of_leaving)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Date of Leaving:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->date_of_leaving?->format('d M, Y') }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Highest Qualification:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->highest_qualification ?? 'Not specified' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Total Experience:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->experience_years ?? '0' }} years</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Financial Information</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Salary Amount:</dt>
                            <dd class="text-sm font-medium text-gray-900">₹{{ number_format($teacher->salary_amount ?? 0, 2) }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Bank Name:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->bank_name ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Account Number:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->bank_account_number ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">IFSC Code:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->bank_ifsc_code ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">PAN Number:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->pan_number ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Aadhar Number:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $teacher->aadhar_number ?? 'Not provided' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($teacher->notes)
            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Additional Notes</h4>
                <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-md">{{ $teacher->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Tab Content: Qualifications -->
        <div id="teacher-content-qualifications" class="teacher-tab-content p-6 hidden">
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Qualifications & Certifications</h4>
                @if($teacher->qualifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($teacher->qualifications as $qualification)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h5 class="text-sm font-medium text-gray-900">{{ $qualification->degree_name }}</h5>
                                    @if($qualification->specialization)
                                        <p class="text-sm text-gray-600">{{ $qualification->specialization }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $qualification->institution_name }}
                                        @if($qualification->university_board)
                                            ({{ $qualification->university_board }})
                                        @endif
                                    </p>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <span class="text-xs text-gray-500">Year: {{ $qualification->year_of_passing }}</span>
                                        @if($qualification->grade_percentage)
                                            <span class="text-xs text-gray-500">Grade: {{ $qualification->grade_percentage }}</span>
                                        @endif
                                        @if($qualification->certificate_number)
                                            <span class="text-xs text-gray-500">Certificate #: {{ $qualification->certificate_number }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex flex-col items-end space-y-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $qualification->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $qualification->is_verified ? 'Verified' : 'Pending' }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($qualification->qualification_type) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No qualifications added yet</p>
                @endif
            </div>

            <!-- Add Qualification Form -->
            <div class="border-t pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Add New Qualification</h4>
                <form action="{{ url('/admin/teachers/' . $teacher->id . '/qualifications') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="qualification_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="academic">Academic</option>
                                <option value="professional">Professional</option>
                                <option value="certification">Certification</option>
                                <option value="training">Training</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Degree Name *</label>
                            <input type="text" name="degree_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Specialization</label>
                            <input type="text" name="specialization" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Institution *</label>
                            <input type="text" name="institution_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">University/Board</label>
                            <input type="text" name="university_board" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Year of Passing *</label>
                            <input type="number" name="year_of_passing" required min="1950" max="{{ date('Y') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Grade/Percentage</label>
                            <input type="text" name="grade_percentage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Certificate Number</label>
                            <input type="text" name="certificate_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Certificate Document</label>
                            <input type="file" name="certificate_document" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500">
                            <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG (Max: 5MB)</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            Add Qualification
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab Content: Subjects -->
        <div id="teacher-content-subjects" class="teacher-tab-content p-6 hidden">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Assigned Subjects</h4>
            @if($teacher->subjects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($teacher->subjects as $subject)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h5 class="text-sm font-medium text-gray-900">{{ $subject->subject_name }}</h5>
                        <p class="text-xs text-gray-500 mt-1">{{ $subject->subject_code }}</p>
                        <span class="inline-block mt-2 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($subject->subject_type) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No subjects assigned yet</p>
            @endif
        </div>

        <!-- Tab Content: Classes -->
        <div id="teacher-content-classes" class="teacher-tab-content p-6 hidden">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Class Teacher Assignments</h4>
            @if($teacher->classesTaught->count() > 0)
                <div class="space-y-3">
                    @foreach($teacher->classesTaught as $section)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 flex justify-between items-center">
                        <div>
                            <h5 class="text-sm font-medium text-gray-900">{{ $section->schoolClass->class_name }} - {{ $section->section_name }}</h5>
                            <p class="text-xs text-gray-500 mt-1">Room: {{ $section->room_number ?? 'Not assigned' }}</p>
                            <p class="text-xs text-gray-500">Capacity: {{ $section->capacity ?? 'N/A' }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            Class Teacher
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">Not assigned as class teacher to any section</p>
            @endif
        </div>

        <!-- Tab Content: Documents -->
        <div id="teacher-content-documents" class="teacher-tab-content p-6 hidden">
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Uploaded Documents</h4>
                @if($teacher->documents->count() > 0)
                    <div class="space-y-3">
                        @foreach($teacher->documents as $document)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900">{{ $document->document_name }}</h5>
                                    <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</p>
                                    <p class="text-xs text-gray-400">{{ $document->file_size_human }} • Uploaded {{ $document->uploaded_at->format('d M, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ $document->file_url }}" target="_blank" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View
                                </a>
                                <form action="{{ url('/admin/documents/' . $document->id) }}" method="POST" onsubmit="return confirm('Delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No documents uploaded yet</p>
                @endif
            </div>

            <!-- Upload Document Form -->
            <div class="border-t pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Upload New Document</h4>
                <form action="{{ url('/admin/teachers/' . $teacher->id . '/documents') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Name *</label>
                            <input type="text" name="document_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Type *</label>
                            <select name="document_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="resume">Resume/CV</option>
                                <option value="certificate">Certificate</option>
                                <option value="experience_letter">Experience Letter</option>
                                <option value="id_proof">ID Proof</option>
                                <option value="address_proof">Address Proof</option>
                                <option value="photo">Photo</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">File *</label>
                            <input type="file" name="document_file" required class="mt-1 block w-full text-sm text-gray-500">
                            <p class="mt-1 text-xs text-gray-500">Max: 10MB</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showTeacherTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.teacher-tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Remove active class from all buttons
    document.querySelectorAll('.teacher-tab-button').forEach(button => {
        button.classList.remove('border-primary-500', 'text-primary-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab
    document.getElementById('teacher-content-' + tabName).classList.remove('hidden');

    // Add active class to selected button
    const activeButton = document.getElementById('teacher-tab-' + tabName);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-primary-500', 'text-primary-600');
}
</script>
@endsection

