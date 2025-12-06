@extends('tenant.layouts.cms')

@section('title', 'Manage Components: ' . $page->title)

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/cms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">CMS Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/cms/pages') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Pages</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/cms/pages/' . $page->id . '/edit') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">{{ $page->title }}</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Components</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manage Components: {{ $page->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">Manage Features, Programs, Testimonials, and Quick Links components</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ url('/admin/cms/pages/' . $page->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-2" />
                Back to Page
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm text-green-800">{{ session('success') }}</p>
    </div>
    @endif

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

    <!-- Component Types Tabs -->
    <div class="bg-white shadow rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showComponentType('features')" id="tab-features" class="component-tab border-b-2 border-primary-500 py-4 px-1 text-sm font-medium text-primary-600">
                    Why Choose Us (Features)
                </button>
                <button onclick="showComponentType('programs')" id="tab-programs" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Our Programs
                </button>
                <button onclick="showComponentType('testimonials')" id="tab-testimonials" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Testimonials
                </button>
                <button onclick="showComponentType('quick_links')" id="tab-quick_links" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Quick Links
                </button>
                @if($pageSlug === 'programs')
                <button onclick="showComponentType('program_cards')" id="tab-program_cards" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Program Cards
                </button>
                @endif
                @if($pageSlug === 'facilities')
                <button onclick="showComponentType('facility_cards')" id="tab-facility_cards" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Facility Cards
                </button>
                <button onclick="showComponentType('amenity_cards')" id="tab-amenity_cards" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Amenity Cards
                </button>
                @endif
                @if($pageSlug === 'admission')
                <button onclick="showComponentType('process_steps')" id="tab-process_steps" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Process Steps
                </button>
                <button onclick="showComponentType('requirement_cards')" id="tab-requirement_cards" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Requirement Cards
                </button>
                <button onclick="showComponentType('date_cards')" id="tab-date_cards" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Important Dates
                </button>
                <button onclick="showComponentType('faq_items')" id="tab-faq_items" class="component-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    FAQ Items
                </button>
                @endif
            </nav>
        </div>

        <!-- Features Section -->
        <div id="component-type-features" class="component-type-content p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Why Choose Us Cards</h3>
                <button onclick="openComponentModal('features')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-4 w-4 mr-2" />
                    Add Feature
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['features'] ?? [] as $index => $feature)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $feature['title'][$defaultLang] ?? $feature['title']['en'] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($feature['description'][$defaultLang] ?? $feature['description']['en'] ?? '', 100) }}</p>
                            <div class="mt-2 flex gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Icon: {{ $feature['icon'] ?? 'book' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Color: {{ $feature['color'] ?? 'primary' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('features', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="features">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No features added yet. Click "Add Feature" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Programs Section -->
        <div id="component-type-programs" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Program Cards</h3>
                <button onclick="openComponentModal('programs')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-4 w-4 mr-2" />
                    Add Program
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['programs'] ?? [] as $index => $program)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $program['title'][$defaultLang] ?? $program['title']['en'] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $program['subtitle'][$defaultLang] ?? $program['subtitle']['en'] ?? '' }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($program['description'][$defaultLang] ?? $program['description']['en'] ?? '', 100) }}</p>
                            <div class="mt-2 flex gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Icon: {{ $program['icon'] ?? 'book' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Color: {{ $program['color'] ?? 'primary' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('programs', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="programs">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No programs added yet. Click "Add Program" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Testimonials Section -->
        <div id="component-type-testimonials" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Testimonials</h3>
                <button onclick="openComponentModal('testimonials')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-4 w-4 mr-2" />
                    Add Testimonial
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['testimonials'] ?? [] as $index => $testimonial)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-2">{{ Str::limit($testimonial['description'][$defaultLang] ?? $testimonial['description']['en'] ?? '', 150) }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="font-medium text-gray-900">{{ $testimonial['author_name'] ?? 'Anonymous' }}</span>
                                @if($testimonial['author_role'] ?? null)
                                <span class="text-sm text-gray-500">- {{ $testimonial['author_role'] }}</span>
                                @endif
                                @if($testimonial['rating'] ?? null)
                                <span class="text-yellow-400">★ {{ $testimonial['rating'] }}/5</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('testimonials', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="testimonials">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No testimonials added yet. Click "Add Testimonial" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Links Section -->
        <div id="component-type-quick_links" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Quick Links</h3>
                <button onclick="openComponentModal('quick_links')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-4 w-4 mr-2" />
                    Add Quick Link
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['quick_links'] ?? [] as $index => $link)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $link['title'][$defaultLang] ?? $link['title']['en'] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($link['description'][$defaultLang] ?? $link['description']['en'] ?? '', 100) }}</p>
                            <a href="{{ $link['url'] ?? '#' }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-800 mt-1 inline-block">{{ $link['url'] ?? 'No URL' }}</a>
                            <div class="mt-2 flex gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Icon: {{ $link['icon'] ?? 'link' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Color: {{ $link['color'] ?? 'primary' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('quick_links', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="quick_links">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No quick links added yet. Click "Add Quick Link" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Program Cards Section (only for programs page) -->
        @if($pageSlug === 'programs')
        <div id="component-type-program_cards" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Program Cards</h3>
                <button onclick="openComponentModal('program_cards')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                    Add Program Card
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['program_cards'] ?? [] as $index => $card)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $card['title']['en'] ?? $card['title'][$defaultLang] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($card['description']['en'] ?? $card['description'][$defaultLang] ?? '', 100) }}</p>
                            @if(isset($card['features']) && is_array($card['features'][$defaultLang] ?? $card['features']['en'] ?? []))
                            <div class="mt-2">
                                <span class="text-xs text-gray-500">Features: {{ count($card['features'][$defaultLang] ?? $card['features']['en'] ?? []) }} items</span>
                            </div>
                            @endif
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('program_cards', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="program_cards">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No program cards added yet. Click "Add Program Card" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif

        <!-- Facility Cards Section (only for facilities page) -->
        @if($pageSlug === 'facilities')
        <div id="component-type-facility_cards" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Facility Cards</h3>
                <button onclick="openComponentModal('facility_cards')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                    Add Facility Card
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['facility_cards'] ?? [] as $index => $card)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $card['title']['en'] ?? $card['title'][$defaultLang] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($card['description']['en'] ?? $card['description'][$defaultLang] ?? '', 100) }}</p>
                            @if(isset($card['features']) && is_array($card['features'][$defaultLang] ?? $card['features']['en'] ?? []))
                            <div class="mt-2">
                                <span class="text-xs text-gray-500">Features: {{ count($card['features'][$defaultLang] ?? $card['features']['en'] ?? []) }} items</span>
                            </div>
                            @endif
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('facility_cards', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="facility_cards">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No facility cards added yet. Click "Add Facility Card" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Amenity Cards Section (only for facilities page) -->
        <div id="component-type-amenity_cards" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Amenity Cards</h3>
                <button onclick="openComponentModal('amenity_cards')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                    Add Amenity Card
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['amenity_cards'] ?? [] as $index => $card)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $card['title']['en'] ?? $card['title'][$defaultLang] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($card['description']['en'] ?? $card['description'][$defaultLang] ?? '', 100) }}</p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('amenity_cards', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="amenity_cards">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No amenity cards added yet. Click "Add Amenity Card" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif

        @if($pageSlug === 'admission')
        <!-- Process Steps Section (only for admission page) -->
        <div id="component-type-process_steps" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Admission Process Steps</h3>
                <button onclick="openComponentModal('process_steps')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                    Add Process Step
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['process_steps'] ?? [] as $index => $step)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary-100 text-primary-800">Step {{ $step['step_number'] ?? ($index + 1) }}</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mt-2">{{ $step['title']['en'] ?? $step['title'][$defaultLang] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($step['description']['en'] ?? $step['description'][$defaultLang] ?? '', 100) }}</p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('process_steps', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="process_steps">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No process steps added yet. Click "Add Process Step" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Requirement Cards Section (only for admission page) -->
        <div id="component-type-requirement_cards" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Requirement Cards</h3>
                <button onclick="openComponentModal('requirement_cards')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                    Add Requirement Card
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['requirement_cards'] ?? [] as $index => $card)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $card['title']['en'] ?? $card['title'][$defaultLang] ?? 'Untitled' }}</h4>
                            @if(isset($card['items']) && is_array($card['items'][$defaultLang] ?? $card['items']['en'] ?? []))
                            <ul class="text-sm text-gray-600 mt-2 list-disc list-inside">
                                @foreach(($card['items'][$defaultLang] ?? $card['items']['en'] ?? []) as $item)
                                <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('requirement_cards', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="requirement_cards">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No requirement cards added yet. Click "Add Requirement Card" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Date Cards Section (only for admission page) -->
        <div id="component-type-date_cards" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Important Dates</h3>
                <button onclick="openComponentModal('date_cards')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                    Add Date Card
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['date_cards'] ?? [] as $index => $card)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $card['label']['en'] ?? $card['label'][$defaultLang] ?? 'Untitled' }}</h4>
                            <p class="text-lg font-bold text-primary-600 mt-1">{{ $card['date']['en'] ?? $card['date'][$defaultLang] ?? '' }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $card['description']['en'] ?? $card['description'][$defaultLang] ?? '' }}</p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('date_cards', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="date_cards">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No date cards added yet. Click "Add Date Card" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- FAQ Items Section (only for admission page) -->
        <div id="component-type-faq_items" class="component-type-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">FAQ Items</h3>
                <button onclick="openComponentModal('faq_items')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                    Add FAQ Item
                </button>
            </div>
            <div class="space-y-4">
                @forelse($components['faq_items'] ?? [] as $index => $faq)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $faq['question']['en'] ?? $faq['question'][$defaultLang] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($faq['answer']['en'] ?? $faq['answer'][$defaultLang] ?? '', 150) }}</p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="openComponentModal('faq_items', {{ $index }})" class="text-primary-600 hover:text-primary-800">
                                <x-heroicon-o-pencil class="h-5 w-5" />
                            </button>
                            <form action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="component_type" value="faq_items">
                                <input type="hidden" name="component_index" value="{{ $index }}">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No FAQ items added yet. Click "Add FAQ Item" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Component Modal -->
