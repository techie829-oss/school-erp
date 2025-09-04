<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ColorPaletteController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Livewire\Volt\Volt;

// Landing Domain Routes (Public)
Route::domain(config('all.domains.primary'))->group(function () {
    Route::get('/', [LandingController::class, 'home'])->name('landing.home');
    Route::get('/features', [LandingController::class, 'features'])->name('landing.features');
    Route::get('/pricing', [LandingController::class, 'pricing'])->name('landing.pricing');
    Route::get('/about', [LandingController::class, 'about'])->name('landing.about');
    Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');
    Route::post('/contact', [LandingController::class, 'submitContact'])->name('landing.contact.submit');
    Route::get('/color-palette', [LandingController::class, 'colorPalette'])->name('landing.color-palette');
    Route::get('/multi-tenancy-demo', [LandingController::class, 'multiTenancyDemo'])->name('landing.multi-tenancy-demo');
});

// Admin Domain Routes (Super Admin + Auth) - MUST BE BEFORE TENANT ROUTES
Route::domain(config('all.domains.admin'))->group(function () {
    // Redirect root to dashboard
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    })->name('admin.root');

    // Auth routes for admin domain (ONLY on admin domain)
    Route::middleware('guest')->group(function () {
        Volt::route('login', 'pages.auth.login')->name('admin.login');
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
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::get('/profile', function () {
            return view('profile');
        })->name('admin.profile');

        // Logout route
        Route::post('/logout', function () {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('admin.login');
        })->name('admin.logout');
    });

    // Super Admin Routes (only accessible on admin domain)
    Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Tenant Management
        Route::resource('tenants', \App\Http\Controllers\Admin\TenantController::class);

        // Vhost Management
        Route::prefix('vhost')->name('vhost.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\VhostController::class, 'index'])->name('index');
            Route::get('/edit', [\App\Http\Controllers\Admin\VhostController::class, 'edit'])->name('edit');
            Route::put('/update', [\App\Http\Controllers\Admin\VhostController::class, 'update'])->name('update');
            Route::get('/show', [\App\Http\Controllers\Admin\VhostController::class, 'show'])->name('show');
            Route::post('/restore', [\App\Http\Controllers\Admin\VhostController::class, 'restore'])->name('restore');
            Route::post('/validate', [\App\Http\Controllers\Admin\VhostController::class, 'validate'])->name('validate');
            Route::get('/system-info', [\App\Http\Controllers\Admin\VhostController::class, 'systemInfo'])->name('system-info');
            Route::get('/backups', [\App\Http\Controllers\Admin\VhostController::class, 'backups'])->name('backups');

            // Herd Configuration Management
            Route::get('/herd/edit', [\App\Http\Controllers\Admin\VhostController::class, 'editHerd'])->name('herd.edit');
            Route::put('/herd/update', [\App\Http\Controllers\Admin\VhostController::class, 'updateHerd'])->name('herd.update');
            Route::get('/herd/show', [\App\Http\Controllers\Admin\VhostController::class, 'showHerd'])->name('herd.show');

            // .herd.yml Configuration Management
            Route::get('/herd-yml/edit', [\App\Http\Controllers\Admin\VhostController::class, 'editHerdYml'])->name('herd-yml.edit');
            Route::put('/herd-yml/update', [\App\Http\Controllers\Admin\VhostController::class, 'updateHerdYml'])->name('herd-yml.update');
            Route::get('/herd-yml/show', [\App\Http\Controllers\Admin\VhostController::class, 'showHerdYml'])->name('herd-yml.show');

            // Service Management
            Route::post('/herd/start', [\App\Http\Controllers\Admin\VhostController::class, 'startHerd'])->name('herd.start');
            Route::post('/herd/stop', [\App\Http\Controllers\Admin\VhostController::class, 'stopHerd'])->name('herd.stop');
            Route::post('/herd/restart', [\App\Http\Controllers\Admin\VhostController::class, 'restartHerd'])->name('herd.restart');
            Route::post('/nginx/start', [\App\Http\Controllers\Admin\VhostController::class, 'startNginx'])->name('nginx.start');
            Route::post('/nginx/stop', [\App\Http\Controllers\Admin\VhostController::class, 'stopNginx'])->name('nginx.stop');
            Route::post('/nginx/restart', [\App\Http\Controllers\Admin\VhostController::class, 'restartNginx'])->name('nginx.restart');
        });

        // User Management
        Route::get('/users', function () {
            return view('admin.users.index');
        })->name('admin.users.index');

        // System Management
        Route::get('/system/overview', function () {
            return view('admin.system.overview');
        })->name('admin.system.overview');
    });

    // Catch-all routes for admin domain - prevent undefined routes from falling through
    Route::get('/about', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });

    Route::get('/contact', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });

    Route::get('/features', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });

    Route::get('/pricing', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });

    Route::get('/programs', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });

    Route::get('/facilities', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });

    Route::get('/admission', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });

    // Generic catch-all for any other undefined routes
    Route::get('/{any}', function ($any) {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    })->where('any', '.*');
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
        // Tenant Admin Dashboard
        Route::get('/', function () {
            return view('tenant.admin.dashboard');
        })->name('dashboard');

        Route::resource('color-palettes', ColorPaletteController::class);
        Route::post('color-palettes/apply-scheme', [ColorPaletteController::class, 'applyScheme'])->name('color-palettes.apply-scheme');
    });
})->where('tenant', '^(?!app$)[a-zA-Z0-9-]+$'); // Exclude 'app' from tenant pattern
