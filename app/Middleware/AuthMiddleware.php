<?php

namespace App\Middleware;

use Framework\Middleware\Middleware;

class AuthMiddleware extends Middleware {
    public function handle($request, $next) {
        if (!auth()->check()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
