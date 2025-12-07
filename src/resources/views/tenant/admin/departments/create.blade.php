@extends('tenant.layouts.admin')

@section('title', 'Add Department')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Add New Department
            </h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/departments') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ url('/admin/departments') }}" method="POST">
        @csrf
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 space-y-4 sm:space-y-6">
            <div>
                <label for="department_name" class="block text-sm font-medium text-gray-700">
                    Department Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="department_name" id="department_name" value="{{ old('department_name') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('department_name') border-red-300 @enderror">
            </div>

            <div>
                <label for="department_code" class="block text-sm font-medium text-gray-700">Department Code</label>
                <input type="text" name="department_code" id="department_code" value="{{ old('department_code') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">e.g., SCI, MATH, ENG</p>
            </div>

            <div>
                <label for="head_teacher_id" class="block text-sm font-medium text-gray-700">Department Head</label>
                <select name="head_teacher_id" id="head_teacher_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">Select Head Teacher</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('head_teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Active
                </label>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
            <a href="{{ url('/admin/departments') }}" class="w-full sm:w-auto inline-flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Add Department
            </button>
        </div>
    </form>
</div>
@endsection

