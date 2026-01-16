<?php

return [
    'name' => env('APP_NAME', 'Framework'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost:8000'),

    'providers' => [
        \App\Providers\AppServiceProvider::class,
        \App\Providers\DatabaseServiceProvider::class,
    ],
];
