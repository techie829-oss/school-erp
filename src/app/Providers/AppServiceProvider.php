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
        // Bind the {tenant} parameter from domain routes
        // This prevents it from being passed to controller methods
        \Route::bind('tenant', function ($value) {
            // The tenant parameter from domain is handled by middleware
            // We don't need to pass it to controllers
            // Return null so it's not passed to controller methods
            return null;
        });
    }
}
