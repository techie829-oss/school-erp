@extends('tenant.layouts.admin')

@section('title', 'Library Settings')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Library</span></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Settings</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Library Settings</h2>
            <p class="mt-1 text-sm text-gray-500">Configure library rules and policies</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ url('/admin/library/settings') }}" method="POST" class="max-w-4xl">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <!-- Issue Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Issue Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="max_books_per_student" class="block text-sm font-medium text-gray-700">Max Books Per Student <span class="text-red-500">*</span></label>
                        <input type="number" name="max_books_per_student" id="max_books_per_student" value="{{ old('max_books_per_student', $settings->max_books_per_student) }}" min="1" max="10" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Maximum number of books a student can issue at once</p>
                    </div>

                    <div>
                        <label for="issue_duration_days" class="block text-sm font-medium text-gray-700">Issue Duration (Days) <span class="text-red-500">*</span></label>
                        <input type="number" name="issue_duration_days" id="issue_duration_days" value="{{ old('issue_duration_days', $settings->issue_duration_days) }}" min="1" max="90" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Default number of days a book can be issued</p>
                    </div>
                </div>
            </div>

            <!-- Fine Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Fine Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fine_per_day" class="block text-sm font-medium text-gray-700">Fine Per Day (₹) <span class="text-red-500">*</span></label>
                        <input type="number" name="fine_per_day" id="fine_per_day" value="{{ old('fine_per_day', $settings->fine_per_day) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Fine amount charged per day for overdue books</p>
                    </div>

                    <div>
                        <label for="book_lost_fine" class="block text-sm font-medium text-gray-700">Book Lost Fine (₹)</label>
                        <input type="number" name="book_lost_fine" id="book_lost_fine" value="{{ old('book_lost_fine', $settings->book_lost_fine) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Fine amount for lost books</p>
                    </div>

                    <div>
                        <label for="book_damage_fine" class="block text-sm font-medium text-gray-700">Book Damage Fine (₹)</label>
                        <input type="number" name="book_damage_fine" id="book_damage_fine" value="{{ old('book_damage_fine', $settings->book_damage_fine) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Fine amount for damaged books</p>
                    </div>
                </div>
            </div>

            <!-- Renewal Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Renewal Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="max_renewals" class="block text-sm font-medium text-gray-700">Max Renewals <span class="text-red-500">*</span></label>
                        <input type="number" name="max_renewals" id="max_renewals" value="{{ old('max_renewals', $settings->max_renewals) }}" min="0" max="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Maximum number of times a book can be renewed</p>
                    </div>

                    <div>
                        <label for="renewal_duration_days" class="block text-sm font-medium text-gray-700">Renewal Duration (Days) <span class="text-red-500">*</span></label>
                        <input type="number" name="renewal_duration_days" id="renewal_duration_days" value="{{ old('renewal_duration_days', $settings->renewal_duration_days) }}" min="1" max="30" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Number of days added when a book is renewed</p>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="send_overdue_notifications" value="1" {{ old('send_overdue_notifications', $settings->send_overdue_notifications) ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Send Overdue Notifications</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Enable automatic notifications for overdue books</p>
                    </div>

                    <div>
                        <label for="overdue_notification_days" class="block text-sm font-medium text-gray-700">Notification Days Before Due</label>
                        <input type="number" name="overdue_notification_days" id="overdue_notification_days" value="{{ old('overdue_notification_days', $settings->overdue_notification_days) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Send notification this many days before due date</p>
                    </div>
                </div>
            </div>

            <!-- Other Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Other Settings</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="allow_online_issue" value="1" {{ old('allow_online_issue', $settings->allow_online_issue) ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Allow Online Issue</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Allow students to issue books online (if student portal is available)</p>
                    </div>

                    <div>
                        <label for="library_rules" class="block text-sm font-medium text-gray-700">Library Rules</label>
                        <textarea name="library_rules" id="library_rules" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('library_rules', $settings->library_rules) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Library rules and policies (will be displayed to students)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/library/books') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Save Settings</button>
        </div>
    </form>
</div>
@endsection

