<?php

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoload
require BASE_PATH . '/vendor/autoload.php';

// Create container
$container = new \Framework\Container\Container();

// Store in global
$GLOBALS['__app'] = $container;

// Load environment variables
$dotenv = new \Framework\Env\DotEnv(BASE_PATH . '/.env');
$dotenv->load();

// Load configuration
$config = new \Framework\Config\Config();

$appConfig = \Framework\Config\Config::load(BASE_PATH . '/config/app.php');
$databaseConfig = \Framework\Config\Config::load(BASE_PATH . '/config/database.php');
$authConfig = \Framework\Config\Config::load(BASE_PATH . '/config/auth.php');

// Merge all configs
$allConfig = [
    'app' => $appConfig,
    'database' => $databaseConfig,
    'auth' => $authConfig,
];

$config = new \Framework\Config\Config($allConfig);

// Register services
$container->singleton(\Framework\Config\Config::class, $config);

// Database
$container->singleton(\Framework\Database\Manager::class, function ($app) {
    return new \Framework\Database\Manager($app[\Framework\Config\Config::class]);
});

// HTTP
$container->singleton(\Framework\Http\Request::class, function () {
    return new \Framework\Http\Request();
});

// Routing
$container->singleton(\Framework\Routing\Router::class, function () {
    return new \Framework\Routing\Router();
});

// View
$container->singleton(\Framework\View\View::class, function () {
    return new \Framework\View\View();
});

// Session
$container->singleton(\Framework\Session\Manager::class, function () {
    return new \Framework\Session\Manager();
});

// Auth
$container->singleton(\Framework\Auth\AuthManager::class, function ($app) {
    return new \Framework\Auth\AuthManager();
});

// Logging
$container->singleton(\Framework\Logging\Logger::class, function () {
    return new \Framework\Logging\Logger();
});

// Security
$container->singleton(\Framework\Security\Csrf::class, function () {
    return new \Framework\Security\Csrf();
});

// Load routes
require BASE_PATH . '/routes/web.php';

return $container;
