<!-- Monthly Summary Report -->
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Days</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $data['summary']['total_days'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Present</dt>
                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $data['summary']['present_days'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Absent</dt>
                <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $data['summary']['absent_days'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Late</dt>
                <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $data['summary']['late_days'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Half Day</dt>
                <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $data['summary']['half_days'] ?? 0 }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Attendance %</dt>
                <dd class="mt-1 text-3xl font-semibold text-primary-600">{{ number_format($data['summary']['percentage'] ?? 0, 1) }}%</dd>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Student
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Class
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
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
                        Percentage
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data['records'] ?? [] as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($record['student']->photo)
                                <img class="h-8 w-8 rounded-full" src="{{ $record['student']->photo_url }}" alt="">
                            @else
                                <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            @endif
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $record['student']->first_name }} {{ $record['student']->last_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $record['student']->admission_number }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $record['class_name'] ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
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
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                        No attendance records found for the selected period.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

