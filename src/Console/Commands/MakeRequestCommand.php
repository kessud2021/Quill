<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Make request command
 */
class MakeRequestCommand extends Command
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
            $this->error('Request name required');
            return 1;
        }

        $name = $arguments[0];
        $requestsPath = app_path('Requests');

        if (!is_dir($requestsPath)) {
            mkdir($requestsPath, 0755, true);
        }

        $file = $requestsPath . '/' . $name . '.php';

        if (file_exists($file)) {
            $this->error("Request already exists: {$name}");
            return 1;
        }

        $stub = $this->getStub($name);
        file_put_contents($file, $stub);

        $this->info("Created request: {$name}");
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

namespace App\Requests;

use Framework\Http\Request;

class {$name}
{
    /**
     * Get validation rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // 'field' => 'required|string|max:255'
        ];
    }

    /**
     * Get custom error messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // 'field.required' => 'The field is required'
        ];
    }
}
PHP;
    }
}
