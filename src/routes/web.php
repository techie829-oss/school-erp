<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SchoolController;
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

        // Tenant Users Management (MUST come before tenant routes to avoid conflicts)
        Route::get('/tenants/{tenant}/users', [\App\Http\Controllers\Admin\TenantController::class, 'usersIndex'])->name('tenants.users.index')->where('tenant', '[a-z0-9-]+');
        Route::get('/tenants/{tenant}/users/count', [\App\Http\Controllers\Admin\TenantController::class, 'usersCount'])->name('tenants.users.count')->where('tenant', '[a-z0-9-]+');
        Route::get('/tenants/{tenant}/users/create', [\App\Http\Controllers\Admin\TenantController::class, 'usersCreate'])->name('tenants.users.create')->where('tenant', '[a-z0-9-]+');
        Route::post('/tenants/{tenant}/users', [\App\Http\Controllers\Admin\TenantController::class, 'usersStore'])->name('tenants.users.store')->where('tenant', '[a-z0-9-]+');
        Route::get('/tenants/{tenant}/users/{userId}', [\App\Http\Controllers\Admin\TenantController::class, 'usersShow'])->name('tenants.users.show')->where('tenant', '[a-z0-9-]+');
        Route::get('/tenants/{tenant}/users/{userId}/edit', [\App\Http\Controllers\Admin\TenantController::class, 'usersEdit'])->name('tenants.users.edit')->where('tenant', '[a-z0-9-]+');
        Route::put('/tenants/{tenant}/users/{userId}', [\App\Http\Controllers\Admin\TenantController::class, 'usersUpdate'])->name('tenants.users.update')->where('tenant', '[a-z0-9-]+');
        Route::get('/tenants/{tenant}/users/{userId}/change-password', [\App\Http\Controllers\Admin\TenantController::class, 'usersChangePassword'])->name('tenants.users.change-password')->where('tenant', '[a-z0-9-]+');
        Route::post('/tenants/{tenant}/users/{userId}/change-password', [\App\Http\Controllers\Admin\TenantController::class, 'usersUpdatePassword'])->name('tenants.users.update-password')->where('tenant', '[a-z0-9-]+');
        Route::get('/tenants/{tenant}/users/{userId}/delete', [\App\Http\Controllers\Admin\TenantController::class, 'usersDelete'])->name('tenants.users.delete')->where('tenant', '[a-z0-9-]+');
        Route::delete('/tenants/{tenant}/users/{userId}', [\App\Http\Controllers\Admin\TenantController::class, 'usersDestroy'])->name('tenants.users.destroy')->where('tenant', '[a-z0-9-]+');



        // Tenant Management (Resource routes - MUST come after specific routes)
        // Custom tenant routes (using {tenant} parameter for slug-based ID, not resource routes)
        Route::get('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [\App\Http\Controllers\Admin\TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'show'])->name('tenants.show');
        Route::get('/tenants/{tenant}/edit', [\App\Http\Controllers\Admin\TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'update'])->name('tenants.update');
        Route::patch('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'update'])->name('tenants.patch');
        Route::delete('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'destroy'])->name('tenants.destroy');

        // Tenant utility routes
        Route::post('/tenants/check-subdomain', [\App\Http\Controllers\Admin\TenantController::class, 'checkSubdomain'])->name('tenants.check-subdomain');
        Route::post('/tenants/cleanup-herd-yml', [\App\Http\Controllers\Admin\TenantController::class, 'cleanupHerdYml'])->name('tenants.cleanup-herd-yml');
        Route::post('/tenants/sync-herd-yml', [\App\Http\Controllers\Admin\TenantController::class, 'syncHerdYmlWithDatabase'])->name('tenants.sync-herd-yml');

        // Debug route for tenant database configuration
        Route::get('/tenants/{tenant}/debug-database', [\App\Http\Controllers\Admin\TenantController::class, 'debugDatabase'])->name('tenants.debug-database')->where('tenant', '[a-z0-9-]+');

        // Tenant Status Management
        Route::post('/tenants/{tenant}/toggle-status', [\App\Http\Controllers\Admin\TenantController::class, 'toggleStatus'])->name('tenants.toggle-status')->where('tenant', '[a-z0-9-]+');

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

        // Temporary fix for tenant admin dashboard route
        Route::get('/tenant-admin-dashboard', function () {
            return redirect()->route('admin.dashboard');
        })->name('tenant.admin.dashboard');
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

    // Tenant Admin Routes (protected)
    Route::middleware(['tenant.auth'])->prefix('admin')->name('tenant.admin.')->group(function () {
        // Dashboard
        Route::get('dashboard', [\App\Http\Controllers\Tenant\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', function () {
            return redirect('/admin/dashboard');
        });

        // Student Management (Manual routes - using studentId to avoid conflict with tenant parameter)
        Route::middleware('feature:students')->group(function () {
            Route::get('students', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'index'])->name('students.index');
            Route::get('students/create', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'create'])->name('students.create');
            Route::post('students', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'store'])->name('students.store');
            Route::get('students/{studentId}', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'show'])->name('students.show')->where('studentId', '[0-9]+');
            Route::get('students/{studentId}/edit', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'edit'])->name('students.edit')->where('studentId', '[0-9]+');
            Route::put('students/{studentId}', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'update'])->name('students.update')->where('studentId', '[0-9]+');
            Route::patch('students/{studentId}', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'update'])->name('students.update.patch')->where('studentId', '[0-9]+');
            Route::delete('students/{studentId}', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'destroy'])->name('students.destroy')->where('studentId', '[0-9]+');

            // Student Academic Actions
            Route::post('students/{studentId}/promote', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'promote'])->name('students.promote')->where('studentId', '[0-9]+');
            Route::post('students/{studentId}/update-status', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'updateAcademicStatus'])->name('students.update-status')->where('studentId', '[0-9]+');
            Route::post('students/{studentId}/complete-enrollment', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'completeEnrollment'])->name('students.complete-enrollment')->where('studentId', '[0-9]+');

            // Student Documents
            Route::post('students/{studentId}/documents', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'uploadDocument'])->name('students.upload-document')->where('studentId', '[0-9]+');
            Route::delete('student-documents/{documentId}', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'deleteDocument'])->name('students.delete-document')->where('documentId', '[0-9]+');
        });

        // Settings Management
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'index'])->name('index');
            Route::post('/general', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateGeneral'])->name('update.general');
            Route::post('/features', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateFeatures'])->name('update.features');
            Route::post('/academic', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateAcademic'])->name('update.academic');
            Route::post('/attendance', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateAttendance'])->name('update.attendance');
            Route::post('/payment', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updatePayment'])->name('update.payment');
            Route::post('/notifications', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateNotifications'])->name('update.notifications');
            Route::delete('/logo', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'deleteLogo'])->name('delete.logo');
        });

        // Class Management
        Route::middleware('feature:classes')->group(function () {
            Route::get('classes', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'index'])->name('classes.index');
            Route::get('classes/create', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'create'])->name('classes.create');
            Route::post('classes', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'store'])->name('classes.store');
            Route::get('classes/{id}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'show'])->name('classes.show')->where('id', '[0-9]+');
            Route::get('classes/{id}/sections', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'getSections'])->name('classes.sections')->where('id', '[0-9]+');
            Route::get('classes/{id}/edit', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'edit'])->name('classes.edit')->where('id', '[0-9]+');
            Route::put('classes/{id}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'update'])->name('classes.update')->where('id', '[0-9]+');
            Route::patch('classes/{id}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'update'])->name('classes.update.patch')->where('id', '[0-9]+');
            Route::delete('classes/{id}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'destroy'])->name('classes.destroy')->where('id', '[0-9]+');

            // Section Management
            Route::get('sections', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'index'])->name('sections.index');
            Route::get('sections/create', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'create'])->name('sections.create');
            Route::post('sections', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'store'])->name('sections.store');
            Route::get('sections/{sectionId}', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'show'])->name('sections.show')->where('sectionId', '[0-9]+');
            Route::get('sections/{sectionId}/edit', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'edit'])->name('sections.edit')->where('sectionId', '[0-9]+');
            Route::put('sections/{sectionId}', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'update'])->name('sections.update')->where('sectionId', '[0-9]+');
            Route::patch('sections/{sectionId}', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'update'])->name('sections.update.patch')->where('sectionId', '[0-9]+');
            Route::delete('sections/{sectionId}', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'destroy'])->name('sections.destroy')->where('sectionId', '[0-9]+');
        });

        // Teacher Management
        Route::middleware('feature:teachers')->group(function () {
            Route::get('teachers', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'index'])->name('teachers.index');
            Route::get('teachers/create', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'create'])->name('teachers.create');
            Route::post('teachers', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'store'])->name('teachers.store');
            Route::get('teachers/{teacherId}', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'show'])->name('teachers.show')->where('teacherId', '[0-9]+');
            Route::get('teachers/{teacherId}/edit', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'edit'])->name('teachers.edit')->where('teacherId', '[0-9]+');
            Route::put('teachers/{teacherId}', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'update'])->name('teachers.update')->where('teacherId', '[0-9]+');
            Route::patch('teachers/{teacherId}', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'update'])->name('teachers.update.patch')->where('teacherId', '[0-9]+');
            Route::delete('teachers/{teacherId}', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'destroy'])->name('teachers.destroy')->where('teacherId', '[0-9]+');

            // Teacher Qualifications & Documents
            Route::post('teachers/{teacherId}/qualifications', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'addQualification'])->name('teachers.add-qualification')->where('teacherId', '[0-9]+');
            Route::post('teachers/{teacherId}/documents', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'uploadDocument'])->name('teachers.upload-document')->where('teacherId', '[0-9]+');
            Route::delete('documents/{documentId}', [\App\Http\Controllers\Tenant\Admin\TeacherController::class, 'deleteDocument'])->name('teachers.delete-document')->where('documentId', '[0-9]+');
        });

        // Department Management
        Route::get('departments', [\App\Http\Controllers\Tenant\Admin\DepartmentController::class, 'index'])->name('departments.index');
        Route::get('departments/create', [\App\Http\Controllers\Tenant\Admin\DepartmentController::class, 'create'])->name('departments.create');
        Route::post('departments', [\App\Http\Controllers\Tenant\Admin\DepartmentController::class, 'store'])->name('departments.store');
        Route::get('departments/{departmentId}', [\App\Http\Controllers\Tenant\Admin\DepartmentController::class, 'show'])->name('departments.show')->where('departmentId', '[0-9]+');
        Route::get('departments/{departmentId}/edit', [\App\Http\Controllers\Tenant\Admin\DepartmentController::class, 'edit'])->name('departments.edit')->where('departmentId', '[0-9]+');
        Route::put('departments/{departmentId}', [\App\Http\Controllers\Tenant\Admin\DepartmentController::class, 'update'])->name('departments.update')->where('departmentId', '[0-9]+');
        Route::patch('departments/{departmentId}', [\App\Http\Controllers\Tenant\Admin\DepartmentController::class, 'update'])->name('departments.update.patch')->where('departmentId', '[0-9]+');
        Route::delete('departments/{departmentId}', [\App\Http\Controllers\Tenant\Admin\DepartmentController::class, 'destroy'])->name('departments.destroy')->where('departmentId', '[0-9]+');

        // Subject Management
        Route::get('subjects', [\App\Http\Controllers\Tenant\Admin\SubjectController::class, 'index'])->name('subjects.index');
        Route::get('subjects/create', [\App\Http\Controllers\Tenant\Admin\SubjectController::class, 'create'])->name('subjects.create');
        Route::post('subjects', [\App\Http\Controllers\Tenant\Admin\SubjectController::class, 'store'])->name('subjects.store');
        Route::get('subjects/{subjectId}', [\App\Http\Controllers\Tenant\Admin\SubjectController::class, 'show'])->name('subjects.show')->where('subjectId', '[0-9]+');
        Route::get('subjects/{subjectId}/edit', [\App\Http\Controllers\Tenant\Admin\SubjectController::class, 'edit'])->name('subjects.edit')->where('subjectId', '[0-9]+');
        Route::put('subjects/{subjectId}', [\App\Http\Controllers\Tenant\Admin\SubjectController::class, 'update'])->name('subjects.update')->where('subjectId', '[0-9]+');
        Route::patch('subjects/{subjectId}', [\App\Http\Controllers\Tenant\Admin\SubjectController::class, 'update'])->name('subjects.update.patch')->where('subjectId', '[0-9]+');
        Route::delete('subjects/{subjectId}', [\App\Http\Controllers\Tenant\Admin\SubjectController::class, 'destroy'])->name('subjects.destroy')->where('subjectId', '[0-9]+');

        // Examination Management
        Route::prefix('examinations')->name('examinations.')->middleware('feature:grades')->group(function () {
            // Grade Scales
            Route::get('grade-scales', [\App\Http\Controllers\Tenant\Admin\GradeScaleController::class, 'index'])->name('grade-scales.index');
            Route::get('grade-scales/create', [\App\Http\Controllers\Tenant\Admin\GradeScaleController::class, 'create'])->name('grade-scales.create');
            Route::post('grade-scales', [\App\Http\Controllers\Tenant\Admin\GradeScaleController::class, 'store'])->name('grade-scales.store');
            Route::get('grade-scales/{id}/edit', [\App\Http\Controllers\Tenant\Admin\GradeScaleController::class, 'edit'])->name('grade-scales.edit');
            Route::put('grade-scales/{id}', [\App\Http\Controllers\Tenant\Admin\GradeScaleController::class, 'update'])->name('grade-scales.update');
            Route::delete('grade-scales/{id}', [\App\Http\Controllers\Tenant\Admin\GradeScaleController::class, 'destroy'])->name('grade-scales.destroy');
        });

        // Examinations Module (requires exams feature)
        Route::prefix('examinations')->name('examinations.')->middleware('feature:exams')->group(function () {
            // Exams
            Route::get('exams', [\App\Http\Controllers\Tenant\Admin\ExamController::class, 'index'])->name('exams.index');
            Route::get('exams/create', [\App\Http\Controllers\Tenant\Admin\ExamController::class, 'create'])->name('exams.create');
            Route::post('exams', [\App\Http\Controllers\Tenant\Admin\ExamController::class, 'store'])->name('exams.store');
            Route::get('exams/{id}', [\App\Http\Controllers\Tenant\Admin\ExamController::class, 'show'])->name('exams.show');
            Route::get('exams/{id}/edit', [\App\Http\Controllers\Tenant\Admin\ExamController::class, 'edit'])->name('exams.edit');
            Route::put('exams/{id}', [\App\Http\Controllers\Tenant\Admin\ExamController::class, 'update'])->name('exams.update');
            Route::delete('exams/{id}', [\App\Http\Controllers\Tenant\Admin\ExamController::class, 'destroy'])->name('exams.destroy');

            // Exam Schedules
            Route::get('schedules', [\App\Http\Controllers\Tenant\Admin\ExamScheduleController::class, 'index'])->name('schedules.index');
            Route::get('schedules/create', [\App\Http\Controllers\Tenant\Admin\ExamScheduleController::class, 'create'])->name('schedules.create');
            Route::post('schedules', [\App\Http\Controllers\Tenant\Admin\ExamScheduleController::class, 'store'])->name('schedules.store');
            Route::get('schedules/bulk-create', [\App\Http\Controllers\Tenant\Admin\ExamScheduleController::class, 'bulkCreate'])->name('schedules.bulk-create');
            Route::post('schedules/bulk', [\App\Http\Controllers\Tenant\Admin\ExamScheduleController::class, 'bulkStore'])->name('schedules.bulk-store');
            Route::get('schedules/{id}/edit', [\App\Http\Controllers\Tenant\Admin\ExamScheduleController::class, 'edit'])->name('schedules.edit');
            Route::put('schedules/{id}', [\App\Http\Controllers\Tenant\Admin\ExamScheduleController::class, 'update'])->name('schedules.update');
            Route::delete('schedules/{id}', [\App\Http\Controllers\Tenant\Admin\ExamScheduleController::class, 'destroy'])->name('schedules.destroy');

            // Exam Results
            Route::get('results', [\App\Http\Controllers\Tenant\Admin\ExamResultController::class, 'index'])->name('results.index');
            Route::get('results/entry', [\App\Http\Controllers\Tenant\Admin\ExamResultController::class, 'entry'])->name('results.entry');
            Route::post('results', [\App\Http\Controllers\Tenant\Admin\ExamResultController::class, 'store'])->name('results.store');
            Route::get('results/{id}/edit', [\App\Http\Controllers\Tenant\Admin\ExamResultController::class, 'edit'])->name('results.edit');
            Route::put('results/{id}', [\App\Http\Controllers\Tenant\Admin\ExamResultController::class, 'update'])->name('results.update');
            Route::delete('results/{id}', [\App\Http\Controllers\Tenant\Admin\ExamResultController::class, 'destroy'])->name('results.destroy');

            // Admit Cards
            Route::get('admit-cards', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'index'])->name('admit-cards.index');
            Route::get('admit-cards/bulk-actions', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'bulkActions'])->name('admit-cards.bulk-actions');
            Route::get('admit-cards/generate', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'generate'])->name('admit-cards.generate');
            Route::post('admit-cards', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'store'])->name('admit-cards.store');
            Route::post('admit-cards/bulk', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'bulkGenerate'])->name('admit-cards.bulk-generate');
            Route::get('admit-cards/bulk-preview', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'bulkPreview'])->name('admit-cards.bulk-preview');
            Route::post('admit-cards/bulk-export', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'bulkExport'])->name('admit-cards.bulk-export');
            Route::post('admit-cards/bulk-delete', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'bulkDestroy'])->name('admit-cards.bulk-destroy');
            Route::delete('admit-cards/{id}', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'destroy'])->name('admit-cards.destroy');
            Route::get('admit-cards/{id}/print', [\App\Http\Controllers\Tenant\Admin\AdmitCardController::class, 'print'])->name('admit-cards.print');

            // Report Cards
            Route::get('report-cards', [\App\Http\Controllers\Tenant\Admin\ReportCardController::class, 'index'])->name('report-cards.index');
            Route::get('report-cards/generate', [\App\Http\Controllers\Tenant\Admin\ReportCardController::class, 'generate'])->name('report-cards.generate');
            Route::post('report-cards', [\App\Http\Controllers\Tenant\Admin\ReportCardController::class, 'store'])->name('report-cards.store');
            Route::post('report-cards/bulk', [\App\Http\Controllers\Tenant\Admin\ReportCardController::class, 'bulkGenerate'])->name('report-cards.bulk-generate');
            Route::get('report-cards/{id}/print', [\App\Http\Controllers\Tenant\Admin\ReportCardController::class, 'print'])->name('report-cards.print');
        });

        // Attendance Management - Students
        Route::prefix('attendance/students')->name('attendance.students.')->middleware('feature:attendance')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'index'])->name('index');
            // Class/Section Calendar View (Option B)
            Route::get('/calendar', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'calendar'])->name('calendar');
            Route::get('/mark', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'mark'])->name('mark');
            Route::post('/save', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'save'])->name('save');
            Route::get('/mark-period', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'markPeriod'])->name('mark.period');
            Route::post('/save-period', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'savePeriod'])->name('save.period');
            Route::get('/bulk', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'bulk'])->name('bulk');
            Route::get('/bulk/students', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'getStudentsForBulk'])->name('bulk.students');
            Route::post('/bulk-save', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'bulkSave'])->name('bulk.save');
            Route::post('/bulk-update', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'bulkUpdate'])->name('bulk.update');
            Route::get('/report', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'report'])->name('report');
            Route::get('/export', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'export'])->name('export');
        });

        // Attendance Management - Teachers
        Route::prefix('attendance/teachers')->name('attendance.teachers.')->middleware('feature:attendance')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'index'])->name('index');
            Route::get('/mark', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'mark'])->name('mark');
            Route::post('/save', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'save'])->name('save');
            Route::get('/report', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'report'])->name('report');
            Route::get('/export', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'export'])->name('export');
        });

        // Holiday Management
        Route::prefix('attendance/holidays')->name('attendance.holidays.')->middleware('feature:attendance')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\HolidayController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Tenant\Admin\HolidayController::class, 'store'])->name('store');
            Route::delete('{id}', [\App\Http\Controllers\Tenant\Admin\HolidayController::class, 'destroy'])->name('destroy');
        });

        // Fee Management
        Route::prefix('fees')->name('fees.')->middleware('feature:fees')->group(function () {
            // Fee Components
            Route::prefix('components')->name('components.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Admin\FeeComponentController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Admin\FeeComponentController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Admin\FeeComponentController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [\App\Http\Controllers\Tenant\Admin\FeeComponentController::class, 'edit'])->name('edit');
                Route::put('/{id}', [\App\Http\Controllers\Tenant\Admin\FeeComponentController::class, 'update'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\Tenant\Admin\FeeComponentController::class, 'destroy'])->name('destroy');
            });

            // Fee Plans
            Route::prefix('plans')->name('plans.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'store'])->name('store');
                Route::get('/{id}', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'edit'])->name('edit');
                Route::put('/{id}', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'update'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/assign', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'assign'])->name('assign');
                Route::post('/{id}/assign', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'assignStore'])->name('assign.store');
                Route::get('/{id}/print', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'print'])->name('print');
                Route::get('/{id}/export', [\App\Http\Controllers\Tenant\Admin\FeePlanController::class, 'export'])->name('export');
            });

            // Fee Collection
            Route::prefix('collection')->name('collection.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'index'])->name('index');
                Route::get('/payments', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'payments'])->name('payments');
                Route::get('/{studentId}', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'show'])->name('show');
                Route::get('/{studentId}/collect', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'collect'])->name('collect');
                Route::post('/{studentId}/payment', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'processPayment'])->name('payment');
                Route::get('/receipt/{paymentId}', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'receipt'])->name('receipt');
            });

            // Student Fee Cards
            Route::prefix('cards')->name('cards.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'index'])->name('index');
                Route::get('/bulk-actions', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'bulkActions'])->name('bulk-actions');
                Route::get('/bulk-preview', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'bulkPreview'])->name('bulk-preview');
                Route::post('/bulk-export', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'bulkExport'])->name('bulk-export');
                Route::get('/{studentId}', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'show'])->name('show');
                Route::get('/{studentId}/print', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'print'])->name('print');
                Route::post('/{feeCardId}/discount', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'applyDiscount'])->name('discount');
                Route::post('/{feeItemId}/waive', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'waiveFee'])->name('waive');
                Route::post('/{feeCardId}/late-fee', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'applyLateFee'])->name('late-fee');
                Route::post('/{studentId}/reminder', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'sendReminder'])->name('reminder');
            });

            // No Dues Certificates
            Route::prefix('no-dues')->name('no-dues.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'index'])->name('index');
                Route::get('/generate', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'generate'])->name('generate');
                Route::post('/', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'store'])->name('store');
                Route::post('/bulk', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'bulkGenerate'])->name('bulk-generate');
                Route::get('/bulk-actions', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'bulkActions'])->name('bulk-actions');
                Route::get('/bulk-preview', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'bulkPreview'])->name('bulk-preview');
                Route::post('/bulk-export', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'bulkExport'])->name('bulk-export');
                Route::post('/bulk-delete', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'bulkDestroy'])->name('bulk-destroy');
                Route::get('/{id}/print', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'print'])->name('print');
                Route::delete('/{id}', [\App\Http\Controllers\Tenant\Admin\NoDuesController::class, 'destroy'])->name('destroy');
            });

            // Payment Receipts
            Route::prefix('receipts')->name('receipts.')->group(function () {
                Route::get('/{paymentId}', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'receipt'])->name('show');
                Route::get('/{paymentId}/download', [\App\Http\Controllers\Tenant\Admin\StudentFeeCardController::class, 'downloadReceipt'])->name('download');
            });

            // Fee Reports
            Route::get('/reports', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'reports'])->name('reports');
        });

        // LMS Management
        Route::prefix('lms')->name('lms.')->middleware('feature:assignments')->group(function () {
            // Courses
            Route::resource('courses', \App\Http\Controllers\Tenant\Admin\CourseController::class);

            // Course Content (Chapters & Topics)
            Route::post('courses/{course}/chapters', [\App\Http\Controllers\Tenant\Admin\ContentController::class, 'storeChapter'])->name('chapters.store');
            Route::put('chapters/{chapter}', [\App\Http\Controllers\Tenant\Admin\ContentController::class, 'updateChapter'])->name('chapters.update');
            Route::delete('chapters/{chapter}', [\App\Http\Controllers\Tenant\Admin\ContentController::class, 'destroyChapter'])->name('chapters.destroy');

            Route::post('chapters/{chapter}/topics', [\App\Http\Controllers\Tenant\Admin\ContentController::class, 'storeTopic'])->name('topics.store');
            Route::put('topics/{topic}', [\App\Http\Controllers\Tenant\Admin\ContentController::class, 'updateTopic'])->name('topics.update');
            Route::delete('topics/{topic}', [\App\Http\Controllers\Tenant\Admin\ContentController::class, 'destroyTopic'])->name('topics.destroy');

            // Assignments
            Route::post('courses/{course}/assignments', [\App\Http\Controllers\Tenant\Admin\AssignmentController::class, 'store'])->name('assignments.store');
            Route::put('assignments/{assignment}', [\App\Http\Controllers\Tenant\Admin\AssignmentController::class, 'update'])->name('assignments.update');
            Route::delete('assignments/{assignment}', [\App\Http\Controllers\Tenant\Admin\AssignmentController::class, 'destroy'])->name('assignments.destroy');

            // Quizzes
            Route::post('courses/{course}/quizzes', [\App\Http\Controllers\Tenant\Admin\QuizController::class, 'store'])->name('quizzes.store');
            Route::put('quizzes/{quiz}', [\App\Http\Controllers\Tenant\Admin\QuizController::class, 'update'])->name('quizzes.update');
            Route::delete('quizzes/{quiz}', [\App\Http\Controllers\Tenant\Admin\QuizController::class, 'destroy'])->name('quizzes.destroy');

            // Quiz Questions
            Route::post('quizzes/{quiz}/questions', [\App\Http\Controllers\Tenant\Admin\QuizController::class, 'storeQuestion'])->name('questions.store');
            Route::put('questions/{question}', [\App\Http\Controllers\Tenant\Admin\QuizController::class, 'updateQuestion'])->name('questions.update');
            Route::delete('questions/{question}', [\App\Http\Controllers\Tenant\Admin\QuizController::class, 'destroyQuestion'])->name('questions.destroy');
        });

        // Notification Logs (SMS / Email) for this tenant
        Route::get('notifications/logs', [\App\Http\Controllers\Tenant\Admin\NotificationLogController::class, 'index'])
            ->name('notifications.logs');

        // API Routes for Biometric/QR Integration
        Route::prefix('api/attendance')->name('api.attendance.')->middleware(['auth:sanctum', 'tenant'])->group(function () {
            Route::post('/mark', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'apiMark'])->name('mark');
            Route::post('/mark-bulk', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'apiMarkBulk'])->name('mark.bulk');
            Route::get('/status/{studentId}', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'apiStatus'])->name('status');
        });

        // Future modules will be added here as they are developed
        // - Grades/Marks
        // - Academic Reports
    });
})->where('tenant', '^(?!app$)[a-zA-Z0-9-]+$'); // Exclude 'app' from tenant pattern
