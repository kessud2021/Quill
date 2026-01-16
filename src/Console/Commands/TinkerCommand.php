<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

/**
 * Tinker command - interactive shell
 */
class TinkerCommand extends Command
{
    /**
     * Handle the command
     *
     * @param array $arguments
     * @return int
     */
    public function handle(array $arguments): int
    {
        $this->info('Quill Tinker - Interactive Shell');
        $this->info('Type "exit" to quit');
        $this->info('');

        while (true) {
            echo 'tinker> ';
            $input = trim(fgets(STDIN));

            if ($input === 'exit') {
                break;
            }

            if (empty($input)) {
                continue;
            }

            try {
                // Wrap in a function for proper variable scope
                $result = $this->evaluate($input);
                if ($result !== null) {
                    var_dump($result);
                }
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }

        return 0;
    }

    /**
     * Evaluate code
     *
     * @param string $code
     * @return mixed
     */
    protected function evaluate(string $code)
    {
        return eval('return ' . $code . ';');
    }
}
