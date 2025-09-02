@extends('school.layout')

@section('title', 'Facilities')

@section('content')
<div class="py-20 text-center">
    <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Facilities</h1>
    <p class="text-xl text-gray-600">Coming Soon - Detailed facility information will be displayed here.</p>
    <a href="{{ route('school.home') }}" class="mt-6 inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
        Back to Home
    </a>
</div>
@endsection
