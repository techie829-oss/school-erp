<?php

return [

    /*
    |--------------------------------------------------------------------------
    | School ERP Domain Configuration
    |--------------------------------------------------------------------------
    | Multi-tenancy domain settings for landing, admin, and tenant access
    |
    */

    'domains' => [
        'primary' => env('PRIMARY_DOMAIN', 'myschool.test'),
        'admin' => env('ADMIN_DOMAIN', 'app.myschool.test'),
        'tenant_pattern' => env('TENANT_PATTERN', '*.myschool.test'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    | Basic company details for landing pages
    |
    */

    'company' => [
        'name' => env('COMPANY_NAME', 'School ERP'),
        'tagline' => env('COMPANY_TAGLINE', 'Complete School Management System'),
        'email' => env('COMPANY_EMAIL', 'info@myschool.com'),
        'phone' => env('COMPANY_PHONE', '+91 98765 43210'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Color Palette Configuration
    |--------------------------------------------------------------------------
    | Customizable color scheme for the application
    |
    */

    'colors' => [
        'primary' => [
            '50' => env('COLOR_PRIMARY_50', '#eff6ff'),
            '100' => env('COLOR_PRIMARY_100', '#dbeafe'),
            '500' => env('COLOR_PRIMARY_500', '#3b82f6'),
            '600' => env('COLOR_PRIMARY_600', '#2563eb'),
            '700' => env('COLOR_PRIMARY_700', '#1d4ed8'),
            '900' => env('COLOR_PRIMARY_900', '#1e3a8a'),
        ],
        'secondary' => [
            '50' => env('COLOR_SECONDARY_50', '#f8fafc'),
            '100' => env('COLOR_SECONDARY_100', '#f1f5f9'),
            '500' => env('COLOR_SECONDARY_500', '#64748b'),
            '600' => env('COLOR_SECONDARY_600', '#475569'),
            '700' => env('COLOR_SECONDARY_700', '#334155'),
            '900' => env('COLOR_SECONDARY_900', '#0f172a'),
        ],
        'accent' => [
            '50' => env('COLOR_ACCENT_50', '#fef3c7'),
            '100' => env('COLOR_ACCENT_100', '#fde68a'),
            '500' => env('COLOR_ACCENT_500', '#f59e0b'),
            '600' => env('COLOR_ACCENT_600', '#d97706'),
            '700' => env('COLOR_ACCENT_700', '#b45309'),
            '900' => env('COLOR_ACCENT_900', '#78350f'),
        ],
        'success' => env('COLOR_SUCCESS', '#10b981'),
        'warning' => env('COLOR_WARNING', '#f59e0b'),
        'error' => env('COLOR_ERROR', '#ef4444'),
        'info' => env('COLOR_INFO', '#3b82f6'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles
    |--------------------------------------------------------------------------
    | Simple on/off switches for major features
    |
    */

    'features' => [
        'multi_tenancy' => env('FEATURE_MULTI_TENANCY', true),
        'online_payments' => env('FEATURE_ONLINE_PAYMENTS', true),
        'sms_notifications' => env('FEATURE_SMS_NOTIFICATIONS', true),
        'lms' => env('FEATURE_LMS', true),
    ],

];
