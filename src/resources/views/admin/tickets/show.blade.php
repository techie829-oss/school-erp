@extends('layouts.admin')

@section('title', 'Ticket Details')
@section('page-title', 'Ticket Details')
@section('page-description', 'View and manage ticket details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $ticket->ticket_number }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $ticket->title }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Ticket
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Tickets
            </a>
        </div>
    </div>

    <!-- Ticket Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Description</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="prose max-w-none">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $ticket->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Comments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Comments</h3>
                </div>

                <!-- Add Comment Form -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <form action="{{ route('admin.tickets.comments', $ticket) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Add Comment</label>
                                <textarea name="comment" id="comment" rows="3" required
                                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Add a comment..."></textarea>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_internal" id="is_internal" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_internal" class="ml-2 text-sm text-gray-700">Internal note (not visible to users)</label>
                            </div>
                            <div>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Add Comment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Comments List -->
                <div class="px-6 py-4">
                    @if($ticket->comments->count() > 0)
                        <div class="space-y-4">
                            @foreach($ticket->comments as $comment)
                                <div class="border-l-4 {{ $comment->is_internal ? 'border-yellow-400 bg-yellow-50' : 'border-blue-400 bg-blue-50' }} p-4 rounded-r-lg">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                                                @if($comment->is_internal)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Internal
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $comment->created_at->format('M j, Y \a\t g:i A') }}</p>
                                            <div class="mt-2 text-gray-900 whitespace-pre-wrap">{{ $comment->comment }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p>No comments yet</p>
                            <p class="text-sm">Be the first to add a comment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status & Priority -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ticket Details</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->status_badge }} mt-1">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->priority_badge }} mt-1">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->type_badge }} mt-1">
                            {{ ucfirst(str_replace('_', ' ', $ticket->type)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Assignment -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Assignment</h3>
                </div>
                <div class="px-6 py-4">
                    <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                                <select name="assigned_to" id="assigned_to" onchange="this.form.submit()"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Unassigned</option>
                                    @foreach($adminUsers as $user)
                                        <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ticket Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Information</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created By</label>
                        <p class="text-sm text-gray-900">{{ $ticket->creator->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created</label>
                        <p class="text-sm text-gray-900">{{ $ticket->created_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>

                    @if($ticket->due_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Due Date</label>
                            <p class="text-sm text-gray-900">{{ $ticket->due_date->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    @endif

                    @if($ticket->resolved_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Resolved</label>
                            <p class="text-sm text-gray-900">{{ $ticket->resolved_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    @endif

                    @if($ticket->tenant)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tenant</label>
                            <p class="text-sm text-gray-900">{{ $ticket->tenant->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
