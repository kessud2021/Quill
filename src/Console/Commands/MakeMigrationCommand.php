<?php

namespace Framework\Console\Commands;

class MakeMigrationCommand extends Command {
    protected $signature = 'make:migration';
    protected $description = 'Create a new migration file';

    public function handle($args) {
        if (empty($args)) {
            $this->error('Please provide a migration name');
            return 1;
        }

        $name = $args[0];
        $timestamp = date('Y_m_d_His');
        $filename = $timestamp . '_' . $name . '.php';
        $path = DATABASE_PATH . '/migrations/' . $filename;

        @mkdir(dirname($path), 0755, true);

        $className = $this->getClassName($name);

        $stub = <<<PHP
<?php

namespace Database\Migrations;

use Framework\Database\Migrations\Migration;

class $className extends Migration {
    public function up() {
        \$this->schema()->create('table_name', function (\$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down() {
        \$this->schema()->drop('table_name');
    }
}
PHP;

        file_put_contents($path, $stub);

        $this->info("Migration created: $filename");
        return 0;
    }

    protected function getClassName($name) {
        $parts = explode('_', $name);
        return implode('', array_map('ucfirst', $parts));
    }
}
