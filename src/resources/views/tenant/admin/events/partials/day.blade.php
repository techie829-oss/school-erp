@php
    $currentDate = \Carbon\Carbon::parse($date);
    $dayEvents = $events->sortBy(function($event) {
        return $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '99:99';
    });
@endphp

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ $currentDate->format('l, F d, Y') }}</h3>
    </div>
    @if($dayEvents->count() > 0)
    <div class="divide-y divide-gray-200">
        @foreach($dayEvents as $event)
        <div class="p-6 hover:bg-gray-50 transition-colors">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    @if($event->start_time && !$event->is_all_day)
                    <div class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
                    </div>
                    @if($event->end_time)
                    <div class="text-xs text-gray-500">
                        - {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}
                    </div>
                    @endif
                    @else
                    <div class="text-sm font-semibold text-gray-900">All Day</div>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <h4 class="text-lg font-semibold text-gray-900">
                            <a href="{{ url('/admin/events/' . $event->id) }}" class="hover:text-primary-600">
                                {{ $event->title }}
                            </a>
                        </h4>
                        @if($event->category)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                            {{ $event->category->name }}
                        </span>
                        @endif
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ ucfirst($event->event_type) }}
                        </span>
                    </div>
                    @if($event->description)
                    <p class="text-sm text-gray-600 mb-2">{{ $event->description }}</p>
                    @endif
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        @if($event->location)
                        <span>📍 {{ $event->location }}</span>
                        @endif
                        <span>Organized by: {{ $event->organizer->name ?? 'N/A' }}</span>
                        <span>{{ $event->participants->count() }} participant(s)</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ url('/admin/events/' . $event->id) }}" class="px-3 py-1 text-sm text-primary-600 hover:text-primary-900">View</a>
                    <a href="{{ url('/admin/events/' . $event->id . '/edit') }}" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900">Edit</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No events on this day</h3>
        <p class="mt-1 text-sm text-gray-500">Create an event to get started.</p>
    </div>
    @endif
</div>

