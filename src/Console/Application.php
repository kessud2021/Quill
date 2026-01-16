<?php

namespace Framework\Console;

/**
 * Console application for CLI commands
 */
class Application
{
    /**
     * Application container
     *
     * @var \Framework\Container\Container
     */
    protected \Framework\Container\Container $container;

    /**
     * Registered commands
     *
     * @var array
     */
    protected array $commands = [];

    /**
     * Create a new console application
     */
    public function __construct()
    {
        $this->container = app();
        $this->registerCommands();
    }

    /**
     * Register all commands
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        $this->commands = [
            'serve' => \Framework\Console\Commands\ServeCommand::class,
            'migrate' => \Framework\Console\Commands\MigrateCommand::class,
            'migrate:rollback' => \Framework\Console\Commands\MigrateRollbackCommand::class,
            'seed' => \Framework\Console\Commands\SeedCommand::class,
            'tinker' => \Framework\Console\Commands\TinkerCommand::class,
            'make:controller' => \Framework\Console\Commands\MakeControllerCommand::class,
            'make:model' => \Framework\Console\Commands\MakeModelCommand::class,
            'make:migration' => \Framework\Console\Commands\MakeMigrationCommand::class,
            'make:request' => \Framework\Console\Commands\MakeRequestCommand::class,
            'make:provider' => \Framework\Console\Commands\MakeProviderCommand::class,
        ];
    }

    /**
     * Handle console input
     *
     * @param int $argc
     * @param array $argv
     * @return int
     */
    public function handle(int $argc, array $argv): int
    {
        if ($argc < 2) {
            $this->showHelp();
            return 0;
        }

        $command = $argv[1];
        $arguments = array_slice($argv, 2);

        if (!isset($this->commands[$command])) {
            echo "Command not found: {$command}\n";
            return 1;
        }

        try {
            $commandClass = $this->commands[$command];
            $commandInstance = new $commandClass();
            return $commandInstance->handle($arguments);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return 1;
        }
    }

    /**
     * Show help
     *
     * @return void
     */
    protected function showHelp(): void
    {
        echo "\nQuill Framework Console\n";
        echo "=======================\n\n";
        echo "Available commands:\n";
        foreach ($this->commands as $name => $class) {
            echo "  php artisan {$name}\n";
        }
        echo "\n";
    }
}
