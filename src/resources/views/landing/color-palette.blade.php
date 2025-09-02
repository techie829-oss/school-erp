@extends('landing.layout')

@section('title', 'Color Palette')
@section('description', 'Customize your School ERP color scheme')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-secondary-900 mb-4">Color Palette System</h1>
            <p class="text-xl text-secondary-600">Customize your School ERP appearance with our flexible color system</p>
        </div>

        <!-- Primary Colors -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-8">Primary Colors</h2>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-primary-50 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">50</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.primary.50') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-primary-100 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">100</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.primary.100') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-primary-500 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">500</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.primary.500') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-primary-600 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">600</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.primary.600') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-primary-700 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">700</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.primary.700') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-primary-900 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">900</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.primary.900') }}</p>
                </div>
            </div>
        </div>

        <!-- Secondary Colors -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-8">Secondary Colors</h2>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-secondary-50 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">50</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.secondary.50') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-secondary-100 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">100</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.secondary.100') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-secondary-500 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">500</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.secondary.500') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-secondary-600 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">600</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.secondary.600') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-secondary-700 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">700</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.secondary.700') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-secondary-900 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">900</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.secondary.900') }}</p>
                </div>
            </div>
        </div>

        <!-- Accent Colors -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-8">Accent Colors</h2>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-accent-50 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">50</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.accent.50') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-accent-100 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">100</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.accent.100') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-accent-500 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">500</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.accent.500') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-accent-600 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">600</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.accent.600') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-accent-700 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">700</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.accent.700') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-accent-900 mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">900</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.accent.900') }}</p>
                </div>
            </div>
        </div>

        <!-- Status Colors -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-8">Status Colors</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-success mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">Success</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.success') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-warning mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">Warning</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.warning') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-error mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">Error</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.error') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-lg bg-info mx-auto mb-2 border border-gray-200"></div>
                    <p class="text-sm font-medium text-secondary-700">Info</p>
                    <p class="text-xs text-secondary-500">{{ config('all.colors.info') }}</p>
                </div>
            </div>
        </div>

        <!-- Color Usage Examples -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-8">Color Usage Examples</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Buttons -->
                <div class="bg-secondary-50 rounded-xl p-8">
                    <h3 class="text-lg font-semibold text-secondary-900 mb-4">Buttons</h3>
                    <div class="space-y-4">
                        <button class="btn-primary px-6 py-3 rounded-lg">Primary Button</button>
                        <button class="btn-secondary px-6 py-3 rounded-lg">Secondary Button</button>
                        <button class="bg-accent-500 text-white px-6 py-3 rounded-lg hover:bg-accent-600 transition-colors">Accent Button</button>
                    </div>
                </div>

                <!-- Cards -->
                <div class="bg-secondary-50 rounded-xl p-8">
                    <h3 class="text-lg font-semibold text-secondary-900 mb-4">Cards</h3>
                    <div class="space-y-4">
                        <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                            <h4 class="text-primary-900 font-medium">Primary Card</h4>
                            <p class="text-primary-700 text-sm">This is a primary colored card</p>
                        </div>
                        <div class="bg-accent-50 border border-accent-200 rounded-lg p-4">
                            <h4 class="text-accent-900 font-medium">Accent Card</h4>
                            <p class="text-accent-700 text-sm">This is an accent colored card</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customization Instructions -->
        <div class="bg-primary-50 rounded-xl p-8">
            <h2 class="text-2xl font-semibold text-primary-900 mb-4">How to Customize Colors</h2>
            <p class="text-primary-700 mb-4">You can easily customize the color scheme by updating the environment variables in your <code class="bg-white px-2 py-1 rounded text-sm">.env</code> file:</p>
            <div class="bg-white rounded-lg p-4 font-mono text-sm">
                <p class="text-secondary-600"># Primary Colors (Blue)</p>
                <p>COLOR_PRIMARY_500=#3b82f6</p>
                <p>COLOR_PRIMARY_600=#2563eb</p>
                <p class="text-secondary-600"># Accent Colors (Amber)</p>
                <p>COLOR_ACCENT_500=#f59e0b</p>
                <p>COLOR_ACCENT_600=#d97706</p>
            </div>
            <p class="text-primary-700 mt-4">All colors automatically update throughout the application using CSS custom properties!</p>
        </div>
    </div>
</section>
@endsection
