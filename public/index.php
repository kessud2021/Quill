<?php

define('FRAMEWORK_START', microtime(true));

require __DIR__ . '/../bootstrap/app.php';

$app = app();
$response = $app->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'] ?? '/', $_GET, $_POST, $_SERVER, $_COOKIE);

foreach ($response->getHeaders() as $name => $value) {
    header("$name: $value");
}

http_response_code($response->getStatusCode());
echo $response->getBody();
