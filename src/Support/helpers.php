<?php

if (!function_exists('view')) {
    function view($view, $data = []) {
        return app('view')->make($view, $data);
    }
}

if (!function_exists('route')) {
    function route($name, $parameters = []) {
        return app('router')->url($name, $parameters);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        return app('csrf')->token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '" />';
    }
}

if (!function_exists('old')) {
    function old($key, $default = null) {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('session')) {
    function session($key = null, $default = null) {
        if (!isset($_SESSION)) {
            session_start();
        }

        if ($key === null) {
            return $_SESSION;
        }

        return $_SESSION[$key] ?? $default;
    }
}

if (!function_exists('auth')) {
    function auth() {
        return app('auth');
    }
}

if (!function_exists('with')) {
    function with($key, $value = null) {
        if (!isset($_SESSION)) {
            session_start();
        }

        if ($value === null) {
            return $_SESSION[$key] ?? null;
        }

        $_SESSION[$key] = $value;
        return $value;
    }
}

if (!function_exists('hash')) {
    function hash($value) {
        return \Framework\Security\Hash::make($value);
    }
}

if (!function_exists('hash_check')) {
    function hash_check($value, $hash) {
        return \Framework\Security\Hash::check($value, $hash);
    }
}

if (!function_exists('escape')) {
    function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('json_response')) {
    function json_response($data, $statusCode = 200) {
        return new \Framework\Http\Response(
            json_encode($data),
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }
}

if (!function_exists('redirect')) {
    function redirect($location, $statusCode = 302) {
        $response = new \Framework\Http\Response('', $statusCode);
        return $response->redirect($location, $statusCode);
    }
}

if (!function_exists('abort')) {
    function abort($code, $message = null) {
        http_response_code($code);
        echo $message ?? "Error $code";
        exit;
    }
}

if (!function_exists('dd')) {
    function dd(...$vars) {
        foreach ($vars as $var) {
            var_dump($var);
        }
        exit;
    }
}

if (!function_exists('log_info')) {
    function log_info($message, $context = []) {
        app('logger')->info($message, $context);
    }
}

if (!function_exists('log_error')) {
    function log_error($message, $context = []) {
        app('logger')->error($message, $context);
    }
}

if (!function_exists('validate')) {
    function validate($data, $rules) {
        $validator = new \Framework\Security\Validator($data, $rules);

        if ($validator->validate()) {
            return true;
        }

        throw new \Framework\Exceptions\ValidationException($validator->errors());
    }
}

if (!function_exists('back')) {
    function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $response = new \Framework\Http\Response('', 302);
        return $response->redirect($referer);
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        return config('app.url') . '/dist/' . $path;
    }
}

if (!function_exists('request')) {
    function request() {
        return app('request') ?? new \Framework\Http\Request();
    }
}

if (!function_exists('db')) {
    function db($connection = null) {
        if ($connection) {
            return app('db')->connection($connection);
        }
        return app('db');
    }
}

if (!function_exists('sql')) {
    function sql($file, $bindings = []) {
        return app('db')->sql()->query($file, $bindings);
    }
}

if (!function_exists('sqlFile')) {
    function sqlFile() {
        return app('db')->sql();
    }
}
