<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\InitializeTenantContext::class,
])->group(function () {
    // Public routes
    Route::get('/', function () {
        if (auth('tadmin')->check()) {
            return redirect('/admin/dashboard');  // Use relative path
        }
        return redirect()->route('tenant.login');
    })->name('tenant.home');

    // Authentication routes
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('tenant.login');
        Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('tenant.login.post');
        Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('tenant.logout');
    });

    // Test route without middleware
    Route::get('/test-admin', function () {
        return 'Tenant admin route is working!';
    });

    // Protected admin routes
    Route::middleware(['tenant.auth'])->prefix('admin')->name('tenant.admin.')->group(function () {
        // Dashboard
        Route::get('dashboard', [\App\Http\Controllers\Tenant\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Redirect /admin to dashboard
        Route::get('/', function () {
            return redirect('/admin/dashboard');  // Use relative path to stay on current domain
        });

        // Student Management
        Route::resource('students', \App\Http\Controllers\Tenant\Admin\StudentController::class);
        Route::get('/students/{student}/profile', [\App\Http\Controllers\Tenant\Admin\StudentController::class, 'profile'])->name('students.profile');

        // Test route to verify tenant routes are working
        Route::get('/test', function () {
            return 'Tenant routes are working!';
        })->name('test');

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
    });
});
