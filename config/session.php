<?php

return [
    'driver' => env('SESSION_DRIVER', 'file'),
    
    'path' => STORAGE_PATH . '/sessions',
    
    'lifetime' => 120,
    
    'secure' => false,
    
    'http_only' => true,
    
    'same_site' => 'lax',
];