<div id="componentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Component</h3>
                <button onclick="closeComponentModal()" class="text-gray-400 hover:text-gray-500">
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                </button>
            </div>
            <form id="componentForm" action="{{ url('/admin/cms/pages/' . $page->id . '/components') }}" method="POST">
                @csrf
                <input type="hidden" name="component_type" id="component_type">
                <input type="hidden" name="component_index" id="component_index">

                <div class="space-y-4">
                    <!-- Language Tabs -->
                    <div class="flex gap-2 border-b">
                        @foreach($languages as $langCode => $langName)
                        <button type="button" onclick="switchComponentLang('{{ $langCode }}')" id="comp-lang-tab-{{ $langCode }}" class="comp-lang-tab px-4 py-2 text-sm font-medium {{ $langCode === $defaultLang ? 'border-b-2 border-primary-500 text-primary-600' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $langName }}
                        </button>
                        @endforeach
                    </div>

                    <!-- Title Fields (all languages) -->
                    @foreach($languages as $langCode => $langName)
                    <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title ({{ $langName }}) <span class="text-red-500">*</span></label>
                        <input type="text" name="title_{{ $langCode }}" id="title_{{ $langCode }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    @endforeach

                    <!-- Description Fields (all languages) -->
                    @foreach($languages as $langCode => $langName)
                    <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description ({{ $langName }})</label>
                        <textarea name="description_{{ $langCode }}" id="description_{{ $langCode }}" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                    </div>
                    @endforeach

                    <!-- Subtitle for Programs -->
                    <div id="subtitle_fields" class="hidden">
                        @foreach($languages as $langCode => $langName)
                        <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle ({{ $langName }})</label>
                            <input type="text" name="subtitle_{{ $langCode }}" id="subtitle_{{ $langCode }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        @endforeach
                    </div>

                    <!-- Icon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                        <input type="text" name="icon" id="icon" placeholder="book, users, building, etc." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Icon name (used for SVG display)</p>
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color Theme</label>
                        <select name="color" id="color" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="primary">Primary</option>
                            <option value="green">Green</option>
                            <option value="blue">Blue</option>
                            <option value="purple">Purple</option>
                            <option value="red">Red</option>
                            <option value="yellow">Yellow</option>
                            <option value="orange">Orange</option>
                            <option value="indigo">Indigo</option>
                            <option value="pink">Pink</option>
                            <option value="teal">Teal</option>
                            <option value="cyan">Cyan</option>
                        </select>
                    </div>

                    <!-- URL for Quick Links and Program Cards -->
                    <div id="url_field" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                        <input type="url" name="url" id="url" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <!-- Features List for Program Cards and Facility Cards -->
                    <div id="features_fields" class="hidden">
                        @foreach($languages as $langCode => $langName)
                        <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Features List ({{ $langName }})</label>
                            <textarea name="features_{{ $langCode }}" id="features_{{ $langCode }}" rows="4" placeholder="Enter features, one per line or separated by commas" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Enter features one per line or separated by commas</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Items List for Requirement Cards -->
                    <div id="items_fields" class="hidden">
                        @foreach($languages as $langCode => $langName)
                        <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Items List ({{ $langName }})</label>
                            <textarea name="items_{{ $langCode }}" id="items_{{ $langCode }}" rows="4" placeholder="Enter items, one per line or separated by commas" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Enter items one per line or separated by commas</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Step Number for Process Steps -->
                    <div id="step_number_field" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Step Number</label>
                        <input type="number" name="step_number" id="step_number" min="1" max="10" value="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <!-- Label, Date, Description for Date Cards -->
                    <div id="date_card_fields" class="hidden space-y-4">
                        @foreach($languages as $langCode => $langName)
                        <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Label ({{ $langName }})</label>
                            <input type="text" name="label_{{ $langCode }}" id="label_{{ $langCode }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date ({{ $langName }})</label>
                            <input type="text" name="date_{{ $langCode }}" id="date_{{ $langCode }}" placeholder="e.g., January 1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description ({{ $langName }})</label>
                            <textarea name="description_{{ $langCode }}" id="description_{{ $langCode }}" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                        </div>
                        @endforeach
                    </div>

                    <!-- Question and Answer for FAQ Items -->
                    <div id="faq_fields" class="hidden space-y-4">
                        @foreach($languages as $langCode => $langName)
                        <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question ({{ $langName }})</label>
                            <input type="text" name="question_{{ $langCode }}" id="question_{{ $langCode }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div class="comp-lang-field comp-lang-field-{{ $langCode }} {{ $langCode !== $defaultLang ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Answer ({{ $langName }})</label>
                            <textarea name="answer_{{ $langCode }}" id="answer_{{ $langCode }}" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                        </div>
                        @endforeach
                    </div>

                    <!-- Author fields for Testimonials -->
                    <div id="testimonial_fields" class="hidden space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Author Name</label>
                            <input type="text" name="author_name" id="author_name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Author Role</label>
                            <input type="text" name="author_role" id="author_role" placeholder="Parent, Student, etc." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Author Initials</label>
                            <input type="text" name="author_initials" id="author_initials" placeholder="SM, JD, etc." maxlength="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                            <select name="rating" id="rating" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeComponentModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Save Component</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentComponentType = 'features';
let currentComponentIndex = null;
let currentComponentData = null;

function showComponentType(type) {
    currentComponentType = type;

    // Update tabs
    document.querySelectorAll('.component-tab').forEach(tab => {
        tab.classList.remove('border-primary-500', 'text-primary-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('tab-' + type).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + type).classList.add('border-primary-500', 'text-primary-600');

    // Show/hide content
    document.querySelectorAll('.component-type-content').forEach(content => {
        content.classList.add('hidden');
    });
    document.getElementById('component-type-' + type).classList.remove('hidden');
}

function openComponentModal(type, index = null) {
    currentComponentType = type;
    currentComponentIndex = index;

    document.getElementById('component_type').value = type;
    document.getElementById('component_index').value = index !== null ? index : '';
    document.getElementById('modalTitle').textContent = index !== null ? 'Edit Component' : 'Add Component';

    // Show/hide type-specific fields
    document.getElementById('subtitle_fields').classList.toggle('hidden', type !== 'programs');
    document.getElementById('url_field').classList.toggle('hidden', !['quick_links', 'program_cards', 'facility_cards'].includes(type));
    document.getElementById('testimonial_fields').classList.toggle('hidden', type !== 'testimonials');
    document.getElementById('features_fields').classList.toggle('hidden', !['program_cards', 'facility_cards'].includes(type));
    document.getElementById('items_fields').classList.toggle('hidden', type !== 'requirement_cards');
    document.getElementById('step_number_field').classList.toggle('hidden', type !== 'process_steps');
    document.getElementById('date_card_fields').classList.toggle('hidden', type !== 'date_cards');
    document.getElementById('faq_fields').classList.toggle('hidden', type !== 'faq_items');

    // Hide title/description for date_cards and faq_items (they use different fields)
    const titleFields = document.querySelectorAll('[id^="title_"]');
    const descFields = document.querySelectorAll('[id^="description_"]');
    const hideTitleDesc = ['date_cards', 'faq_items'].includes(type);
    titleFields.forEach(field => field.closest('.comp-lang-field').classList.toggle('hidden', hideTitleDesc));
    descFields.forEach(field => {
        if (!field.closest('#date_card_fields') && !field.closest('#faq_fields')) {
            field.closest('.comp-lang-field').classList.toggle('hidden', hideTitleDesc);
        }
    });

    // Load data if editing
    if (index !== null) {
        const components = @json($components);
        currentComponentData = components[type][index];

        // Populate form fields
        @foreach($languages as $langCode => $langName)
        if (currentComponentData.title && currentComponentData.title['{{ $langCode }}']) {
            document.getElementById('title_{{ $langCode }}').value = currentComponentData.title['{{ $langCode }}'];
        }
        if (currentComponentData.description && currentComponentData.description['{{ $langCode }}']) {
            document.getElementById('description_{{ $langCode }}').value = currentComponentData.description['{{ $langCode }}'];
        }
        if (type === 'programs' && currentComponentData.subtitle && currentComponentData.subtitle['{{ $langCode }}']) {
            document.getElementById('subtitle_{{ $langCode }}').value = currentComponentData.subtitle['{{ $langCode }}'];
        }
        if ((type === 'program_cards' || type === 'facility_cards') && currentComponentData.features && currentComponentData.features['{{ $langCode }}']) {
            const featuresArray = currentComponentData.features['{{ $langCode }}'];
            document.getElementById('features_{{ $langCode }}').value = Array.isArray(featuresArray) ? featuresArray.join('\n') : featuresArray;
        }
        if (type === 'requirement_cards' && currentComponentData.items && currentComponentData.items['{{ $langCode }}']) {
            const itemsArray = currentComponentData.items['{{ $langCode }}'];
            document.getElementById('items_{{ $langCode }}').value = Array.isArray(itemsArray) ? itemsArray.join('\n') : itemsArray;
        }
        if (type === 'date_cards') {
            if (currentComponentData.label && currentComponentData.label['{{ $langCode }}']) {
                document.getElementById('label_{{ $langCode }}').value = currentComponentData.label['{{ $langCode }}'];
            }
            if (currentComponentData.date && currentComponentData.date['{{ $langCode }}']) {
                document.getElementById('date_{{ $langCode }}').value = currentComponentData.date['{{ $langCode }}'];
            }
            if (currentComponentData.description && currentComponentData.description['{{ $langCode }}']) {
                document.getElementById('description_{{ $langCode }}').value = currentComponentData.description['{{ $langCode }}'];
            }
        }
        if (type === 'faq_items') {
            if (currentComponentData.question && currentComponentData.question['{{ $langCode }}']) {
                document.getElementById('question_{{ $langCode }}').value = currentComponentData.question['{{ $langCode }}'];
            }
            if (currentComponentData.answer && currentComponentData.answer['{{ $langCode }}']) {
                document.getElementById('answer_{{ $langCode }}').value = currentComponentData.answer['{{ $langCode }}'];
            }
        }
        @endforeach

        if (currentComponentData.icon) document.getElementById('icon').value = currentComponentData.icon;
        if (currentComponentData.color) document.getElementById('color').value = currentComponentData.color;
        if ((type === 'quick_links' || type === 'program_cards' || type === 'facility_cards') && currentComponentData.url) {
            document.getElementById('url').value = currentComponentData.url;
        }
        if (type === 'process_steps' && currentComponentData.step_number) {
            document.getElementById('step_number').value = currentComponentData.step_number;
        }
        if (type === 'testimonials') {
            if (currentComponentData.author_name) document.getElementById('author_name').value = currentComponentData.author_name;
            if (currentComponentData.author_role) document.getElementById('author_role').value = currentComponentData.author_role;
            if (currentComponentData.author_initials) document.getElementById('author_initials').value = currentComponentData.author_initials;
            if (currentComponentData.rating) document.getElementById('rating').value = currentComponentData.rating;
        }
    } else {
        // Clear form
        document.getElementById('componentForm').reset();
        document.getElementById('component_type').value = type;
        document.getElementById('component_index').value = '';
    }

    document.getElementById('componentModal').classList.remove('hidden');
}

function closeComponentModal() {
    document.getElementById('componentModal').classList.add('hidden');
    document.getElementById('componentForm').reset();
    currentComponentData = null;
}

function switchComponentLang(langCode) {
    // Update tabs
    document.querySelectorAll('.comp-lang-tab').forEach(tab => {
        tab.classList.remove('border-b-2', 'border-primary-500', 'text-primary-600');
        tab.classList.add('text-gray-500');
    });
    document.getElementById('comp-lang-tab-' + langCode).classList.remove('text-gray-500');
    document.getElementById('comp-lang-tab-' + langCode).classList.add('border-b-2', 'border-primary-500', 'text-primary-600');

    // Show/hide fields
    document.querySelectorAll('.comp-lang-field').forEach(field => {
        field.classList.add('hidden');
    });
    document.querySelectorAll('.comp-lang-field-' + langCode).forEach(field => {
        field.classList.remove('hidden');
    });
}

// Close modal on outside click
document.getElementById('componentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeComponentModal();
    }
});
</script>
@endsection
