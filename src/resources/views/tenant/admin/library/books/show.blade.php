@extends('tenant.layouts.admin')

@section('title', 'Book Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/library/books') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Books</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $book->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">by {{ $book->author }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/library/books/' . $book->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/library/books') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Book Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Book Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $book->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Author</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->author }}</dd>
                    </div>
                    @if($book->isbn)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->isbn }}</dd>
                    </div>
                    @endif
                    @if($book->publisher)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Publisher</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->publisher }}</dd>
                    </div>
                    @endif
                    @if($book->category)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->category->name }}</dd>
                    </div>
                    @endif
                    @if($book->edition)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Edition</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->edition }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Language</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->language ?? 'English' }}</dd>
                    </div>
                    @if($book->publication_year)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Publication Year</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->publication_year }}</dd>
                    </div>
                    @endif
                    @if($book->rack_number)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Rack Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->rack_number }}</dd>
                    </div>
                    @endif
                    @if($book->barcode)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $book->barcode }}</dd>
                    </div>
                    @endif
                    @if($book->price)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Price</dt>
                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($book->price, 2) }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $book->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($book->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
                @if($book->description)
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $book->description }}</dd>
                </div>
                @endif
            </div>

            <!-- Active Issues -->
            @if($book->activeIssues->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Active Issues ({{ $book->activeIssues->count() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issue Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($book->activeIssues as $issue)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-medium text-gray-900">{{ $issue->student->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $issue->student->admission_number }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $issue->issue_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $issue->due_date->format('d M Y') }}
                                    @if($issue->is_overdue)
                                        <span class="ml-2 text-red-600 text-xs">(Overdue)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $issue->status === 'issued' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($issue->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium">
                                    <a href="{{ url('/admin/library/issues/' . $issue->id) }}" class="text-primary-600 hover:text-primary-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Summary</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Total Copies</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $book->copies }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Available Copies</dt>
                        <dd class="text-sm font-medium text-green-600">{{ $book->available_copies }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Issued Copies</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $book->copies - $book->available_copies }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-sm font-bold text-gray-900">Availability</dt>
                        <dd class="text-sm font-bold {{ $book->is_available ? 'text-green-600' : 'text-red-600' }}">
                            {{ $book->is_available ? 'Available' : 'Not Available' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Issue History -->
            @if($book->issues->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Issue History</h3>
                <div class="text-sm text-gray-500">
                    <p>Total Issues: {{ $book->issues->count() }}</p>
                    <p class="mt-2">Returned: {{ $book->issues->where('status', 'returned')->count() }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

