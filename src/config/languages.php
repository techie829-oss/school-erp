<?php

return [
    'default' => env('APP_LOCALE', 'en'),
    'fallback' => 'en',
    'supported' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'code' => 'en',
            'dir' => 'ltr',
        ],
        'hi' => [
            'name' => 'Hindi',
            'native' => 'हिंदी',
            'code' => 'hi',
            'dir' => 'ltr',
        ],
        'kn' => [
            'name' => 'Kannada',
            'native' => 'ಕನ್ನಡ',
            'code' => 'kn',
            'dir' => 'ltr',
        ],
    ],
];
