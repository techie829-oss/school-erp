@php
    $currentDate = \Carbon\Carbon::parse($date);
    $startOfWeek = $currentDate->copy()->startOfWeek();
    $endOfWeek = $currentDate->copy()->endOfWeek();
    
    $daysOfWeek = [];
    $current = $startOfWeek->copy();
    while ($current->lte($endOfWeek)) {
        $dateKey = $current->format('Y-m-d');
        $dayEvents = $events->filter(function($event) use ($current) {
            $eventStart = \Carbon\Carbon::parse($event->start_date);
            $eventEnd = $event->end_date ? \Carbon\Carbon::parse($event->end_date) : $eventStart;
            return $current->between($eventStart, $eventEnd);
        });
        
        $daysOfWeek[] = [
            'date' => $current->copy(),
            'events' => $dayEvents,
        ];
        $current->addDay();
    }
@endphp

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <div class="grid grid-cols-7 divide-x divide-gray-200">
            @foreach($daysOfWeek as $day)
            <div class="p-4 {{ $day['date']->isToday() ? 'bg-primary-50' : 'bg-white' }}">
                <div class="text-center mb-3">
                    <div class="text-xs font-medium text-gray-500 uppercase">
                        {{ $day['date']->format('D') }}
                    </div>
                    <div class="text-2xl font-bold {{ $day['date']->isToday() ? 'text-primary-600' : 'text-gray-900' }}">
                        {{ $day['date']->day }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $day['date']->format('M Y') }}
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($day['events'] as $event)
                    <a href="{{ url('/admin/events/' . $event->id) }}" class="block p-2 text-xs rounded hover:opacity-80" style="background-color: {{ $event->category->color ?? '#3b82f6' }}20; color: {{ $event->category->color ?? '#3b82f6' }}; border-left: 3px solid {{ $event->category->color ?? '#3b82f6' }}">
                        @if($event->start_time && !$event->is_all_day)
                            <div class="font-semibold">{{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</div>
                        @endif
                        <div class="truncate">{{ $event->title }}</div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

