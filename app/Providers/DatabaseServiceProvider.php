<?php

namespace App\Providers;

use Framework\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider {
    public function register() {
        // Database already registered in Application::registerCoreServices
    }

    public function boot() {
        // Run any database boot logic
    }
}
