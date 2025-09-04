@extends('school.layout')

@section('title', 'Admission')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Admission Information</h1>
            <p class="text-xl text-gray-600 mb-8">Learn about our admission process and requirements for joining our school community.</p>
            <a href="{{ route('tenant.home', ['tenant' => $tenantSubdomain]) }}" class="mt-6 inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
