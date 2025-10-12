@extends('layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-description', 'View system notifications and alerts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Notifications</h2>
        <p class="mt-1 text-sm text-gray-600">Stay updated with system alerts and important information</p>
    </div>

    <!-- Notification Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Unassigned Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-medium text-gray-900">Unassigned Tickets</h3>
                    <p class="text-2xl font-bold text-yellow-600">{{ $unassignedTickets }}</p>
                    <p class="text-sm text-gray-500">Tickets need assignment</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.tickets.index', ['assigned_to' => 'unassigned']) }}" class="text-sm text-yellow-600 hover:text-yellow-700 font-medium">
                    View tickets →
                </a>
            </div>
        </div>

        <!-- High Priority Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-medium text-gray-900">High Priority</h3>
                    <p class="text-2xl font-bold text-red-600">{{ $highPriorityTickets }}</p>
                    <p class="text-sm text-gray-500">Urgent tickets</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.tickets.index', ['priority' => 'high']) }}" class="text-sm text-red-600 hover:text-red-700 font-medium">
                    View tickets →
                </a>
            </div>
        </div>

        <!-- Overdue Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-medium text-gray-900">Overdue</h3>
                    <p class="text-2xl font-bold text-orange-600">{{ $overdueTickets }}</p>
                    <p class="text-sm text-gray-500">Past due date</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.tickets.index', ['overdue' => 'true']) }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                    View tickets →
                </a>
            </div>
        </div>
    </div>

    <!-- My Tickets -->
    @if($myTickets->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">My Assigned Tickets</h3>
                <p class="text-sm text-gray-500">Tickets assigned to you</p>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($myTickets as $ticket)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->priority_badge }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->status_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <p class="text-sm text-gray-900">{{ $ticket->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        Created by {{ $ticket->creator->name }} • {{ $ticket->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                    View →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Activities -->
    @if($recentActivities->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
                <p class="text-sm text-gray-500">Latest system activities</p>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($recentActivities as $activity)
                    <div class="px-6 py-3">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $activity->action_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    </span>
                                    <span class="text-sm text-gray-900">{{ $activity->model_name }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    by {{ $activity->user ? $activity->user->name : 'System' }} • {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
