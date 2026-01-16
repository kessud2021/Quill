<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Seed command - seed database with data
 */
class SeedCommand extends Command
{
    /**
     * Handle the command
     *
     * @param array $arguments
     * @return int
     */
    public function handle(array $arguments): int
    {
        $this->info('Seeding database...');

        $seedersPath = database_path('seeders');

        if (!is_dir($seedersPath)) {
            $this->error('Seeders directory not found');
            return 1;
        }

        $files = glob($seedersPath . '/*.php');

        if (empty($files)) {
            $this->info('No seeders found');
            return 0;
        }

        foreach ($files as $file) {
            require $file;
            $className = $this->getClassNameFromFile($file);

            if (class_exists($className)) {
                $seeder = new $className();
                if (method_exists($seeder, 'run')) {
                    $seeder->run();
                    $this->info("Seeded: " . basename($file));
                }
            }
        }

        $this->info('Seeding completed');
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
