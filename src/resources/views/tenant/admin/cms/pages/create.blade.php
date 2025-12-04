@extends('tenant.layouts.cms')

@section('title', 'Create Page')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/cms/pages') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Pages</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Create</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create New Page</h2>
            <p class="mt-1 text-sm text-gray-500">Add a new page to your website</p>
        </div>
    </div>

    <form action="#" method="POST" class="max-w-4xl">
        @csrf

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Page Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Enter page title">
                </div>

                <div>
                    <label for="template" class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                    <select name="template" id="template" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="custom">Custom Page</option>
                        <option value="home">Home</option>
                        <option value="about">About</option>
                        <option value="programs">Programs</option>
                        <option value="facilities">Facilities</option>
                        <option value="admission">Admission</option>
                        <option value="contact">Contact</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">URL Slug</label>
                    <input type="text" name="slug" id="slug" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="auto-generated-from-title">
                    <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from title</p>
                </div>
            </div>

            <!-- Content Editor -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Page Content</label>
                <textarea name="content" id="content" rows="15" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Enter page content..."></textarea>
                <p class="mt-1 text-xs text-gray-500">Use HTML or plain text. Rich text editor will be added later.</p>
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                <textarea name="excerpt" id="excerpt" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Short description of the page..."></textarea>
            </div>

            <!-- SEO Settings -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="SEO title for search engines">
                    </div>
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="SEO description for search engines"></textarea>
                    </div>
                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="keyword1, keyword2, keyword3">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/cms/pages') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Save Page</button>
        </div>
    </form>
</div>
@endsection

