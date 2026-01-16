<?php

namespace Framework\Config;

class Repository {
    protected $items = [];

    public function get($key, $default = null) {
        if (strpos($key, '.') === false) {
            return $this->items[$key] ?? $default;
        }

        $parts = explode('.', $key);
        $value = $this->items;

        foreach ($parts as $part) {
            if (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return $default;
            }
        }

        return $value;
    }

    public function set($key, $value) {
        if (strpos($key, '.') === false) {
            $this->items[$key] = $value;
            return;
        }

        $parts = explode('.', $key);
        $key = array_pop($parts);
        $array = &$this->items;

        foreach ($parts as $part) {
            if (!isset($array[$part])) {
                $array[$part] = [];
            }
            $array = &$array[$part];
        }

        $array[$key] = $value;
    }

    public function has($key) {
        return $this->get($key) !== null;
    }

    public function all() {
        return $this->items;
    }
}
