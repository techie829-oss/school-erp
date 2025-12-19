{{-- @var $student \App\Models\Student --}}
{{-- @var $tenant \App\Models\Tenant --}}
{{-- @var $classes \Illuminate\Support\Collection<\App\Models\SchoolClass> --}}
{{-- @var $sections \Illuminate\Support\Collection<\App\Models\Section> --}}

@extends('tenant.layouts.admin')

@section('title', 'Student Profile')

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
                    <a href="{{ url('/admin/students') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Students</a>
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
            <nav class="-mb-px flex space-x-4 sm:space-x-8 px-4 sm:px-6 overflow-x-auto" aria-label="Tabs">
                <button onclick="showStudentTab('overview')" id="student-tab-overview" class="student-tab-button border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Overview
                </button>
                <button onclick="showStudentTab('academic')" id="student-tab-academic" class="student-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Academic History
                </button>
                <button onclick="showStudentTab('exam-results')" id="student-tab-exam-results" class="student-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Exam Results ({{ $examResults->count() }})
                </button>
                <button onclick="showStudentTab('documents')" id="student-tab-documents" class="student-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Documents ({{ $student->documents->count() }})
                </button>
                @if(isset($featureSettings['attendance']) && $featureSettings['attendance'] === true)
                <button onclick="showStudentTab('attendance')" id="student-tab-attendance" class="student-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Attendance
                </button>
                @endif
                @if(isset($featureSettings['fees']) && $featureSettings['fees'] === true)
                <button onclick="showStudentTab('fees')" id="student-tab-fees" class="student-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Fee Card
                </button>
                @endif
                <button onclick="showStudentTab('actions')" id="student-tab-actions" class="student-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Actions
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
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-medium text-gray-900">Academic Progression (Class Enrollments)</h4>
                <span class="text-xs text-gray-500">{{ $student->enrollments->count() }} enrollment(s) total</span>
            </div>
            @if($student->enrollments->count() > 0)
                <div class="space-y-4">
                    @foreach($student->enrollments as $enrollment)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $enrollment->is_current ? 'border-primary-300 bg-primary-50' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h5 class="text-sm font-medium text-gray-900">
                                        {{ $enrollment->schoolClass->class_name }}
                                        @if($enrollment->section)
                                            - Section {{ $enrollment->section->section_name }}
                                        @endif
                                    </h5>
                                    @if($enrollment->roll_number)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Roll: {{ $enrollment->roll_number }}
                                        </span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-500">Academic Year:</span>
                                        <span class="text-gray-900 font-medium">{{ $enrollment->academic_year }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Enrollment Date:</span>
                                        <span class="text-gray-900 font-medium">{{ $enrollment->enrollment_date?->format('M d, Y') ?? 'Not specified' }}</span>
                                    </div>
                                </div>
                                <div class="mt-2 p-2 bg-gray-50 rounded border">
                                    <p class="text-xs text-gray-600">
                                        <span class="font-medium">Duration:</span>
                                        @if($enrollment->start_date)
                                            {{ $enrollment->start_date->format('M d, Y') }}
                                            @if($enrollment->end_date)
                                                <span class="text-gray-500">to</span> {{ $enrollment->end_date->format('M d, Y') }}
                                                @php
                                                    $duration = $enrollment->start_date->diffInDays($enrollment->end_date);
                                                @endphp
                                                <span class="text-gray-500">({{ $duration }} days)</span>
                                            @else
                                                <span class="text-gray-500">to</span> <span class="text-green-600 font-medium">Present</span>
                                                @php
                                                    $duration = $enrollment->start_date->diffInDays(now());
                                                @endphp
                                                <span class="text-gray-500">({{ $duration }} days so far)</span>
                                            @endif
                                        @else
                                            <span class="text-gray-500">Duration not specified</span>
                                        @endif
                                    </p>
                                </div>
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

        <!-- Tab Content: Exam Results -->
        <div id="student-content-exam-results" class="student-tab-content hidden p-6">
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Exam Results</h4>

                @if($examResults->count() > 0)
                <div class="space-y-6">
                    @foreach($examResults as $examId => $results)
                    @php
                        $exam = $results->first()->exam;
                        $totalMarks = $results->sum('marks_obtained');
                        $maxMarks = $results->sum('max_marks');
                        $overallPercentage = $maxMarks > 0 ? round(($totalMarks / $maxMarks) * 100, 2) : 0;
                        $subjectsPassed = $results->where('status', 'pass')->count();
                        $subjectsFailed = $results->where('status', 'fail')->count();
                        $subjectsAbsent = $results->where('status', 'absent')->orWhere('is_absent', true)->count();
                    @endphp
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h5 class="text-base font-semibold text-gray-900">{{ $exam->exam_name }}</h5>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ ucfirst(str_replace('_', ' ', $exam->exam_type)) }}
                                    @if($exam->academic_year)
                                        • {{ $exam->academic_year }}
                                    @endif
                                    @if($exam->start_date && $exam->end_date)
                                        • {{ \Carbon\Carbon::parse($exam->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($exam->end_date)->format('M d, Y') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-primary-600">{{ $overallPercentage }}%</div>
                                <div class="text-xs text-gray-500">Overall</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-green-600">{{ $subjectsPassed }}</div>
                                <div class="text-xs text-gray-500">Passed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-red-600">{{ $subjectsFailed }}</div>
                                <div class="text-xs text-gray-500">Failed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-600">{{ $subjectsAbsent }}</div>
                                <div class="text-xs text-gray-500">Absent</div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Marks</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Max</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">%</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Grade</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($results as $result)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $result->subject->subject_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $result->is_absent ? '-' : number_format($result->marks_obtained, 2) }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ $result->max_marks }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $result->is_absent ? '-' : number_format($result->percentage, 2) }}%
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $result->grade ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center">
                                            @if($result->is_absent)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Absent</span>
                                            @elseif($result->status == 'pass')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Pass</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Fail</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-2 text-sm font-semibold text-gray-900">Total</td>
                                        <td class="px-4 py-2 text-sm font-semibold text-center text-gray-900">{{ number_format($totalMarks, 2) }}</td>
                                        <td class="px-4 py-2 text-sm font-semibold text-center text-gray-900">{{ $maxMarks }}</td>
                                        <td class="px-4 py-2 text-sm font-semibold text-center text-primary-600">{{ $overallPercentage }}%</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex space-x-4">
                                @php
                                    $admitCard = $admitCards->where('exam_id', $exam->id)->first();
                                    $reportCard = $reportCards->where('exam_id', $exam->id)->first();
                                @endphp
                                @if($admitCard)
                                <a href="{{ url('/admin/examinations/admit-cards/' . $admitCard->id . '/print') }}" target="_blank" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    View Admit Card
                                </a>
                                @endif
                                @if($reportCard)
                                <a href="{{ url('/admin/examinations/report-cards/' . $reportCard->id . '/print') }}" target="_blank" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    View Report Card
                                </a>
                                @endif
                            </div>
                            <a href="{{ url('/admin/examinations/exams/' . $exam->id) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                View Exam Details →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No exam results</h3>
                    <p class="mt-1 text-sm text-gray-500">This student hasn't taken any exams yet.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Tab Content: Documents -->
        <div id="student-content-documents" class="student-tab-content hidden p-6">
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Uploaded Documents</h4>
                @if($student->documents->count() > 0)
                    <div class="space-y-3">
                        @foreach($student->documents as $document)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900">{{ $document->document_name }}</h5>
                                    <p class="text-xs text-gray-500">{{ $document->document_type_label }}</p>
                                    <p class="text-xs text-gray-400">{{ $document->formatted_file_size }} • Uploaded {{ $document->uploaded_at->format('d M, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ $document->file_url }}" target="_blank" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View
                                </a>
                                <form action="{{ url('/admin/student-documents/' . $document->id) }}" method="POST" onsubmit="return confirm('Delete this document?')">
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
                <form action="{{ url('/admin/students/' . $student->id . '/documents') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Name *</label>
                            <input type="text" name="document_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Type *</label>
                            <select name="document_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="birth_certificate">Birth Certificate</option>
                                <option value="id_proof">ID Proof</option>
                                <option value="address_proof">Address Proof</option>
                                <option value="previous_marksheet">Previous Marksheet</option>
                                <option value="transfer_certificate">Transfer Certificate</option>
                                <option value="medical_certificate">Medical Certificate</option>
                                <option value="photo">Photo</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">File *</label>
                            <input type="file" name="document_file" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            <p class="mt-1 text-xs text-gray-500">Max: 10MB</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab Content: Attendance Calendar -->
        @if(isset($featureSettings['attendance']) && $featureSettings['attendance'] === true)
        <div id="student-content-attendance" class="student-tab-content hidden p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Attendance Calendar</h4>
                    <p class="text-xs text-gray-500">
                        Month-wise view of this student's attendance. Click arrows to change month.
                    </p>
                </div>
                @php
                    $currentMonth = $month;
                    $currentYear = $year;
                    $prev = \Carbon\Carbon::create($year, $month, 1)->subMonth();
                    $next = \Carbon\Carbon::create($year, $month, 1)->addMonth();
                @endphp
                <div class="inline-flex items-center space-x-2">
                    <a href="{{ url('/admin/students/' . $student->id . '?attendance_month=' . $prev->month . '&attendance_year=' . $prev->year) }}"
                       class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-md text-xs text-gray-700 bg-white hover:bg-gray-50">
                        ‹ {{ $prev->format('M Y') }}
                    </a>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                    </span>
                    <a href="{{ url('/admin/students/' . $student->id . '?attendance_month=' . $next->month . '&attendance_year=' . $next->year) }}"
                       class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-md text-xs text-gray-700 bg-white hover:bg-gray-50">
                        {{ $next->format('M Y') }} ›
                    </a>
                </div>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 mb-4">
                <div class="bg-green-50 border border-green-200 rounded-md p-2 text-center">
                    <div class="text-[11px] text-gray-600">Present</div>
                    <div class="text-sm font-semibold text-green-700">{{ $summary['present'] }}</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-md p-2 text-center">
                    <div class="text-[11px] text-gray-600">Absent</div>
                    <div class="text-sm font-semibold text-red-700">{{ $summary['absent'] }}</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-2 text-center">
                    <div class="text-[11px] text-gray-600">Late</div>
                    <div class="text-sm font-semibold text-yellow-700">{{ $summary['late'] }}</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-md p-2 text-center">
                    <div class="text-[11px] text-gray-600">Half Day</div>
                    <div class="text-sm font-semibold text-blue-700">{{ $summary['half_day'] }}</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-md p-2 text-center">
                    <div class="text-[11px] text-gray-600">Leave</div>
                    <div class="text-sm font-semibold text-purple-700">{{ $summary['on_leave'] }}</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-md p-2 text-center">
                    <div class="text-[11px] text-gray-600">Marked Days</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $summary['total_marked'] }}</div>
                </div>
            </div>

            @php
                $firstOfMonth = \Carbon\Carbon::create($year, $month, 1);
                $startWeekDay = (int) $firstOfMonth->dayOfWeek; // 0 (Sun) .. 6 (Sat)
                $daysInMonth = count($attendanceCalendar);
                $dayIndex = 0;
            @endphp

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <table class="min-w-full border-collapse text-[11px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-2 py-1 text-center font-medium text-gray-600">Sun</th>
                            <th class="px-2 py-1 text-center font-medium text-gray-600">Mon</th>
                            <th class="px-2 py-1 text-center font-medium text-gray-600">Tue</th>
                            <th class="px-2 py-1 text-center font-medium text-gray-600">Wed</th>
                            <th class="px-2 py-1 text-center font-medium text-gray-600">Thu</th>
                            <th class="px-2 py-1 text-center font-medium text-gray-600">Fri</th>
                            <th class="px-2 py-1 text-center font-medium text-gray-600">Sat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentDay = 1; @endphp
                        @while($currentDay <= $daysInMonth)
                            <tr>
                                @for($weekDay = 0; $weekDay < 7; $weekDay++)
                                    @php
                                        $cellContent = null;
                                        if ($currentDay === 1 && $weekDay < $startWeekDay) {
                                            // Empty cell before the first day of month
                                            $cellContent = null;
                                        } elseif ($currentDay <= $daysInMonth) {
                                            $cellContent = $attendanceCalendar[$currentDay - 1];
                                            $currentDay++;
                                        }
                                    @endphp
                                    <td class="h-16 border border-gray-100 align-top px-1 py-1 text-center">
                                        @if($cellContent)
                                            @php
                                                $status = $cellContent['status'];
                                                $color = $cellContent['color'];
                                                $badgeClass = match($color) {
                                                    'green' => 'bg-green-100 text-green-800',
                                                    'red' => 'bg-red-100 text-red-800',
                                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                                    'blue' => 'bg-blue-100 text-blue-800',
                                                    'purple' => 'bg-purple-100 text-purple-800',
                                                    'gray' => 'bg-gray-100 text-gray-600',
                                                    default => 'bg-gray-100 text-gray-600',
                                                };
                                                $label = $status ? ucfirst(str_replace('_', ' ', $status)) : '—';
                                            @endphp
                                            <div class="flex flex-col h-full items-center justify-start">
                                                <span class="text-[11px] font-semibold text-gray-800">
                                                    {{ $cellContent['day'] }}
                                                </span>
                                                <span class="mt-1 inline-flex px-1.5 py-0.5 rounded-full {{ $badgeClass }}">
                                                    {{ $label }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endwhile
                    </tbody>
                </table>
            </div>

            <p class="mt-2 text-[11px] text-gray-500">
                Note: Only days with attendance marked are counted. Unmarked days are shown as “—”.
            </p>
        </div>
        @endif

        <!-- Tab Content: Fee Card -->
        @if(isset($featureSettings['fees']) && $featureSettings['fees'] === true)
        <div id="student-content-fees" class="student-tab-content hidden p-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Student Fee Management</h4>
                <p class="text-sm text-gray-600 mb-4">View detailed fee cards, payment history, and apply discounts</p>
                <a href="{{ url('/admin/fees/cards/' . $student->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Complete Fee Card
                </a>
            </div>

            @php
                $feeCards = $student->studentFeeCard ? [$student->studentFeeCard] : [];
            @endphp

            @if(count($feeCards) > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total Amount</div>
                        <div class="text-2xl font-bold text-gray-900">₹{{ number_format($student->studentFeeCard->total_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Paid Amount</div>
                        <div class="text-2xl font-bold text-emerald-600">₹{{ number_format($student->studentFeeCard->paid_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Balance Due</div>
                        <div class="text-2xl font-bold text-red-600">₹{{ number_format($student->studentFeeCard->balance_amount ?? 0, 2) }}</div>
                    </div>
                </div>

                <div class="flex gap-3">
                    @if(($student->studentFeeCard->balance_amount ?? 0) > 0)
                        <a href="{{ url('/admin/fees/collection/' . $student->id . '/collect') }}"
                           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Collect Payment
                        </a>
                    @endif
                    <a href="{{ url('/admin/fees/cards/' . $student->id . '/print') }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Fee Card
                    </a>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">No Fee Card Assigned</h3>
                            <p class="mt-1 text-sm text-yellow-700">This student has not been assigned to any fee plan yet. Please assign a fee plan to generate the fee card.</p>
                            <div class="mt-3">
                                <a href="{{ url('/admin/fees/plans') }}" class="text-sm font-medium text-yellow-800 hover:text-yellow-900">
                                    Go to Fee Plans →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endif

        <!-- Tab Content: Actions -->
        <div id="student-content-actions" class="student-tab-content hidden p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Promote Student -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            Promote Student
                        </h4>
                        <p class="mt-1 text-sm text-gray-600">Promote student to next class for new academic year</p>
                    </div>
                    <form action="{{ url('/admin/students/' . $student->id . '/promote') }}" method="POST" class="p-6">
                        @csrf
                        <div class="space-y-4">
                            @if($student->currentEnrollment)
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm text-blue-800">
                                        <strong>Currently enrolled in:</strong> {{ $student->currentEnrollment->schoolClass->class_name }}
                                        {{ $student->currentEnrollment->section ? '- Section ' . $student->currentEnrollment->section->section_name : '' }}
                                    </p>
                                </div>
                            @else
                                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800">Student is not currently enrolled in any class</p>
                                </div>
                            @endif

                            <div>
                                <label for="promote_to_class_id" class="block text-sm font-medium text-gray-700">Promote To Class *</label>
                                <select name="to_class_id" id="promote_to_class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Class</option>
                                    @foreach(is_iterable($classes ?? null) ? $classes : [] as $class)
                                        {{-- @var $class \App\Models\SchoolClass --}}
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="promote_to_section_id" class="block text-sm font-medium text-gray-700">Section</label>
                                <select name="to_section_id" id="promote_to_section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Section</option>
                                    @foreach(is_iterable($sections ?? null) ? $sections : [] as $section)
                                        {{-- @var $section \App\Models\Section --}}
                                        <option value="{{ $section->id }}" data-class-id="{{ $section->class_id }}">
                                            {{ $section->schoolClass->class_name }} - {{ $section->section_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="promote_academic_year" class="block text-sm font-medium text-gray-700">Academic Year *</label>
                                    <input type="text" name="academic_year" id="promote_academic_year" required placeholder="2025-2026" value="{{ date('Y') }}-{{ date('Y') + 1 }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="promote_roll_number" class="block text-sm font-medium text-gray-700">New Roll Number</label>
                                    <input type="text" name="roll_number" id="promote_roll_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="promote_percentage" class="block text-sm font-medium text-gray-700">Percentage</label>
                                    <input type="number" name="percentage" id="promote_percentage" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="promote_grade" class="block text-sm font-medium text-gray-700">Grade</label>
                                    <input type="text" name="grade" id="promote_grade" maxlength="10" placeholder="A+" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="promote_remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                                <textarea name="remarks" id="promote_remarks" rows="2" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                            </div>

                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Promote Student
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Update Academic Status -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Update Academic Status
                        </h4>
                        <p class="mt-1 text-sm text-gray-600">Change overall student status (Active, Alumni, Transferred, etc.)</p>
                    </div>
                    <form action="{{ url('/admin/students/' . $student->id . '/update-status') }}" method="POST" class="p-6">
                        @csrf
                        <div class="space-y-4">
                            <div class="p-3 bg-{{ $student->is_active ? 'green' : 'red' }}-50 border border-{{ $student->is_active ? 'green' : 'red' }}-200 rounded-lg">
                                <p class="text-sm text-{{ $student->is_active ? 'green' : 'red' }}-800">
                                    <strong>Current Status:</strong> {{ ucfirst(str_replace('_', ' ', $student->overall_status)) }}
                                    ({{ $student->is_active ? 'Active' : 'Inactive' }})
                                </p>
                            </div>

                            <div>
                                <label for="overall_status" class="block text-sm font-medium text-gray-700">Overall Status *</label>
                                <select name="overall_status" id="overall_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="active" {{ $student->overall_status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="alumni" {{ $student->overall_status == 'alumni' ? 'selected' : '' }}>Alumni (Graduated)</option>
                                    <option value="transferred" {{ $student->overall_status == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="dropped_out" {{ $student->overall_status == 'dropped_out' ? 'selected' : '' }}>Dropped Out</option>
                                </select>
                            </div>

                            <div>
                                <label for="is_active" class="block text-sm font-medium text-gray-700">Active Status *</label>
                                <select name="is_active" id="is_active" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="1" {{ $student->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$student->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div>
                                <label for="status_remarks" class="block text-sm font-medium text-gray-700">Status Remarks</label>
                                <textarea name="status_remarks" id="status_remarks" rows="3" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ $student->status_remarks }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Explain the reason for this status change</p>
                            </div>

                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-xs text-yellow-800">
                                    <strong>Note:</strong> Marking student as Inactive or changing status to Alumni/Transferred/Dropped Out will automatically end their current enrollment.
                                </p>
                            </div>

                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Complete Current Enrollment -->
                @if($student->currentEnrollment)
                <div class="border border-gray-200 rounded-lg lg:col-span-2">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Complete Current Enrollment
                        </h4>
                        <p class="mt-1 text-sm text-gray-600">Mark the current enrollment as completed with result (Passed, Failed, etc.) without promoting</p>
                    </div>
                    <form action="{{ url('/admin/students/' . $student->id . '/complete-enrollment') }}" method="POST" class="p-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="result" class="block text-sm font-medium text-gray-700">Result *</label>
                                <select name="result" id="result" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Result</option>
                                    <option value="passed">Passed</option>
                                    <option value="failed">Failed</option>
                                    <option value="transferred">Transferred</option>
                                    <option value="dropped">Dropped Out</option>
                                </select>
                            </div>

                            <div>
                                <label for="complete_percentage" class="block text-sm font-medium text-gray-700">Percentage</label>
                                <input type="number" name="percentage" id="complete_percentage" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="complete_grade" class="block text-sm font-medium text-gray-700">Grade</label>
                                <input type="text" name="grade" id="complete_grade" maxlength="10" placeholder="A+" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="complete_remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                                <input type="text" name="remarks" id="complete_remarks" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Complete Enrollment
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
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

    // Dependent sections dropdown in Promote Student form
    const classSelect = document.getElementById('promote_to_class_id');
    const sectionSelect = document.getElementById('promote_to_section_id');
    if (classSelect && sectionSelect) {
        const allSectionOptions = Array.from(sectionSelect.options);

        function filterSectionsByClass() {
            const selectedClassId = classSelect.value;
            const firstOption = allSectionOptions[0];

            sectionSelect.innerHTML = '';
            sectionSelect.appendChild(firstOption.cloneNode(true));

            allSectionOptions.slice(1).forEach(function(option) {
                const optionClassId = option.getAttribute('data-class-id');
                if (!selectedClassId || optionClassId === selectedClassId) {
                    sectionSelect.appendChild(option.cloneNode(true));
                }
            });
        }

        classSelect.addEventListener('change', filterSectionsByClass);
        filterSectionsByClass();
    }
});
</script>
@endsection

