<!-- Department-wise Teacher Attendance Summary -->
<div class="space-y-6">
    <!-- Overall Summary -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">{{ count($data['records'] ?? []) }}</div>
                    <div class="text-sm text-blue-100">Total Teachers</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">{{ $data['summary']['present'] ?? 0 }}</div>
                    <div class="text-sm text-blue-100">Present Today</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">{{ $data['summary']['absent'] ?? 0 }}</div>
                    <div class="text-sm text-blue-100">Absent Today</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">{{ number_format($data['summary']['percentage'] ?? 0, 1) }}%</div>
                    <div class="text-sm text-blue-100">Overall Attendance</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teachers List (same as daily for now) -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Employee ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Teacher Name
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
                        Hours
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data['records'] ?? [] as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->teacher->employee_id ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($record->teacher->photo)
                                <img class="h-10 w-10 rounded-full" src="{{ $record->teacher->photo_url }}" alt="">
                            @else
                                <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            @endif
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $record->teacher->first_name }} {{ $record->teacher->last_name }}
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
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        No attendance records found for the selected department.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

