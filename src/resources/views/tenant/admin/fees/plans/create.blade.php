@extends('tenant.layouts.admin')

@section('title', 'Create Fee Plan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/fees/plans') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Fee Plans</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Create Fee Plan
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Set up a new fee structure for a class
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/fees/plans') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ url('/admin/fees/plans') }}" method="POST" id="feePlanForm">
        @csrf

        <!-- Basic Details -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Plan Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Plan Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('name') border-red-300 @enderror"
                        placeholder="e.g., Class 10 Annual Fee 2024-25">
                </div>

                <!-- Class -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('class_id') border-red-300 @enderror">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Academic Year -->
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700">
                        Academic Year <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('academic_year') border-red-300 @enderror"
                        placeholder="2024-2025">
                </div>

                <!-- Term -->
                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700">
                        Term <span class="text-red-500">*</span>
                    </label>
                    <select name="term" id="term" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('term') border-red-300 @enderror">
                        <option value="">Select Term</option>
                        <option value="annual" {{ old('term') == 'annual' ? 'selected' : '' }}>Annual</option>
                        <option value="semester_1" {{ old('term') == 'semester_1' ? 'selected' : '' }}>Semester 1</option>
                        <option value="semester_2" {{ old('term') == 'semester_2' ? 'selected' : '' }}>Semester 2</option>
                        <option value="quarterly_1" {{ old('term') == 'quarterly_1' ? 'selected' : '' }}>Quarterly 1</option>
                        <option value="quarterly_2" {{ old('term') == 'quarterly_2' ? 'selected' : '' }}>Quarterly 2</option>
                        <option value="quarterly_3" {{ old('term') == 'quarterly_3' ? 'selected' : '' }}>Quarterly 3</option>
                        <option value="quarterly_4" {{ old('term') == 'quarterly_4' ? 'selected' : '' }}>Quarterly 4</option>
                    </select>
                </div>

                <!-- Effective From -->
                <div>
                    <label for="effective_from" class="block text-sm font-medium text-gray-700">
                        Effective From <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="effective_from" id="effective_from" value="{{ old('effective_from', date('Y-m-d')) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('effective_from') border-red-300 @enderror">
                </div>

                <!-- Effective To -->
                <div>
                    <label for="effective_to" class="block text-sm font-medium text-gray-700">Effective To</label>
                    <input type="date" name="effective_to" id="effective_to" value="{{ old('effective_to') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Optional plan description">{{ old('description') }}</textarea>
                </div>

                <!-- Is Active -->
                <div class="md:col-span-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Active Plan
                    </label>
                </div>
            </div>
        </div>

        <!-- Fee Components -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Fee Components</h3>
                <button type="button" onclick="addComponent()"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Component
                </button>
            </div>

            <div id="componentsContainer" class="space-y-4">
                <!-- Components will be added here dynamically -->
            </div>

            <div id="noComponentsMessage" class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                <p class="mt-2 text-sm">No components added yet. Click "Add Component" to start.</p>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ url('/admin/fees/plans') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Create Fee Plan
            </button>
        </div>
    </form>
    </div>
</div>

<script>
let componentIndex = 0;
const components = @json($components);

function addComponent() {
    const container = document.getElementById('componentsContainer');
    const noMessage = document.getElementById('noComponentsMessage');

    const componentHtml = `
        <div class="border border-gray-200 rounded-md p-4" id="component-${componentIndex}">
            <div class="flex justify-between items-start mb-3">
                <h4 class="text-sm font-medium text-gray-900">Component #${componentIndex + 1}</h4>
                <button type="button" onclick="removeComponent(${componentIndex})"
                    class="text-red-600 hover:text-red-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Fee Component <span class="text-red-500">*</span>
                    </label>
                    <select name="components[${componentIndex}][fee_component_id]"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        required>
                        <option value="">Select Component</option>
                        ${components.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Amount <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="components[${componentIndex}][amount]"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="0.00" step="0.01" min="0" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="date" name="components[${componentIndex}][due_date]"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="flex items-center pt-6">
                    <input type="checkbox" name="components[${componentIndex}][is_mandatory]" value="1" checked
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label class="ml-2 text-sm text-gray-700">Mandatory</label>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', componentHtml);
    noMessage.style.display = 'none';
    componentIndex++;
}

function removeComponent(index) {
    const element = document.getElementById(`component-${index}`);
    element.remove();

    const container = document.getElementById('componentsContainer');
    const noMessage = document.getElementById('noComponentsMessage');

    if (container.children.length === 0) {
        noMessage.style.display = 'block';
    }
}

// Add first component on load
window.addEventListener('DOMContentLoaded', () => {
    addComponent();
});
</script>
@endsection

