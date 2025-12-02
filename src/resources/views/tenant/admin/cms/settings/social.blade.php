@extends('tenant.layouts.cms')

@section('title', 'Social Media Settings')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/cms/settings') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Settings</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Social Media</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Social Media Settings</h2>
            <p class="mt-1 text-sm text-gray-500">Add your social media links</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ url('/admin/cms/settings/social') }}" method="POST" class="max-w-2xl">
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
            <div class="space-y-4">
                <div>
                    <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                    <input type="url" name="social_facebook" id="social_facebook" value="{{ old('social_facebook', $settings->social_facebook ?? '') }}" placeholder="https://facebook.com/yourschool" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                    <input type="url" name="social_twitter" id="social_twitter" value="{{ old('social_twitter', $settings->social_twitter ?? '') }}" placeholder="https://twitter.com/yourschool" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                    <input type="url" name="social_instagram" id="social_instagram" value="{{ old('social_instagram', $settings->social_instagram ?? '') }}" placeholder="https://instagram.com/yourschool" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="social_linkedin" class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                    <input type="url" name="social_linkedin" id="social_linkedin" value="{{ old('social_linkedin', $settings->social_linkedin ?? '') }}" placeholder="https://linkedin.com/company/yourschool" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="social_youtube" class="block text-sm font-medium text-gray-700">YouTube URL</label>
                    <input type="url" name="social_youtube" id="social_youtube" value="{{ old('social_youtube', $settings->social_youtube ?? '') }}" placeholder="https://youtube.com/@yourschool" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
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

