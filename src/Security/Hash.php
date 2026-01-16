<?php

namespace Framework\Security;

class Hash {
    public static function make($password, $options = []) {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => $options['rounds'] ?? 10,
        ]);
    }

    public static function check($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function needsRehash($hash, $options = []) {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, [
            'cost' => $options['rounds'] ?? 10,
        ]);
    }
}
