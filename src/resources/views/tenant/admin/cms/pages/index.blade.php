@extends('tenant.layouts.cms')

@section('title', 'Pages')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Pages</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Pages</h2>
            <p class="mt-1 text-sm text-gray-500">Manage your website pages</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ url('/admin/cms/pages/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                Create New Page
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Pages List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($pages as $page)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $page['title'] }}</div>
                        <div class="text-sm text-gray-500">/{{ $page['slug'] }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($page['template']) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $page['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($page['status']) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $page['updated_at']->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ url('/admin/cms/pages/' . $page['id'] . '/edit') }}" class="text-primary-600 hover:text-primary-900">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </a>
                            <a href="{{ url('/admin/cms/pages/' . $page['id']) }}" class="text-gray-600 hover:text-gray-900">
                                <x-heroicon-o-eye class="h-5 w-5" />
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

