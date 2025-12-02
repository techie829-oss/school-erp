@extends('tenant.layouts.admin')

@section('title', 'Edit Mark')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/grades/marks') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Marks</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Edit</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Mark</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $mark->student->full_name ?? 'Student' }} - {{ $mark->subject->subject_name ?? 'Subject' }}</p>
        </div>
    </div>

    <form action="{{ url('/admin/grades/marks/' . $mark->id) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

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
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Student Information</h3>
                <p class="text-sm text-gray-900">{{ $mark->student->full_name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500">{{ $mark->schoolClass->class_name ?? '' }} {{ $mark->section->section_name ?? '' }}</p>
            </div>

            <div>
                <label for="marks_obtained" class="block text-sm font-medium text-gray-700">Marks Obtained <span class="text-red-500">*</span></label>
                <input type="number" name="marks_obtained" id="marks_obtained" value="{{ old('marks_obtained', $mark->marks_obtained) }}" step="0.01" min="0" max="{{ $mark->max_marks }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Out of {{ $mark->max_marks }} marks</p>
            </div>

            <div>
                <label for="max_marks" class="block text-sm font-medium text-gray-700">Max Marks</label>
                <input type="number" name="max_marks" id="max_marks" value="{{ old('max_marks', $mark->max_marks) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_absent" id="is_absent" value="1" {{ old('is_absent', $mark->is_absent) ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_absent" class="ml-2 block text-sm text-gray-700">Mark as Absent</label>
            </div>

            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('remarks', $mark->remarks) }}</textarea>
            </div>

            @if($mark->percentage || $mark->grade)
            <div class="p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Current Result</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Percentage:</span> <span class="font-medium">{{ number_format($mark->percentage, 2) }}%</span></div>
                    @if($mark->grade)
                    <div><span class="text-gray-500">Grade:</span> <span class="font-medium">{{ $mark->grade }}</span></div>
                    @endif
                    @if($mark->gpa)
                    <div><span class="text-gray-500">GPA:</span> <span class="font-medium">{{ $mark->gpa }}</span></div>
                    @endif
                    <div><span class="text-gray-500">Status:</span> <span class="font-medium">{{ ucfirst($mark->status) }}</span></div>
                </div>
            </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/grades/marks') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Update Mark</button>
        </div>
    </form>
</div>
@endsection

