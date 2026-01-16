<?php

namespace Framework\Console\Commands;

class SeedCommand extends Command {
    protected $signature = 'seed';
    protected $description = 'Seed the database with initial data';

    public function handle($args) {
        $seedsPath = DATABASE_PATH . '/seeds';

        if (!is_dir($seedsPath)) {
            $this->warn('No seeds directory found');
            return 0;
        }

        $files = glob($seedsPath . '/*.php');

        if (empty($files)) {
            $this->info('No seeds to run');
            return 0;
        }

        foreach ($files as $file) {
            $className = basename($file, '.php');

            try {
                include $file;
                $class = "Database\\Seeds\\$className";

                if (class_exists($class)) {
                    $seeder = new $class();
                    $seeder->run();
                    $this->info("Seeded: $className");
                }
            } catch (\Exception $e) {
                $this->error("Failed to seed $className: " . $e->getMessage());
            }
        }

        return 0;
    }
}
