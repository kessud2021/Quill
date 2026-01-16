<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Make model command
 */
class MakeModelCommand extends Command
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
            $this->error('Model name required');
            return 1;
        }

        $name = $arguments[0];
        $modelsPath = app_path('Models');

        if (!is_dir($modelsPath)) {
            mkdir($modelsPath, 0755, true);
        }

        $file = $modelsPath . '/' . $name . '.php';

        if (file_exists($file)) {
            $this->error("Model already exists: {$name}");
            return 1;
        }

        $stub = $this->getStub($name);
        file_put_contents($file, $stub);

        $this->info("Created model: {$name}");
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

namespace App\Models;

use Framework\Database\Model;

class {$name} extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected ?string \$table = null;

    /**
     * Primary key
     *
     * @var string
     */
    protected string \$primaryKey = 'id';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected array \$fillable = [];

    /**
     * Soft delete column
     *
     * @var string|null
     */
    protected ?string \$softDeleteColumn = 'deleted_at';
}
PHP;
    }
}
