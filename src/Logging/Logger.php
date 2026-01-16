<?php

namespace Framework\Logging;

/**
 * File-based structured logger
 */
class Logger
{
    /**
     * Log file path
     *
     * @var string
     */
    protected string $path;

    /**
     * Log levels
     *
     * @var array
     */
    protected array $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

    /**
     * Create a new logger
     *
     * @param string $path
     */
    public function __construct(string $path = '')
    {
        $this->path = $path ?: storage_path('logs/app.log');
        $this->ensureLogDirectory();
    }

    /**
     * Ensure log directory exists
     *
     * @return void
     */
    protected function ensureLogDirectory(): void
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Log a debug message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Log an info message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log a notice message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    /**
     * Log a warning message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log an error message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log a critical message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Log an alert message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    /**
     * Log an emergency message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }

    /**
     * Log a message
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $entry = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;

        file_put_contents($this->path, $entry, FILE_APPEND);
    }

    /**
     * Get log contents
     *
     * @return string
     */
    public function getContents(): string
    {
        if (!file_exists($this->path)) {
            return '';
        }

        return file_get_contents($this->path);
    }

    /**
     * Clear log file
     *
     * @return void
     */
    public function clear(): void
    {
        if (file_exists($this->path)) {
            file_put_contents($this->path, '');
        }
    }
}
