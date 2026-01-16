<?php

namespace Framework\Routing;

class Router {
    protected $routes = [];
    protected $namedRoutes = [];
    protected $currentGroup = [];

    public function get($path, $action) {
        return $this->addRoute('GET', $path, $action);
    }

    public function post($path, $action) {
        return $this->addRoute('POST', $path, $action);
    }

    public function put($path, $action) {
        return $this->addRoute('PUT', $path, $action);
    }

    public function patch($path, $action) {
        return $this->addRoute('PATCH', $path, $action);
    }

    public function delete($path, $action) {
        return $this->addRoute('DELETE', $path, $action);
    }

    public function any($path, $action) {
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            $this->addRoute($method, $path, $action);
        }
    }

    protected function addRoute($method, $path, $action) {
        $path = $this->applyGroup($path);
        
        $route = new Route($method, $path, $action);

        if (!empty($this->currentGroup)) {
            $route->middleware($this->currentGroup['middleware'] ?? []);
        }

        $this->routes[] = $route;

        if (isset($this->currentGroup['name'])) {
            $route->name($this->currentGroup['name'] . '.' . ($this->currentGroup['name_count'] ?? 0)++);
        }

        return $route;
    }

    public function group($options, $callback) {
        $previousGroup = $this->currentGroup;
        $this->currentGroup = $options;

        if (!isset($this->currentGroup['name_count'])) {
            $this->currentGroup['name_count'] = 0;
        }

        call_user_func($callback, $this);

        $this->currentGroup = $previousGroup;
    }

    protected function applyGroup($path) {
        if (!empty($this->currentGroup) && isset($this->currentGroup['prefix'])) {
            return '/' . trim($this->currentGroup['prefix'], '/') . $path;
        }
        return $path;
    }

    public function match($method, $uri) {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        foreach ($this->routes as $route) {
            if ($route->matches($method, $path)) {
                return $route;
            }
        }

        return null;
    }

    public function url($name, $parameters = []) {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route->buildUrl($parameters);
            }
        }
        return null;
    }

    public function getRoutes() {
        return $this->routes;
    }
}
