<?php

namespace App\Middleware;

use Framework\Middleware\Middleware;

class CorsMiddleware extends Middleware {
    public function handle($request, $next) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($request->getMethod() === 'OPTIONS') {
            return new \Framework\Http\Response('', 200);
        }

        return $next($request);
    }
}
