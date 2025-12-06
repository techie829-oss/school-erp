@extends('tenant.layouts.cms')

@section('title', 'General Settings')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/cms/settings') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Settings</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">General</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">General Settings</h2>
            <p class="mt-1 text-sm text-gray-500">Configure your website's basic information</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ url('/admin/cms/settings/general') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
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
            <!-- Header Settings Section -->
            <div class="border-b pb-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Header Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="default_language" class="block text-sm font-medium text-gray-700 mb-2">Default Language</label>
                        <select name="default_language" id="default_language" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            @php
                                $languages = config('content.pages.languages', ['en' => 'English']);
                                $currentLang = old('default_language', $settings->default_language ?? config('content.pages.default_language', 'en'));
                            @endphp
                            @foreach($languages as $langCode => $langName)
                            <option value="{{ $langCode }}" {{ $currentLang === $langCode ? 'selected' : '' }}>
                                {{ $langName }}
                            </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Set the default language for your website content</p>
                    </div>
                </div>
            </div>

            <!-- General Settings Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings->site_name ?? $tenant->data['name'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">The name of your school/website</p>
                </div>

                <div class="md:col-span-2">
                    <label for="site_tagline" class="block text-sm font-medium text-gray-700">Site Tagline</label>
                    <input type="text" name="site_tagline" id="site_tagline" value="{{ old('site_tagline', $settings->site_tagline ?? '') }}" placeholder="Excellence in Education" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">A short tagline for your website</p>
                </div>

                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700">Logo</label>
                    @if($settings && $settings->logo)
                    <div class="mt-2 mb-2">
                        <img src="{{ Storage::url($settings->logo) }}" alt="Logo" class="h-16 w-auto">
                    </div>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="mt-1 text-xs text-gray-500">Recommended: 200x60px, PNG or SVG</p>
                </div>

                <div>
                    <label for="favicon" class="block text-sm font-medium text-gray-700">Favicon</label>
                    @if($settings && $settings->favicon)
                    <div class="mt-2 mb-2">
                        <img src="{{ Storage::url($settings->favicon) }}" alt="Favicon" class="h-8 w-8">
                    </div>
                    @endif
                    <input type="file" name="favicon" id="favicon" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="mt-1 text-xs text-gray-500">Recommended: 32x32px, ICO or PNG</p>
                </div>

                <div class="md:col-span-2">
                    <label for="footer_text" class="block text-sm font-medium text-gray-700">Footer Text</label>
                    <textarea name="footer_text" id="footer_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('footer_text', $settings->footer_text ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Text to display in website footer</p>
                </div>

                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings->contact_email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                    <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings->contact_phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <label for="contact_address" class="block text-sm font-medium text-gray-700">Contact Address</label>
                    <textarea name="contact_address" id="contact_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('contact_address', $settings->contact_address ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/cms/settings') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Save Settings</button>
        </div>
    </form>
</div>
@endsection

