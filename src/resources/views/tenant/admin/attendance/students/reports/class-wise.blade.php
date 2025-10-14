<!-- Class-wise Attendance Summary -->
<div class="space-y-6">
    <!-- Overall Summary -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">{{ $data['summary']['total_classes'] ?? 0 }}</div>
                    <div class="text-sm text-indigo-100">Total Classes</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">{{ $data['summary']['total_students'] ?? 0 }}</div>
                    <div class="text-sm text-indigo-100">Total Students</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">{{ number_format($data['summary']['avg_attendance'] ?? 0, 1) }}%</div>
                    <div class="text-sm text-indigo-100">Average Attendance</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">{{ $data['summary']['total_days'] ?? 0 }}</div>
                    <div class="text-sm text-indigo-100">Days Covered</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Class-wise Breakdown -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($data['records'] ?? [] as $record)
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $record['class_name'] }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $record['student_count'] }} students
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="text-3xl font-bold {{ $record['percentage'] >= 75 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($record['percentage'], 1) }}%
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-5 gap-2 text-center text-xs">
                    <div>
                        <div class="text-green-600 font-semibold">{{ $record['present'] }}</div>
                        <div class="text-gray-500">Present</div>
                    </div>
                    <div>
                        <div class="text-red-600 font-semibold">{{ $record['absent'] }}</div>
                        <div class="text-gray-500">Absent</div>
                    </div>
                    <div>
                        <div class="text-yellow-600 font-semibold">{{ $record['late'] }}</div>
                        <div class="text-gray-500">Late</div>
                    </div>
                    <div>
                        <div class="text-blue-600 font-semibold">{{ $record['half_day'] }}</div>
                        <div class="text-gray-500">Half Day</div>
                    </div>
                    <div>
                        <div class="text-purple-600 font-semibold">{{ $record['on_leave'] }}</div>
                        <div class="text-gray-500">Leave</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                            <div style="width:{{ $record['percentage'] }}%"
                                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $record['percentage'] >= 75 ? 'bg-green-500' : 'bg-red-500' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3">
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No data available</h3>
                <p class="mt-1 text-sm text-gray-500">No attendance records found for the selected period.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Detailed Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Class
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Students
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Days
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Present
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Absent
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Late
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Half Day
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        On Leave
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Attendance %
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($data['records'] ?? [] as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $record['class_name'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        {{ $record['student_count'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        {{ $record['total_days'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600">
                        {{ $record['present'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-600">
                        {{ $record['absent'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-yellow-600">
                        {{ $record['late'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-blue-600">
                        {{ $record['half_day'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-purple-600">
                        {{ $record['on_leave'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $record['percentage'] >= 75 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ number_format($record['percentage'], 1) }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

