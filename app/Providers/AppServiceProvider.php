<?php

namespace App\Providers;

use Framework\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    public function register() {
        $this->registerCsrf();
        $this->registerValidation();
    }

    public function boot() {
        // Application boot logic
    }

    protected function registerCsrf() {
        $this->app->singleton('csrf', function () {
            return new \Framework\Security\Csrf();
        });
    }

    protected function registerValidation() {
        $this->app->bind('validator', function ($app, $data, $rules) {
            return new \Framework\Security\Validator($data, $rules);
        });
    }
}
