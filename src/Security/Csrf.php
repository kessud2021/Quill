<?php

namespace Framework\Security;

class Csrf {
    protected $tokenKey = '_token';

    public function generate() {
        $token = bin2hex(random_bytes(32));

        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION[$this->tokenKey] = $token;

        return $token;
    }

    public function token() {
        if (!isset($_SESSION)) {
            session_start();
        }

        return $_SESSION[$this->tokenKey] ?? $this->generate();
    }

    public function verify($token) {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (!isset($_SESSION[$this->tokenKey])) {
            return false;
        }

        return hash_equals($_SESSION[$this->tokenKey], $token);
    }

    public function verifyRequest($request) {
        $token = $request->input($this->tokenKey) ?? $request->getHeader('X-CSRF-Token');

        return $this->verify($token);
    }
}
