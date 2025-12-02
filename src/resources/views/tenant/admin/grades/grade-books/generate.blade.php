@extends('tenant.layouts.admin')

@section('title', 'Generate Grade Books')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/grades/grade-books') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Grade Books</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Generate</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Generate Grade Books</h2>
            <p class="mt-1 text-sm text-gray-500">Generate grade books for students based on their marks</p>
        </div>
    </div>

    <!-- Single Student Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Generate for Single Student</h3>
        <form action="{{ url('/admin/grades/grade-books') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student <span class="text-red-500">*</span></label>
                    <select name="student_id" id="student_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Student</option>
                        <!-- Students will be loaded dynamically based on class selection -->
                    </select>
                </div>
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $classId) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700">Section</label>
                    <select name="section_id" id="section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id', $sectionId) == $section->id ? 'selected' : '' }}>{{ $section->section_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year <span class="text-red-500">*</span></label>
                    <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', $academicYear) }}" placeholder="e.g., 2024-2025" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700">Term</label>
                    <input type="text" name="term" id="term" value="{{ old('term', $term) }}" placeholder="e.g., Term 1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Generate</button>
            </div>
        </form>
    </div>

    <!-- Bulk Generate Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Generate</h3>
        <form action="{{ url('/admin/grades/grade-books/bulk') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="bulk_class_id" class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" id="bulk_class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $classId) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bulk_section_id" class="block text-sm font-medium text-gray-700">Section</label>
                    <select name="section_id" id="bulk_section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id', $sectionId) == $section->id ? 'selected' : '' }}>{{ $section->section_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bulk_academic_year" class="block text-sm font-medium text-gray-700">Academic Year <span class="text-red-500">*</span></label>
                    <input type="text" name="academic_year" id="bulk_academic_year" value="{{ old('academic_year', $academicYear) }}" placeholder="e.g., 2024-2025" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="bulk_term" class="block text-sm font-medium text-gray-700">Term</label>
                    <input type="text" name="term" id="bulk_term" value="{{ old('term', $term) }}" placeholder="e.g., Term 1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="skip_existing" id="skip_existing" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="skip_existing" class="ml-2 block text-sm text-gray-700">Skip existing grade books</label>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Bulk Generate</button>
            </div>
        </form>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<script>
document.getElementById('class_id')?.addEventListener('change', function() {
    const classId = this.value;
    if (classId) {
        window.location.href = '{{ url("/admin/grades/grade-books/generate") }}?class_id=' + classId;
    }
});

document.getElementById('bulk_class_id')?.addEventListener('change', function() {
    const classId = this.value;
    if (classId) {
        const url = new URL(window.location.href);
        url.searchParams.set('class_id', classId);
        window.location.href = url.toString();
    }
});
</script>
@endsection

