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
        'email' => env('COMPANY_EMAIL', 'care@' . env('PRIMARY_DOMAIN', 'myschool.test')),
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
            '50' => '#eff6ff',
            '100' => '#dbeafe',
            '500' => '#3b82f6',
            '600' => '#2563eb',
            '700' => '#1d4ed8',
            '900' => '#1e3a8a',
        ],
        'secondary' => [
            '50' => '#f8fafc',
            '100' => '#f1f5f9',
            '500' => '#64748b',
            '600' => '#475569',
            '700' => '#334155',
            '900' => '#0f172a',
        ],
        'accent' => [
            '50' => '#fef3c7',
            '100' => '#fde68a',
            '500' => '#f59e0b',
            '600' => '#d97706',
            '700' => '#b45309',
            '900' => '#78350f',
        ],
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'error' => '#ef4444',
        'info' => '#3b82f6',
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

    /*
    |--------------------------------------------------------------------------
    | CMS Fields Configuration
    |--------------------------------------------------------------------------
    | Define CMS fields for each page with: name, section, status
    | status: 'enabled' = use CMS data, 'disabled' = use default/hardcoded data
    |
    */

    'cms_fields' => [
        'home' => [
            ['name' => 'hero_badge', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_heading', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_description', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_button_text', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_button_url', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'stats_students', 'section' => 'stats', 'status' => 'enabled'],
            ['name' => 'stats_teachers', 'section' => 'stats', 'status' => 'enabled'],
            ['name' => 'stats_programs', 'section' => 'stats', 'status' => 'enabled'],
            ['name' => 'stats_years', 'section' => 'stats', 'status' => 'enabled'],
            ['name' => 'features_title', 'section' => 'features', 'status' => 'enabled'],
            ['name' => 'features_description', 'section' => 'features', 'status' => 'enabled'],
            ['name' => 'programs_title', 'section' => 'programs', 'status' => 'enabled'],
            ['name' => 'programs_description', 'section' => 'programs', 'status' => 'enabled'],
            ['name' => 'testimonials_title', 'section' => 'testimonials', 'status' => 'enabled'],
        ],
        'about' => [
            ['name' => 'hero_heading', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_description', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'story_title', 'section' => 'story', 'status' => 'enabled'],
            ['name' => 'story_content', 'section' => 'story', 'status' => 'enabled'],
            ['name' => 'stat1_value', 'section' => 'stats', 'status' => 'enabled'],
            ['name' => 'stat1_label', 'section' => 'stats', 'status' => 'enabled'],
            ['name' => 'stat2_value', 'section' => 'stats', 'status' => 'enabled'],
            ['name' => 'stat2_label', 'section' => 'stats', 'status' => 'enabled'],
            ['name' => 'info_title', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'info_item1_label', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'info_item1_value', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'info_item2_label', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'info_item2_value', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'info_item3_label', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'info_item3_value', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'info_item4_label', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'info_item4_value', 'section' => 'info', 'status' => 'enabled'],
            ['name' => 'mission_title', 'section' => 'mission', 'status' => 'enabled'],
            ['name' => 'mission_description', 'section' => 'mission', 'status' => 'enabled'],
            ['name' => 'value1_title', 'section' => 'mission', 'status' => 'enabled'],
            ['name' => 'value1_description', 'section' => 'mission', 'status' => 'enabled'],
            ['name' => 'value2_title', 'section' => 'mission', 'status' => 'enabled'],
            ['name' => 'value2_description', 'section' => 'mission', 'status' => 'enabled'],
            ['name' => 'value3_title', 'section' => 'mission', 'status' => 'enabled'],
            ['name' => 'value3_description', 'section' => 'mission', 'status' => 'enabled'],
            ['name' => 'vision_title', 'section' => 'vision', 'status' => 'enabled'],
            ['name' => 'vision_content', 'section' => 'vision', 'status' => 'enabled'],
            ['name' => 'principles_title', 'section' => 'vision', 'status' => 'enabled'],
            ['name' => 'principle1_title', 'section' => 'vision', 'status' => 'enabled'],
            ['name' => 'principle1_description', 'section' => 'vision', 'status' => 'enabled'],
            ['name' => 'principle2_title', 'section' => 'vision', 'status' => 'enabled'],
            ['name' => 'principle2_description', 'section' => 'vision', 'status' => 'enabled'],
            ['name' => 'principle3_title', 'section' => 'vision', 'status' => 'enabled'],
            ['name' => 'principle3_description', 'section' => 'vision', 'status' => 'enabled'],
        ],
        'programs' => [
            ['name' => 'hero_heading', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_description', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'programs_title', 'section' => 'programs', 'status' => 'enabled'],
            ['name' => 'programs_description', 'section' => 'programs', 'status' => 'enabled'],
            ['name' => 'highlights_title', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlights_description', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlight1_title', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlight1_description', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlight2_title', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlight2_description', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlight3_title', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlight3_description', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlight4_title', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'highlight4_description', 'section' => 'highlights', 'status' => 'enabled'],
            ['name' => 'cta_title', 'section' => 'cta', 'status' => 'enabled'],
            ['name' => 'cta_description', 'section' => 'cta', 'status' => 'enabled'],
            ['name' => 'cta_button_text', 'section' => 'cta', 'status' => 'enabled'],
        ],
        'facilities' => [
            ['name' => 'hero_heading', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_description', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'facilities_title', 'section' => 'facilities', 'status' => 'enabled'],
            ['name' => 'facilities_description', 'section' => 'facilities', 'status' => 'enabled'],
            ['name' => 'amenities_title', 'section' => 'amenities', 'status' => 'enabled'],
            ['name' => 'amenities_description', 'section' => 'amenities', 'status' => 'enabled'],
            ['name' => 'cta_title', 'section' => 'cta', 'status' => 'enabled'],
            ['name' => 'cta_description', 'section' => 'cta', 'status' => 'enabled'],
            ['name' => 'cta_button_text', 'section' => 'cta', 'status' => 'enabled'],
        ],
        'admission' => [
            ['name' => 'hero_badge', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_heading', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_description', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'process_badge', 'section' => 'process', 'status' => 'enabled'],
            ['name' => 'process_title', 'section' => 'process', 'status' => 'enabled'],
            ['name' => 'process_description', 'section' => 'process', 'status' => 'enabled'],
            ['name' => 'requirements_badge', 'section' => 'requirements', 'status' => 'enabled'],
            ['name' => 'requirements_title', 'section' => 'requirements', 'status' => 'enabled'],
            ['name' => 'requirements_description', 'section' => 'requirements', 'status' => 'enabled'],
            ['name' => 'dates_badge', 'section' => 'dates', 'status' => 'enabled'],
            ['name' => 'dates_title', 'section' => 'dates', 'status' => 'enabled'],
            ['name' => 'dates_description', 'section' => 'dates', 'status' => 'enabled'],
            ['name' => 'faq_badge', 'section' => 'faq', 'status' => 'enabled'],
            ['name' => 'faq_title', 'section' => 'faq', 'status' => 'enabled'],
            ['name' => 'faq_description', 'section' => 'faq', 'status' => 'enabled'],
            ['name' => 'cta_title', 'section' => 'cta', 'status' => 'enabled'],
            ['name' => 'cta_description', 'section' => 'cta', 'status' => 'enabled'],
            ['name' => 'cta_button_text', 'section' => 'cta', 'status' => 'enabled'],
            ['name' => 'cta_button_text_2', 'section' => 'cta', 'status' => 'enabled'],
        ],
        'contact' => [
            ['name' => 'hero_badge', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_heading', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'hero_description', 'section' => 'hero', 'status' => 'enabled'],
            ['name' => 'form_badge', 'section' => 'form', 'status' => 'enabled'],
            ['name' => 'form_title', 'section' => 'form', 'status' => 'enabled'],
            ['name' => 'form_description', 'section' => 'form', 'status' => 'enabled'],
            ['name' => 'form_button_text', 'section' => 'form', 'status' => 'enabled'],
            ['name' => 'contact_info_badge', 'section' => 'contact_info', 'status' => 'enabled'],
            ['name' => 'contact_info_title', 'section' => 'contact_info', 'status' => 'enabled'],
            ['name' => 'contact_info_description', 'section' => 'contact_info', 'status' => 'enabled'],
            ['name' => 'address', 'section' => 'contact_info', 'status' => 'enabled'],
            ['name' => 'phone', 'section' => 'contact_info', 'status' => 'enabled'],
            ['name' => 'email', 'section' => 'contact_info', 'status' => 'enabled'],
            ['name' => 'office_hours_title', 'section' => 'office_hours', 'status' => 'enabled'],
            ['name' => 'office_hours_weekdays', 'section' => 'office_hours', 'status' => 'enabled'],
            ['name' => 'office_hours_saturday', 'section' => 'office_hours', 'status' => 'enabled'],
            ['name' => 'office_hours_sunday', 'section' => 'office_hours', 'status' => 'enabled'],
            ['name' => 'map_badge', 'section' => 'map', 'status' => 'enabled'],
            ['name' => 'map_title', 'section' => 'map', 'status' => 'enabled'],
            ['name' => 'map_description', 'section' => 'map', 'status' => 'enabled'],
            ['name' => 'map_embed_url', 'section' => 'map', 'status' => 'enabled'],
            ['name' => 'social_badge', 'section' => 'social', 'status' => 'enabled'],
            ['name' => 'social_title', 'section' => 'social', 'status' => 'enabled'],
            ['name' => 'social_description', 'section' => 'social', 'status' => 'enabled'],
        ],
    ],

];
