@extends('tenant.layouts.admin')

@section('title', 'Notice Board')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Notice Board</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Notice Board</h2>
            <p class="mt-1 text-sm text-gray-500">Manage and publish notices for your school</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/notices/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Notice
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search notices..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="notice_type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="notice_type" id="notice_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Types</option>
                        <option value="general" {{ request('notice_type') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="academic" {{ request('notice_type') == 'academic' ? 'selected' : '' }}>Academic</option>
                        <option value="event" {{ request('notice_type') == 'event' ? 'selected' : '' }}>Event</option>
                        <option value="announcement" {{ request('notice_type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                        <option value="circular" {{ request('notice_type') == 'circular' ? 'selected' : '' }}>Circular</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div>
                    <label for="target_audience" class="block text-sm font-medium text-gray-700">Audience</label>
                    <select name="target_audience" id="target_audience" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Audiences</option>
                        <option value="all" {{ request('target_audience') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="students" {{ request('target_audience') == 'students' ? 'selected' : '' }}>Students</option>
                        <option value="teachers" {{ request('target_audience') == 'teachers' ? 'selected' : '' }}>Teachers</option>
                        <option value="staff" {{ request('target_audience') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="parents" {{ request('target_audience') == 'parents' ? 'selected' : '' }}>Parents</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ url('/admin/notices') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Clear</a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Filter</button>
            </div>
        </form>
    </div>

    <!-- Notices List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($notices->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($notices as $notice)
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <a href="{{ url('/admin/notices/' . $notice->id) }}" class="hover:text-primary-600">
                                    {{ $notice->title }}
                                </a>
                            </h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $notice->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($notice->priority === 'high' ? 'bg-orange-100 text-orange-800' : ($notice->priority === 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($notice->priority) }}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ ucfirst($notice->notice_type) }}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $notice->status === 'published' ? 'bg-green-100 text-green-800' : ($notice->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($notice->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit(strip_tags($notice->content), 150) }}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span>Target: {{ ucfirst($notice->target_audience) }}</span>
                            <span>Start: {{ $notice->start_date->format('M d, Y') }}</span>
                            @if($notice->end_date)
                            <span>End: {{ $notice->end_date->format('M d, Y') }}</span>
                            @endif
                            <span>Created by: {{ $notice->creator->name ?? 'N/A' }}</span>
                            <span>Read: {{ $notice->read_count }} times</span>
                            @if($notice->attachments->count() > 0)
                            <span class="text-primary-600">{{ $notice->attachments->count() }} attachment(s)</span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4 flex space-x-2">
                        <a href="{{ url('/admin/notices/' . $notice->id) }}" class="px-3 py-1 text-sm text-primary-600 hover:text-primary-900">View</a>
                        <a href="{{ url('/admin/notices/' . $notice->id . '/edit') }}" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900">Edit</a>
                        <form action="{{ url('/admin/notices/' . $notice->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this notice?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 text-sm text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notices->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No notices</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new notice.</p>
            <div class="mt-6">
                <a href="{{ url('/admin/notices/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    Create Notice
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

