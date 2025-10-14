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

    // Tenant Admin Routes (protected)
    Route::middleware(['tenant.auth'])->prefix('admin')->name('tenant.admin.')->group(function () {
        // Dashboard
        Route::get('dashboard', [\App\Http\Controllers\Tenant\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', function () {
            return redirect('/admin/dashboard');
        });

        // Student Management (Manual routes - using studentId to avoid conflict with tenant parameter)
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

        // Settings Management
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'index'])->name('index');
            Route::post('/general', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateGeneral'])->name('update.general');
            Route::post('/features', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateFeatures'])->name('update.features');
            Route::post('/academic', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateAcademic'])->name('update.academic');
            Route::post('/attendance', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updateAttendance'])->name('update.attendance');
            Route::post('/payment', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'updatePayment'])->name('update.payment');
            Route::delete('/logo', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'deleteLogo'])->name('delete.logo');
        });

        // Class Management
        Route::get('classes', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'index'])->name('classes.index');
        Route::get('classes/create', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'create'])->name('classes.create');
        Route::post('classes', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'store'])->name('classes.store');
        Route::get('classes/{classId}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'show'])->name('classes.show')->where('classId', '[0-9]+');
        Route::get('classes/{classId}/edit', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'edit'])->name('classes.edit')->where('classId', '[0-9]+');
        Route::put('classes/{classId}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'update'])->name('classes.update')->where('classId', '[0-9]+');
        Route::patch('classes/{classId}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'update'])->name('classes.update.patch')->where('classId', '[0-9]+');
        Route::delete('classes/{classId}', [\App\Http\Controllers\Tenant\Admin\ClassController::class, 'destroy'])->name('classes.destroy')->where('classId', '[0-9]+');

        // Section Management
        Route::get('sections', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'index'])->name('sections.index');
        Route::get('sections/create', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'create'])->name('sections.create');
        Route::post('sections', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'store'])->name('sections.store');
        Route::get('sections/{sectionId}', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'show'])->name('sections.show')->where('sectionId', '[0-9]+');
        Route::get('sections/{sectionId}/edit', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'edit'])->name('sections.edit')->where('sectionId', '[0-9]+');
        Route::put('sections/{sectionId}', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'update'])->name('sections.update')->where('sectionId', '[0-9]+');
        Route::patch('sections/{sectionId}', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'update'])->name('sections.update.patch')->where('sectionId', '[0-9]+');
        Route::delete('sections/{sectionId}', [\App\Http\Controllers\Tenant\Admin\SectionController::class, 'destroy'])->name('sections.destroy')->where('sectionId', '[0-9]+');

        // Teacher Management
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

        // Attendance Management - Students
        Route::prefix('attendance/students')->name('attendance.students.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'index'])->name('index');
            Route::get('/mark', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'mark'])->name('mark');
            Route::post('/save', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'save'])->name('save');
            Route::get('/report', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'report'])->name('report');
            Route::get('/export', [\App\Http\Controllers\Tenant\Admin\StudentAttendanceController::class, 'export'])->name('export');
        });

        // Attendance Management - Teachers
        Route::prefix('attendance/teachers')->name('attendance.teachers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'index'])->name('index');
            Route::get('/mark', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'mark'])->name('mark');
            Route::post('/save', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'save'])->name('save');
            Route::get('/report', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'report'])->name('report');
            Route::get('/export', [\App\Http\Controllers\Tenant\Admin\TeacherAttendanceController::class, 'export'])->name('export');
        });

        // Fee Management
        Route::prefix('fees')->name('fees.')->group(function () {
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
            });

            // Fee Collection
            Route::prefix('collection')->name('collection.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'index'])->name('index');
                Route::get('/{studentId}', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'show'])->name('show');
                Route::get('/{studentId}/collect', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'collect'])->name('collect');
                Route::post('/{studentId}/payment', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'processPayment'])->name('payment');
                Route::get('/receipt/{paymentId}', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'receipt'])->name('receipt');
            });

            // Fee Reports
            Route::get('/reports', [\App\Http\Controllers\Tenant\Admin\FeeCollectionController::class, 'reports'])->name('reports');
        });

        // Future modules will be added here as they are developed
        // - Grades/Marks
        // - Academic Reports
    });
})->where('tenant', '^(?!app$)[a-zA-Z0-9-]+$'); // Exclude 'app' from tenant pattern
