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
    | Hosting Configuration
    |--------------------------------------------------------------------------
    | Application hosting type and server configuration
    |
    */

    'hosting' => [
        'type' => env('HOSTING_TYPE', 'laravel-herd'), // laravel-herd, apache, nginx
        'server' => [
            'laravel-herd' => [
                'enabled' => true,
                'config_path' => env('HERD_CONFIG_PATH', '~/.config/herd'),
                'vhost_path' => env('HERD_VHOST_PATH', '~/.config/herd/config/nginx/valet.conf'),
                'php_version' => env('HERD_PHP_VERSION', '8.3'),
                'mysql_version' => env('HERD_MYSQL_VERSION', '8.0'),
                'redis_version' => env('HERD_REDIS_VERSION', '7.0'),
            ],
            'apache' => [
                'enabled' => false,
                'config_path' => env('APACHE_CONFIG_PATH', '/etc/apache2/sites-available'),
                'vhost_path' => env('APACHE_VHOST_PATH', '/etc/apache2/sites-available/000-default.conf'),
                'document_root' => env('APACHE_DOCUMENT_ROOT', '/var/www/html'),
                'mod_rewrite' => env('APACHE_MOD_REWRITE', true),
            ],
            'nginx' => [
                'enabled' => false,
                'config_path' => env('NGINX_CONFIG_PATH', '/etc/nginx/sites-available'),
                'vhost_path' => env('NGINX_VHOST_PATH', '/etc/nginx/sites-available/default'),
                'document_root' => env('NGINX_DOCUMENT_ROOT', '/var/www/html'),
                'php_fpm_socket' => env('NGINX_PHP_FPM_SOCKET', '/var/run/php/php8.3-fpm.sock'),
            ],
        ],
        'ssl' => [
            'enabled' => env('SSL_ENABLED', false),
            'cert_path' => env('SSL_CERT_PATH', '/etc/ssl/certs/ssl-cert-snakeoil.pem'),
            'key_path' => env('SSL_KEY_PATH', '/etc/ssl/private/ssl-cert-snakeoil.key'),
            'auto_redirect' => env('SSL_AUTO_REDIRECT', true),
        ],
        'cache' => [
            'enabled' => env('CACHE_ENABLED', true),
            'driver' => env('CACHE_DRIVER', 'redis'),
            'ttl' => env('CACHE_TTL', 3600),
        ],
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
        'vhost_management' => env('FEATURE_VHOST_MANAGEMENT', true),
    ],

];
