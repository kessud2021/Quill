<?php

namespace Framework\Session;

class Manager {
    protected $sessionPath;
    protected $sessionName = 'FRAMEWORK_SESSION';

    public function __construct($config = []) {
        $this->sessionPath = $config['path'] ?? STORAGE_PATH . '/sessions';

        if (!is_dir($this->sessionPath)) {
            mkdir($this->sessionPath, 0755, true);
        }

        ini_set('session.save_path', $this->sessionPath);
        ini_set('session.name', $this->sessionName);

        session_start();
    }

    public function all() {
        return $_SESSION;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function put($key, $value) {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function forget($key) {
        unset($_SESSION[$key]);
        return $this;
    }

    public function flush() {
        $_SESSION = [];
        return $this;
    }

    public function regenerate() {
        session_regenerate_id(true);
        return $this;
    }

    public function getToken() {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_token'];
    }

    public function destroy() {
        session_destroy();
    }
}
