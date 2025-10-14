@extends('tenant.layouts.admin')

@section('title', 'Create Fee Plan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ url('/admin/fees/plans') }}" 
                   class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Create Fee Plan</h1>
            </div>
            <p class="text-gray-600">Set up a new fee structure for a class</p>
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
        <form action="{{ url('/admin/fees/plans') }}" method="POST" id="feePlanForm">
            @csrf

            <!-- Basic Details -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Basic Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Plan Name -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            Plan Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., Class 10 Annual Fee 2024-25"
                               required>
                    </div>

                    <!-- Class -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            Class <span class="text-red-500">*</span>
                        </label>
                        <select name="class_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Academic Year -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="academic_year" 
                               value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="2024-2025"
                               required>
                    </div>

                    <!-- Term -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            Term <span class="text-red-500">*</span>
                        </label>
                        <select name="term" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required>
                            <option value="">-- Select Term --</option>
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
                        <label class="block text-gray-700 font-semibold mb-2">
                            Effective From <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="effective_from" 
                               value="{{ old('effective_from', date('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>

                    <!-- Effective To -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            Effective To
                        </label>
                        <input type="date" 
                               name="effective_to" 
                               value="{{ old('effective_to') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea name="description" 
                              rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Optional plan description">{{ old('description') }}</textarea>
                </div>

                <!-- Is Active -->
                <div class="mt-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-gray-700 font-semibold">Active Plan</span>
                    </label>
                </div>
            </div>

            <!-- Fee Components -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Fee Components</h2>
                    <button type="button" 
                            onclick="addComponent()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i>Add Component
                    </button>
                </div>

                <div id="componentsContainer">
                    <!-- Components will be added here dynamically -->
                </div>

                <div id="noComponentsMessage" class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>No components added yet. Click "Add Component" to start.</p>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition">
                    <i class="fas fa-save mr-2"></i>Create Fee Plan
                </button>
                <a href="{{ url('/admin/fees/plans') }}" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
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
        <div class="border border-gray-300 rounded-lg p-4 mb-4" id="component-${componentIndex}">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-semibold text-gray-800">Component #${componentIndex + 1}</h3>
                <button type="button" 
                        onclick="removeComponent(${componentIndex})"
                        class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fee Component *</label>
                    <select name="components[${componentIndex}][fee_component_id]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                            required>
                        <option value="">-- Select Component --</option>
                        ${components.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                    <input type="number" 
                           name="components[${componentIndex}][amount]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                           placeholder="0.00"
                           step="0.01"
                           min="0"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" 
                           name="components[${componentIndex}][due_date]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="components[${componentIndex}][is_mandatory]" 
                               value="1"
                               checked
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Mandatory</span>
                    </label>
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

