<!-- Teacher-wise Attendance History -->
<div class="space-y-6">
    <!-- Using daily report format for individual teacher -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-8 sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex sm:items-center">
                @if(isset($data['records']) && $data['records']->first())
                    @php $teacher = $data['records']->first()->teacher; @endphp
                    @if($teacher->photo)
                        <img class="h-20 w-20 rounded-full ring-4 ring-white" src="{{ $teacher->photo_url }}" alt="">
                    @else
                        <span class="h-20 w-20 rounded-full bg-white flex items-center justify-center ring-4 ring-white">
                            <svg class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    @endif
                    <div class="mt-4 sm:mt-0 sm:ml-6">
                        <h3 class="text-2xl font-bold text-white">
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </h3>
                        <p class="text-sm text-indigo-100">
                            Employee ID: {{ $teacher->employee_id }} |
                            Department: {{ $teacher->department->name ?? 'N/A' }}
                        </p>
                    </div>
                @endif
            </div>
            <div class="mt-5 sm:mt-0 text-center sm:text-right">
                <div class="text-4xl font-bold text-white">{{ number_format($data['summary']['percentage'] ?? 0, 1) }}%</div>
                <div class="text-sm text-indigo-100">Overall Attendance</div>
            </div>
        </div>
    </div>

    <!-- Summary Grid -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Total Days</dt>
                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $data['summary']['total'] ?? 0 }}</dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Present</dt>
                <dd class="mt-1 text-2xl font-semibold text-green-600">{{ $data['summary']['present'] ?? 0 }}</dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Absent</dt>
                <dd class="mt-1 text-2xl font-semibold text-red-600">{{ $data['summary']['absent'] ?? 0 }}</dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Late</dt>
                <dd class="mt-1 text-2xl font-semibold text-yellow-600">{{ $data['summary']['late'] ?? 0 }}</dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Half Day</dt>
                <dd class="mt-1 text-2xl font-semibold text-blue-600">{{ $data['summary']['half_day'] ?? 0 }}</dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">On Leave</dt>
                <dd class="mt-1 text-2xl font-semibold text-purple-600">{{ $data['summary']['on_leave'] ?? 0 }}</dd>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Day
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check In
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check Out
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Hours
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Remarks
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data['records'] ?? [] as $record)
                <tr class="{{ $record->status == 'absent' ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->attendance_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $record->attendance_date->format('l') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $record->check_in_time ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $record->check_out_time ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->total_hours ? number_format($record->total_hours, 2) . ' hrs' : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $record->remarks ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        No attendance records found for the selected period.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

