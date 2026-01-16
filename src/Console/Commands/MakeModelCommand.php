<?php

namespace Framework\Console\Commands;

class MakeModelCommand extends Command {
    protected $signature = 'make:model';
    protected $description = 'Create a new model class';

    public function handle($args) {
        if (empty($args)) {
            $this->error('Please provide a model name');
            return 1;
        }

        $name = $args[0];
        $path = APP_PATH . '/Models/' . $name . '.php';

        if (file_exists($path)) {
            $this->error("Model already exists: $name");
            return 1;
        }

        $namespace = 'App\\Models';
        $class = basename($name);
        $table = strtolower($class) . 's';

        $stub = <<<PHP
<?php

namespace $namespace;

use Framework\Database\Model;

class $class extends Model {
    protected \$table = '$table';
    protected \$fillable = [];
    protected \$timestamps = true;
}
PHP;

        @mkdir(dirname($path), 0755, true);
        file_put_contents($path, $stub);

        $this->info("Model created: $name");
        return 0;
    }
}
