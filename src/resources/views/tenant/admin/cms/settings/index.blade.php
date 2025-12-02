@extends('tenant.layouts.cms')

@section('title', 'CMS Settings')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Settings</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">CMS Settings</h2>
            <p class="mt-1 text-sm text-gray-500">Configure your website settings and appearance</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Settings Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- General Settings -->
        <a href="{{ url('/admin/cms/settings/general') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                    <x-heroicon-o-cog-6-tooth class="h-6 w-6 text-primary-600" />
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">General Settings</h3>
                    <p class="text-sm text-gray-500">Site name, logo, contact info</p>
                </div>
            </div>
            <div class="text-sm text-primary-600 hover:text-primary-900">Configure →</div>
        </a>

        <!-- Theme Settings -->
        <a href="{{ url('/admin/cms/settings/theme') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <x-heroicon-o-paint-brush class="h-6 w-6 text-purple-600" />
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Theme & Colors</h3>
                    <p class="text-sm text-gray-500">Customize colors and SCSS</p>
                </div>
            </div>
            <div class="text-sm text-primary-600 hover:text-primary-900">Customize →</div>
        </a>

        <!-- Social Media -->
        <a href="{{ url('/admin/cms/settings/social') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <x-heroicon-o-link class="h-6 w-6 text-green-600" />
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Social Media</h3>
                    <p class="text-sm text-gray-500">Social media links</p>
                </div>
            </div>
            <div class="text-sm text-primary-600 hover:text-primary-900">Configure →</div>
        </a>
    </div>
</div>
@endsection

