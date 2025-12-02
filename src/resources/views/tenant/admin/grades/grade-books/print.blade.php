@extends('tenant.layouts.admin')

@section('title', 'Print Grade Book')

@section('content')
<div class="space-y-6">
    <!-- Print Controls -->
    <div class="bg-white shadow rounded-lg p-4 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Grade Book</h2>
            <p class="text-sm text-gray-500">{{ $gradeBook->student->full_name ?? 'Student' }} - {{ $gradeBook->academic_year }}</p>
        </div>
        <div>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ url('/admin/grades/grade-books/' . $gradeBook->id) }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <!-- Grade Book Content -->
    <div class="bg-white shadow rounded-lg p-8 print:p-4" style="page-break-after: always;">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $tenant->data['name'] ?? 'School' }}</h1>
            <p class="text-lg text-gray-600 mt-2">Grade Book</p>
        </div>

        <!-- Student Information -->
        <div class="mb-6 grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Student Name</p>
                <p class="text-lg font-medium text-gray-900">{{ $gradeBook->student->full_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Admission Number</p>
                <p class="text-lg font-medium text-gray-900">{{ $gradeBook->student->admission_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Class</p>
                <p class="text-lg font-medium text-gray-900">{{ $gradeBook->schoolClass->class_name ?? '' }} {{ $gradeBook->section->section_name ?? '' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Academic Year</p>
                <p class="text-lg font-medium text-gray-900">{{ $gradeBook->academic_year }}</p>
            </div>
            @if($gradeBook->term)
            <div>
                <p class="text-sm text-gray-500">Term</p>
                <p class="text-lg font-medium text-gray-900">{{ $gradeBook->term }}</p>
            </div>
            @endif
        </div>

        <!-- Summary -->
        <div class="mb-6 grid grid-cols-4 gap-4">
            <div class="p-4 border rounded-lg text-center">
                <p class="text-sm text-gray-500">Total Marks</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($gradeBook->total_marks, 2) }}</p>
                <p class="text-xs text-gray-500">/ {{ number_format($gradeBook->max_total_marks, 2) }}</p>
            </div>
            <div class="p-4 border rounded-lg text-center">
                <p class="text-sm text-gray-500">Percentage</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($gradeBook->percentage, 2) }}%</p>
            </div>
            <div class="p-4 border rounded-lg text-center">
                <p class="text-sm text-gray-500">Grade</p>
                <p class="text-2xl font-bold text-gray-900">{{ $gradeBook->overall_grade ?? '-' }}</p>
                @if($gradeBook->overall_gpa)
                    <p class="text-xs text-gray-500">GPA: {{ $gradeBook->overall_gpa }}</p>
                @endif
            </div>
            <div class="p-4 border rounded-lg text-center">
                <p class="text-sm text-gray-500">Rank</p>
                <p class="text-2xl font-bold text-gray-900">{{ $gradeBook->rank ? '#' . $gradeBook->rank : '-' }}</p>
            </div>
        </div>

        <!-- Subject-wise Marks -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Subject-wise Performance</h3>
            <table class="min-w-full divide-y divide-gray-200 border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border">Subject</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border">Total Marks</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border">Obtained</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border">Percentage</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border">Grade</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($marksBySubject as $subjectId => $subjectMarks)
                        @php
                            $subject = $subjectMarks->first()->subject;
                            $totalMarks = $subjectMarks->sum('marks_obtained');
                            $maxMarks = $subjectMarks->sum('max_marks');
                            $percentage = $maxMarks > 0 ? round(($totalMarks / $maxMarks) * 100, 2) : 0;
                            $passed = $subjectMarks->where('status', 'pass')->count() > 0;
                            $avgGrade = $subjectMarks->whereNotNull('grade')->pluck('grade')->first();
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 border">{{ $subject->subject_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900 border">{{ number_format($maxMarks, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900 border">{{ number_format($totalMarks, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900 border">{{ number_format($percentage, 2) }}%</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900 border">{{ $avgGrade ?? '-' }}</td>
                            <td class="px-4 py-3 text-center border">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $passed ? 'Pass' : 'Fail' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="p-4 border rounded-lg text-center">
                <p class="text-sm text-gray-500">Total Subjects</p>
                <p class="text-xl font-bold text-gray-900">{{ $gradeBook->total_subjects }}</p>
            </div>
            <div class="p-4 border rounded-lg text-center">
                <p class="text-sm text-gray-500">Passed</p>
                <p class="text-xl font-bold text-green-600">{{ $gradeBook->passed_subjects }}</p>
            </div>
            <div class="p-4 border rounded-lg text-center">
                <p class="text-sm text-gray-500">Failed</p>
                <p class="text-xl font-bold text-red-600">{{ $gradeBook->failed_subjects }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Generated On</p>
                    <p class="text-sm font-medium text-gray-900">{{ $gradeBook->generated_at ? $gradeBook->generated_at->format('F d, Y') : now()->format('F d, Y') }}</p>
                </div>
                @if($gradeBook->generatedBy)
                <div>
                    <p class="text-sm text-gray-500">Generated By</p>
                    <p class="text-sm font-medium text-gray-900">{{ $gradeBook->generatedBy->name ?? 'System' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .bg-white {
        background: white !important;
    }
    button, a {
        display: none !important;
    }
}
</style>
@endsection

