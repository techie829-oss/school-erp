@extends('tenant.layouts.admin')

@section('title', 'Bulk Attendance Operations')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Bulk Operations</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Bulk Attendance Operations
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Mark attendance for multiple students across date ranges
            </p>
        </div>
        <div class="mt-4 md:mt-0">
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

    <!-- Bulk Mark Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Mark Attendance</h3>
        <form method="POST" action="{{ url('/admin/attendance/students/bulk-save') }}" id="bulkForm">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Date From *</label>
                        <input type="date" name="date_from" id="date_from" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Date To *</label>
                        <input type="date" name="date_to" id="date_to" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="bulk_class_id" class="block text-sm font-medium text-gray-700">Class *</label>
                        <select name="class_id" id="bulk_class_id" required onchange="loadStudents()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="bulk_section_id" class="block text-sm font-medium text-gray-700">Section</label>
                        <select name="section_id" id="bulk_section_id" onchange="loadStudents()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">All Sections</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" data-class-id="{{ $section->class_id }}">
                                    {{ $section->schoolClass->class_name ?? 'Class' }} - {{ $section->section_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="bulk_status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="bulk_status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="half_day">Half Day</option>
                        <option value="on_leave">On Leave</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Students *</label>
                    <div class="border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto" id="studentsContainer">
                        <p class="text-sm text-gray-500">Select class and section to load students</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Bulk Attendance
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function loadStudents() {
    const classId = document.getElementById('bulk_class_id').value;
    const sectionId = document.getElementById('bulk_section_id').value;
    const container = document.getElementById('studentsContainer');

    if (!classId) {
        container.innerHTML = '<p class="text-sm text-gray-500">Select class to load students</p>';
        return;
    }

    container.innerHTML = '<p class="text-sm text-gray-500">Loading students...</p>';

    fetch(`/admin/attendance/students/bulk/students?class_id=${classId}${sectionId ? '&section_id=' + sectionId : ''}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.students && data.students.length > 0) {
            let html = '<div class="space-y-2">';
            data.students.forEach(student => {
                html += `
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="student_ids[]" value="${student.id}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-900">${student.full_name} (${student.admission_number})</span>
                    </label>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="text-sm text-gray-500">No students found</p>';
        }
    })
    .catch(error => {
        container.innerHTML = '<p class="text-sm text-red-500">Error loading students</p>';
    });
}

// Filter sections by class
document.getElementById('bulk_class_id').addEventListener('change', function() {
    const classId = this.value;
    const sectionSelect = document.getElementById('bulk_section_id');
    const options = sectionSelect.querySelectorAll('option[data-class-id]');
    
    options.forEach(option => {
        if (!classId || option.getAttribute('data-class-id') === classId) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
    
    if (classId) {
        loadStudents();
    }
});
</script>
@endsection

