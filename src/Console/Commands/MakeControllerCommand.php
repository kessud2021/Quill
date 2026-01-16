<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Make controller command
 */
class MakeControllerCommand extends Command
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
            $this->error('Controller name required');
            return 1;
        }

        $name = $arguments[0];
        $controllersPath = app_path('Controllers');

        if (!is_dir($controllersPath)) {
            mkdir($controllersPath, 0755, true);
        }

        $file = $controllersPath . '/' . $name . '.php';

        if (file_exists($file)) {
            $this->error("Controller already exists: {$name}");
            return 1;
        }

        $stub = $this->getStub($name);
        file_put_contents($file, $stub);

        $this->info("Created controller: {$name}");
        return 0;
    }

    /**
     * Get stub
     *
     * @param string $name
     * @return string
     */
    protected function getStub(string $name): string
    {
        return <<<PHP
<?php

namespace App\Controllers;

use Framework\Foundation\Controller;
use Framework\Http\Request;

class {$name} extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing
     */
    public function index()
    {
        return response('Index action');
    }

    /**
     * Show create form
     */
    public function create()
    {
        return response('Create action');
    }

    /**
     * Store a new record
     */
    public function store(Request \$request)
    {
        return response('Store action');
    }

    /**
     * Show a record
     */
    public function show(\$id)
    {
        return response('Show action for ID: ' . \$id);
    }

    /**
     * Show edit form
     */
    public function edit(\$id)
    {
        return response('Edit action');
    }

    /**
     * Update a record
     */
    public function update(Request \$request, \$id)
    {
        return response('Update action');
    }

    /**
     * Delete a record
     */
    public function destroy(\$id)
    {
        return response('Delete action');
    }
}
PHP;
    }
}
