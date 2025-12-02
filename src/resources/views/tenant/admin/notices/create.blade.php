@extends('tenant.layouts.admin')

@section('title', 'Create Notice')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/notices') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Notices</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Create</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create Notice</h2>
            <p class="mt-1 text-sm text-gray-500">Publish a new notice for your school</p>
        </div>
    </div>

    <form action="{{ url('/admin/notices') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
        @csrf

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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="notice_type" class="block text-sm font-medium text-gray-700">Notice Type <span class="text-red-500">*</span></label>
                    <select name="notice_type" id="notice_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="general" {{ old('notice_type') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="academic" {{ old('notice_type') == 'academic' ? 'selected' : '' }}>Academic</option>
                        <option value="event" {{ old('notice_type') == 'event' ? 'selected' : '' }}>Event</option>
                        <option value="announcement" {{ old('notice_type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                        <option value="circular" {{ old('notice_type') == 'circular' ? 'selected' : '' }}>Circular</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" id="priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }} selected>Normal</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div>
                    <label for="target_audience" class="block text-sm font-medium text-gray-700">Target Audience <span class="text-red-500">*</span></label>
                    <select name="target_audience" id="target_audience" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }} selected>All</option>
                        <option value="students" {{ old('target_audience') == 'students' ? 'selected' : '' }}>Students</option>
                        <option value="teachers" {{ old('target_audience') == 'teachers' ? 'selected' : '' }}>Teachers</option>
                        <option value="staff" {{ old('target_audience') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="parents" {{ old('target_audience') == 'parents' ? 'selected' : '' }}>Parents</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Leave empty for notices without expiry</p>
                </div>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Content <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="10" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('content') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">You can use basic HTML formatting</p>
            </div>

            <div>
                <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments</label>
                <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                <p class="mt-1 text-xs text-gray-500">You can upload multiple files (Max 10MB per file)</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/notices') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Create Notice</button>
        </div>
    </form>
</div>
@endsection

