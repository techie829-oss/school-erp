<!-- Monthly Teacher Attendance Summary -->
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Days</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $data['summary']['total'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Present</dt>
                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $data['summary']['present'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Absent</dt>
                <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $data['summary']['absent'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Late</dt>
                <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $data['summary']['late'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">On Leave</dt>
                <dd class="mt-1 text-3xl font-semibold text-purple-600">{{ $data['summary']['on_leave'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Attendance %</dt>
                <dd class="mt-1 text-3xl font-semibold text-primary-600">{{ number_format($data['summary']['percentage'] ?? 0, 1) }}%</dd>
            </div>
        </div>
    </div>

    <!-- Teacher List (using same data as daily for now) -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Teacher
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Department
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check In
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check Out
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total Hours
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data['records'] ?? [] as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($record->teacher->photo)
                                <img class="h-8 w-8 rounded-full" src="{{ $record->teacher->photo_url }}" alt="">
                            @else
                                <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            @endif
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $record->teacher->first_name }} {{ $record->teacher->last_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $record->teacher->employee_id }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $record->teacher->department->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($record->status == 'present')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Present
                            </span>
                        @elseif($record->status == 'absent')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Absent
                            </span>
                        @elseif($record->status == 'late')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Late
                            </span>
                        @elseif($record->status == 'on_leave')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                On Leave
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($record->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        {{ $record->check_in_time ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        {{ $record->check_out_time ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                        {{ $record->total_hours ? number_format($record->total_hours, 2) . ' hrs' : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No attendance records found for the selected period.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

