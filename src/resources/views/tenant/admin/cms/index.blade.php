@extends('tenant.layouts.cms')

@section('title', 'CMS Dashboard')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Content Management System</h2>
            <p class="mt-1 text-sm text-gray-500">Manage your school website content</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                    <x-heroicon-o-document-text class="h-6 w-6 text-primary-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pages</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pages'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ url('/admin/cms/pages') }}" class="text-sm text-primary-600 hover:text-primary-900">Manage Pages →</a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <x-heroicon-o-newspaper class="h-6 w-6 text-purple-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Blog Posts</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['posts'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ url('/admin/cms/posts') }}" class="text-sm text-primary-600 hover:text-primary-900">Manage Posts →</a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <x-heroicon-o-photo class="h-6 w-6 text-green-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Media Files</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['media'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ url('/admin/cms/media') }}" class="text-sm text-primary-600 hover:text-primary-900">Media Library →</a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                    <x-heroicon-o-bars-3 class="h-6 w-6 text-orange-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Menus</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['menus'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ url('/admin/cms/menus') }}" class="text-sm text-primary-600 hover:text-primary-900">Manage Menus →</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ url('/admin/cms/pages/create') }}" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <x-heroicon-o-plus-circle class="h-8 w-8 text-gray-400 mr-3" />
                <div>
                    <p class="font-medium text-gray-900">Create New Page</p>
                    <p class="text-sm text-gray-500">Add a new page to your website</p>
                </div>
            </a>

            <a href="{{ url('/admin/cms/posts/create') }}" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <x-heroicon-o-plus-circle class="h-8 w-8 text-gray-400 mr-3" />
                <div>
                    <p class="font-medium text-gray-900">Write Blog Post</p>
                    <p class="text-sm text-gray-500">Create a new blog post or news article</p>
                </div>
            </a>

            <a href="{{ url('/admin/cms/settings') }}" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors">
                <x-heroicon-o-cog-6-tooth class="h-8 w-8 text-gray-400 mr-3" />
                <div>
                    <p class="font-medium text-gray-900">CMS Settings</p>
                    <p class="text-sm text-gray-500">Configure site settings and theme</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Getting Started -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-o-information-circle class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Getting Started with CMS</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Start by configuring your site settings and theme, then create your first page or blog post.</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li><a href="{{ url('/admin/cms/settings') }}" class="underline">Configure CMS Settings & Theme</a></li>
                        <li><a href="{{ url('/admin/cms/pages') }}" class="underline">Manage Pages</a></li>
                        <li><a href="{{ url('/admin/cms/media') }}" class="underline">Upload Media Files</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

