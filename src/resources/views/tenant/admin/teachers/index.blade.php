@extends('tenant.layouts.admin')

@section('title', 'Teachers')

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
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Teachers</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Teachers
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage teaching staff and faculty members
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/teachers/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Teacher
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Teachers</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['active'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">On Leave</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['on_leave'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <form method="GET" action="{{ url('/admin/teachers') }}" class="space-y-4 sm:space-y-6">
            <!-- Search Bar -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Teachers</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by name, employee ID, email, or phone..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                </div>
            </div>

            <!-- Filter Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Department Filter -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department_id" id="department_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>

                <!-- Employment Type Filter -->
                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                    <select name="employment_type" id="employment_type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">All Types</option>
                        <option value="permanent" {{ request('employment_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="temporary" {{ request('employment_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                        <option value="visiting" {{ request('employment_type') == 'visiting' ? 'selected' : '' }}>Visiting</option>
                    </select>
                </div>

                <!-- Gender Filter -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" id="gender" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">All Genders</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pt-4 border-t border-gray-200">
                <a href="{{ url('/admin/teachers') }}" class="text-sm text-gray-600 hover:text-gray-900">Clear Filters</a>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Teachers List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($teachers as $teacher)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Left Section: Teacher Info -->
                    <div class="flex items-start space-x-4 flex-1 min-w-0">
                        <!-- Photo/Avatar -->
                        <div class="flex-shrink-0">
                            @if($teacher->photo)
                                <img class="h-14 w-14 rounded-full object-cover border-2 border-gray-200" src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}">
                            @else
                                <div class="h-14 w-14 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-lg border-2 border-gray-200">
                                    {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Teacher Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-semibold text-gray-900">{{ $teacher->full_name }}</h3>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                    @if($teacher->status === 'active') bg-green-100 text-green-800
                                    @elseif($teacher->status === 'on_leave') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $teacher->status)) }}
                                </span>
                                @if($teacher->employment_type)
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($teacher->employment_type) }}
                                </span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-500 mt-0.5">
                                <span class="font-medium">{{ $teacher->employee_id }}</span>
                                @if($teacher->designation)
                                    <span class="mx-1">•</span>
                                    <span>{{ $teacher->designation }}</span>
                                @endif
                            </p>

                            <!-- Additional Info -->
                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600">
                                @if($teacher->department)
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="text-gray-500">Dept:</span>
                                    <span class="ml-1 font-medium text-gray-900">{{ $teacher->department->department_name }}</span>
                                </div>
                                @endif
                                @if($teacher->email)
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-500">Email:</span>
                                    <span class="ml-1 font-medium text-gray-900 truncate max-w-[200px]">{{ $teacher->email }}</span>
                                </div>
                                @endif
                                @if($teacher->phone)
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="text-gray-500">Phone:</span>
                                    <span class="ml-1 font-medium text-gray-900">{{ $teacher->phone }}</span>
                                </div>
                                @endif
                                @if($teacher->date_of_joining)
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-gray-500">Service:</span>
                                    <span class="ml-1 font-medium text-primary-600">{{ $teacher->years_of_service }} years</span>
                                </div>
                                @endif
                                @if($teacher->highest_qualification)
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span class="text-gray-500">Qualification:</span>
                                    <span class="ml-1 font-medium text-gray-900">{{ $teacher->highest_qualification }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Actions -->
                    <div class="flex-shrink-0 flex items-center justify-end space-x-3 md:ml-4">
                        <a href="{{ url('/admin/teachers/' . $teacher->id) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium whitespace-nowrap">
                            View
                        </a>
                        <a href="{{ url('/admin/teachers/' . $teacher->id . '/edit') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium whitespace-nowrap">
                            Edit
                        </a>
                        <form action="{{ url('/admin/teachers/' . $teacher->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium whitespace-nowrap">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No teachers found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding your first teacher</p>
                    <div class="mt-6">
                        <a href="{{ url('/admin/teachers/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Teacher
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($teachers->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $teachers->links() }}
        </div>
    @endif
</div>
@endsection

