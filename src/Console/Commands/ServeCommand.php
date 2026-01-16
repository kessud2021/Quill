<?php

namespace Framework\Console\Commands;

class ServeCommand extends Command {
    protected $signature = 'serve';
    protected $description = 'Start the development server';

    public function handle($args) {
        $host = 'localhost';
        $port = '8000';

        if (!empty($args)) {
            if (isset($args[0])) {
                $host = $args[0];
            }
            if (isset($args[1])) {
                $port = $args[1];
            }
        }

        $this->info("Starting server at http://$host:$port");
        $this->info("Press Ctrl+C to stop the server");

        $root = PUBLIC_PATH;
        $command = "php -S $host:$port -t $root";

        passthru($command);

        return 0;
    }
}
