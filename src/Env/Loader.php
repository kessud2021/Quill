<?php

namespace Framework\Env;

class Loader {
    protected $variables = [];

    public function __construct($file = null) {
        if ($file && file_exists($file)) {
            $this->load($file);
        }
    }

    public function load($file = null) {
        if ($file === null) {
            $file = FRAMEWORK_PATH . '/.env';
        }

        if (!file_exists($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            $value = $this->parseValue($value);

            $this->variables[$key] = $value;
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }

    protected function parseValue($value) {
        if (in_array($value, ['true', 'false'])) {
            return $value === 'true';
        }

        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }

        if (($value[0] ?? null) === '"' && ($value[-1] ?? null) === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }

    public function get($key, $default = null) {
        return $this->variables[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    public function has($key) {
        return isset($this->variables[$key]) || isset($_ENV[$key]) || getenv($key) !== false;
    }
}
