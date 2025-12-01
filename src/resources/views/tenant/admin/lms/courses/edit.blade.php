@extends('tenant.layouts.admin')

@section('title', 'Edit Course')

@section('content')
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ url('/admin/dashboard') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ url('/admin/lms/courses') }}"
                            class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Courses</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Edit Course: {{ $course->course_name }}
                </h2>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form action="{{ url('/admin/lms/courses/' . $course->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-8 divide-y divide-gray-200 p-6">
                @csrf
                @method('PUT')
                <div class="space-y-8 divide-y divide-gray-200">
                    <div>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <!-- Course Name -->
                            <div class="sm:col-span-4">
                                <label for="course_name" class="block text-sm font-medium text-gray-700">Course Name <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input type="text" name="course_name" id="course_name"
                                        value="{{ old('course_name', $course->course_name) }}" required
                                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('course_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Course Code -->
                            <div class="sm:col-span-2">
                                <label for="course_code" class="block text-sm font-medium text-gray-700">Course Code</label>
                                <div class="mt-1">
                                    <input type="text" name="course_code" id="course_code"
                                        value="{{ old('course_code', $course->course_code) }}"
                                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('course_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Class -->
                            <div class="sm:col-span-2">
                                <label for="class_id" class="block text-sm font-medium text-gray-700">Class <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="class_id" name="class_id" required
                                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('class_id', $course->class_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('class_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div class="sm:col-span-2">
                                <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="subject_id" name="subject_id" required
                                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->subject_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('subject_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Teacher -->
                            <div class="sm:col-span-2">
                                <label for="teacher_id" class="block text-sm font-medium text-gray-700">Teacher <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="teacher_id" name="teacher_id" required
                                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Select Teacher</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}"
                                                {{ old('teacher_id', $course->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('teacher_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <div class="mt-1">
                                    <textarea id="description" name="description" rows="3"
                                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $course->description) }}</textarea>
                                </div>
                            </div>

                            <!-- Course Image -->
                            <div class="sm:col-span-6">
                                <label for="course_image" class="block text-sm font-medium text-gray-700">Course
                                    Image</label>
                                @if ($course->course_image)
                                    <div class="mt-2 mb-4">
                                        <img src="{{ $course->course_image_url }}" alt="Current Image"
                                            class="h-32 w-32 object-cover rounded-md">
                                    </div>
                                @endif
                                <div
                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="course_image"
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                                <span>Upload a file</span>
                                                <input id="course_image" name="course_image" type="file"
                                                    class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="sm:col-span-2">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <div class="mt-1">
                                    <input type="date" name="start_date" id="start_date"
                                        value="{{ old('start_date', $course->start_date ? $course->start_date->format('Y-m-d') : '') }}"
                                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                <div class="mt-1">
                                    <input type="date" name="end_date" id="end_date"
                                        value="{{ old('end_date', $course->end_date ? $course->end_date->format('Y-m-d') : '') }}"
                                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <!-- Is Active -->
                            <div class="sm:col-span-2 flex items-center h-full pt-6">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="is_active" name="is_active" type="checkbox" value="1"
                                            {{ old('is_active', $course->is_active) ? 'checked' : '' }}
                                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700">Active</label>
                                        <p class="text-gray-500">Course is visible to students.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex justify-end">
                        <a href="{{ url('/admin/lms/courses') }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Update Course
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
