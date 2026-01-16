<?php

namespace Framework\Database\Migrations;

use Framework\Database\Manager;
use Framework\Database\Schema\Builder;

class MigrationRunner {
    protected $manager;
    protected $migrationsPath;
    protected $batch = 1;

    public function __construct(Manager $manager, $migrationsPath = null) {
        $this->manager = $manager;
        $this->migrationsPath = $migrationsPath ?? DATABASE_PATH . '/migrations';
    }

    public function run() {
        $this->ensureMigrationsTable();

        $files = $this->getPendingMigrations();

        foreach ($files as $file) {
            $this->runMigration($file);
        }
    }

    public function rollback() {
        $this->ensureMigrationsTable();

        $batch = $this->getLastBatch();

        if ($batch === null) {
            return;
        }

        $migrations = $this->getMigrationsForBatch($batch);

        foreach (array_reverse($migrations) as $migration) {
            $this->rollbackMigration($migration);
        }
    }

    protected function runMigration($file) {
        $className = $this->getMigrationClassName($file);
        $class = "Database\\Migrations\\$className";

        if (!class_exists($class)) {
            include $this->migrationsPath . '/' . $file;
        }

        $migration = new $class();
        $migration->up();

        $this->recordMigration($file);
    }

    protected function rollbackMigration($migration) {
        $file = $migration['migration'];
        $className = $this->getMigrationClassName($file);
        $class = "Database\\Migrations\\$className";

        if (!class_exists($class)) {
            include $this->migrationsPath . '/' . $file . '.php';
        }

        $instance = new $class();
        $instance->down();

        $this->forgetMigration($file);
    }

    protected function ensureMigrationsTable() {
        $connection = $this->manager->connection();

        try {
            $connection->selectOne('SELECT 1 FROM migrations LIMIT 1');
        } catch (\Exception $e) {
            $schema = new Builder($connection);
            $schema->create('migrations', function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
                $table->timestamps();
            });
        }
    }

    protected function getPendingMigrations() {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob($this->migrationsPath . '/*.php');
        $pending = [];

        foreach ($files as $file) {
            $migration = basename($file, '.php');

            if (!$this->hasMigrationRun($migration)) {
                $pending[] = basename($file);
            }
        }

        sort($pending);

        return $pending;
    }

    protected function hasMigrationRun($migration) {
        $result = $this->manager->connection()->selectOne(
            'SELECT 1 FROM migrations WHERE migration = ?',
            [$migration]
        );

        return $result !== null;
    }

    protected function recordMigration($migration) {
        $this->manager->connection()->insert(
            'INSERT INTO migrations (migration, batch, created_at, updated_at) VALUES (?, ?, ?, ?)',
            [
                $migration,
                $this->getNextBatch(),
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
            ]
        );
    }

    protected function forgetMigration($migration) {
        $this->manager->connection()->delete(
            'DELETE FROM migrations WHERE migration = ?',
            [$migration]
        );
    }

    protected function getLastBatch() {
        $result = $this->manager->connection()->selectOne(
            'SELECT MAX(batch) as batch FROM migrations'
        );

        return $result['batch'] ?? null;
    }

    protected function getNextBatch() {
        $last = $this->getLastBatch();
        return ($last ?? 0) + 1;
    }

    protected function getMigrationsForBatch($batch) {
        return $this->manager->connection()->select(
            'SELECT * FROM migrations WHERE batch = ? ORDER BY migration DESC',
            [$batch]
        );
    }

    protected function getMigrationClassName($file) {
        $file = basename($file, '.php');
        $parts = explode('_', $file);
        
        // Remove timestamp parts (YYYY_MM_DD_HHMMSS)
        for ($i = 0; $i < 4 && !empty($parts); $i++) {
            array_shift($parts);
        }

        return implode('', array_map('ucfirst', $parts));
    }
}
