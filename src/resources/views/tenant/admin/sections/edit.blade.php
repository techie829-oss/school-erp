@extends('tenant.layouts.admin')

@section('title', 'Edit Section')

@section('content')
{{-- @var $section \App\Models\Section --}}
{{-- @var $classes \Illuminate\Support\Collection<\App\Models\SchoolClass> --}}
{{-- @var $teachers \Illuminate\Support\Collection<\App\Models\User> --}}
{{-- @var $tenant \App\Models\Tenant --}}
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
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
                    <a href="{{ url('/admin/classes') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Classes</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/classes/' . $section->schoolClass->id) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">{{ $section->schoolClass->class_name }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Section</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Section</h1>
        <p class="mt-1 text-sm text-gray-500">Update section information</p>
    </div>

    <form action="{{ url('/admin/sections/' . $section->id) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <div class="text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-4 sm:p-6 space-y-4 sm:space-y-6">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id', $section->class_id) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="section_name" class="block text-sm font-medium text-gray-700">Section Name <span class="text-red-500">*</span></label>
                <input type="text" name="section_name" id="section_name" value="{{ old('section_name', $section->section_name) }}" required maxlength="25"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Maximum 25 characters</p>
            </div>

            <div>
                <label for="group_name" class="block text-sm font-medium text-gray-700">Group Name <span class="text-gray-500 text-xs font-normal">(Optional)</span></label>
                <input type="text" name="group_name" id="group_name" value="{{ old('group_name', $section->group_name) }}" placeholder="e.g., Science Group, Commerce Group"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Optional group name to organize sections (e.g., Science Group, Commerce Group)</p>
            </div>

            <div>
                <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number</label>
                <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $section->room_number) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $section->capacity) }}" min="1" max="200"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <div>
                <label for="class_teacher_id" class="block text-sm font-medium text-gray-700">Class Teacher</label>
                <select name="class_teacher_id" id="class_teacher_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">No Class Teacher</option>
                    @foreach($teachers as $teacher)
                        @php
                            $assignments = $teacherAssignments[$teacher->id] ?? [];
                            $hasAssignments = !empty($assignments);
                        @endphp
                        <option value="{{ $teacher->id }}" {{ old('class_teacher_id', $section->class_teacher_id) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->full_name }}
                            @if($teacher->employee_id)
                                ({{ $teacher->employee_id }})
                            @endif
                            @if($teacher->department)
                                - {{ $teacher->department->department_name }}
                            @endif
                            @if($hasAssignments)
                                - Already: {{ implode(', ', $assignments) }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">All active teachers are shown. Teachers can be assigned to multiple sections/classes. Current assignments are shown after teacher name.</p>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $section->is_active) ? 'checked' : '' }}
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
            </div>

            <!-- Assigned Subjects -->
            @if($allowSectionWiseAssignment ?? true)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Section-Specific Subjects <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                </label>
                <p class="text-xs text-gray-500 mb-3">Select additional subjects specific to this section. Common subjects from the class are automatically included. This will help filter subjects when creating exam schedules and timetables.</p>
                <div class="border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto bg-gray-50">
                    @if(isset($subjects) && $subjects->count() > 0)
                        <div class="space-y-2">
                            @foreach($subjects as $subject)
                                <label class="flex items-center p-2 hover:bg-white rounded cursor-pointer">
                                    <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                                        {{ in_array($subject->id, old('subjects', $assignedSubjectIds ?? [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">
                                        {{ $subject->subject_name }}
                                        @if($subject->subject_code)
                                            <span class="text-gray-500">({{ $subject->subject_code }})</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">No active subjects available. <a href="{{ url('/admin/subjects/create') }}" class="text-primary-600 hover:text-primary-700">Create a subject</a> first.</p>
                    @endif
                </div>
                <p class="mt-2 text-xs text-gray-500">Selected subjects will be available when creating exam schedules for this section.</p>
            </div>
            @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Subject Assignment
                </label>
                <div class="border border-gray-300 rounded-md p-4 bg-blue-50">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 font-medium mb-1">Student-wise Subject Assignment Enabled</p>
                            <p class="text-xs text-blue-700">
                                Subjects are assigned at the student level, not at the section level.
                                Please assign subjects individually to each student in the student management section.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ url('/admin/classes/' . $section->schoolClass->id) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Update Section
            </button>
        </div>
    </form>
</div>
@endsection

