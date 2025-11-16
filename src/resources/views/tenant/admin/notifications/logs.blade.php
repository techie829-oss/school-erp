@extends('tenant.layouts.admin')

@section('title', 'Notification Logs')

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
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/settings') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                        Settings
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Notification Logs</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Notification Logs
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                View SMS and Email notification attempts for this school (success, failed, skipped).
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Channel</label>
                <select name="channel" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xs">
                    <option value="">All</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel }}" {{ request('channel') === $channel ? 'selected' : '' }}>
                            {{ strtoupper($channel) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xs">
                    <option value="">All</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', ucfirst($type)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xs">
                    <option value="">All</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="skipped" {{ request('status') === 'skipped' ? 'selected' : '' }}>Skipped</option>
                </select>
            </div>
            <div class="flex items-end justify-end gap-2">
                <a href="{{ url('/admin/notifications/logs') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Reset
                </a>
                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-xs font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Date & Time</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Channel</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Type</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Recipient</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Status</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logs as $log)
                        @php
                            $props = $log->properties ?? [];
                            $channel = $props['channel'] ?? '-';
                            $type = $props['type'] ?? 'generic';
                            $recipient = $props['recipient'] ?? '-';
                            $status = str_replace('notification_', '', $log->action);

                            $statusClasses = match($status) {
                                'success' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'skipped' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-700">
                                <div>{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-700">
                                <span class="uppercase">{{ $channel }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-700">
                                {{ str_replace('_', ' ', ucfirst($type)) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-700">
                                {{ $recipient }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $statusClasses }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-600 text-[11px]">
                                @if(isset($props['reason']))
                                    <div>Reason: {{ $props['reason'] }}</div>
                                @endif
                                @if(isset($props['error']))
                                    <div class="text-red-600">Error: {{ \Illuminate\Support\Str::limit($props['error'], 120) }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">
                                No notification logs found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $logs->links() }}
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 text-xs text-blue-800">
        <p>
            Only logs for this school (tenant) are shown here. SMS / Email delivery issues will appear as <strong>Failed</strong> or <strong>Skipped</strong>.
            Core fee and student operations are not blocked by notification failures.
        </p>
    </div>
</div>
@endsection


