@extends('tenant.layouts.cms')

@section('title', 'Page Preview')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/cms/pages') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Pages</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Preview</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $page['title'] }}</h2>
            <p class="mt-1 text-sm text-gray-500">Page preview</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ url('/admin/cms/pages/' . $page['id'] . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <x-heroicon-o-pencil class="h-5 w-5 mr-2" />
                Edit Page
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Title</h3>
                <p class="mt-1 text-lg text-gray-900">{{ $page['title'] }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Slug</h3>
                <p class="mt-1 text-sm text-gray-900">/{{ $page['slug'] }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Template</h3>
                <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ ucfirst($page['template']) }}
                </span>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $page['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($page['status']) }}
                </span>
            </div>

            @if($page['content'])
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Content</h3>
                <div class="mt-1 prose max-w-none">
                    {!! nl2br(e($page['content'])) !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

