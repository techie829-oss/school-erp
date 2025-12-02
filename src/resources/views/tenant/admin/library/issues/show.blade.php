@extends('tenant.layouts.admin')

@section('title', 'Book Issue Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/library/issues') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Book Issues</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Book Issue Details</h2>
            <p class="mt-1 text-sm text-gray-500">Issue #{{ $issue->id }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/library/issues') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Issue Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Issue Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Book</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $issue->book->title }}</dd>
                        <dd class="text-xs text-gray-500">by {{ $issue->book->author }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Student</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $issue->student->full_name }}</dd>
                        <dd class="text-xs text-gray-500">{{ $issue->student->admission_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issue->issue_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $issue->due_date->format('d M Y') }}
                            @if($issue->is_overdue)
                                <span class="ml-2 text-red-600 text-xs font-semibold">(Overdue)</span>
                            @endif
                        </dd>
                    </div>
                    @if($issue->return_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Return Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issue->return_date->format('d M Y') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $issue->status === 'issued' ? 'bg-blue-100 text-blue-800' : ($issue->status === 'returned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($issue->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($issue->renewal_count > 0)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Renewals</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issue->renewal_count }} time(s)</dd>
                    </div>
                    @endif
                    @if($issue->fine_amount > 0)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fine Amount</dt>
                        <dd class="mt-1 text-sm font-semibold text-red-600">₹{{ number_format($issue->fine_amount, 2) }}</dd>
                    </div>
                    @endif
                    @if($issue->paid_fine > 0)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Paid Fine</dt>
                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($issue->paid_fine, 2) }}</dd>
                    </div>
                    @endif
                    @if($issue->issuedBy)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Issued By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issue->issuedBy->name }}</dd>
                    </div>
                    @endif
                    @if($issue->returnedBy)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Returned By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issue->returnedBy->name }}</dd>
                    </div>
                    @endif
                </dl>
                @if($issue->issue_notes)
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500">Issue Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $issue->issue_notes }}</dd>
                </div>
                @endif
                @if($issue->return_notes)
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500">Return Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $issue->return_notes }}</dd>
                </div>
                @endif
            </div>

            <!-- Actions -->
            @if(in_array($issue->status, ['issued', 'overdue']))
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                
                <!-- Return Book Form -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Return Book</h4>
                    <form action="{{ url('/admin/library/issues/' . $issue->id . '/return') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="return_date" class="block text-sm font-medium text-gray-700">Return Date <span class="text-red-500">*</span></label>
                                <input type="date" name="return_date" id="return_date" value="{{ old('return_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            @if($issue->is_overdue || $issue->fine_amount > 0)
                            <div>
                                <label for="fine_amount" class="block text-sm font-medium text-gray-700">Fine Amount</label>
                                <input type="number" name="fine_amount" id="fine_amount" value="{{ old('fine_amount', $issue->fine_amount) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">Calculated: ₹{{ number_format($issue->fine_amount, 2) }}</p>
                            </div>
                            @endif
                            <div>
                                <label for="return_notes" class="block text-sm font-medium text-gray-700">Return Notes</label>
                                <input type="text" name="return_notes" id="return_notes" value="{{ old('return_notes') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Return Book
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Renew Book Form -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Renew Book</h4>
                    <form action="{{ url('/admin/library/issues/' . $issue->id . '/renew') }}" method="POST">
                        @csrf
                        <p class="text-sm text-gray-600 mb-3">Extend the due date for this book issue.</p>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            Renew Book
                        </button>
                    </form>
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
                    @if($issue->is_overdue)
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Days Overdue</dt>
                        <dd class="text-sm font-semibold text-red-600">{{ $issue->due_date->diffInDays(now()) }} days</dd>
                    </div>
                    @else
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Days Remaining</dt>
                        <dd class="text-sm font-semibold text-green-600">{{ max(0, now()->diffInDays($issue->due_date, false)) }} days</dd>
                    </div>
                    @endif
                    @if($issue->fine_amount > 0)
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-sm font-bold text-gray-900">Outstanding Fine</dt>
                        <dd class="text-sm font-bold text-red-600">₹{{ number_format($issue->fine_amount - $issue->paid_fine, 2) }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Book Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Book Details</h3>
                <div class="text-sm">
                    <p class="font-medium text-gray-900">{{ $issue->book->title }}</p>
                    <p class="text-gray-500 mt-1">by {{ $issue->book->author }}</p>
                    @if($issue->book->isbn)
                    <p class="text-gray-500 mt-1">ISBN: {{ $issue->book->isbn }}</p>
                    @endif
                    <a href="{{ url('/admin/library/books/' . $issue->book->id) }}" class="mt-2 inline-block text-primary-600 hover:text-primary-900 text-xs">View Book Details →</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

