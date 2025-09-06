<?php

require_once 'vendor/autoload.php';

use App\Models\Tenant;
use App\Models\AdminUser;
use App\Services\TenantUserValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING REAL LOGIN PROCESS ===\n\n";

// Test shared database tenant
$tenant = Tenant::where('data->subdomain', 'svps')->first();
$email = 'svps@gmail.com';
$password = 'password';

echo "Testing login for: $email on tenant: " . $tenant->data['name'] . "\n\n";

// Simulate the exact same process as the Livewire component
echo "=== STEP 1: SIMULATE LIVEWIRE LOGIN ===\n";

// Clear any existing session
Session::flush();
Auth::logout();

// Simulate form data
$credentials = ['email' => $email, 'password' => $password];

echo "Attempting authentication with admin guard...\n";

// Try authentication with admin guard (same as LoginForm)
$authResult = Auth::guard('admin')->attempt($credentials);

if (!$authResult) {
    echo "❌ Authentication failed\n";
    exit(1);
}

echo "✅ Authentication successful\n";

// Check authenticated user
$user = Auth::guard('admin')->user();
echo "✅ Authenticated user: " . $user->email . "\n";
echo "   Admin Type: " . $user->admin_type . "\n";
echo "   Tenant ID: " . $user->tenant_id . "\n";

echo "\n=== STEP 2: CHECK TENANT DOMAIN ACCESS ===\n";

// Now check domain access (after authentication)
$host = 'svps.myschool.test';
$subdomain = 'svps';

$validationService = new TenantUserValidationService();
$hasAccess = $validationService->validateDomainAccess($email, $subdomain);

if (!$hasAccess) {
    echo "❌ Domain access validation failed\n";
    $allowedDomains = $validationService->getAllowedDomainsForUser($email);
    echo "   Allowed domains: " . implode(', ', $allowedDomains) . "\n";
    exit(1);
}

echo "✅ Domain access validation successful\n";

echo "\n=== STEP 3: CHECK REDIRECT LOGIC ===\n";

// Check redirect logic
if ($host !== config('all.domains.admin')) {
    // This is a tenant domain
    $redirectRoute = route('tenant.admin.dashboard', ['tenant' => $subdomain], absolute: false);
    echo "✅ Redirect route: " . $redirectRoute . "\n";
    
    // Check if the route exists
    try {
        $url = route('tenant.admin.dashboard', ['tenant' => $subdomain], absolute: true);
        echo "✅ Full URL: " . $url . "\n";
    } catch (Exception $e) {
        echo "❌ Route error: " . $e->getMessage() . "\n";
    }
} else {
    echo "ℹ️  This is admin domain\n";
}

echo "\n=== STEP 4: TEST SESSION REGENERATION ===\n";

// Simulate session regeneration
Session::regenerate();
echo "✅ Session regenerated\n";

echo "\n=== ALL STEPS COMPLETED ===\n";
echo "✅ Login process should work correctly!\n";

// Clean up
Auth::logout();
Session::flush();

echo "✅ Session cleaned up\n";
echo "=== TEST COMPLETE ===\n";
