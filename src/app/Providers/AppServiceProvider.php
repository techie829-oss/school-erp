<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bind {tenant} parameter ONLY for admin domain routes
        // Tenant domains use subdomain approach - tenant comes from domain, not route parameter
        \Route::bind('tenant', function ($value, $route) {
            $request = request();
            $host = $request->getHost();
            $adminDomain = config('all.domains.admin');

            // ONLY bind on admin domain (app.myschool.test)
            // This handles routes like /admin/tenants/{tenant}
            if ($host === $adminDomain) {
                return \App\Models\Tenant::find($value);
            }

            // On tenant domains (e.g., svps.myschool.test), return null
            // Tenant is resolved by middleware from the subdomain, not from route parameter
            return null;
        });
    }
}
