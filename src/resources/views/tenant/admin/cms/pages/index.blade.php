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
            <p class="mt-1 text-sm text-gray-500">Manage content for your website pages</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pages as $page)
        <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $page->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $page->slug === '' ? '/' : '/' . $page->slug }}</p>
                    </div>
                    @if($page->is_published)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Published
                        </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Draft
                        </span>
                    @endif
                </div>

                @if($page->meta_description)
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $page->meta_description }}</p>
                @endif

                <div class="flex items-center justify-between">
                    <a href="{{ url('/admin/cms/pages/' . $page->id . '/edit') }}" class="text-primary-600 hover:text-primary-900 font-medium text-sm">
                        Edit Content →
                            </a>
                    <a href="{{ url($page->slug === '' ? '/' : '/' . $page->slug) }}" target="_blank" class="text-gray-500 hover:text-gray-700 text-sm">
                        <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                            </a>
                        </div>
            </div>
        </div>
                @endforeach
    </div>
</div>
@endsection
