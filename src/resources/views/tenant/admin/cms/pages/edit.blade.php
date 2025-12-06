@extends('tenant.layouts.cms')

@section('title', 'Edit Page: ' . $page->title)

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/cms/pages') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Pages</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">{{ $page->title }}</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit: {{ $page->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">Manage content for this page ({{ $page->slug === '' ? '/' : '/' . $page->slug }})</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            @if($page->slug === '')
            <a href="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" class="inline-flex items-center px-4 py-2 border border-primary-300 rounded-md shadow-sm text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100">
                <x-heroicon-o-squares-2x2 class="h-4 w-4 mr-2" />
                Manage Components
            </a>
            @endif
            <a href="{{ url($page->slug === '' ? '/' : '/' . $page->slug) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4 mr-2" />
                View Page
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="text-sm text-red-800">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ url('/admin/cms/pages/' . $page->id) }}" method="POST" class="max-w-6xl">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Page Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('meta_description', $page->meta_description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">SEO meta description (max 500 characters)</p>
                </div>

                <div>
                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="keyword1, keyword2, keyword3">
                    <p class="mt-1 text-xs text-gray-500">Comma-separated keywords</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Published</span>
                    </label>
                </div>
            </div>

            <!-- CMS Fields -->
            @if(!empty($fields))
            <div class="border-t pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Page Fields</h3>
                        <p class="text-sm text-gray-500 mt-1">Manage individual fields for this page in multiple languages.</p>
                    </div>
                    <div class="flex gap-2">
                        @foreach($languages as $langCode => $langName)
                        <button type="button"
                                onclick="switchLanguage('{{ $langCode }}')"
                                id="lang-tab-{{ $langCode }}"
                                class="lang-tab px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $langCode === $defaultLang ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $langName }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-4">Fields marked as <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Enabled</span> will use CMS data, while <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Disabled</span> fields use default values.</p>

                @php
                    $fieldsBySection = [];
                    foreach ($fields as $field) {
                        $section = $field['section'];
                        if (!isset($fieldsBySection[$section])) {
                            $fieldsBySection[$section] = [];
                        }
                        $fieldsBySection[$section][] = $field;
                    }
                @endphp

                @foreach($fieldsBySection as $section => $sectionFields)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4 capitalize">{{ str_replace('_', ' ', $section) }} Section</h4>
                    <div class="space-y-4">
                        @foreach($sectionFields as $field)
                        <div class="bg-white p-4 rounded border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700 capitalize">
                                    {{ str_replace('_', ' ', $field['name']) }}
                                </label>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $field['status'] === 'enabled' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($field['status']) }}
                                </span>
                            </div>

                            @foreach($languages as $langCode => $langName)
                            @php
                                $langKey = "{$field['name']}_{$langCode}";
                                $defaultValue = $defaultValues[$field['name']][$langCode] ?? '';
                                $fieldValue = $fieldValues[$langKey] ?? '';
                            @endphp
                            <div class="lang-field lang-field-{{ $langCode }} mb-3 {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                                <label for="field_{{ $langKey }}" class="block text-xs font-medium text-gray-600 mb-1">
                                    {{ $langName }}:
                                </label>
                                @if($field['status'] === 'enabled')
                                <input type="text"
                                       name="field_{{ $langKey }}"
                                       id="field_{{ $langKey }}"
                                       value="{{ old("field_{$langKey}", $fieldValue) }}"
                                       placeholder="{{ $defaultValue }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">
                                    <span class="font-medium">Default:</span> <span class="text-gray-400 italic">{{ $defaultValue ?: 'No default value' }}</span>
                                </p>
                                @else
                                <input type="text"
                                       value="{{ $fieldValue ?: $defaultValue }}"
                                       placeholder="{{ $defaultValue }}"
                                       disabled
                                       class="block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">
                                    <span class="font-medium">Default:</span> <span class="text-gray-400 italic">{{ $defaultValue ?: 'No default value' }}</span>
                                    <br>This field is disabled. Using default value from config.
                                </p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="border-t pt-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <p class="text-sm text-yellow-800">No fields configured for this page. Add fields in <code class="bg-yellow-100 px-1 rounded">config/all.php</code> under <code class="bg-yellow-100 px-1 rounded">cms_fields</code>.</p>
                </div>
            </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/cms/pages') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Update Page</button>
        </div>
    </form>
</div>

<script>
function switchLanguage(langCode) {
    // Update tab buttons
    document.querySelectorAll('.lang-tab').forEach(tab => {
        tab.classList.remove('bg-primary-600', 'text-white');
        tab.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
    });

    const activeTab = document.getElementById('lang-tab-' + langCode);
    if (activeTab) {
        activeTab.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        activeTab.classList.add('bg-primary-600', 'text-white');
    }

    // Show/hide language fields
    document.querySelectorAll('.lang-field').forEach(field => {
        field.classList.add('hidden');
    });

    document.querySelectorAll('.lang-field-' + langCode).forEach(field => {
        field.classList.remove('hidden');
    });
}
</script>
@endsection
