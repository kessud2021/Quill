<?php

namespace Framework\Security;

/**
 * Password hashing using bcrypt
 */
class Hash
{
    /**
     * Hash a password
     *
     * @param string $password
     * @return string
     */
    public static function make(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify a password
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function check(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if a hash needs to be rehashed
     *
     * @param string $hash
     * @return bool
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT);
    }
}
