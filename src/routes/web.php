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
    })->middleware('redirect.school.admin')->name('admin.root');

    // Auth routes for admin domain (ONLY on admin domain)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
        Route::get('/forgot-password', function () {
            return view('auth.forgot-password');
        })->name('admin.password.request');
        Route::get('/reset-password/{token}', function () {
            return view('auth.reset-password');
        })->name('admin.password.reset');
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
    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('admin.logout');
    });

    // Tenant check route for admin domain
    Route::post('/check-tenant-user', [\App\Http\Controllers\Auth\TenantCheckController::class, 'checkTenantUser'])->name('check.tenant.user');

    // Super Admin Routes (only accessible on admin domain)
    Route::middleware(['auth:admin', 'verified', 'enforce.admin.access'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Tenant Users Management (MUST come before resource routes to avoid conflicts)
        Route::get('/tenants/{tenant}/users', [\App\Http\Controllers\Admin\TenantController::class, 'usersIndex'])->name('tenants.users.index');
        Route::get('/tenants/{tenant}/users/count', [\App\Http\Controllers\Admin\TenantController::class, 'usersCount'])->name('tenants.users.count');
        Route::get('/tenants/{tenant}/users/create', [\App\Http\Controllers\Admin\TenantController::class, 'usersCreate'])->name('tenants.users.create');
        Route::post('/tenants/{tenant}/users', [\App\Http\Controllers\Admin\TenantController::class, 'usersStore'])->name('tenants.users.store');
        Route::get('/tenants/{tenant}/users/{userId}', [\App\Http\Controllers\Admin\TenantController::class, 'usersShow'])->name('tenants.users.show');
        Route::get('/tenants/{tenant}/users/{userId}/edit', [\App\Http\Controllers\Admin\TenantController::class, 'usersEdit'])->name('tenants.users.edit');
        Route::put('/tenants/{tenant}/users/{userId}', [\App\Http\Controllers\Admin\TenantController::class, 'usersUpdate'])->name('tenants.users.update');
        Route::get('/tenants/{tenant}/users/{userId}/change-password', [\App\Http\Controllers\Admin\TenantController::class, 'usersChangePassword'])->name('tenants.users.change-password');
        Route::post('/tenants/{tenant}/users/{userId}/change-password', [\App\Http\Controllers\Admin\TenantController::class, 'usersUpdatePassword'])->name('tenants.users.update-password');
        Route::get('/tenants/{tenant}/users/{userId}/delete', [\App\Http\Controllers\Admin\TenantController::class, 'usersDelete'])->name('tenants.users.delete');
        Route::delete('/tenants/{tenant}/users/{userId}', [\App\Http\Controllers\Admin\TenantController::class, 'usersDestroy'])->name('tenants.users.destroy');



        // Tenant Management (Resource routes - MUST come after specific routes)
        Route::resource('tenants', \App\Http\Controllers\Admin\TenantController::class);
        Route::post('/tenants/check-subdomain', [\App\Http\Controllers\Admin\TenantController::class, 'checkSubdomain'])->name('tenants.check-subdomain');
        Route::post('/tenants/cleanup-herd-yml', [\App\Http\Controllers\Admin\TenantController::class, 'cleanupHerdYml'])->name('tenants.cleanup-herd-yml');
        Route::post('/tenants/sync-herd-yml', [\App\Http\Controllers\Admin\TenantController::class, 'syncHerdYmlWithDatabase'])->name('tenants.sync-herd-yml');

        // Debug route for tenant database configuration
        Route::get('/tenants/{tenant}/debug-database', [\App\Http\Controllers\Admin\TenantController::class, 'debugDatabase'])->name('tenants.debug-database');


        // Tenant Status Management
        Route::post('/tenants/{tenant}/toggle-status', [\App\Http\Controllers\Admin\TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');

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

        // Admin Users Management
        Route::resource('users', \App\Http\Controllers\Admin\AdminUserController::class)->only(['index', 'show']);
        Route::get('/users/{user}/change-password', [\App\Http\Controllers\Admin\AdminUserController::class, 'changePassword'])->name('users.change-password');
        Route::post('/users/{user}/change-password', [\App\Http\Controllers\Admin\AdminUserController::class, 'updatePassword'])->name('users.update-password');
        Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

        // System Management
        Route::get('/system/overview', [\App\Http\Controllers\Admin\SystemController::class, 'overview'])->name('system.overview');
        Route::get('/system/logs', [\App\Http\Controllers\Admin\SystemController::class, 'logs'])->name('system.logs');
        Route::post('/system/cache-clear', [\App\Http\Controllers\Admin\SystemController::class, 'clearCache'])->name('system.cache.clear');
        Route::post('/system/route-clear', [\App\Http\Controllers\Admin\SystemController::class, 'clearRoutes'])->name('system.route.clear');
        Route::post('/system/view-clear', [\App\Http\Controllers\Admin\SystemController::class, 'clearViews'])->name('system.view.clear');
        Route::post('/system/logs-clear', [\App\Http\Controllers\Admin\SystemController::class, 'clearLogs'])->name('system.logs.clear');

        // Ticket Management
        Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);
        Route::post('/tickets/{ticket}/comments', [\App\Http\Controllers\Admin\TicketController::class, 'addComment'])->name('tickets.comments');
        Route::patch('/tickets/{ticket}/status', [\App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('tickets.status');

        // Activity Logs
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::get('/activity-logs/export', [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::delete('/activity-logs/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])->name('activity-logs.clear');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/api', [\App\Http\Controllers\Admin\NotificationController::class, 'getNotifications'])->name('notifications.api');
        Route::post('/notifications/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
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
Route::domain('{tenant}.' . config('all.domains.primary'))->middleware(['tenant.context', 'validate.tenant.domain'])->group(function () {
    // Public tenant pages
    Route::get('/', [SchoolController::class, 'home'])->name('tenant.home');
    Route::get('/about', [SchoolController::class, 'about'])->name('tenant.about');
    Route::get('/programs', [SchoolController::class, 'programs'])->name('tenant.programs');
    Route::get('/facilities', [SchoolController::class, 'facilities'])->name('tenant.facilities');
    Route::get('/admission', [SchoolController::class, 'admission'])->name('tenant.admission');
    Route::get('/contact', [SchoolController::class, 'contact'])->name('tenant.contact');

    // Auth routes for tenants (ONLY on tenant domains)
    Route::middleware('guest')->group(function () {
        Route::get('/login', function () {
            $tenant = tenant();
            // Debug: Check if tenant is loaded correctly
            if (!$tenant) {
                // Try to get tenant by subdomain
                $subdomain = request()->route('tenant');
                $tenant = \App\Models\Tenant::where('data->subdomain', $subdomain)->first();
            }
            return view('tenant.auth.login', compact('tenant'));
        })->name('tenant.login');
        Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('tenant.login.post');
        Route::get('/forgot-password', function () {
            return view('tenant.auth.forgot-password');
        })->name('tenant.password.request');
        Route::get('/reset-password/{token}', function () {
            return view('tenant.auth.reset-password');
        })->name('tenant.password.reset');
    });

    // Logout route (not in guest middleware)
    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('tenant.logout');

    Route::middleware('tenant.auth')->group(function () {
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

    // Note: Tenant admin routes are now handled by tenant.php (Stancl Tenancy)
    // This keeps the routes cleaner and properly isolated per tenant
})->where('tenant', '^(?!app$)[a-zA-Z0-9-]+$'); // Exclude 'app' from tenant pattern
