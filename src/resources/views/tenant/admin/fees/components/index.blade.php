@extends('tenant.layouts.admin')

@section('title', 'Fee Components')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Fee Components</h1>
                <p class="text-gray-600 mt-1">Manage fee types and components</p>
            </div>
            <a href="{{ url('/admin/fees/components/create') }}" 
               class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-plus mr-2"></i>Add Component
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Components Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($components->count() > 0)
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left">Code</th>
                            <th class="px-6 py-4 text-left">Name</th>
                            <th class="px-6 py-4 text-left">Type</th>
                            <th class="px-6 py-4 text-left">Description</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($components as $component)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $component->code }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $component->name }}</td>
                                <td class="px-6 py-4">
                                    @if($component->type == 'recurring')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            <i class="fas fa-sync-alt mr-1"></i>Recurring
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>One Time
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-sm">{{ $component->description ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($component->is_active)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ url('/admin/fees/components/' . $component->id . '/edit') }}" 
                                           class="px-3 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ url('/admin/fees/components/' . $component->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure? This cannot be undone if component is not in use.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-red-100 text-red-600 rounded hover:bg-red-200 transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50">
                    {{ $components->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-money-bill-wave text-5xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Fee Components Yet</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first fee component</p>
                    <a href="{{ url('/admin/fees/components/create') }}" 
                       class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition inline-block">
                        <i class="fas fa-plus mr-2"></i>Add First Component
                    </a>
                </div>
            @endif
        </div>

        <!-- Quick Stats -->
        @if($components->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-list text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Total Components</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $components->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Active Components</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $components->where('is_active', 1)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-sync-alt text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Recurring Type</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $components->where('type', 'recurring')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

