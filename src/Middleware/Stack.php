<?php

namespace Framework\Middleware;

use Framework\Http\Request;
use Framework\Http\Response;

/**
 * Middleware pipeline/stack
 */
class Stack
{
    /**
     * Middleware stack
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * Final handler
     *
     * @var callable|null
     */
    protected $finalHandler = null;

    /**
     * Add middleware to the stack
     *
     * @param string|callable $middleware
     * @return self
     */
    public function add($middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Set the final handler
     *
     * @param callable $handler
     * @return self
     */
    public function then(callable $handler): self
    {
        $this->finalHandler = $handler;
        return $this;
    }

    /**
     * Execute the middleware stack
     *
     * @param Request $request
     * @return Response
     */
    public function execute(Request $request): Response
    {
        $middlewares = array_reverse($this->middleware);

        $next = $this->finalHandler ?? function ($request) {
            return new Response('No handler defined');
        };

        foreach ($middlewares as $middleware) {
            $next = $this->wrap($middleware, $next);
        }

        return $next($request);
    }

    /**
     * Wrap a middleware
     *
     * @param string|callable $middleware
     * @param callable $next
     * @return callable
     */
    protected function wrap($middleware, callable $next): callable
    {
        return function (Request $request) use ($middleware, $next) {
            if (is_string($middleware)) {
                $middleware = app($middleware);
            }

            if (is_callable($middleware)) {
                return $middleware($request, $next);
            }

            if (method_exists($middleware, 'handle')) {
                return $middleware->handle($request, $next);
            }

            return $next($request);
        };
    }
}
