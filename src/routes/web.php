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

        // Tenant Database Management
        Route::post('/tenants/{tenant}/test-database', [\App\Http\Controllers\Admin\TenantController::class, 'testDatabaseConnection'])->name('tenants.test-database');
        Route::post('/tenants/{tenant}/create-database', [\App\Http\Controllers\Admin\TenantController::class, 'createDatabase'])->name('tenants.create-database');
        Route::post('/tenants/{tenant}/run-migrations', [\App\Http\Controllers\Admin\TenantController::class, 'runMigrations'])->name('tenants.run-migrations');
        Route::get('/tenants/{tenant}/database-tables', [\App\Http\Controllers\Admin\TenantController::class, 'getDatabaseTables'])->name('tenants.database-tables');
        Route::get('/tenants/{tenant}/database-info', [\App\Http\Controllers\Admin\TenantController::class, 'getDatabaseInfo'])->name('tenants.database-info');

        // Tenant Management (Resource routes - MUST come after specific routes)
        Route::resource('tenants', \App\Http\Controllers\Admin\TenantController::class)->except(['update']);
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
Route::domain('{tenant}.' . config('all.domains.primary'))->middleware(['switch.tenant.database', 'validate.tenant.domain'])->group(function () {
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

    // Logout route (not in guest middleware)
    Route::post('/logout', function () {
        // Clear tenant session data
        session()->forget(['tenant_user', 'tenant_id', 'tenant_database_switched']);

        // Also clear Laravel auth session if it exists
        if (auth()->check()) {
            auth()->logout();
        }

        // Regenerate session for security
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')])
            ->with('success', 'You have been logged out successfully.');
    })->name('tenant.logout');

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

    // Admin routes for tenants
    Route::middleware(['tenant.auth'])->prefix('admin')->name('tenant.admin.')->group(function () {
        // Tenant Admin Dashboard
        Route::get('/', [\App\Http\Controllers\Tenant\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Student Management
        Route::resource('students', \App\Http\Controllers\Tenant\Admin\StudentController::class);
        Route::get('/students/{student}/profile', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'profile'])->name('students.profile');

        // Teacher Management
        Route::resource('teachers', \App\Http\Controllers\Tenant\Admin\TeacherController::class);
        Route::get('/teachers/{teacher}/profile', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'profile'])->name('teachers.profile');

        // Class Management
        Route::resource('classes', \App\Http\Controllers\Tenant\Admin\ClassController::class);
        Route::get('/classes/{class}/students', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'students'])->name('classes.students');
        Route::post('/classes/{class}/students', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'addStudent'])->name('classes.add-student');
        Route::delete('/classes/{class}/students/{student}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'removeStudent'])->name('classes.remove-student');

        // Attendance Management
        Route::resource('attendance', \App\Http\Controllers\Tenant\Admin\AttendanceController::class);
        Route::get('/attendance/class/{class}/date/{date}', [\App\Http\Controllers\Tenant\Admin\AttendanceController::class, 'classAttendance'])->name('attendance.class');
        Route::post('/attendance/mark', [\App\Http\Controllers\Tenant\Admin\AttendanceController::class, 'markAttendance'])->name('attendance.mark');

        // Grades Management
        Route::resource('grades', \App\Http\Controllers\Tenant\Admin\GradeController::class);
        Route::get('/grades/student/{student}', [\App\Http\Controllers\Tenant\Admin\GradeController::class, 'studentGrades'])->name('grades.student');
        Route::get('/grades/class/{class}', [\App\Http\Controllers\Tenant\Admin\GradeController::class, 'classGrades'])->name('grades.class');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [\App\Http\Controllers\Tenant\Admin\ReportController::class, 'attendance'])->name('attendance');
            Route::get('/grades', [\App\Http\Controllers\Tenant\Admin\ReportController::class, 'grades'])->name('grades');
            Route::get('/students', [\App\Http\Controllers\Tenant\Admin\ReportController::class, 'students'])->name('students');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'index'])->name('index');
            Route::post('/school', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateSchool'])->name('school');
            Route::post('/academic', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateAcademic'])->name('academic');
        });

        Route::resource('color-palettes', ColorPaletteController::class);
        Route::post('color-palettes/apply-scheme', [ColorPaletteController::class, 'applyScheme'])->name('color-palettes.apply-scheme');
    });
})->where('tenant', '^(?!app$)[a-zA-Z0-9-]+$'); // Exclude 'app' from tenant pattern
