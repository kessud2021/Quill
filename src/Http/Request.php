<?php

namespace Framework\Http;

/**
 * HTTP Request object
 * 
 * Encapsulates the current HTTP request with input handling and validation
 */
class Request
{
    /**
     * HTTP method
     *
     * @var string
     */
    protected string $method;

    /**
     * Request URI
     *
     * @var string
     */
    protected string $uri;

    /**
     * Request path
     *
     * @var string
     */
    protected string $path;

    /**
     * Query parameters
     *
     * @var array
     */
    protected array $query;

    /**
     * Post/Form data
     *
     * @var array
     */
    protected array $post;

    /**
     * Files
     *
     * @var array
     */
    protected array $files;

    /**
     * Server data
     *
     * @var array
     */
    protected array $server;

    /**
     * Headers
     *
     * @var array
     */
    protected array $headers;

    /**
     * Create a new request instance
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->query = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        $this->parseUri();
        $this->parseHeaders();
    }

    /**
     * Parse the URI and path
     *
     * @return void
     */
    protected function parseUri(): void
    {
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (strpos($this->uri, '?') !== false) {
            $this->path = substr($this->uri, 0, strpos($this->uri, '?'));
        } else {
            $this->path = $this->uri;
        }

        // Remove leading slash if present
        if ($this->path !== '/' && str_starts_with($this->path, '/')) {
            $this->path = substr($this->path, 1);
        }
    }

    /**
     * Parse headers from server
     *
     * @return void
     */
    protected function parseHeaders(): void
    {
        $this->headers = [];

        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = substr($key, 5);
                $header = str_replace('_', '-', strtolower($header));
                $this->headers[$header] = $value;
            }
        }
    }

    /**
     * Get the HTTP method
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Check if request method is GET
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Check if request method is POST
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Check if request is AJAX
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return strtolower($this->header('x-requested-with')) === 'xmlhttprequest';
    }

    /**
     * Get the request path
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path ?: '/';
    }

    /**
     * Get the full URI
     *
     * @return string
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * Get an input value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * Get all input data
     *
     * @return array
     */
    public function all(): array
    {
        return array_merge($this->query, $this->post);
    }

    /**
     * Get query parameters
     *
     * @return array
     */
    public function query(): array
    {
        return $this->query;
    }

    /**
     * Get post data
     *
     * @return array
     */
    public function post(): array
    {
        return $this->post;
    }

    /**
     * Get files
     *
     * @return array
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * Get a file
     *
     * @param string $name
     * @return array|null
     */
    public function file(string $name): ?array
    {
        return $this->files[$name] ?? null;
    }

    /**
     * Get a header
     *
     * @param string $name
     * @param string|null $default
     * @return string|null
     */
    public function header(string $name, ?string $default = null): ?string
    {
        return $this->headers[strtolower($name)] ?? $default;
    }

    /**
     * Get all headers
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Check if input key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->post[$key]) || isset($this->query[$key]);
    }

    /**
     * Get input as array
     *
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $result[$key] = $this->input($key);
            }
        }
        return $result;
    }

    /**
     * Get IP address
     *
     * @return string
     */
    public function ip(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        return trim($ip);
    }

    /**
     * Get user agent
     *
     * @return string
     */
    public function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}
