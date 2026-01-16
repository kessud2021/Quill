<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Make migration command
 */
class MakeMigrationCommand extends Command
{
    /**
     * Handle the command
     *
     * @param array $arguments
     * @return int
     */
    public function handle(array $arguments): int
    {
        if (empty($arguments[0])) {
            $this->error('Migration name required');
            return 1;
        }

        $name = $arguments[0];
        $migrationsPath = database_path('migrations');

        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $className = $this->getClassName($name);
        $file = $migrationsPath . '/' . $timestamp . '_' . $name . '.php';

        $stub = $this->getStub($className);
        file_put_contents($file, $stub);

        $this->info("Created migration: {$name}");
        return 0;
    }

    /**
     * Get class name
     *
     * @param string $name
     * @return string
     */
    protected function getClassName(string $name): string
    {
        return str_replace('_', '', ucwords($name, '_'));
    }

    /**
     * Get stub
     *
     * @param string $className
     * @return string
     */
    protected function getStub(string $className): string
    {
        return <<<PHP
<?php

class {$className}
{
    /**
     * Run the migration
     */
    public function up(): void
    {
        // Create table or modify schema
    }

    /**
     * Rollback the migration
     */
    public function down(): void
    {
        // Reverse the migration
    }
}
PHP;
    }
}
