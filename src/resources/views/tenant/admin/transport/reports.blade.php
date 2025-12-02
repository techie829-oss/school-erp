@extends('tenant.layouts.admin')

@section('title', 'Transport Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Transport Reports</h2>
            <p class="mt-1 text-sm text-gray-500">Comprehensive transport billing and payment reports</p>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ url('/admin/transport/bills/reports') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Report Type</label>
                    <select name="report_type" id="report_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="collection" {{ request('report_type') === 'collection' ? 'selected' : '' }}>Collection Report</option>
                        <option value="outstanding" {{ request('report_type') === 'outstanding' ? 'selected' : '' }}>Outstanding Report</option>
                        <option value="route_wise" {{ request('report_type') === 'route_wise' ? 'selected' : '' }}>Route-wise Summary</option>
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
                    <label class="block text-sm font-medium text-gray-700">Student (Optional)</label>
                    <select name="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Students</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Route (Optional)</label>
                    <select name="route_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Routes</option>
                        @foreach($routes ?? [] as $route)
                            <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->name }}
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
                </div>
                <a href="{{ url('/admin/transport/bills/reports') }}" class="text-sm text-gray-600 hover:text-gray-900">
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
                    @if(request('report_type') !== 'outstanding')
                        {{ request('from_date') }} to {{ request('to_date') }}
                    @else
                        As of {{ now()->format('d M Y') }}
                    @endif
                </p>
            </div>

            <!-- Summary Cards -->
            @if(isset($summary))
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 border-b">
                    @foreach($summary as $key => $value)
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                            <p class="text-xl font-bold text-gray-900">
                                @if(str_contains($key, 'amount') || str_contains($key, 'collected') || str_contains($key, 'outstanding') || str_contains($key, 'total') && !str_contains($key, 'count') && !str_contains($key, 'bills'))
                                    ₹{{ number_format($value, 2) }}
                                @else
                                    {{ number_format($value, 0) }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Report Data Table -->
            <div class="overflow-x-auto">
                @if(request('report_type') === 'collection')
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bill Number</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reportData as $payment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->student->full_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->bill->bill_number ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">₹{{ number_format($payment->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No payments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif(request('report_type') === 'outstanding')
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bill Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Outstanding</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reportData as $bill)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $bill->bill_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $bill->student->full_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $bill->assignment->route->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $bill->due_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">₹{{ number_format($bill->net_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">₹{{ number_format($bill->paid_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-red-600">₹{{ number_format($bill->outstanding_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bill->status === 'overdue' ? 'bg-red-100 text-red-800' : ($bill->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No outstanding bills found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif(request('report_type') === 'route_wise')
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Bills</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Collected</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Outstanding</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reportData as $route)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $route['route_name'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $route['total_bills'] }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">₹{{ number_format($route['total_amount'], 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-green-600">₹{{ number_format($route['total_collected'], 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-red-600">₹{{ number_format($route['total_outstanding'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif(request('report_type') === 'payment_method')
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Count</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reportData as $method)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $method['method'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $method['count'] }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">₹{{ number_format($method['total_amount'], 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ number_format($method['percentage'], 2) }}%</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

