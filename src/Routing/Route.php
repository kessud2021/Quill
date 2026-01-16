<?php

namespace Framework\Routing;

class Route {
    protected $method;
    protected $path;
    protected $action;
    protected $controller;
    protected $controllerMethod;
    protected $parameters = [];
    protected $middleware = [];
    protected $name;
    protected $pattern = '/\{(\w+)\}/';

    public function __construct($method, $path, $action) {
        $this->method = $method;
        $this->path = $path;
        
        if (is_string($action) && strpos($action, '@') !== false) {
            list($this->controller, $this->controllerMethod) = explode('@', $action);
            $this->action = $action;
        } else {
            $this->action = $action;
        }
    }

    public function matches($method, $path) {
        if ($method !== $this->method) {
            return false;
        }

        return $this->pathMatches($path);
    }

    protected function pathMatches($path) {
        $pattern = preg_replace($this->pattern, '([^/]+)', $this->path);
        $pattern = '^' . $pattern . '$';
        
        if (!preg_match('/' . $pattern . '/', $path, $matches)) {
            return false;
        }

        if (preg_match_all($this->pattern, $this->path, $paramNames)) {
            $paramNames = $paramNames[1];
            foreach ($paramNames as $i => $name) {
                $this->parameters[$name] = $matches[$i + 1];
            }
        }

        return true;
    }

    public function getController() {
        return $this->controller ?: $this->action;
    }

    public function getMethod() {
        return $this->controllerMethod ?? 'handle';
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function middleware($middleware) {
        if (is_string($middleware)) {
            $this->middleware[] = $middleware;
        } else {
            $this->middleware = array_merge($this->middleware, (array)$middleware);
        }
        return $this;
    }

    public function getMiddleware() {
        return $this->middleware;
    }

    public function name($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function buildUrl($parameters = []) {
        $url = $this->path;

        foreach ($parameters as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        return $url;
    }

    public function getPath() {
        return $this->path;
    }
}
