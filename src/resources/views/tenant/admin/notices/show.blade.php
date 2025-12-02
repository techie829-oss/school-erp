@extends('tenant.layouts.admin')

@section('title', 'Notice Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/notices') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Notices</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $notice->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">Notice Details</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/notices/' . $notice->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/notices') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Notice Content -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $notice->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($notice->priority === 'high' ? 'bg-orange-100 text-orange-800' : ($notice->priority === 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($notice->priority) }}
                    </span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ ucfirst($notice->notice_type) }}
                    </span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $notice->status === 'published' ? 'bg-green-100 text-green-800' : ($notice->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($notice->status) }}
                    </span>
                </div>
                <div class="prose max-w-none">
                    {!! nl2br(e($notice->content)) !!}
                </div>
            </div>

            <!-- Attachments -->
            @if($notice->attachments->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Attachments ({{ $notice->attachments->count() }})</h3>
                <div class="space-y-3">
                    @foreach($notice->attachments as $attachment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                        <div class="flex items-center space-x-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-sm font-medium text-primary-600 hover:text-primary-900">{{ $attachment->file_name }}</a>
                                <p class="text-xs text-gray-500">{{ $attachment->formatted_size }}</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-900">Download</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Notice Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notice Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($notice->notice_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Target Audience</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($notice->target_audience) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $notice->start_date->format('M d, Y') }}</dd>
                    </div>
                    @if($notice->end_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">End Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $notice->end_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $notice->creator->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $notice->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Read Count</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $notice->read_count }} times</dd>
                    </div>
                </dl>
            </div>

            <!-- Read Status -->
            @if($notice->reads->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Read By ({{ $notice->reads->count() }})</h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($notice->reads->take(10) as $read)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-900">{{ $read->user->name ?? 'N/A' }}</span>
                        <span class="text-gray-500">{{ $read->read_at->format('M d, Y') }}</span>
                    </div>
                    @endforeach
                    @if($notice->reads->count() > 10)
                    <p class="text-xs text-gray-500 mt-2">And {{ $notice->reads->count() - 10 }} more...</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

