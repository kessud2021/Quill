<?php

use Framework\Container\Container;
use Framework\Config\Config;

if (!function_exists('app')) {
    /**
     * Get the application container instance or resolve a service
     *
     * @param string|null $abstract
     * @return Container|mixed
     */
    function app(?string $abstract = null)
    {
        global $__app;

        if ($abstract === null) {
            return $__app;
        }

        return $__app->make($abstract);
    }
}

if (!function_exists('config')) {
    /**
     * Get a configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        return app(Config::class)->get($key, $default);
    }
}

if (!function_exists('env')) {
    /**
     * Get an environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('db')) {
    /**
     * Get the database manager
     *
     * @return \Framework\Database\Manager
     */
    function db()
    {
        return app(\Framework\Database\Manager::class);
    }
}

if (!function_exists('view')) {
    /**
     * Create a new view instance
     *
     * @param string $name
     * @param array $data
     * @return \Framework\View\View
     */
    function view(string $name, array $data = [])
    {
        return app(\Framework\View\View::class)->create($name, $data);
    }
}

if (!function_exists('response')) {
    /**
     * Create a new response instance
     *
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return \Framework\Http\Response
     */
    function response($content = '', int $status = 200, array $headers = [])
    {
        return new \Framework\Http\Response($content, $status, $headers);
    }
}

if (!function_exists('json_response')) {
    /**
     * Create a new JSON response
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return \Framework\Http\JsonResponse
     */
    function json_response($data = [], int $status = 200, array $headers = [])
    {
        return new \Framework\Http\JsonResponse($data, $status, $headers);
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect response
     *
     * @param string $url
     * @param int $status
     * @return \Framework\Http\Response
     */
    function redirect(string $url, int $status = 302)
    {
        $response = response('', $status, ['Location' => $url]);
        return $response;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back
     *
     * @return \Framework\Http\Response
     */
    function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return redirect($referer);
    }
}

if (!function_exists('route')) {
    /**
     * Get the URL for a named route
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    function route(string $name, array $parameters = [])
    {
        return app(\Framework\Routing\Router::class)->url($name, $parameters);
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL
     *
     * @param string $path
     * @return string
     */
    function url(string $path = '')
    {
        $base = config('app.url', 'http://localhost:8000');
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset URL
     *
     * @param string $path
     * @return string
     */
    function asset(string $path)
    {
        return url('public/' . ltrim($path, '/'));
    }
}

if (!function_exists('auth')) {
    /**
     * Get the auth manager
     *
     * @return \Framework\Auth\AuthManager
     */
    function auth()
    {
        return app(\Framework\Auth\AuthManager::class);
    }
}

if (!function_exists('request')) {
    /**
     * Get the current request
     *
     * @return \Framework\Http\Request
     */
    function request()
    {
        return app(\Framework\Http\Request::class);
    }
}

if (!function_exists('session')) {
    /**
     * Get the session manager
     *
     * @return \Framework\Session\Manager
     */
    function session()
    {
        return app(\Framework\Session\Manager::class);
    }
}

if (!function_exists('logger')) {
    /**
     * Get the logger
     *
     * @return \Framework\Logging\Logger
     */
    function logger()
    {
        return app(\Framework\Logging\Logger::class);
    }
}

if (!function_exists('abort')) {
    /**
     * Abort with an HTTP status code
     *
     * @param int $status
     * @param string $message
     * @return void
     */
    function abort(int $status, string $message = '')
    {
        throw new \Framework\Exception\HttpException($status, $message);
    }
}

if (!function_exists('abort_if')) {
    /**
     * Abort if a condition is true
     *
     * @param bool $condition
     * @param int $status
     * @param string $message
     * @return void
     */
    function abort_if(bool $condition, int $status, string $message = '')
    {
        if ($condition) {
            abort($status, $message);
        }
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die
     *
     * @param mixed ...$vars
     * @return void
     */
    function dd(...$vars)
    {
        echo '<pre>';
        var_dump(...$vars);
        echo '</pre>';
        exit;
    }
}

if (!function_exists('dump')) {
    /**
     * Dump a variable
     *
     * @param mixed ...$vars
     * @return void
     */
    function dump(...$vars)
    {
        echo '<pre>';
        var_dump(...$vars);
        echo '</pre>';
    }
}

if (!function_exists('hash_password')) {
    /**
     * Hash a password
     *
     * @param string $password
     * @return string
     */
    function hash_password(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}

if (!function_exists('verify_password')) {
    /**
     * Verify a password
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    function verify_password(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get or create a CSRF token
     *
     * @return string
     */
    function csrf_token(): string
    {
        return app(\Framework\Security\Csrf::class)->getToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field
     *
     * @return string
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('old')) {
    /**
     * Get an old input value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old(string $key, $default = null)
    {
        return session()->get('old_input.' . $key, $default);
    }
}

if (!function_exists('now')) {
    /**
     * Get the current timestamp
     *
     * @return int
     */
    function now(): int
    {
        return time();
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the base path
     *
     * @param string $path
     * @return string
     */
    function base_path(string $path = ''): string
    {
        return defined('BASE_PATH') ? BASE_PATH . ($path ? '/' . $path : '') : $path;
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the app path
     *
     * @param string $path
     * @return string
     */
    function app_path(string $path = ''): string
    {
        return base_path('app' . ($path ? '/' . $path : ''));
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the storage path
     *
     * @param string $path
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path ? '/' . $path : ''));
    }
}

if (!function_exists('resources_path')) {
    /**
     * Get the resources path
     *
     * @param string $path
     * @return string
     */
    function resources_path(string $path = ''): string
    {
        return base_path('resources' . ($path ? '/' . $path : ''));
    }
}

if (!function_exists('database_path')) {
    /**
     * Get the database path
     *
     * @param string $path
     * @return string
     */
    function database_path(string $path = ''): string
    {
        return base_path('database' . ($path ? '/' . $path : ''));
    }
}

if (!function_exists('class_basename')) {
    /**
     * Get the class basename
     *
     * @param object|string $class
     * @return string
     */
    function class_basename($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('str_slug')) {
    /**
     * Convert a string to a slug
     *
     * @param string $string
     * @param string $separator
     * @return string
     */
    function str_slug(string $string, string $separator = '-'): string
    {
        $string = mb_strtolower($string);
        $string = preg_replace('/[^a-z0-9]+/i', $separator, $string);
        return trim($string, $separator);
    }
}

if (!function_exists('str_contains')) {
    /**
     * Check if a string contains a substring (PHP 8.0 compatibility)
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('collect')) {
    /**
     * Create a collection from an array
     *
     * @param array $items
     * @return \Framework\Support\Collection
     */
    function collect(array $items = [])
    {
        return new \Framework\Support\Collection($items);
    }
}
