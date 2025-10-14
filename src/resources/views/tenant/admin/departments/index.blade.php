@extends('tenant.layouts.admin')

@section('title', 'Departments')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Departments
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage academic departments
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/departments/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Department
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

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Departments List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($departments->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($departments as $department)
                <li>
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-primary-600">{{ $department->department_name }}</h3>
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    @if($department->department_code)
                                        <span class="mr-4">Code: {{ $department->department_code }}</span>
                                    @endif
                                    <span class="mr-4">Teachers: {{ $department->teachers_count }}</span>
                                    @if($department->headTeacher)
                                        <span>Head: {{ $department->headTeacher->full_name }}</span>
                                    @endif
                                </div>
                                @if($department->description)
                                    <p class="mt-1 text-sm text-gray-600">{{ Str::limit($department->description, 100) }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <a href="{{ url('/admin/departments/' . $department->id . '/edit') }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">Edit</a>
                                <form action="{{ url('/admin/departments/' . $department->id) }}" method="POST" onsubmit="return confirm('Delete this department?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No departments</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new department.</p>
                <div class="mt-6">
                    <a href="{{ url('/admin/departments/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Department
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($departments->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $departments->links() }}
        </div>
    @endif
</div>
@endsection

