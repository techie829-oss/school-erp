@extends('tenant.layouts.admin')

@section('title', 'Mark Teacher Attendance')

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
                    <a href="{{ url('/admin/attendance/teachers') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Teacher Attendance</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Mark Attendance</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Mark Teacher Attendance
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Record daily attendance for teaching staff
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/attendance/teachers') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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
                @if($errors->any())
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Selection Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ url('/admin/attendance/teachers/mark') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Date *</label>
                <input type="date" name="date" id="date" value="{{ $date }}" max="{{ date('Y-m-d') }}" required
                    onchange="this.form.submit()"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            @if($departments->count() > 0)
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700">Department (Optional)</label>
                <select name="department_id" id="department_id" onchange="this.form.submit()"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
        </form>
    </div>

    <!-- Attendance Form -->
    @if($teachers->count() > 0)
    <form action="{{ url('/admin/attendance/teachers/save') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="bg-white shadow rounded-lg p-6">
            <!-- Quick Actions -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    Teachers: {{ $teachers->count() }}
                </h3>
                <button type="button" onclick="markAllPresent()" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    ✓ Mark All Present
                </button>
            </div>

            <!-- Teachers List -->
            <div class="space-y-3">
                @foreach($teachers as $index => $teacher)
                @php
                    $attendance = $existingAttendance->get($teacher->id);
                    $defaultStatus = $attendance ? $attendance->status : 'present';
                @endphp
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <input type="hidden" name="attendance[{{ $index }}][teacher_id]" value="{{ $teacher->id }}">

                    <div class="flex items-center space-x-4 flex-1">
                        <!-- Photo -->
                        <div class="flex-shrink-0">
                            @if($teacher->photo)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-medium">
                                    {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Name & Department -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $teacher->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $teacher->department->department_name ?? 'No Department' }} • {{ $teacher->employee_id }}</p>
                        </div>

                        <!-- Status Select -->
                        <div class="w-32">
                            <select name="attendance[{{ $index }}][status]"
                                class="teacher-status block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                onchange="toggleTimeInputs(this, {{ $index }})">
                                <option value="present" {{ $defaultStatus == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ $defaultStatus == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ $defaultStatus == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="half_day" {{ $defaultStatus == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                <option value="on_leave" {{ $defaultStatus == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                            </select>
                        </div>

                        <!-- Check-in -->
                        <div class="w-28 time-input-{{ $index }}" id="time-container-{{ $index }}">
                            <input type="time" name="attendance[{{ $index }}][check_in_time]"
                                value="{{ $attendance?->check_in_time ?? substr($settings->school_start_time, 0, 5) }}"
                                class="time-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xs">
                        </div>

                        <!-- Check-out -->
                        <div class="w-28 time-input-{{ $index }}">
                            <input type="time" name="attendance[{{ $index }}][check_out_time]"
                                value="{{ $attendance?->check_out_time ?? substr($settings->school_end_time, 0, 5) }}"
                                class="time-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xs">
                        </div>

                        <!-- Remarks -->
                        <div class="w-40">
                            <input type="text" name="attendance[{{ $index }}][remarks]"
                                value="{{ $attendance?->remarks ?? '' }}"
                                placeholder="Remarks"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xs">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ url('/admin/attendance/teachers') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Attendance ({{ $teachers->count() }} teachers)
                </button>
            </div>
        </div>
    </form>
    @else
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No Teachers Found</h3>
        <p class="mt-1 text-sm text-gray-500">Add teachers to start marking attendance.</p>
    </div>
    @endif
</div>

<script>
function markAllPresent() {
    document.querySelectorAll('.teacher-status').forEach((select, index) => {
        select.value = 'present';
        toggleTimeInputs(select, index);
    });
}

function toggleTimeInputs(selectElement, index) {
    const status = selectElement.value;
    const timeInputs = document.querySelectorAll('.time-input-' + index);

    // Hide time inputs for absent, on_leave, and holiday
    if (['absent', 'on_leave', 'holiday'].includes(status)) {
        timeInputs.forEach(container => {
            container.style.display = 'none';
            // Clear the time input values
            const input = container.querySelector('.time-input');
            if (input) input.value = '';
        });
    } else {
        timeInputs.forEach(container => {
            container.style.display = 'block';
            // Set default times if empty
            const input = container.querySelector('.time-input');
            if (input && !input.value) {
                if (input.name.includes('check_in')) {
                    input.value = '09:00';
                } else {
                    input.value = '17:00';
                }
            }
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get school timings from settings
    const schoolStartTime = '{{ substr($settings->school_start_time ?? "09:00:00", 0, 5) }}';
    const schoolEndTime = '{{ substr($settings->school_end_time ?? "17:00:00", 0, 5) }}';

    document.querySelectorAll('.teacher-status').forEach((select, index) => {
        toggleTimeInputs(select, index);
    });

    // Update default times in toggleTimeInputs function
    window.schoolStartTime = schoolStartTime;
    window.schoolEndTime = schoolEndTime;
});

// Update the toggleTimeInputs to use dynamic times
function toggleTimeInputs(selectElement, index) {
    const status = selectElement.value;
    const timeInputs = document.querySelectorAll('.time-input-' + index);
    const schoolStart = window.schoolStartTime || '09:00';
    const schoolEnd = window.schoolEndTime || '17:00';

    // Hide time inputs for absent, on_leave, and holiday
    if (['absent', 'on_leave', 'holiday'].includes(status)) {
        timeInputs.forEach(container => {
            container.style.display = 'none';
            // Clear the time input values
            const input = container.querySelector('.time-input');
            if (input) input.value = '';
        });
    } else {
        timeInputs.forEach(container => {
            container.style.display = 'block';
            // Set default times if empty
            const input = container.querySelector('.time-input');
            if (input && !input.value) {
                if (input.name.includes('check_in')) {
                    input.value = schoolStart;
                } else {
                    input.value = schoolEnd;
                }
            }
        });
    }
}
</script>
@endsection

