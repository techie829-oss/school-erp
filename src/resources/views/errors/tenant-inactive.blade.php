<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institution Inactive</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 mb-6">
                <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Institution Inactive
            </h1>

            <!-- Message -->
            <p class="text-lg text-gray-600 mb-8">
                This institution is currently inactive and cannot be accessed.
                @if(isset($tenant))
                <br><br>
                <span class="text-sm text-gray-500">Institution: {{ $tenant->data['name'] ?? 'Unknown' }}</span>
                @endif
            </p>

            <!-- Support Message -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>Need help?</strong><br>
                    Please contact the system administrator or support team for assistance.
                </p>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                @auth
                    <!-- Logout Form -->
                    <form method="POST" action="{{ url('/logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="block w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors font-medium">
                            Logout
                        </button>
                    </form>
                @endauth

                <a href="{{ url('/') }}" class="block w-full bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                    Go to Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html>

