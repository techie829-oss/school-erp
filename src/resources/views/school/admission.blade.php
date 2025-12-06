@extends('school.layout')

@section('title', 'Admission')

@section('content')
@php
    $tenantId = $tenant['id'] ?? null;
    if (!$tenantId && isset($tenant) && is_object($tenant)) {
        $tenantId = $tenant->id ?? null;
    }
@endphp
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-24 lg:py-32 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23000000\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <div class="mb-6">
                <span class="inline-block px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold mb-4">
                    {{ cms_field('admission', 'hero_badge', 'Join Our Community', $tenantId) }}
                </span>
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                {{ cms_field('admission', 'hero_heading', 'Admission Information', $tenantId) }}
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                {{ cms_field('admission', 'hero_description', 'Learn about our admission process and requirements for joining our school community.', $tenantId) }}
            </p>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-indigo-200 rounded-full opacity-20 blur-xl"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-purple-300 rounded-full opacity-20 blur-2xl"></div>
</section>

<!-- Admission Process -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">{{ cms_field('admission', 'process_badge', 'How to Apply', $tenantId) }}</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('admission', 'process_title', 'Admission Process', $tenantId) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('admission', 'process_description', 'Simple steps to join our school community', $tenantId) }}</p>
        </div>

        @php
            $processSteps = cms_components('admission', 'process_steps', $tenantId);
        @endphp
        @if(isset($processSteps) && is_array($processSteps) && count($processSteps) > 0)
        <div class="relative">
            <!-- Timeline Line -->
            <div class="hidden lg:block absolute top-0 left-1/2 transform -translate-x-1/2 w-1 h-full bg-gradient-to-b from-primary-200 via-primary-400 to-primary-200"></div>

            <div class="space-y-12 lg:space-y-0">
                @foreach($processSteps as $index => $step)
                @php
                    $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($step['color'] ?? 'blue');
                    $gradientMap = [
                        'blue' => ['from' => 'from-blue-50', 'to' => 'to-blue-100', 'border' => 'border-blue-200', 'bg' => 'bg-blue-600'],
                        'green' => ['from' => 'from-green-50', 'to' => 'to-green-100', 'border' => 'border-green-200', 'bg' => 'bg-green-600'],
                        'purple' => ['from' => 'from-purple-50', 'to' => 'to-purple-100', 'border' => 'border-purple-200', 'bg' => 'bg-purple-600'],
                        'orange' => ['from' => 'from-orange-50', 'to' => 'to-orange-100', 'border' => 'border-orange-200', 'bg' => 'bg-orange-600'],
                        'teal' => ['from' => 'from-teal-50', 'to' => 'to-teal-100', 'border' => 'border-teal-200', 'bg' => 'bg-teal-600'],
                    ];
                    $gradient = $gradientMap[$step['color'] ?? 'blue'] ?? $gradientMap['blue'];
                    $isEven = ($index + 1) % 2 == 0;
                @endphp
                <div class="relative flex flex-col {{ $isEven ? 'lg:flex-row-reverse' : 'lg:flex-row' }} items-center">
                    <div class="lg:w-1/2 {{ $isEven ? 'lg:pl-12' : 'lg:pr-12' }} mb-8 lg:mb-0">
                        <div class="bg-gradient-to-br {{ $gradient['from'] }} {{ $gradient['to'] }} rounded-2xl p-8 border {{ $gradient['border'] }}">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 {{ $gradient['bg'] }} rounded-xl flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-xl">{{ $step['step_number'] ?? ($index + 1) }}</span>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $step['title'] ?? '' }}</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                {{ $step['description'] ?? '' }}
                            </p>
                        </div>
                    </div>
                    <div class="lg:w-1/2 {{ $isEven ? 'lg:pr-12 lg:text-right' : 'lg:pl-12 lg:text-left' }} text-center">
                        <div class="inline-block">
                            <div class="w-20 h-20 {{ $gradient['bg'] }} rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Requirements Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">{{ cms_field('admission', 'requirements_badge', 'What You Need', $tenantId) }}</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('admission', 'requirements_title', 'Admission Requirements', $tenantId) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('admission', 'requirements_description', 'Documents and information needed for admission', $tenantId) }}</p>
        </div>

        @php
            $requirementCards = cms_components('admission', 'requirement_cards', $tenantId);
        @endphp
        @if(isset($requirementCards) && is_array($requirementCards) && count($requirementCards) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($requirementCards as $card)
            @php
                $colorClasses = \App\Helpers\ComponentHelper::getColorClasses($card['color'] ?? 'blue');
                $gradientMap = [
                    'blue' => ['from' => 'from-blue-100', 'to' => 'to-blue-200', 'text' => 'text-blue-600'],
                    'green' => ['from' => 'from-green-100', 'to' => 'to-green-200', 'text' => 'text-green-600'],
                    'purple' => ['from' => 'from-purple-100', 'to' => 'to-purple-200', 'text' => 'text-purple-600'],
                ];
                $gradient = $gradientMap[$card['color'] ?? 'blue'] ?? $gradientMap['blue'];
            @endphp
            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br {{ $gradient['from'] }} {{ $gradient['to'] }} rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 {{ $gradient['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $card['title'] ?? '' }}</h3>
                @if(isset($card['items']) && is_array($card['items']) && count($card['items']) > 0)
                <ul class="space-y-2 text-gray-700">
                    @foreach($card['items'] as $item)
                    <li class="flex items-start">
                        <svg class="w-5 h-5 {{ $gradient['text'] }} mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- Important Dates -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">{{ cms_field('admission', 'dates_badge', 'Timeline', $tenantId) }}</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('admission', 'dates_title', 'Important Dates', $tenantId) }}</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ cms_field('admission', 'dates_description', 'Key dates for the admission process', $tenantId) }}</p>
        </div>

        @php
            $dateCards = cms_components('admission', 'date_cards', $tenantId);
        @endphp
        @if(isset($dateCards) && is_array($dateCards) && count($dateCards) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($dateCards as $card)
            @php
                $gradientMap = [
                    'blue' => ['from' => 'from-blue-50', 'to' => 'to-blue-100', 'border' => 'border-blue-200', 'text' => 'text-blue-600'],
                    'green' => ['from' => 'from-green-50', 'to' => 'to-green-100', 'border' => 'border-green-200', 'text' => 'text-green-600'],
                    'purple' => ['from' => 'from-purple-50', 'to' => 'to-purple-100', 'border' => 'border-purple-200', 'text' => 'text-purple-600'],
                    'orange' => ['from' => 'from-orange-50', 'to' => 'to-orange-100', 'border' => 'border-orange-200', 'text' => 'text-orange-600'],
                ];
                $gradient = $gradientMap[$card['color'] ?? 'blue'] ?? $gradientMap['blue'];
            @endphp
            <div class="bg-gradient-to-br {{ $gradient['from'] }} {{ $gradient['to'] }} rounded-xl p-6 border {{ $gradient['border'] }}">
                <div class="{{ $gradient['text'] }} font-bold text-sm mb-2">{{ $card['label'] ?? '' }}</div>
                <div class="text-2xl font-bold text-gray-900 mb-2">{{ $card['date'] ?? '' }}</div>
                <div class="text-gray-700 text-sm">{{ $card['description'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">{{ cms_field('admission', 'faq_badge', 'Got Questions?', $tenantId) }}</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ cms_field('admission', 'faq_title', 'Frequently Asked Questions', $tenantId) }}</h2>
            <p class="text-xl text-gray-600">{{ cms_field('admission', 'faq_description', 'Common questions about our admission process', $tenantId) }}</p>
        </div>

        @php
            $faqItems = cms_components('admission', 'faq_items', $tenantId);
        @endphp
        @if(isset($faqItems) && is_array($faqItems) && count($faqItems) > 0)
        <div class="space-y-4">
            @foreach($faqItems as $faq)
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <button class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(this)">
                    <span class="text-lg font-semibold text-gray-900">{{ $faq['question'] ?? '' }}</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="hidden px-6 pb-5 text-gray-700">
                    {{ $faq['answer'] ?? '' }}
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary-600 via-primary-700 to-primary-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ cms_field('admission', 'cta_title', 'Ready to Begin Your Journey?', $tenantId) }}</h2>
        <p class="text-xl md:text-2xl text-primary-100 mb-10 max-w-2xl mx-auto">{{ cms_field('admission', 'cta_description', 'Start your admission process today and join our vibrant learning community', $tenantId) }}</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/contact') }}" class="group inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                {{ cms_field('admission', 'cta_button_text', 'Contact Admission Office', $tenantId) }}
                <svg class="ml-2 w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
            <a href="{{ url('/') }}" class="inline-flex items-center px-8 py-4 bg-transparent text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-300 border-2 border-white shadow-lg hover:shadow-xl">
                {{ cms_field('admission', 'cta_button_text_2', 'Learn More About Us', $tenantId) }}
            </a>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full opacity-10 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full opacity-10 blur-3xl"></div>
</section>

<script>
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('svg');
    const isHidden = content.classList.contains('hidden');

    // Close all other FAQs
    document.querySelectorAll('[onclick*="toggleFAQ"]').forEach(btn => {
        if (btn !== button) {
            btn.nextElementSibling.classList.add('hidden');
            btn.querySelector('svg').classList.remove('rotate-180');
        }
    });

    // Toggle current FAQ
    if (isHidden) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
@endsection
