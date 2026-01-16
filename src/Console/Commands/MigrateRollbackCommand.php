<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Migrate rollback command
 */
class MigrateRollbackCommand extends Command
{
    /**
     * Handle the command
     *
     * @param array $arguments
     * @return int
     */
    public function handle(array $arguments): int
    {
        $this->info('Rolling back migrations...');

        $migrationsPath = database_path('migrations');

        if (!is_dir($migrationsPath)) {
            $this->error('Migrations directory not found');
            return 1;
        }

        $files = array_reverse(glob($migrationsPath . '/*.php'));

        if (empty($files)) {
            $this->info('No migrations to rollback');
            return 0;
        }

        foreach ($files as $file) {
            require $file;
            $className = $this->getClassNameFromFile($file);

            if (class_exists($className)) {
                $migration = new $className();
                if (method_exists($migration, 'down')) {
                    $migration->down();
                    $this->info("Rolled back: " . basename($file));
                }
            }
        }

        $this->info('Rollback completed');
        return 0;
    }

    /**
     * Get class name from file
     *
     * @param string $file
     * @return string
     */
    protected function getClassNameFromFile(string $file): string
    {
        $content = file_get_contents($file);

        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $matches[1];
        }

        return '';
    }
}
