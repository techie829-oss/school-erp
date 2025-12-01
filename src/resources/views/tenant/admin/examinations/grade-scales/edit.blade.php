@extends('tenant.layouts.admin')

@section('title', 'Edit Grade Scale')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Edit Grade Scale
            </h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/examinations/grade-scales') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
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
    <form action="{{ url('/admin/examinations/grade-scales/' . $gradeScale->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="grade_name" class="block text-sm font-medium text-gray-700">
                        Grade Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="grade_name" id="grade_name" value="{{ old('grade_name', $gradeScale->grade_name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('grade_name') border-red-300 @enderror">
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $gradeScale->order) }}" min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="min_percentage" class="block text-sm font-medium text-gray-700">
                        Minimum Percentage <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="min_percentage" id="min_percentage" value="{{ old('min_percentage', $gradeScale->min_percentage) }}" required
                        step="0.01" min="0" max="100"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('min_percentage') border-red-300 @enderror">
                </div>

                <div>
                    <label for="max_percentage" class="block text-sm font-medium text-gray-700">
                        Maximum Percentage <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="max_percentage" id="max_percentage" value="{{ old('max_percentage', $gradeScale->max_percentage) }}" required
                        step="0.01" min="0" max="100"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('max_percentage') border-red-300 @enderror">
                </div>
            </div>

            <div>
                <label for="gpa_value" class="block text-sm font-medium text-gray-700">GPA Value</label>
                <input type="number" name="gpa_value" id="gpa_value" value="{{ old('gpa_value', $gradeScale->gpa_value) }}"
                    step="0.01" min="0" max="10"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $gradeScale->description) }}</textarea>
            </div>

            <div class="flex items-center space-x-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_pass" id="is_pass" value="1" {{ old('is_pass', $gradeScale->is_pass) ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_pass" class="ml-2 block text-sm text-gray-700">
                        Passing Grade
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $gradeScale->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ url('/admin/examinations/grade-scales') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Update Grade Scale
            </button>
        </div>
    </form>
</div>
@endsection

