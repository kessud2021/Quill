<?php

namespace Framework\Console\Commands;

use Framework\Database\Migrations\MigrationRunner;

class MigrateCommand extends Command {
    protected $signature = 'migrate';
    protected $description = 'Run pending database migrations';

    public function handle($args) {
        $runner = new MigrationRunner(app('db'), DATABASE_PATH . '/migrations');

        try {
            $runner->run();
            $this->info('Migrations completed successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }
    }
}
