@extends('tenant.layouts.admin')

@section('title', 'Bulk Entry Marks')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/grades/marks') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Marks</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Bulk Entry</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Bulk Entry Marks</h2>
            <p class="mt-1 text-sm text-gray-500">Enter marks for multiple students at once</p>
        </div>
    </div>

    <!-- Selection Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ url('/admin/grades/marks/entry') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id', $classId) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700">Section <span class="text-gray-500 text-xs">(Optional)</span></label>
                    <select name="section_id" id="section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Students (No Section)</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section_id', $sectionId) == $section->id ? 'selected' : '' }}>{{ $section->section_name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Leave empty to show all students in the class</p>
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject <span class="text-red-500">*</span></label>
                    <select name="subject_id" id="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id', $subjectId) == $subject->id ? 'selected' : '' }}>{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="exam_id" class="block text-sm font-medium text-gray-700">Exam (Optional)</label>
                    <select name="exam_id" id="exam_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">None</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ request('exam_id', $examId) == $exam->id ? 'selected' : '' }}>{{ $exam->exam_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Load Students</button>
            </div>
        </form>
    </div>

    @if($classId && $subjectId && $students && $students->count() > 0)
    <form action="{{ url('/admin/grades/marks/bulk-entry') }}" method="POST">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="section_id" value="{{ $sectionId }}">
        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
        <input type="hidden" name="exam_id" value="{{ $examId }}">

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label for="mark_type" class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                    <select name="mark_type" id="mark_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="assignment">Assignment</option>
                        <option value="quiz">Quiz</option>
                        <option value="project">Project</option>
                        <option value="test">Test</option>
                        <option value="exam">Exam</option>
                        <option value="homework">Homework</option>
                        <option value="classwork">Classwork</option>
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="assessment_date" class="block text-sm font-medium text-gray-700">Assessment Date</label>
                    <input type="date" name="assessment_date" id="assessment_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="max_marks" class="block text-sm font-medium text-gray-700">Max Marks <span class="text-red-500">*</span></label>
                    <input type="number" name="max_marks" id="max_marks" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Marks Obtained</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Absent</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->admission_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <input type="number" name="marks[{{ $student->id }}][marks_obtained]" step="0.01" min="0" class="marks-input w-24 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" data-student-id="{{ $student->id }}">
                                <input type="hidden" name="marks[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <input type="checkbox" name="marks[{{ $student->id }}][is_absent]" value="1" class="absent-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" data-student-id="{{ $student->id }}">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="marks[{{ $student->id }}][remarks]" placeholder="Optional" maxlength="500" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/grades/marks') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Save Marks</button>
        </div>
    </form>

    <script>
    document.querySelectorAll('.absent-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const studentId = this.dataset.studentId;
            const marksInput = document.querySelector(`.marks-input[data-student-id="${studentId}"]`);
            if (this.checked) {
                marksInput.value = '0';
                marksInput.disabled = true;
            } else {
                marksInput.disabled = false;
            }
        });
    });
    </script>
    @elseif($classId && $subjectId && (!$students || $students->count() == 0))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">No students found for the selected class and section.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
// Auto-load sections when class is selected
document.getElementById('class_id')?.addEventListener('change', function() {
    const classId = this.value;
    const sectionSelect = document.getElementById('section_id');

    if (classId) {
        // Reload page with class_id to get sections
        const url = new URL(window.location.href);
        url.searchParams.set('class_id', classId);
        url.searchParams.delete('section_id'); // Reset section when class changes
        window.location.href = url.toString();
    }
});
</script>
@endsection

