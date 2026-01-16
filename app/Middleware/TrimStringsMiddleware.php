<?php

namespace App\Middleware;

use Framework\Middleware\Middleware;

class TrimStringsMiddleware extends Middleware {
    protected $skip = ['password', 'password_confirmation'];

    public function handle($request, $next) {
        $data = [];

        foreach ($request->all() as $key => $value) {
            if (in_array($key, $this->skip)) {
                $data[$key] = $value;
            } else {
                $data[$key] = is_string($value) ? trim($value) : $value;
            }
        }

        return $next($request);
    }
}
