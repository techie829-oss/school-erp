@extends('layouts.admin')

@section('title', 'System Logs')
@section('page-title', 'System Logs')
@section('page-description', 'View and filter system logs')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">System Logs</h2>
            <p class="mt-1 text-sm text-gray-600">View and filter Laravel application logs</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.system.overview') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Overview
            </a>
            <form action="{{ route('admin.system.logs.clear') }}" method="POST" class="inline-block">
                @csrf
                <button type="submit"
                        onclick="return confirm('Are you sure you want to clear all logs? A backup will be created.')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Clear Logs
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="flex gap-3 mb-4">
    <div class="bg-white rounded border border-gray-200 px-3 py-2 text-sm">
        <span class="font-semibold text-gray-900">{{ number_format($stats['total']) }}</span> Total
    </div>
    <div class="bg-red-50 rounded border border-red-200 px-3 py-2 text-sm">
        <span class="font-semibold text-red-700">{{ number_format($stats['error']) }}</span> Errors
    </div>
    <div class="bg-yellow-50 rounded border border-yellow-200 px-3 py-2 text-sm">
        <span class="font-semibold text-yellow-700">{{ number_format($stats['warning']) }}</span> Warnings
    </div>
    <div class="bg-blue-50 rounded border border-blue-200 px-3 py-2 text-sm">
        <span class="font-semibold text-blue-700">{{ number_format($stats['info']) }}</span> Info
    </div>
    <div class="bg-green-50 rounded border border-green-200 px-3 py-2 text-sm">
        <span class="font-semibold text-green-700">{{ number_format($stats['debug']) }}</span> Debug
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded border border-gray-200 p-3 mb-4">
    <form method="GET" action="{{ route('admin.system.logs') }}" class="flex flex-wrap gap-3 items-end">
        <div class="min-w-[120px]">
            <select name="level" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="all" {{ $level === 'all' ? 'selected' : '' }}>All Levels</option>
                <option value="error" {{ $level === 'error' ? 'selected' : '' }}>Error</option>
                <option value="warning" {{ $level === 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="info" {{ $level === 'info' ? 'selected' : '' }}>Info</option>
                <option value="debug" {{ $level === 'debug' ? 'selected' : '' }}>Debug</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="Search in messages..."
                   class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="min-w-[100px]">
            <select name="limit" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                <option value="200" {{ $limit == 200 ? 'selected' : '' }}>200</option>
                <option value="500" {{ $limit == 500 ? 'selected' : '' }}>500</option>
                <option value="1000" {{ $limit == 1000 ? 'selected' : '' }}>1000</option>
            </select>
        </div>
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
            Filter
        </button>
    </form>
</div>

<!-- Logs Display -->
<div class="bg-white rounded border border-gray-200">
    <div class="p-3 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">
            Log Entries <span class="text-xs font-normal text-gray-500">({{ count($logs) }} of {{ number_format($stats['total']) }})</span>
        </h3>
        <button onclick="window.location.reload()" class="text-blue-600 hover:text-blue-700 text-xs font-medium">
            Refresh
        </button>
    </div>

    <div class="p-3">
        @if(count($logs) > 0)
        <div class="space-y-1">
            @foreach($logs as $index => $log)
            <div class="border-l-2 p-2 rounded-r
                {{ $log['level'] === 'ERROR' ? 'border-red-500 bg-red-50' :
                   ($log['level'] === 'WARNING' ? 'border-yellow-500 bg-yellow-50' :
                   ($log['level'] === 'INFO' ? 'border-blue-500 bg-blue-50' : 'border-green-500 bg-green-50')) }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ $log['level'] === 'ERROR' ? 'bg-red-200 text-red-900' :
                               ($log['level'] === 'WARNING' ? 'bg-yellow-200 text-yellow-900' :
                               ($log['level'] === 'INFO' ? 'bg-blue-200 text-blue-900' : 'bg-green-200 text-green-900')) }}">
                            {{ $log['level'] }}
                        </span>
                        <span class="text-xs text-gray-600 font-mono whitespace-nowrap">{{ $log['timestamp'] }}</span>
                        <div class="text-sm text-gray-900 truncate flex-1 min-w-0" id="short-msg-{{ $index }}">
                            {{ Str::limit($log['message'], 120) }}
                        </div>
                    </div>
                    <button onclick="toggleLog{{ $index }}()"
                            id="toggle-btn-{{ $index }}"
                            class="text-xs text-blue-600 hover:text-blue-700 font-medium whitespace-nowrap">
                        Details
                    </button>
                </div>
                <pre class="hidden text-xs text-gray-700 mt-1 p-2 bg-white rounded border border-gray-200 overflow-x-auto whitespace-pre-wrap"
                     id="full-msg-{{ $index }}">{{ $log['full_message'] }}</pre>
            </div>

            <script>
                function toggleLog{{ $index }}() {
                    const shortMsg = document.getElementById('short-msg-{{ $index }}');
                    const fullMsg = document.getElementById('full-msg-{{ $index }}');
                    const toggleBtn = document.getElementById('toggle-btn-{{ $index }}');

                    if (fullMsg.classList.contains('hidden')) {
                        shortMsg.classList.add('hidden');
                        fullMsg.classList.remove('hidden');
                        toggleBtn.textContent = 'Hide';
                    } else {
                        shortMsg.classList.remove('hidden');
                        fullMsg.classList.add('hidden');
                        toggleBtn.textContent = 'Details';
                    }
                }
            </script>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 text-gray-500">
            <p class="text-sm font-medium">No log entries found</p>
            <p class="text-xs mt-1">Try adjusting your filters</p>
        </div>
        @endif
    </div>
</div>
@endsection

