<?php

namespace Framework\Routing;

use Framework\Http\Request;
use Framework\Http\Response;

/**
 * Router for handling HTTP requests and routing
 */
class Router
{
    /**
     * All registered routes
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * Named routes
     *
     * @var array
     */
    protected array $namedRoutes = [];

    /**
     * Current route group prefix
     *
     * @var string
     */
    protected string $groupPrefix = '';

    /**
     * Current route group middleware
     *
     * @var array
     */
    protected array $groupMiddleware = [];

    /**
     * Register a GET route
     *
     * @param string $path
     * @param callable|string|array $action
     * @return Route
     */
    public function get(string $path, $action): Route
    {
        return $this->addRoute(['GET', 'HEAD'], $path, $action);
    }

    /**
     * Register a POST route
     *
     * @param string $path
     * @param callable|string|array $action
     * @return Route
     */
    public function post(string $path, $action): Route
    {
        return $this->addRoute(['POST'], $path, $action);
    }

    /**
     * Register a PUT route
     *
     * @param string $path
     * @param callable|string|array $action
     * @return Route
     */
    public function put(string $path, $action): Route
    {
        return $this->addRoute(['PUT'], $path, $action);
    }

    /**
     * Register a PATCH route
     *
     * @param string $path
     * @param callable|string|array $action
     * @return Route
     */
    public function patch(string $path, $action): Route
    {
        return $this->addRoute(['PATCH'], $path, $action);
    }

    /**
     * Register a DELETE route
     *
     * @param string $path
     * @param callable|string|array $action
     * @return Route
     */
    public function delete(string $path, $action): Route
    {
        return $this->addRoute(['DELETE'], $path, $action);
    }

    /**
     * Register a route for all HTTP methods
     *
     * @param string $path
     * @param callable|string|array $action
     * @return Route
     */
    public function any(string $path, $action): Route
    {
        return $this->addRoute(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'], $path, $action);
    }

    /**
     * Register a resource route
     *
     * @param string $name
     * @param string $controller
     * @return void
     */
    public function resource(string $name, string $controller): void
    {
        $this->get($name, [$controller, 'index'])->name("{$name}.index");
        $this->get("{$name}/create", [$controller, 'create'])->name("{$name}.create");
        $this->post($name, [$controller, 'store'])->name("{$name}.store");
        $this->get("{$name}/{id}", [$controller, 'show'])->name("{$name}.show");
        $this->get("{$name}/{id}/edit", [$controller, 'edit'])->name("{$name}.edit");
        $this->put("{$name}/{id}", [$controller, 'update'])->name("{$name}.update");
        $this->delete("{$name}/{id}", [$controller, 'destroy'])->name("{$name}.destroy");
    }

    /**
     * Create a route group
     *
     * @param array $attributes
     * @param callable $callback
     * @return void
     */
    public function group(array $attributes, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix = $previousPrefix . ($attributes['prefix'] ?? '');
        $this->groupMiddleware = array_merge($previousMiddleware, $attributes['middleware'] ?? []);

        call_user_func($callback, $this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    /**
     * Add a route
     *
     * @param array $methods
     * @param string $path
     * @param callable|string|array $action
     * @return Route
     */
    protected function addRoute(array $methods, string $path, $action): Route
    {
        $path = $this->groupPrefix . $path;
        $route = new Route($methods, $path, $action);

        if (!empty($this->groupMiddleware)) {
            $route->middleware($this->groupMiddleware);
        }

        $this->routes[] = $route;
        return $route;
    }

    /**
     * Match a request to a route
     *
     * @param Request $request
     * @return Route|null
     */
    public function match(Request $request): ?Route
    {
        $path = $request->path();
        $method = $request->method();

        foreach ($this->routes as $route) {
            if ($route->matches($path, $method)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Get the URL for a named route
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    public function url(string $name, array $parameters = []): string
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                $path = $route->getPath();

                foreach ($parameters as $param => $value) {
                    $path = str_replace('{' . $param . '}', $value, $path);
                }

                return '/' . ltrim($path, '/');
            }
        }

        return '/';
    }

    /**
     * Get all routes
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
