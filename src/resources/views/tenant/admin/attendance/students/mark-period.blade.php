@extends('tenant.layouts.admin')

@section('title', 'Mark Period-wise Attendance')

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
                    <a href="{{ url('/admin/attendance/students') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Student Attendance</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Period-wise Attendance</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Mark Period-wise Attendance
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Record attendance by period/subject for detailed tracking
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-2">
            <a href="{{ url('/admin/attendance/students/mark') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Daily Attendance
            </a>
            <a href="{{ url('/admin/attendance/students') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') ?? 'There were errors with your submission' }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Selection Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ url('/admin/attendance/students/mark-period') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date *</label>
                    <input type="date" name="date" id="date" value="{{ $date }}" max="{{ date('Y-m-d') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class *</label>
                    <select name="class_id" id="class_id" required onchange="this.form.submit()"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700">Section <span class="text-gray-500 text-xs">(Optional)</span></label>
                    <select name="section_id" id="section_id" {{ !$classId ? 'disabled' : '' }} onchange="this.form.submit()"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Students (No Section)</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ $sectionId == $section->id ? 'selected' : '' }}>
                                {{ $section->section_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Leave empty to show all students in the class</p>
                </div>

                <div>
                    <label for="period_number" class="block text-sm font-medium text-gray-700">Period *</label>
                    <select name="period_number" id="period_number" required onchange="this.form.submit()"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @for($p = 1; $p <= 10; $p++)
                            <option value="{{ $p }}" {{ $periodNumber == $p ? 'selected' : '' }}>Period {{ $p }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                    <select name="subject_id" id="subject_id" onchange="this.form.submit()"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        @if($teacher)
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
            <p class="text-sm text-blue-800">
                <strong>Subject Teacher:</strong> {{ $teacher->full_name }}
            </p>
        </div>
        @endif
    </div>

    <!-- Attendance Form -->
    @if($students->count() > 0)
    <form action="{{ url('/admin/attendance/students/save-period') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="section_id" value="{{ $sectionId }}">
        <input type="hidden" name="period_number" value="{{ $periodNumber }}">
        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
        @if($teacher)
        <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <!-- Quick Actions -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    Students: {{ $students->count() }} | Period {{ $periodNumber }}@if($subjectId) - {{ $subjects->firstWhere('id', $subjectId)?->subject_name }}@endif
                </h3>
                <div class="flex space-x-2">
                    <button type="button" onclick="markAllStatus('present')" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        ✓ Mark All Present
                    </button>
                    <button type="button" onclick="markAllStatus('absent')" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        ✗ Mark All Absent
                    </button>
                </div>
            </div>

            <!-- Students List -->
            <div class="space-y-3">
                @foreach($students as $index => $student)
                @php
                    $attendance = $existingAttendance->get($student->id);
                    $defaultStatus = $attendance ? $attendance->status : 'present';
                @endphp
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">

                    <div class="flex items-center space-x-4 flex-1">
                        <!-- Roll Number -->
                        <div class="flex-shrink-0 w-12 text-center">
                            <span class="text-sm font-medium text-gray-900">{{ $student->currentEnrollment?->roll_number ?? '-' }}</span>
                        </div>

                        <!-- Photo -->
                        <div class="flex-shrink-0">
                            @if($student->photo)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $student->photo_url }}" alt="{{ $student->full_name }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-medium">
                                    {{ substr($student->first_name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Name -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $student->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $student->admission_number }}</p>
                        </div>

                        <!-- Status Select -->
                        <div class="w-40">
                            <select name="attendance[{{ $index }}][status]" class="attendance-status block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="present" {{ $defaultStatus == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ $defaultStatus == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ $defaultStatus == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="half_day" {{ $defaultStatus == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                <option value="on_leave" {{ $defaultStatus == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                            </select>
                        </div>

                        <!-- Remarks -->
                        <div class="w-48">
                            <input type="text" name="attendance[{{ $index }}][remarks]"
                                value="{{ $attendance?->remarks ?? '' }}"
                                placeholder="Remarks (optional)"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ url('/admin/attendance/students') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Period Attendance ({{ $students->count() }} students)
                </button>
            </div>
        </div>
    </form>
    @else
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Select Class and Section</h3>
        <p class="mt-1 text-sm text-gray-500">Choose a class and section from the dropdowns above to mark period-wise attendance.</p>
    </div>
    @endif
</div>

<script>
function markAllStatus(status) {
    document.querySelectorAll('.attendance-status').forEach(select => {
        select.value = status;
    });
}
</script>
@endsection

