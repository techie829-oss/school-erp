@extends('tenant.layouts.admin')

@section('title', 'Add Mark')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a>
            </li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/grades/marks') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Marks</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Add</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Add Mark</h2>
        </div>
    </div>

    <form action="{{ url('/admin/grades/marks') }}" method="POST" class="max-w-3xl">
        @csrf

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <label for="section_id" class="block text-sm font-medium text-gray-700">Section <span class="text-gray-500 text-xs">(Optional)</span></label>
                    <select name="section_id" id="section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Students (No Section)</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id', $sectionId) == $section->id ? 'selected' : '' }}>{{ $section->section_name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Leave empty to show all students in the class</p>
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject <span class="text-red-500">*</span></label>
                    <select name="subject_id" id="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $subjectId) == $subject->id ? 'selected' : '' }}>{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student <span class="text-red-500">*</span></label>
                    <select name="student_id" id="student_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->full_name }} ({{ $student->admission_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="mark_type" class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                    <select name="mark_type" id="mark_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="assignment" {{ old('mark_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        <option value="quiz" {{ old('mark_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="project" {{ old('mark_type') == 'project' ? 'selected' : '' }}>Project</option>
                        <option value="test" {{ old('mark_type') == 'test' ? 'selected' : '' }}>Test</option>
                        <option value="exam" {{ old('mark_type') == 'exam' ? 'selected' : '' }}>Exam</option>
                        <option value="homework" {{ old('mark_type') == 'homework' ? 'selected' : '' }}>Homework</option>
                        <option value="classwork" {{ old('mark_type') == 'classwork' ? 'selected' : '' }}>Classwork</option>
                    </select>
                </div>

                <div>
                    <label for="exam_id" class="block text-sm font-medium text-gray-700">Exam (Optional)</label>
                    <select name="exam_id" id="exam_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">None</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ old('exam_id', $examId) == $exam->id ? 'selected' : '' }}>{{ $exam->exam_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="assessment_date" class="block text-sm font-medium text-gray-700">Assessment Date</label>
                    <input type="date" name="assessment_date" id="assessment_date" value="{{ old('assessment_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="marks_obtained" class="block text-sm font-medium text-gray-700">Marks Obtained <span class="text-red-500">*</span></label>
                    <input type="number" name="marks_obtained" id="marks_obtained" value="{{ old('marks_obtained') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="max_marks" class="block text-sm font-medium text-gray-700">Max Marks <span class="text-red-500">*</span></label>
                    <input type="number" name="max_marks" id="max_marks" value="{{ old('max_marks') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_absent" id="is_absent" value="1" {{ old('is_absent') ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_absent" class="ml-2 block text-sm text-gray-700">Mark as Absent</label>
            </div>

            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/grades/marks') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Save Mark</button>
        </div>
    </form>
</div>

<script>
document.getElementById('class_id').addEventListener('change', function() {
    const classId = this.value;
    if (classId) {
        window.location.href = '{{ url("/admin/grades/marks/create") }}?class_id=' + classId;
    }
});
</script>
@endsection

