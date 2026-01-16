<?php

namespace Framework\Cache;

class Store {
    protected $path;
    protected $defaultTtl = 3600;

    public function __construct($path = null) {
        $this->path = $path ?? STORAGE_PATH . '/cache';

        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function put($key, $value, $minutes = null) {
        $ttl = $minutes ? $minutes * 60 : $this->defaultTtl;
        $expiration = time() + $ttl;

        $data = [
            'value' => $value,
            'expiration' => $expiration,
        ];

        $file = $this->getPath($key);
        file_put_contents($file, serialize($data));

        return $this;
    }

    public function get($key, $default = null) {
        $file = $this->getPath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expiration'] < time()) {
            unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public function has($key) {
        return $this->get($key) !== null;
    }

    public function forget($key) {
        $file = $this->getPath($key);

        if (file_exists($file)) {
            unlink($file);
        }

        return $this;
    }

    public function flush() {
        $files = glob($this->path . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return $this;
    }

    public function remember($key, $minutes, $callback) {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = call_user_func($callback);

        $this->put($key, $value, $minutes);

        return $value;
    }

    protected function getPath($key) {
        $hash = md5($key);
        return $this->path . '/' . $hash . '.cache';
    }
}
