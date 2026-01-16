<?php

/**
 * Quill Framework Application Entry Point
 */

// Bootstrap the application
$app = require dirname(__DIR__) . '/bootstrap/app.php';

// Get the request and router
$request = $app->make(\Framework\Http\Request::class);
$router = $app->make(\Framework\Routing\Router::class);

try {
    // Match route
    $route = $router->match($request);

    if (!$route) {
        throw new \Framework\Exception\HttpException(404, 'Route not found');
    }

    // Get action
    $action = $route->getAction();

    // Resolve controller and method
    if (is_array($action)) {
        [$controller, $method] = $action;

        $controllerInstance = $app->make($controller);
        $response = $app->callMethod($controllerInstance, $method, [
            'request' => $request,
            'id' => $route->getParameters($request->path())['id'] ?? null,
        ]);
    } elseif (is_string($action) && strpos($action, '@') !== false) {
        [$controller, $method] = explode('@', $action);

        $controllerInstance = $app->make($controller);
        $response = $app->callMethod($controllerInstance, $method, [
            'request' => $request,
        ]);
    } elseif (is_callable($action)) {
        $response = $action($request);
    } else {
        throw new \Framework\Exception\HttpException(500, 'Invalid action');
    }

    // Ensure we have a response
    if (!($response instanceof \Framework\Http\Response)) {
        $response = response((string)$response);
    }

    // Send response
    $response->send();

} catch (\Framework\Exception\HttpException $e) {
    http_response_code($e->getStatus());
    echo "Error {$e->getStatus()}: " . ($e->getMessage() ?: 'An error occurred');
} catch (\Exception $e) {
    http_response_code(500);
    if (config('app.debug')) {
        echo "<pre>";
        echo "Exception: " . get_class($e) . "\n";
        echo "Message: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "\nStack Trace:\n";
        echo $e->getTraceAsString();
        echo "</pre>";
    } else {
        echo "Error: An unexpected error occurred";
    }
}
