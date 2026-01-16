<?php

namespace Framework\Routing;

/**
 * Route definition
 */
class Route
{
    /**
     * HTTP methods
     *
     * @var array
     */
    protected array $methods;

    /**
     * Route path
     *
     * @var string
     */
    protected string $path;

    /**
     * Route action
     *
     * @var callable|string|array
     */
    protected $action;

    /**
     * Route name
     *
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * Middleware
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * Route parameters from URI
     *
     * @var array
     */
    protected array $parameters = [];

    /**
     * Create a new route
     *
     * @param array $methods
     * @param string $path
     * @param callable|string|array $action
     */
    public function __construct(array $methods, string $path, $action)
    {
        $this->methods = array_map('strtoupper', $methods);
        $this->path = $path;
        $this->action = $action;
    }

    /**
     * Set the route name
     *
     * @param string $name
     * @return self
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the route name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Add middleware to route
     *
     * @param string|array $middleware
     * @return self
     */
    public function middleware($middleware): self
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        $this->middleware = array_merge($this->middleware, $middlewares);
        return $this;
    }

    /**
     * Get middleware
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Get the action
     *
     * @return callable|string|array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get HTTP methods
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Check if route matches URI and method
     *
     * @param string $uri
     * @param string $method
     * @return bool
     */
    public function matches(string $uri, string $method): bool
    {
        if (!in_array($method, $this->methods)) {
            return false;
        }

        $pattern = $this->compilePattern($this->path);
        return (bool)preg_match($pattern, $uri, $matches);
    }

    /**
     * Get parameters from URI
     *
     * @param string $uri
     * @return array
     */
    public function getParameters(string $uri): array
    {
        $pattern = $this->compilePattern($this->path);
        
        if (!preg_match($pattern, $uri, $matches)) {
            return [];
        }

        // Remove full match
        array_shift($matches);

        // Extract parameter names from path
        $paramNames = [];
        if (preg_match_all('/\{([a-z_]+)\}/', $this->path, $paramMatches)) {
            $paramNames = $paramMatches[1];
        }

        // Combine parameter names with values
        $parameters = [];
        foreach ($paramNames as $index => $name) {
            $parameters[$name] = $matches[$index] ?? null;
        }

        return $parameters;
    }

    /**
     * Compile route path to regex pattern
     *
     * @param string $path
     * @return string
     */
    protected function compilePattern(string $path): string
    {
        // Escape special regex characters except braces
        $escaped = preg_replace('/[.+?^${}()|[\]\\\\]/', '\\$0', $path);

        // Replace {param} with regex capture group
        $pattern = preg_replace('/\{([a-z_]+)\}/', '([a-z0-9_-]+)', $escaped);

        return '#^' . $pattern . '$#i';
    }
}
