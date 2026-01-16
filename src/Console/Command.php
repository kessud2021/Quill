<?php

namespace Framework\Console;

/**
 * Base console command class
 */
abstract class Command
{
    /**
     * Command name
     *
     * @var string
     */
    protected string $name = '';

    /**
     * Command description
     *
     * @var string
     */
    protected string $description = '';

    /**
     * Arguments
     *
     * @var array
     */
    protected array $arguments = [];

    /**
     * Handle the command
     *
     * @param array $arguments
     * @return int
     */
    abstract public function handle(array $arguments): int;

    /**
     * Write output
     *
     * @param string $message
     * @return void
     */
    protected function info(string $message): void
    {
        echo $message . "\n";
    }

    /**
     * Write error
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message): void
    {
        echo "Error: {$message}\n";
    }

    /**
     * Get an argument
     *
     * @param int $index
     * @param string|null $default
     * @return string|null
     */
    protected function argument(int $index, ?string $default = null): ?string
    {
        return $this->arguments[$index] ?? $default;
    }
}
