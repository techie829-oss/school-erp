@extends('tenant.layouts.admin')

@section('title', 'Library Reports')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Library</span></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Reports</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Library Reports</h2>
            <p class="mt-1 text-sm text-gray-500">Generate and view library reports</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Report Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700">Report Type <span class="text-red-500">*</span></label>
                    <select name="report_type" id="report_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Report Type</option>
                        <option value="popular_books" {{ request('report_type') == 'popular_books' ? 'selected' : '' }}>Popular Books</option>
                        <option value="overdue_books" {{ request('report_type') == 'overdue_books' ? 'selected' : '' }}>Overdue Books</option>
                        <option value="student_history" {{ request('report_type') == 'student_history' ? 'selected' : '' }}>Student History</option>
                        <option value="category_wise" {{ request('report_type') == 'category_wise' ? 'selected' : '' }}>Category Wise</option>
                        <option value="fine_collection" {{ request('report_type') == 'fine_collection' ? 'selected' : '' }}>Fine Collection</option>
                        <option value="issue_statistics" {{ request('report_type') == 'issue_statistics' ? 'selected' : '' }}>Issue Statistics</option>
                    </select>
                </div>

                <div id="date_range" style="display: none;">
                    <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="from_date" id="from_date" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div id="date_range_to" style="display: none;">
                    <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="to_date" id="to_date" value="{{ request('to_date', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div id="student_filter" style="display: none;">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                    <select name="student_id" id="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="category_filter" style="display: none;">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ url('/admin/library/reports') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Clear</a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Generate Report</button>
            </div>
        </form>
    </div>

    <!-- Report Results -->
    @if(isset($reportData) && $reportData)
    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                @if($reportType == 'popular_books') Popular Books Report
                @elseif($reportType == 'overdue_books') Overdue Books Report
                @elseif($reportType == 'student_history') Student History Report
                @elseif($reportType == 'category_wise') Category Wise Report
                @elseif($reportType == 'fine_collection') Fine Collection Report
                @elseif($reportType == 'issue_statistics') Issue Statistics Report
                @endif
            </h3>
        </div>

        @if($summary)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @foreach($summary as $key => $value)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm font-medium text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">
                    @if(is_numeric($value))
                        @if(str_contains($key, 'fine') || str_contains($key, 'amount'))
                            ₹{{ number_format($value, 2) }}
                        @else
                            {{ number_format($value) }}
                        @endif
                    @else
                        {{ $value }}
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Report Data Tables -->
        @if($reportType == 'popular_books')
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Issue Count</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reportData as $item)
                    <tr>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">{{ $item->book->title }}</div>
                            <div class="text-xs text-gray-500">by {{ $item->book->author }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $item->book->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $item->issue_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @elseif($reportType == 'overdue_books')
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Days Overdue</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Fine</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reportData as $issue)
                    <tr>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">{{ $issue->book->title }}</div>
                            <div class="text-xs text-gray-500">by {{ $issue->book->author }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">{{ $issue->student->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $issue->student->admission_number }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $issue->due_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-center text-red-600 font-semibold">{{ $issue->due_date->diffInDays(now()) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900">₹{{ number_format($issue->fine_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @elseif($reportType == 'student_history' || $reportType == 'fine_collection')
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issue Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Return Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        @if($reportType == 'fine_collection')
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Fine</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reportData as $issue)
                    <tr>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">{{ $issue->book->title }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">{{ $issue->student->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $issue->student->admission_number }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $issue->issue_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $issue->due_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $issue->return_date ? $issue->return_date->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $issue->status === 'returned' ? 'bg-green-100 text-green-800' : ($issue->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($issue->status) }}
                            </span>
                        </td>
                        @if($reportType == 'fine_collection')
                        <td class="px-4 py-3 text-sm text-right text-gray-900">₹{{ number_format($issue->fine_amount, 2) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @elseif($reportType == 'category_wise')
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Issues</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Available</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reportData as $book)
                    <tr>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">{{ $book->title }}</div>
                            <div class="text-xs text-gray-500">by {{ $book->author }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $book->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $book->issues_count }}</td>
                        <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $book->available_copies }} / {{ $book->copies }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif
</div>

<script>
document.getElementById('report_type').addEventListener('change', function() {
    const reportType = this.value;
    const dateRange = document.getElementById('date_range');
    const dateRangeTo = document.getElementById('date_range_to');
    const studentFilter = document.getElementById('student_filter');
    const categoryFilter = document.getElementById('category_filter');

    // Hide all filters first
    dateRange.style.display = 'none';
    dateRangeTo.style.display = 'none';
    studentFilter.style.display = 'none';
    categoryFilter.style.display = 'none';

    // Show relevant filters based on report type
    if (['popular_books', 'student_history', 'fine_collection', 'issue_statistics'].includes(reportType)) {
        dateRange.style.display = 'block';
        dateRangeTo.style.display = 'block';
    }

    if (reportType === 'student_history') {
        studentFilter.style.display = 'block';
    }

    if (reportType === 'category_wise') {
        categoryFilter.style.display = 'block';
    }
});

// Initialize on page load
if (document.getElementById('report_type').value) {
    document.getElementById('report_type').dispatchEvent(new Event('change'));
}
</script>
@endsection

