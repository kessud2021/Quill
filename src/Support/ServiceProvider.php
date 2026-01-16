<?php

namespace Framework\Support;

class ServiceProvider {
    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function register() {
        // Register bindings
    }

    public function boot() {
        // Bootstrap services
    }

    protected function publishes($source, $destination) {
        // Copy assets/config files from source to destination
    }
}
