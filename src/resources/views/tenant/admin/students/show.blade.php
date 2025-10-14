@extends('tenant.layouts.admin')

@section('title', 'Student Profile')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center">
                @if($student->photo)
                    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="h-16 w-16 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center border-2 border-gray-200">
                        <span class="text-primary-600 font-medium text-xl">
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <div class="ml-4">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {{ $student->full_name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $student->admission_number }} • {{ $student->currentEnrollment?->schoolClass?->class_name ?? 'Not Enrolled' }}{{ $student->currentEnrollment?->section ? ' - ' . $student->currentEnrollment->section->section_name : '' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/students/' . $student->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="{{ url('/admin/students') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    @php
        $statusColors = [
            'active' => 'bg-green-50 border-green-200 text-green-800',
            'alumni' => 'bg-gray-50 border-gray-200 text-gray-800',
            'transferred' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'dropped_out' => 'bg-red-50 border-red-200 text-red-800',
        ];
        $statusColor = $statusColors[$student->overall_status] ?? 'bg-gray-50 border-gray-200 text-gray-800';

        $enrollmentStatus = $student->currentEnrollment?->enrollment_status ?? 'Not enrolled';
    @endphp
    <div class="rounded-lg border-2 {{ $statusColor }} p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium">
                    Status: {{ ucfirst(str_replace('_', ' ', $student->overall_status)) }}
                    @if($student->currentEnrollment)
                        • Current Class: {{ ucfirst($enrollmentStatus) }}
                    @endif
                </h3>
                @if($student->status_remarks)
                    <p class="mt-1 text-sm opacity-75">{{ $student->status_remarks }}</p>
                @endif
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $student->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <dt class="text-sm font-medium text-gray-500 truncate">Age</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $student->age ?? 0 }} years</dd>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <dt class="text-sm font-medium text-gray-500 truncate">Roll Number</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $student->currentEnrollment?->roll_number ?? 'Not Assigned' }}</dd>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <dt class="text-sm font-medium text-gray-500 truncate">Academic Year</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $student->currentEnrollment?->academic_year ?? '-' }}</dd>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <dt class="text-sm font-medium text-gray-500 truncate">Documents</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $student->documents->count() }}</dd>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white shadow rounded-lg">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showStudentTab('overview')" id="student-tab-overview" class="student-tab-button border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Overview
                </button>
                <button onclick="showStudentTab('academic')" id="student-tab-academic" class="student-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Academic History
                </button>
                <button onclick="showStudentTab('documents')" id="student-tab-documents" class="student-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Documents ({{ $student->documents->count() }})
                </button>
            </nav>
        </div>

        <!-- Tab Content: Overview -->
        <div id="student-content-overview" class="student-tab-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Details -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Personal Details</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Full Name:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $student->full_name }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Date of Birth:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $student->date_of_birth?->format('d M, Y') }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Gender:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ ucfirst($student->gender) }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Blood Group:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $student->blood_group ?? 'Not specified' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Category:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ strtoupper($student->category) }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Contact Details -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Contact Details</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Email:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $student->email ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Phone:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $student->phone ?? 'Not provided' }}</dd>
                        </div>
                        @if($student->current_address)
                        <div class="py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500 mb-1">Current Address:</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ $student->current_address['address'] ?? '' }}<br>
                                {{ $student->current_address['city'] ?? '' }}, {{ $student->current_address['state'] ?? '' }} - {{ $student->current_address['pincode'] ?? '' }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Parent Details -->
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Parent/Guardian Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($student->father_name)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h5 class="text-xs font-medium text-gray-500 mb-2">Father</h5>
                            <p class="text-sm font-medium text-gray-900">{{ $student->father_name }}</p>
                            @if($student->father_phone)
                                <p class="text-sm text-gray-600">📞 {{ $student->father_phone }}</p>
                            @endif
                            @if($student->father_email)
                                <p class="text-sm text-gray-600">✉️ {{ $student->father_email }}</p>
                            @endif
                        </div>
                        @endif

                        @if($student->mother_name)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h5 class="text-xs font-medium text-gray-500 mb-2">Mother</h5>
                            <p class="text-sm font-medium text-gray-900">{{ $student->mother_name }}</p>
                            @if($student->mother_phone)
                                <p class="text-sm text-gray-600">📞 {{ $student->mother_phone }}</p>
                            @endif
                            @if($student->mother_email)
                                <p class="text-sm text-gray-600">✉️ {{ $student->mother_email }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Academic History (Enrollments) -->
        <div id="student-content-academic" class="student-tab-content hidden p-6">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Academic Progression (Class Enrollments)</h4>
            @if($student->enrollments->count() > 0)
                <div class="space-y-4">
                    @foreach($student->enrollments as $enrollment)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $enrollment->is_current ? 'border-primary-300 bg-primary-50' : '' }}">
                        <div class="flex items-start justify-between">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900">
                                    {{ $enrollment->schoolClass->class_name }}
                                    @if($enrollment->section)
                                        - Section {{ $enrollment->section->section_name }}
                                    @endif
                                    @if($enrollment->roll_number)
                                        (Roll: {{ $enrollment->roll_number }})
                                    @endif
                                </h5>
                                <p class="text-sm text-gray-500">{{ $enrollment->academic_year }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $enrollment->start_date->format('M d, Y') }}
                                    @if($enrollment->end_date)
                                        - {{ $enrollment->end_date->format('M d, Y') }}
                                    @else
                                        - Present
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                @if($enrollment->is_current)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Current
                                    </span>
                                @elseif($enrollment->result)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $enrollment->result == 'promoted' || $enrollment->result == 'passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($enrollment->result) }}
                                    </span>
                                @endif
                                @if($enrollment->percentage)
                                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $enrollment->percentage }}%</p>
                                @endif
                                @if($enrollment->grade)
                                    <p class="text-xs text-gray-500">Grade: {{ $enrollment->grade }}</p>
                                @endif
                            </div>
                        </div>
                        @if($enrollment->remarks)
                            <p class="mt-2 text-sm text-gray-600 italic">{{ $enrollment->remarks }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500">No enrollment history available</p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Documents -->
        <div id="student-content-documents" class="student-tab-content hidden p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-medium text-gray-900">Documents</h4>
                <button class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    + Upload Document
                </button>
            </div>

            @if($student->documents->count() > 0)
                <div class="space-y-3">
                    @foreach($student->documents as $document)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $document->document_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $document->document_type_label }} • {{ $document->formatted_file_size }} • Uploaded {{ $document->uploaded_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ $document->file_url }}" target="_blank" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No documents uploaded yet</p>
                    <button class="mt-3 text-sm text-primary-600 hover:text-primary-700 font-medium">
                        Upload First Document
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function showStudentTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.student-tab-content').forEach(el => el.classList.add('hidden'));

    // Remove active class from all buttons
    document.querySelectorAll('.student-tab-button').forEach(el => {
        el.classList.remove('border-primary-500', 'text-primary-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab
    document.getElementById('student-content-' + tabName).classList.remove('hidden');

    // Add active class to selected button
    const activeButton = document.getElementById('student-tab-' + tabName);
    activeButton.classList.add('border-primary-500', 'text-primary-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');

    // Save active tab to localStorage
    localStorage.setItem('student_profile_active_tab', tabName);
}

// On page load, restore last active tab
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = localStorage.getItem('student_profile_active_tab');
    if (savedTab && document.getElementById('student-content-' + savedTab)) {
        showStudentTab(savedTab);
    }
});
</script>
@endsection

