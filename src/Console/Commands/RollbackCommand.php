<?php

namespace Framework\Console\Commands;

use Framework\Database\Migrations\MigrationRunner;

class RollbackCommand extends Command {
    protected $signature = 'migrate:rollback';
    protected $description = 'Rollback the last batch of database migrations';

    public function handle($args) {
        $runner = new MigrationRunner(app('db'), DATABASE_PATH . '/migrations');

        try {
            $runner->rollback();
            $this->info('Rollback completed successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error('Rollback failed: ' . $e->getMessage());
            return 1;
        }
    }
}
