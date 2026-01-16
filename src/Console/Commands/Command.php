<?php

namespace Framework\Console\Commands;

abstract class Command {
    protected $signature;
    protected $description;

    abstract public function handle($args);

    protected function info($message) {
        echo "[INFO] $message\n";
    }

    protected function error($message) {
        echo "[ERROR] $message\n";
    }

    protected function warn($message) {
        echo "[WARN] $message\n";
    }

    protected function line($message = '') {
        echo $message . "\n";
    }
}
