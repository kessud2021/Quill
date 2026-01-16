<?php

namespace Framework\Session;

/**
 * Session manager
 */
class Manager
{
    /**
     * Session started flag
     *
     * @var bool
     */
    protected bool $started = false;

    /**
     * Session data
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Create a new session manager
     */
    public function __construct()
    {
        $this->start();
    }

    /**
     * Start the session
     *
     * @return void
     */
    public function start(): void
    {
        if (!$this->started) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->data = $_SESSION;
            $this->started = true;
        }
    }

    /**
     * Get a session value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $_SESSION;

        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Put a value in the session
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function put(string $key, $value): void
    {
        $keys = explode('.', $key);
        $current = &$_SESSION;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $current[$segment] = $value;
            } else {
                if (!isset($current[$segment])) {
                    $current[$segment] = [];
                }
                $current = &$current[$segment];
            }
        }

        $this->data = $_SESSION;
    }

    /**
     * Push a value onto a session array
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function push(string $key, $value): void
    {
        $current = $this->get($key, []);

        if (!is_array($current)) {
            $current = [];
        }

        $current[] = $value;
        $this->put($key, $current);
    }

    /**
     * Remove a value from the session
     *
     * @param string $key
     * @return mixed
     */
    public function forget(string $key)
    {
        $keys = explode('.', $key);
        $value = null;
        $current = &$_SESSION;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $value = $current[$segment] ?? null;
                unset($current[$segment]);
            } else {
                if (isset($current[$segment])) {
                    $current = &$current[$segment];
                } else {
                    break;
                }
            }
        }

        $this->data = $_SESSION;
        return $value;
    }

    /**
     * Flush all session data
     *
     * @return void
     */
    public function flush(): void
    {
        $_SESSION = [];
        $this->data = [];
    }

    /**
     * Check if a session key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Get all session data
     *
     * @return array
     */
    public function all(): array
    {
        return $_SESSION;
    }
}
