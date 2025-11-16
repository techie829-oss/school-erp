@extends('tenant.layouts.admin')

@section('title', 'Fee Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Fee Reports</h2>
            <p class="mt-1 text-sm text-gray-500">Comprehensive fee collection and outstanding reports</p>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ url('/admin/fees/reports') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Report Type</label>
                    <select name="report_type" id="report_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="collection" {{ request('report_type') === 'collection' ? 'selected' : '' }}>Collection Report</option>
                        <option value="outstanding" {{ request('report_type') === 'outstanding' ? 'selected' : '' }}>Outstanding Report</option>
                        <option value="defaulters" {{ request('report_type') === 'defaulters' ? 'selected' : '' }}>Defaulters List</option>
                        <option value="class_wise" {{ request('report_type') === 'class_wise' ? 'selected' : '' }}>Class-wise Summary</option>
                        <option value="payment_method" {{ request('report_type') === 'payment_method' ? 'selected' : '' }}>Payment Method Wise</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Class (Optional)</label>
                    <select name="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Classes</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Generate Report
                    </button>
                    <button type="button" onclick="exportReport()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export to Excel
                    </button>
                </div>
                <a href="{{ url('/admin/fees/reports') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Report Results -->
    @if(isset($reportData))
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ ucwords(str_replace('_', ' ', request('report_type', 'collection'))) }} Report
                </h3>
                <p class="text-sm text-gray-600">
                    {{ request('from_date') }} to {{ request('to_date') }}
                </p>
            </div>

            <!-- Summary Cards -->
            @if(isset($summary))
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 border-b">
                    @foreach($summary as $key => $value)
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                            <p class="text-xl font-bold text-gray-900">
                                @if(str_contains($key, 'amount') || str_contains($key, 'total'))
                                    ₹{{ number_format($value, 2) }}
                                @else
                                    {{ $value }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Report Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($reportData['headers'] ?? [] as $header)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reportData['data'] ?? [] as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $cell }}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($reportData['headers'] ?? [1]) }}" class="px-6 py-8 text-center text-gray-500">
                                    No data found for the selected criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No Report Generated</h3>
            <p class="mt-1 text-sm text-gray-500">Select a report type and date range, then click "Generate Report"</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function exportReport() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = '{{ url("/admin/fees/reports") }}?' + params.toString();
}
</script>
@endpush
@endsection

