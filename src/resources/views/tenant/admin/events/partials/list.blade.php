<div class="bg-white shadow rounded-lg overflow-hidden">
    @if($events->count() > 0)
    <div class="divide-y divide-gray-200">
        @foreach($events as $event)
        <div class="p-6 hover:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <a href="{{ url('/admin/events/' . $event->id) }}" class="hover:text-primary-600">
                                {{ $event->title }}
                            </a>
                        </h3>
                        @if($event->category)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                            {{ $event->category->name }}
                        </span>
                        @endif
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ ucfirst($event->event_type) }}
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : ($event->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($event->description, 150) }}</p>
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        <span>{{ $event->formatted_date_range }}</span>
                        @if($event->location)
                        <span>📍 {{ $event->location }}</span>
                        @endif
                        <span>Organized by: {{ $event->organizer->name ?? 'N/A' }}</span>
                        <span>{{ $event->participants->count() }} participant(s)</span>
                    </div>
                </div>
                <div class="ml-4 flex space-x-2">
                    <a href="{{ url('/admin/events/' . $event->id) }}" class="px-3 py-1 text-sm text-primary-600 hover:text-primary-900">View</a>
                    <a href="{{ url('/admin/events/' . $event->id . '/edit') }}" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900">Edit</a>
                    <form action="{{ url('/admin/events/' . $event->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
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
        {{ $events->links() }}
    </div>
    @else
    <div class="p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No events</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new event.</p>
        <div class="mt-6">
            <a href="{{ url('/admin/events/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                Create Event
            </a>
        </div>
    </div>
    @endif
</div>

