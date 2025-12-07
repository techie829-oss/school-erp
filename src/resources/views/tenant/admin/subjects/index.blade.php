@extends('tenant.layouts.admin')

@section('title', 'Subjects')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Subjects
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage academic subjects and courses
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/subjects/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Subject
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" action="{{ url('/admin/subjects') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search subjects..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="subject_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="subject_type" id="subject_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Types</option>
                        <option value="core" {{ request('subject_type') == 'core' ? 'selected' : '' }}>Core</option>
                        <option value="elective" {{ request('subject_type') == 'elective' ? 'selected' : '' }}>Elective</option>
                        <option value="optional" {{ request('subject_type') == 'optional' ? 'selected' : '' }}>Optional</option>
                        <option value="extra_curricular" {{ request('subject_type') == 'extra_curricular' ? 'selected' : '' }}>Extra Curricular</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Subjects Grid -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($subjects->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                @foreach($subjects as $subject)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow flex flex-col h-full">
                    <!-- Header Section -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0 pr-2">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $subject->subject_name }}</h3>
                            @if($subject->subject_code)
                                <p class="text-xs text-gray-500 mt-1">Code: {{ $subject->subject_code }}</p>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0 {{ $subject->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $subject->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Type Badge -->
                    <div class="mb-3">
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $subject->subject_type)) }}
                        </span>
                    </div>

                    <!-- Description -->
                    @if($subject->description)
                        <p class="text-xs text-gray-600 mb-3 line-clamp-2 flex-grow">{{ Str::limit($subject->description, 60) }}</p>
                    @else
                        <div class="flex-grow"></div>
                    @endif

                    <!-- Action Buttons - Always at bottom -->
                    <div class="mt-auto pt-3 border-t border-gray-200 flex items-center justify-end gap-3">
                        <a href="{{ url('/admin/subjects/' . $subject->id . '/edit') }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                            Edit
                        </a>
                        <form action="{{ url('/admin/subjects/' . $subject->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subject?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-12">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No subjects found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first subject</p>
                    <div class="mt-6">
                        <a href="{{ url('/admin/subjects/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Subject
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($subjects->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $subjects->links() }}
        </div>
    @endif
</div>
@endsection

