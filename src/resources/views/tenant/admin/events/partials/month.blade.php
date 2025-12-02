@php
    $currentDate = \Carbon\Carbon::parse($date);
    $startOfMonth = $currentDate->copy()->startOfMonth();
    $endOfMonth = $currentDate->copy()->endOfMonth();
    $startOfCalendar = $startOfMonth->copy()->startOfWeek();
    $endOfCalendar = $endOfMonth->copy()->endOfWeek();

    // Group events by date
    $eventsByDate = [];
    foreach ($events as $event) {
        $eventStart = \Carbon\Carbon::parse($event->start_date);
        $eventEnd = $event->end_date ? \Carbon\Carbon::parse($event->end_date) : $eventStart;

        $current = $eventStart->copy();
        while ($current->lte($eventEnd) && $current->lte($endOfCalendar)) {
            if ($current->gte($startOfCalendar)) {
                $dateKey = $current->format('Y-m-d');
                if (!isset($eventsByDate[$dateKey])) {
                    $eventsByDate[$dateKey] = [];
                }
                $eventsByDate[$dateKey][] = $event;
            }
            $current->addDay();
        }
    }

    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $currentDay = $startOfCalendar->copy();
@endphp

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($daysOfWeek as $day)
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $day }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @while($currentDay->lte($endOfCalendar))
                <tr>
                    @for($i = 0; $i < 7; $i++)
                    @php
                        $isCurrentMonth = $currentDay->month == $currentDate->month;
                        $isToday = $currentDay->isToday();
                        $dateKey = $currentDay->format('Y-m-d');
                        $dayEvents = $eventsByDate[$dateKey] ?? [];
                    @endphp
                    <td class="px-2 py-3 align-top border border-gray-200 {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50' }} {{ $isToday ? 'ring-2 ring-primary-500' : '' }}" style="min-height: 120px; width: 14.28%;">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium {{ $isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }} {{ $isToday ? 'text-primary-600' : '' }}">
                                {{ $currentDay->day }}
                            </span>
                            @if($isToday)
                            <span class="text-xs text-primary-600 font-semibold">Today</span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            @foreach(array_slice($dayEvents, 0, 3) as $event)
                            <a href="{{ url('/admin/events/' . $event->id) }}" class="block px-2 py-1 text-xs rounded truncate hover:opacity-80" style="background-color: {{ $event->category->color ?? '#3b82f6' }}20; color: {{ $event->category->color ?? '#3b82f6' }}; border-left: 3px solid {{ $event->category->color ?? '#3b82f6' }}">
                                @if($event->start_time && !$event->is_all_day)
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }} -
                                @endif
                                {{ Str::limit($event->title, 20) }}
                            </a>
                            @endforeach
                            @if(count($dayEvents) > 3)
                            <span class="text-xs text-gray-500">+{{ count($dayEvents) - 3 }} more</span>
                            @endif
                        </div>
                    </td>
                    @php $currentDay->addDay(); @endphp
                    @endfor
                </tr>
                @endwhile
            </tbody>
        </table>
    </div>
</div>

