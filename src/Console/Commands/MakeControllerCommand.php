<?php

namespace Framework\Console\Commands;

class MakeControllerCommand extends Command {
    protected $signature = 'make:controller';
    protected $description = 'Create a new controller class';

    public function handle($args) {
        if (empty($args)) {
            $this->error('Please provide a controller name');
            return 1;
        }

        $name = $args[0];
        $path = APP_PATH . '/Controllers/' . $name . '.php';

        if (file_exists($path)) {
            $this->error("Controller already exists: $name");
            return 1;
        }

        $namespace = 'App\\Controllers';
        $class = basename($name);

        $stub = <<<PHP
<?php

namespace $namespace;

class $class {
    public function index() {
        return view('index');
    }
}
PHP;

        @mkdir(dirname($path), 0755, true);
        file_put_contents($path, $stub);

        $this->info("Controller created: $name");
        return 0;
    }
}
