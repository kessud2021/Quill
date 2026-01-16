<?php

namespace Framework\Auth;

class Guard {
    protected $user;
    protected $sessionKey = 'auth_user';

    public function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function attempt($email, $password) {
        $model = \App\Models\User::where('email', $email)->first();

        if (!$model) {
            return false;
        }

        if (!\Framework\Security\Hash::check($password, $model->getAttribute('password'))) {
            return false;
        }

        $this->login($model);

        return true;
    }

    public function login($user) {
        $_SESSION[$this->sessionKey] = serialize($user);
        $this->user = $user;
    }

    public function logout() {
        unset($_SESSION[$this->sessionKey]);
        $this->user = null;
    }

    public function user() {
        if ($this->user === null && isset($_SESSION[$this->sessionKey])) {
            $this->user = unserialize($_SESSION[$this->sessionKey]);
        }

        return $this->user;
    }

    public function check() {
        return $this->user() !== null;
    }

    public function guest() {
        return !$this->check();
    }

    public function id() {
        $user = $this->user();
        return $user ? $user->getAttribute('id') : null;
    }
}
