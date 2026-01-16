<?php

use Framework\Foundation\Application;

if (!defined('FRAMEWORK_PATH')) {
    define('FRAMEWORK_PATH', dirname(__DIR__));
    define('APP_PATH', FRAMEWORK_PATH . '/app');
    define('CONFIG_PATH', FRAMEWORK_PATH . '/config');
    define('STORAGE_PATH', FRAMEWORK_PATH . '/storage');
    define('RESOURCES_PATH', FRAMEWORK_PATH . '/resources');
    define('PUBLIC_PATH', FRAMEWORK_PATH . '/public');
    define('DATABASE_PATH', FRAMEWORK_PATH . '/database');
}

require FRAMEWORK_PATH . '/vendor/autoload.php';

$app = new Application(FRAMEWORK_PATH);

$app->loadEnvironment();
$app->registerCoreServices();
$app->loadConfiguration();

$app->singleton('auth', function ($app) {
    return new \Framework\Auth\Guard();
});

$app->singleton('cache', function ($app) {
    return new \Framework\Cache\Store($app['config']->get('cache.path'));
});

$app->singleton('session', function ($app) {
    return new \Framework\Session\Manager($app['config']->get('session'));
});

$app->singleton('request', function ($app) {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return new \Framework\Http\Request($method, $uri, $_GET, $_POST, $_SERVER, $_COOKIE);
});

require FRAMEWORK_PATH . '/routes/web.php';

$app->registerServiceProviders();
$app->bootProviders();

return $app;

function app($service = null) {
    global $app;
    if ($service === null) {
        return $app;
    }
    return $app->get($service);
}

function config($key = null, $default = null) {
    if ($key === null) {
        return app('config');
    }
    return app('config')->get($key, $default);
}

function env($key, $default = null) {
    return app('env')->get($key, $default);
}
