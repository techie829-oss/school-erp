@extends('layouts.admin')

@section('title', 'Activity Log Details')
@section('page-title', 'Activity Log Details')
@section('page-description', 'View detailed activity log information')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Activity Log Details</h2>
            <p class="mt-1 text-sm text-gray-600">Detailed information about this activity</p>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Activity Logs
        </a>
    </div>

    <!-- Activity Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Activity Information</h3>
        </div>

        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <!-- Date & Time -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $activityLog->created_at->format('M j, Y \a\t g:i A') }}
                        <span class="text-gray-500">({{ $activityLog->created_at->diffForHumans() }})</span>
                    </dd>
                </div>

                <!-- User -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">User</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $activityLog->user ? $activityLog->user->name : 'System' }}
                        <span class="text-gray-500">({{ $activityLog->user_type }})</span>
                    </dd>
                </div>

                <!-- Action -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Action</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activityLog->action_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $activityLog->action)) }}
                        </span>
                    </dd>
                </div>

                <!-- Model -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Model</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $activityLog->model_name }}
                        @if($activityLog->model_id)
                            <span class="text-gray-500">(ID: {{ $activityLog->model_id }})</span>
                        @endif
                    </dd>
                </div>

                <!-- Tenant -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tenant</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $activityLog->tenant ? $activityLog->tenant->name : 'N/A' }}
                        @if($activityLog->tenant_id)
                            <span class="text-gray-500">(ID: {{ $activityLog->tenant_id }})</span>
                        @endif
                    </dd>
                </div>

                <!-- IP Address -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activityLog->ip_address ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Properties -->
    @if($activityLog->properties && count($activityLog->properties) > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Additional Properties</h3>
                <p class="text-sm text-gray-500">Additional data associated with this activity</p>
            </div>

            <div class="px-6 py-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
    @endif

    <!-- User Agent -->
    @if($activityLog->user_agent)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User Agent</h3>
                <p class="text-sm text-gray-500">Browser and device information</p>
            </div>

            <div class="px-6 py-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <code class="text-sm text-gray-800 break-all">{{ $activityLog->user_agent }}</code>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Model Information -->
    @if($activityLog->model)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Related {{ $activityLog->model_name }}</h3>
                <p class="text-sm text-gray-500">Information about the related model</p>
            </div>

            <div class="px-6 py-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($activityLog->model->toArray(), JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
