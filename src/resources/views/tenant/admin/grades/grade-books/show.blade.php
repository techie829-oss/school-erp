@extends('tenant.layouts.admin')

@section('title', 'Grade Book Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/grades/grade-books') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Grade Books</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Grade Book Details</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $gradeBook->student->full_name ?? 'Student' }} - {{ $gradeBook->academic_year }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/grades/grade-books/' . $gradeBook->id . '/print') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </a>
        </div>
    </div>

    <!-- Student Information -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Student Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">Name</p>
                <p class="text-sm font-medium text-gray-900">{{ $gradeBook->student->full_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Admission Number</p>
                <p class="text-sm font-medium text-gray-900">{{ $gradeBook->student->admission_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Class</p>
                <p class="text-sm font-medium text-gray-900">{{ $gradeBook->schoolClass->class_name ?? '' }} {{ $gradeBook->section->section_name ?? '' }}</p>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Summary</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-500">Total Marks</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($gradeBook->total_marks, 2) }} / {{ number_format($gradeBook->max_total_marks, 2) }}</p>
            </div>
            <div class="p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-gray-500">Percentage</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($gradeBook->percentage, 2) }}%</p>
            </div>
            <div class="p-4 bg-purple-50 rounded-lg">
                <p class="text-sm text-gray-500">Grade</p>
                <p class="text-2xl font-bold text-gray-900">{{ $gradeBook->overall_grade ?? '-' }}</p>
                @if($gradeBook->overall_gpa)
                    <p class="text-sm text-gray-500">GPA: {{ $gradeBook->overall_gpa }}</p>
                @endif
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg">
                <p class="text-sm text-gray-500">Rank</p>
                <p class="text-2xl font-bold text-gray-900">{{ $gradeBook->rank ? '#' . $gradeBook->rank : '-' }}</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">Academic Year</p>
                <p class="text-sm font-medium text-gray-900">{{ $gradeBook->academic_year }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Term</p>
                <p class="text-sm font-medium text-gray-900">{{ $gradeBook->term ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $gradeBook->status === 'pass' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($gradeBook->status) }}
                </span>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">Total Subjects</p>
                <p class="text-sm font-medium text-gray-900">{{ $gradeBook->total_subjects }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Passed Subjects</p>
                <p class="text-sm font-medium text-green-600">{{ $gradeBook->passed_subjects }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Failed Subjects</p>
                <p class="text-sm font-medium text-red-600">{{ $gradeBook->failed_subjects }}</p>
            </div>
        </div>
    </div>

    <!-- Marks by Subject -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Marks by Subject</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Marks</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Obtained</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Percentage</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Grade</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
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
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $subject->subject_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ number_format($maxMarks, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ number_format($totalMarks, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ number_format($percentage, 2) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                @php
                                    $avgGrade = $subjectMarks->whereNotNull('grade')->pluck('grade')->first();
                                @endphp
                                {{ $avgGrade ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $passed ? 'Pass' : 'Fail' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

