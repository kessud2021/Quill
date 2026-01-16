<?php

namespace Framework\Routing;

class UrlGenerator {
    protected $router;
    protected $baseUrl;

    public function __construct(Router $router, $baseUrl = null) {
        $this->router = $router;
        $this->baseUrl = $baseUrl ?? config('app.url', 'http://localhost:8000');
    }

    public function to($path) {
        return $this->baseUrl . $path;
    }

    public function route($name, $parameters = []) {
        $url = $this->router->url($name, $parameters);

        if (!$url) {
            throw new \Exception("Route [$name] not found");
        }

        return $this->to($url);
    }

    public function current() {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    public function previous($fallback = null) {
        return $_SERVER['HTTP_REFERER'] ?? $fallback;
    }

    public function full() {
        return $this->baseUrl . $this->current();
    }

    public function asset($path) {
        return $this->baseUrl . '/dist/' . $path;
    }
}
