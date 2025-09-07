<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configure authentication middleware to use correct guard and login route
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'redirect.school.admin' => \App\Http\Middleware\RedirectSchoolAdminToTenant::class,
            'switch.tenant.database' => \App\Http\Middleware\SwitchTenantDatabase::class,
            'tenant.auth' => \App\Http\Middleware\TenantAuth::class,
            'validate.tenant.domain' => \App\Http\Middleware\ValidateTenantDomain::class,
            'enforce.admin.access' => \App\Http\Middleware\EnforceAdminAccessPolicy::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
