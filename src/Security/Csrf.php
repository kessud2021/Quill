<?php

namespace Framework\Security;

/**
 * CSRF token management
 */
class Csrf
{
    /**
     * Token session key
     *
     * @var string
     */
    protected string $tokenKey = '_token';

    /**
     * Get or create a CSRF token
     *
     * @return string
     */
    public function getToken(): string
    {
        $session = session();

        if (!$session->has($this->tokenKey)) {
            $session->put($this->tokenKey, bin2hex(random_bytes(32)));
        }

        return $session->get($this->tokenKey);
    }

    /**
     * Verify a CSRF token
     *
     * @param string $token
     * @return bool
     */
    public function verify(string $token): bool
    {
        return hash_equals($this->getToken(), $token);
    }

    /**
     * Regenerate token
     *
     * @return string
     */
    public function regenerate(): string
    {
        session()->forget($this->tokenKey);
        return $this->getToken();
    }
}
