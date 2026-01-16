<?php

return [
    'default' => env('CACHE_DRIVER', 'file'),

    'drivers' => [
        'file' => [
            'driver' => 'file',
            'path' => STORAGE_PATH . '/cache',
        ],
    ],

    'prefix' => 'framework_cache_',
    'ttl' => 3600,
];
