@extends('tenant.layouts.admin')

@section('title', 'Event Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/events') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Events</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">Event Details</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/events/' . $event->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/events') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center space-x-3 mb-4">
                    @if($event->category)
                    <span class="px-3 py-1 text-sm font-semibold rounded-full" style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                        {{ $event->category->name }}
                    </span>
                    @endif
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ ucfirst($event->event_type) }}
                    </span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : ($event->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>

                @if($event->description)
                <div class="prose max-w-none mb-4">
                    {!! nl2br(e($event->description)) !!}
                </div>
                @endif

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->formatted_date_range }}</dd>
                    </div>
                    @if($event->location)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-gray-900">📍 {{ $event->location }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Organized By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->organizer->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Participants -->
            @if($event->participants->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Participants ({{ $event->participants->count() }})</h3>
                <div class="space-y-2">
                    @foreach($event->participants as $participant)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($participant->participant_type) }}</span>
                            @if($participant->participant_id)
                            <span class="text-sm text-gray-500"> - ID: {{ $participant->participant_id }}</span>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $participant->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($participant->status === 'declined' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($participant->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Event Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($event->event_type) }}</dd>
                    </div>
                    @if($event->category)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->category->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">All Day</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->is_all_day ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ ucfirst($event->status) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

