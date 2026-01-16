<?php

namespace Framework\Console;

class Application {
    protected $commands = [];

    public function __construct() {
        $this->registerDefaultCommands();
    }

    protected function registerDefaultCommands() {
        $this->command('migrate', Commands\MigrateCommand::class);
        $this->command('migrate:rollback', Commands\RollbackCommand::class);
        $this->command('seed', Commands\SeedCommand::class);
        $this->command('make:controller', Commands\MakeControllerCommand::class);
        $this->command('make:model', Commands\MakeModelCommand::class);
        $this->command('make:migration', Commands\MakeMigrationCommand::class);
        $this->command('serve', Commands\ServeCommand::class);
    }

    public function command($name, $class) {
        $this->commands[$name] = $class;
    }

    public function handle($argc, $argv) {
        if ($argc < 2) {
            $this->showHelp();
            return 0;
        }

        $command = $argv[1];
        $args = array_slice($argv, 2);

        if (!isset($this->commands[$command])) {
            echo "Command not found: $command\n";
            return 1;
        }

        $commandClass = $this->commands[$command];
        $instance = new $commandClass();

        return $instance->handle($args);
    }

    protected function showHelp() {
        echo "Framework Console\n\n";
        echo "Available commands:\n";
        foreach ($this->commands as $name => $class) {
            echo "  $name\n";
        }
    }
}
