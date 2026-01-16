<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Serve command - run PHP development server
 */
class ServeCommand extends Command
{
    /**
     * Handle the command
     *
     * @param array $arguments
     * @return int
     */
    public function handle(array $arguments): int
    {
        $host = $arguments[0] ?? 'localhost';
        $port = $arguments[1] ?? 8000;

        $this->info("Starting development server at http://{$host}:{$port}");

        $publicPath = base_path('public');
        passthru("php -S {$host}:{$port} -t {$publicPath}");

        return 0;
    }
}
