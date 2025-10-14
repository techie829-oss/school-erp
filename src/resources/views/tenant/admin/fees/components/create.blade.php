@extends('tenant.layouts.admin')

@section('title', 'Create Fee Component')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ url('/admin/fees/components') }}" 
                   class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Create Fee Component</h1>
            </div>
            <p class="text-gray-600">Add a new fee type or component</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="{{ url('/admin/fees/components') }}" method="POST">
                @csrf

                <!-- Code -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Component Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           value="{{ old('code') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase"
                           placeholder="e.g., TUI, TRANS, EXAM"
                           required>
                    <p class="text-sm text-gray-500 mt-1">Short unique code (auto-converted to uppercase)</p>
                </div>

                <!-- Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Component Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., Tuition Fee, Transport Fee"
                           required>
                </div>

                <!-- Type -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Fee Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">-- Select Type --</option>
                        <option value="recurring" {{ old('type') == 'recurring' ? 'selected' : '' }}>
                            Recurring (Monthly/Quarterly/Annually)
                        </option>
                        <option value="one_time" {{ old('type') == 'one_time' ? 'selected' : '' }}>
                            One Time (Admission, Registration, etc.)
                        </option>
                    </select>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Description
                    </label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Optional description">{{ old('description') }}</textarea>
                </div>

                <!-- Is Active -->
                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-gray-700 font-semibold">Active Component</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-1 ml-8">Component will be available for use in fee plans</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-save mr-2"></i>Create Component
                    </button>
                    <a href="{{ url('/admin/fees/components') }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <h3 class="font-semibold text-blue-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>Component Types
            </h3>
            <div class="space-y-2 text-sm text-blue-800">
                <p><strong>Recurring:</strong> Fees charged regularly (Monthly, Quarterly, Annually)</p>
                <p class="ml-4">Examples: Tuition Fee, Transport Fee, Library Fee</p>
                <p class="mt-2"><strong>One Time:</strong> Fees charged once</p>
                <p class="ml-4">Examples: Admission Fee, Registration Fee, Exam Fee</p>
            </div>
        </div>
    </div>
</div>
@endsection

