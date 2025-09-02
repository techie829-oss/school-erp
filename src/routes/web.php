<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ColorPaletteController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Livewire\Volt\Volt;

// Landing Pages (ONLY for primary domain - NO auth routes)
Route::domain(config('all.domains.primary'))->group(function () {
    Route::get('/', [LandingController::class, 'home'])->name('landing.home');
    Route::get('/features', [LandingController::class, 'features'])->name('landing.features');
    Route::get('/pricing', [LandingController::class, 'pricing'])->name('landing.pricing');
    Route::get('/about', [LandingController::class, 'about'])->name('landing.about');
    Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');
    Route::post('/contact', [LandingController::class, 'submitContact'])->name('landing.contact.submit');
    Route::get('/colors', [LandingController::class, 'colorPalette'])->name('landing.colors');
    Route::get('/multi-tenancy-demo', [LandingController::class, 'multiTenancyDemo'])->name('landing.multi-tenancy-demo');
});

// Dynamic Tenant Routes (for any tenant domain matching the pattern)
Route::domain('{tenant}.' . config('all.domains.primary'))->group(function () {
    // Public tenant pages
    Route::get('/', [SchoolController::class, 'home'])->name('tenant.home');
    Route::get('/about', [SchoolController::class, 'about'])->name('tenant.about');
    Route::get('/programs', [SchoolController::class, 'programs'])->name('tenant.programs');
    Route::get('/facilities', [SchoolController::class, 'facilities'])->name('tenant.facilities');
    Route::get('/admission', [SchoolController::class, 'admission'])->name('tenant.admission');
    Route::get('/contact', [SchoolController::class, 'contact'])->name('tenant.contact');

    // Auth routes for tenants (ONLY on tenant domains)
    Route::middleware('guest')->group(function () {
        Volt::route('login', 'pages.auth.login')->name('tenant.login');
        Volt::route('register', 'pages.auth.register')->name('tenant.register');
        Volt::route('forgot-password', 'pages.auth.forgot-password')->name('tenant.password.request');
        Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('tenant.password.reset');
    });

    Route::middleware('auth')->group(function () {
        Volt::route('verify-email', 'pages.auth.verify-email')->name('tenant.verification.notice');
        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('tenant.verification.verify');
        Volt::route('confirm-password', 'pages.auth.confirm-password')->name('tenant.password.confirm');

        // Protected tenant routes
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('tenant.dashboard');

        Route::get('/profile', function () {
            return view('profile');
        })->name('tenant.profile');
    });

    // Admin routes for tenants
    Route::middleware(['auth', 'verified'])->prefix('admin')->name('tenant.admin.')->group(function () {
        Route::resource('color-palettes', ColorPaletteController::class);
        Route::post('color-palettes/apply-scheme', [ColorPaletteController::class, 'applyScheme'])->name('color-palettes.apply-scheme');
    });
})->where('tenant', '^(?!app$)[a-zA-Z0-9-]+$'); // Exclude 'app' from tenant pattern

// Admin Domain Routes (Super Admin + Auth)
Route::domain(config('all.domains.admin'))->group(function () {
    // Auth routes for admin domain (ONLY on admin domain)
    Route::middleware('guest')->group(function () {
        Volt::route('login', 'pages.auth.login')->name('admin.login');
        Volt::route('register', 'pages.auth.register')->name('admin.register');
        Volt::route('forgot-password', 'pages.auth.forgot-password')->name('admin.password.request');
        Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('admin.password.reset');
    });

    Route::middleware('auth')->group(function () {
        Volt::route('verify-email', 'pages.auth.verify-email')->name('admin.verification.notice');
        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('admin.verification.verify');
        Volt::route('confirm-password', 'pages.auth.confirm-password')->name('admin.password.confirm');

        // Protected admin routes
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('admin.dashboard');

        Route::get('/profile', function () {
            return view('profile');
        })->name('admin.profile');
    });

    // Super Admin Routes (only accessible on admin domain)
    Route::middleware(['auth', 'admin.user'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Tenant Management
        Route::get('/tenants', function () {
            return view('admin.tenants.index');
        })->name('tenants.index');

        // User Management
        Route::get('/users', function () {
            return view('admin.users.index');
        })->name('users.index');

        // System Management
        Route::get('/system/overview', function () {
            return view('admin.system.overview');
        })->name('system.overview');
    });
});

// Super Admin Routes (only accessible on admin domain) - Temporarily commented out
// Route::domain(config('all.domains.admin'))->group(function () {
//     require __DIR__.'/super-admin.php';
// });
