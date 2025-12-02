@extends('tenant.layouts.admin')

@section('title', 'Events & Calendar')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Events</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Events & Calendar</h2>
            <p class="mt-1 text-sm text-gray-500">Manage school events and calendar</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/events/categories') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Categories
            </a>
            <a href="{{ url('/admin/events/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Event
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

    <!-- View Toggle & Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <div class="flex space-x-2">
                <a href="{{ url('/admin/events?view=month&date=' . $date) }}" class="px-3 py-1 text-sm font-medium rounded-md {{ $view === 'month' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Month
                </a>
                <a href="{{ url('/admin/events?view=week&date=' . $date) }}" class="px-3 py-1 text-sm font-medium rounded-md {{ $view === 'week' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Week
                </a>
                <a href="{{ url('/admin/events?view=day&date=' . $date) }}" class="px-3 py-1 text-sm font-medium rounded-md {{ $view === 'day' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Day
                </a>
                <a href="{{ url('/admin/events?view=list') }}" class="px-3 py-1 text-sm font-medium rounded-md {{ $view === 'list' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    List
                </a>
            </div>
            <div class="flex items-center space-x-2">
                @php
                    $currentDate = \Carbon\Carbon::parse($date);
                    $prevDate = $currentDate->copy()->subMonth();
                    $nextDate = $currentDate->copy()->addMonth();
                @endphp
                <a href="{{ url('/admin/events?view=' . $view . '&date=' . $prevDate->format('Y-m-d')) }}" class="p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span class="text-sm font-medium text-gray-700">
                    @if($view === 'month')
                        {{ $currentDate->format('F Y') }}
                    @elseif($view === 'week')
                        Week of {{ $currentDate->startOfWeek()->format('M d') }}
                    @else
                        {{ $currentDate->format('F d, Y') }}
                    @endif
                </span>
                <a href="{{ url('/admin/events?view=' . $view . '&date=' . $nextDate->format('Y-m-d')) }}" class="p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ url('/admin/events?view=' . $view . '&date=' . now()->format('Y-m-d')) }}" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900">
                    Today
                </a>
            </div>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="view" value="{{ $view }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search events..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>
            <div>
                <label for="event_type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="event_type" id="event_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Types</option>
                    <option value="general" {{ request('event_type') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="academic" {{ request('event_type') == 'academic' ? 'selected' : '' }}>Academic</option>
                    <option value="sports" {{ request('event_type') == 'sports' ? 'selected' : '' }}>Sports</option>
                    <option value="cultural" {{ request('event_type') == 'cultural' ? 'selected' : '' }}>Cultural</option>
                    <option value="meeting" {{ request('event_type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                    <option value="holiday" {{ request('event_type') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                </select>
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="md:col-span-4 flex justify-end space-x-3">
                <a href="{{ url('/admin/events') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Clear</a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Filter</button>
            </div>
        </form>
    </div>

    <!-- Calendar/List View -->
    @if($view === 'list')
        @include('tenant.admin.events.partials.list')
    @elseif($view === 'month')
        @include('tenant.admin.events.partials.month')
    @elseif($view === 'week')
        @include('tenant.admin.events.partials.week')
    @else
        @include('tenant.admin.events.partials.day')
    @endif
</div>
@endsection

