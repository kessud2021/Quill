<?php

namespace Framework\Http;

class Request {
    protected $method;
    protected $uri;
    protected $query;
    protected $post;
    protected $server;
    protected $cookies;

    public function __construct($method = 'GET', $uri = '/', $query = [], $post = [], $server = [], $cookies = []) {
        $this->method = $method;
        $this->uri = $uri;
        $this->query = $query;
        $this->post = $post;
        $this->server = $server;
        $this->cookies = $cookies;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getUri() {
        return $this->uri;
    }

    public function getPath() {
        $path = parse_url($this->uri, PHP_URL_PATH);
        return $path ?: '/';
    }

    public function getQuery($key = null, $default = null) {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function getPost($key = null, $default = null) {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function input($key, $default = null) {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function getCookie($key = null, $default = null) {
        if ($key === null) {
            return $this->cookies;
        }
        return $this->cookies[$key] ?? $default;
    }

    public function getHeader($name) {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? null;
    }

    public function isJson() {
        return strpos($this->getHeader('Content-Type') ?? '', 'application/json') !== false;
    }

    public function json() {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function isAjax() {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    public function ip() {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function all() {
        return array_merge($this->query, $this->post);
    }

    public function only($keys) {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->input($key);
        }
        return $result;
    }

    public function except($keys) {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    public function has($key) {
        return $this->input($key) !== null;
    }

    public function filled($key) {
        return $this->has($key) && !empty($this->input($key));
    }
}
