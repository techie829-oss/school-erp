@extends('tenant.layouts.admin')

@section('title', 'Generate No Dues Certificates')

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
                    <a href="{{ url('/admin/fees/no-dues') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">No Dues Certificates</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Generate</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Generate No Dues Certificates
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                @if(isset($class))
                    Generate certificates for {{ $class->class_name }}
                @else
                    Select a class to generate no-dues certificates
                @endif
            </p>
        </div>
    </div>

    @if(!isset($class))
    <!-- Class Selection -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ url('/admin/fees/no-dues/generate') }}" class="max-w-2xl">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700">
                    Select Class <span class="text-red-500">*</span>
                </label>
                <select name="class_id" id="class_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">Select a Class</option>
                    @foreach($classes as $classOption)
                        <option value="{{ $classOption->id }}" {{ request('class_id') == $classOption->id ? 'selected' : '' }}>
                            {{ $classOption->class_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mt-6 flex justify-end">
                <a href="{{ url('/admin/fees/no-dues') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 mr-3">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Continue
                </button>
            </div>
        </form>
    </div>
    @else
    <!-- Form -->
    <form action="{{ url('/admin/fees/no-dues/bulk') }}" method="POST" class="max-w-2xl" id="generateForm" onsubmit="return confirmGeneration()">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->id }}">

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

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <!-- Section -->
            <div>
                <label for="section_id" class="block text-sm font-medium text-gray-700">
                    Section (Optional)
                </label>
                <select name="section_id" id="section_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->section_name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Leave empty to generate for all sections in the class</p>
            </div>

            <!-- Issue Date -->
            <div>
                <label for="issue_date" class="block text-sm font-medium text-gray-700">
                    Issue Date <span class="text-red-500">*</span>
                </label>
                <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <!-- Remarks -->
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700">
                    Remarks (Optional)
                </label>
                <textarea name="remarks" id="remarks" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('remarks') }}</textarea>
            </div>

            <!-- Generate Options -->
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Generation Options</h3>

                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="radio" name="generate_type" id="generate_all" value="all" checked
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                        <label for="generate_all" class="ml-2 block text-sm text-gray-700">
                            Generate for all students in selected class/section
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" name="generate_type" id="generate_missing" value="missing"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                        <label for="generate_missing" class="ml-2 block text-sm text-gray-700">
                            Generate only for students without certificates
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/fees/no-dues') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Generate Certificates
            </button>
        </div>
    </form>
</div>

<script>
function confirmGeneration() {
    const classSelect = '{{ $class->class_name }}';
    const sectionSelect = document.getElementById('section_id');
    const selectedSection = sectionSelect.value ? sectionSelect.options[sectionSelect.selectedIndex].text : 'All Sections';
    const generateType = document.querySelector('input[name="generate_type"]:checked').value;

    const message = `Please confirm the following details:\n\n` +
        `Class: ${classSelect}\n` +
        `Section: ${selectedSection}\n` +
        `Generate Type: ${generateType === 'all' ? 'All students' : 'Missing only'}\n\n` +
        `Are you sure you want to generate no-dues certificates with these settings?`;

    return confirm(message);
}
</script>
    @endif
@endsection

