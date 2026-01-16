<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Make provider command
 */
class MakeProviderCommand extends Command
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
            $this->error('Provider name required');
            return 1;
        }

        $name = $arguments[0];
        $providersPath = app_path('Providers');

        if (!is_dir($providersPath)) {
            mkdir($providersPath, 0755, true);
        }

        $file = $providersPath . '/' . $name . '.php';

        if (file_exists($file)) {
            $this->error("Provider already exists: {$name}");
            return 1;
        }

        $stub = $this->getStub($name);
        file_put_contents($file, $stub);

        $this->info("Created provider: {$name}");
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

namespace App\Providers;

use Framework\Container\Container;

class {$name}
{
    /**
     * Container instance
     *
     * @var Container
     */
    protected Container \$container;

    /**
     * Constructor
     *
     * @param Container \$container
     */
    public function __construct(Container \$container)
    {
        \$this->container = \$container;
    }

    /**
     * Register services
     *
     * @return void
     */
    public function register(): void
    {
        // Register services into the container
    }

    /**
     * Boot services
     *
     * @return void
     */
    public function boot(): void
    {
        // Boot services after registration
    }
}
PHP;
    }
}
