<?php

return [
    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => STORAGE_PATH . '/logs/app.log',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => STORAGE_PATH . '/logs',
        ],

        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'daily'],
        ],
    ],
];
