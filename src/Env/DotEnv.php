<?php

namespace Framework\Env;

/**
 * Environment variable loader from .env files
 */
class DotEnv
{
    /**
     * Path to .env file
     *
     * @var string
     */
    protected string $path;

    /**
     * Create a new DotEnv instance
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Load the .env file
     *
     * @return void
     */
    public function load(): void
    {
        if (!file_exists($this->path)) {
            return;
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse line
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes
                if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
                    $value = substr($value, 1, -1);
                } elseif (str_starts_with($value, "'") && str_ends_with($value, "'")) {
                    $value = substr($value, 1, -1);
                }

                // Set environment variable
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}
