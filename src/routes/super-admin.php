<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SystemController;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| These routes are only accessible to super admin users
| and only work on the app.myschool.com domain
|
*/

Route::middleware(['auth', 'admin.user'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Tenant Management
    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    Route::post('tenants/{tenant}/deactivate', [TenantController::class, 'deactivate'])->name('tenants.deactivate');
    Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');

    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/make-admin', [UserController::class, 'makeAdmin'])->name('users.make-admin');
    Route::post('users/{user}/make-super-admin', [UserController::class, 'makeSuperAdmin'])->name('users.make-super-admin');
    Route::post('users/{user}/revoke-admin', [UserController::class, 'revokeAdmin'])->name('users.revoke-admin');

    // System Management
    Route::get('system/overview', [SystemController::class, 'overview'])->name('system.overview');
    Route::get('system/logs', [SystemController::class, 'logs'])->name('system.logs');
    Route::post('system/clear-cache', [SystemController::class, 'clearCache'])->name('system.clear-cache');
    Route::post('system/backup', [SystemController::class, 'backup'])->name('system.backup');
});
