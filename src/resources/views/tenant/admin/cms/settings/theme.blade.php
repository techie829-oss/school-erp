@extends('tenant.layouts.cms')

@section('title', 'Theme Settings')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/cms/settings') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Settings</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Theme</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Theme & Color Settings</h2>
            <p class="mt-1 text-sm text-gray-500">Customize your website's color scheme and SCSS</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ url('/admin/cms/settings/theme') }}" method="POST" id="theme-form">
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

        <div class="bg-white shadow rounded-lg p-6 space-y-8">
            <!-- Primary Colors -->
            <div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Primary Colors</h3>
                    <p class="text-sm text-gray-500 mt-1">Main brand color used for buttons, links, highlights, and key UI elements</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        $primaryDescriptions = [
                            '50' => ['label' => 'Primary 50 (Lightest)', 'desc' => 'Very light backgrounds, subtle highlights', 'example' => 'Hero section backgrounds, badge backgrounds'],
                            '100' => ['label' => 'Primary 100 (Light)', 'desc' => 'Light backgrounds and subtle elements', 'example' => 'Info cards, hover states, section backgrounds'],
                            '500' => ['label' => 'Primary 500 (Base)', 'desc' => 'Standard primary color for general use', 'example' => 'Icons, borders, secondary buttons'],
                            '600' => ['label' => 'Primary 600 (Main)', 'desc' => 'Main brand color - most commonly used', 'example' => 'Primary buttons, links, active states, CTAs'],
                            '700' => ['label' => 'Primary 700 (Dark)', 'desc' => 'Darker shade for hover and emphasis', 'example' => 'Button hover states, active links, focus rings'],
                            '900' => ['label' => 'Primary 900 (Darkest)', 'desc' => 'Darkest shade for text and strong emphasis', 'example' => 'Headings, important text, dark overlays']
                        ];
                    @endphp
                    @foreach(['50', '100', '500', '600', '700', '900'] as $shade)
                    <div class="space-y-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <label for="primary_color_{{ $shade }}" class="block text-sm font-semibold text-gray-900">{{ $primaryDescriptions[$shade]['label'] }}</label>
                            <p class="text-xs text-gray-600 mt-1">{{ $primaryDescriptions[$shade]['desc'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 italic">Example: {{ $primaryDescriptions[$shade]['example'] }}</p>
                        </div>
                        <div class="flex items-center gap-3 mt-3">
                            <div class="relative">
                                <input type="color" name="primary_color_{{ $shade }}" id="primary_color_{{ $shade }}" value="{{ old("primary_color_{$shade}", $theme->{"primary_color_{$shade}"} ?? '#3b82f6') }}" class="h-14 w-20 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-primary-500 transition-colors">
                            </div>
                            <div class="flex-1">
                                <input type="text" id="primary_color_{{ $shade }}_hex" value="{{ old("primary_color_{$shade}", $theme->{"primary_color_{$shade}"} ?? '#3b82f6') }}" pattern="^#[0-9A-Fa-f]{6}$" class="w-full block rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono" placeholder="#3b82f6">
                            </div>
                            <div class="color-preview h-10 w-10 rounded border-2 border-gray-300" style="background-color: {{ old("primary_color_{$shade}", $theme->{"primary_color_{$shade}"} ?? '#3b82f6') }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Secondary Colors -->
            <div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Secondary Colors</h3>
                    <p class="text-sm text-gray-500 mt-1">Supporting color for secondary actions, text, and neutral UI elements</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        $secondaryDescriptions = [
                            '50' => ['label' => 'Secondary 50 (Lightest)', 'desc' => 'Very light neutral backgrounds', 'example' => 'Alternate section backgrounds, subtle dividers'],
                            '100' => ['label' => 'Secondary 100 (Light)', 'desc' => 'Light neutral elements', 'example' => 'Card backgrounds, input backgrounds, disabled states'],
                            '500' => ['label' => 'Secondary 500 (Base)', 'desc' => 'Standard secondary color', 'example' => 'Secondary buttons, borders, icons'],
                            '600' => ['label' => 'Secondary 600 (Main)', 'desc' => 'Main secondary color', 'example' => 'Secondary buttons, text links, badges'],
                            '700' => ['label' => 'Secondary 700 (Dark)', 'desc' => 'Darker secondary for emphasis', 'example' => 'Hover states, active secondary elements'],
                            '900' => ['label' => 'Secondary 900 (Darkest)', 'desc' => 'Darkest for text and strong contrast', 'example' => 'Body text, headings, dark UI elements']
                        ];
                    @endphp
                    @foreach(['50', '100', '500', '600', '700', '900'] as $shade)
                    <div class="space-y-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <label for="secondary_color_{{ $shade }}" class="block text-sm font-semibold text-gray-900">{{ $secondaryDescriptions[$shade]['label'] }}</label>
                            <p class="text-xs text-gray-600 mt-1">{{ $secondaryDescriptions[$shade]['desc'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 italic">Example: {{ $secondaryDescriptions[$shade]['example'] }}</p>
                        </div>
                        <div class="flex items-center gap-3 mt-3">
                            <div class="relative">
                                <input type="color" name="secondary_color_{{ $shade }}" id="secondary_color_{{ $shade }}" value="{{ old("secondary_color_{$shade}", $theme->{"secondary_color_{$shade}"} ?? '#64748b') }}" class="h-14 w-20 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-primary-500 transition-colors">
                            </div>
                            <div class="flex-1">
                                <input type="text" id="secondary_color_{{ $shade }}_hex" value="{{ old("secondary_color_{$shade}", $theme->{"secondary_color_{$shade}"} ?? '#64748b') }}" pattern="^#[0-9A-Fa-f]{6}$" class="w-full block rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono" placeholder="#64748b">
                            </div>
                            <div class="color-preview h-10 w-10 rounded border-2 border-gray-300" style="background-color: {{ old("secondary_color_{$shade}", $theme->{"secondary_color_{$shade}"} ?? '#64748b') }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Accent Colors -->
            <div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Accent Colors</h3>
                    <p class="text-sm text-gray-500 mt-1">Highlight color for special elements, callouts, and decorative accents</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        $accentDescriptions = [
                            '50' => ['label' => 'Accent 50 (Lightest)', 'desc' => 'Very light accent backgrounds', 'example' => 'Highlight sections, subtle callouts'],
                            '100' => ['label' => 'Accent 100 (Light)', 'desc' => 'Light accent elements', 'example' => 'Info boxes, feature highlights, badges'],
                            '500' => ['label' => 'Accent 500 (Base)', 'desc' => 'Standard accent color', 'example' => 'Icons, decorative elements, borders'],
                            '600' => ['label' => 'Accent 600 (Main)', 'desc' => 'Main accent color', 'example' => 'Accent buttons, highlights, special links'],
                            '700' => ['label' => 'Accent 700 (Dark)', 'desc' => 'Darker accent for emphasis', 'example' => 'Hover states, active accent elements'],
                            '900' => ['label' => 'Accent 900 (Darkest)', 'desc' => 'Darkest accent for strong emphasis', 'example' => 'Important highlights, dark accents']
                        ];
                    @endphp
                    @foreach(['50', '100', '500', '600', '700', '900'] as $shade)
                    <div class="space-y-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <label for="accent_color_{{ $shade }}" class="block text-sm font-semibold text-gray-900">{{ $accentDescriptions[$shade]['label'] }}</label>
                            <p class="text-xs text-gray-600 mt-1">{{ $accentDescriptions[$shade]['desc'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 italic">Example: {{ $accentDescriptions[$shade]['example'] }}</p>
                        </div>
                        <div class="flex items-center gap-3 mt-3">
                            <div class="relative">
                                <input type="color" name="accent_color_{{ $shade }}" id="accent_color_{{ $shade }}" value="{{ old("accent_color_{$shade}", $theme->{"accent_color_{$shade}"} ?? '#f59e0b') }}" class="h-14 w-20 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-primary-500 transition-colors">
                            </div>
                            <div class="flex-1">
                                <input type="text" id="accent_color_{{ $shade }}_hex" value="{{ old("accent_color_{$shade}", $theme->{"accent_color_{$shade}"} ?? '#f59e0b') }}" pattern="^#[0-9A-Fa-f]{6}$" class="w-full block rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono" placeholder="#f59e0b">
                            </div>
                            <div class="color-preview h-10 w-10 rounded border-2 border-gray-300" style="background-color: {{ old("accent_color_{$shade}", $theme->{"accent_color_{$shade}"} ?? '#f59e0b') }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Status Colors -->
            <div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Status Colors</h3>
                    <p class="text-sm text-gray-500 mt-1">Colors for alerts, notifications, and status indicators throughout the system</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $statusDescriptions = [
                            'success' => ['label' => 'Success Color', 'desc' => 'Positive actions and successful states', 'example' => 'Success messages, completed tasks, checkmarks, positive alerts'],
                            'warning' => ['label' => 'Warning Color', 'desc' => 'Cautionary messages and warnings', 'example' => 'Warning alerts, pending items, attention needed, caution badges'],
                            'error' => ['label' => 'Error Color', 'desc' => 'Errors and critical messages', 'example' => 'Error messages, validation errors, delete buttons, danger alerts'],
                            'info' => ['label' => 'Info Color', 'desc' => 'Informational messages and tips', 'example' => 'Info alerts, tooltips, help text, information badges']
                        ];
                    @endphp
                    @foreach(['success', 'warning', 'error', 'info'] as $status)
                    @php
                        $defaultColor = $status === 'success' ? '#10b981' : ($status === 'warning' ? '#f59e0b' : ($status === 'error' ? '#ef4444' : '#3b82f6'));
                        $currentColor = old("{$status}_color", $theme->{"{$status}_color"} ?? $defaultColor);
                    @endphp
                    <div class="space-y-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <label for="{{ $status }}_color" class="block text-sm font-semibold text-gray-900">{{ $statusDescriptions[$status]['label'] }}</label>
                            <p class="text-xs text-gray-600 mt-1">{{ $statusDescriptions[$status]['desc'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 italic">Example: {{ $statusDescriptions[$status]['example'] }}</p>
                        </div>
                        <div class="flex items-center gap-3 mt-3">
                            <div class="relative">
                                <input type="color" name="{{ $status }}_color" id="{{ $status }}_color" value="{{ $currentColor }}" class="h-14 w-20 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-primary-500 transition-colors">
                            </div>
                            <div class="flex-1">
                                <input type="text" id="{{ $status }}_color_hex" value="{{ $currentColor }}" pattern="^#[0-9A-Fa-f]{6}$" class="w-full block rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono" placeholder="{{ $defaultColor }}">
                            </div>
                            <div class="color-preview h-10 w-10 rounded border-2 border-gray-300" style="background-color: {{ $currentColor }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Custom CSS -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Custom CSS</h3>
                <textarea name="custom_css" id="custom_css" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono text-sm">{{ old('custom_css', $theme->custom_css ?? '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Add custom CSS to override default styles</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/cms/settings') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Save Theme</button>
        </div>
    </form>
</div>

<script>
// Sync color picker with hex input and preview
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="color"]').forEach(colorInput => {
        const hexInput = document.getElementById(colorInput.id + '_hex');
        const preview = colorInput.closest('.space-y-2')?.querySelector('.color-preview');

        if (!hexInput) return;

        // Update hex input and preview when color picker changes
        colorInput.addEventListener('input', function() {
            hexInput.value = this.value;
            if (preview) {
                preview.style.backgroundColor = this.value;
            }
        });

        // Update color picker and preview when hex input changes
        hexInput.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorInput.value = this.value;
                if (preview) {
                    preview.style.backgroundColor = this.value;
                }
            }
        });

        // Initialize hex input value
        hexInput.value = colorInput.value;
        if (preview) {
            preview.style.backgroundColor = colorInput.value;
        }
    });
});
</script>
@endsection

