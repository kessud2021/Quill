<?php

namespace Framework\Config;

/**
 * Configuration manager
 * 
 * Handles application configuration with dot notation access
 */
class Config
{
    /**
     * Configuration items
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Create a new Config instance
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get a configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->items;

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
     * Set a configuration value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $current = &$this->items;

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
    }

    /**
     * Check if a configuration key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $keys = explode('.', $key);
        $value = $this->items;

        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all configuration
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get a configuration group
     *
     * @param string $group
     * @return array
     */
    public function group(string $group): array
    {
        return $this->get($group, []);
    }

    /**
     * Load configuration from a file
     *
     * @param string $path
     * @return array
     */
    public static function load(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        return require $path;
    }
}
