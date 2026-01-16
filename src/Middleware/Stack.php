<?php

namespace Framework\Middleware;

class Stack {
    protected $middleware = [];

    public function push($middleware) {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function resolve($request, $next) {
        if (empty($this->middleware)) {
            return $next($request);
        }

        $this->middleware = array_reverse($this->middleware);

        $pipeline = function ($request) use ($next) {
            return $next($request);
        };

        foreach ($this->middleware as $middleware) {
            $pipeline = $this->createMiddlewarePipeline($middleware, $pipeline);
        }

        return $pipeline($request);
    }

    protected function createMiddlewarePipeline($middleware, $next) {
        return function ($request) use ($middleware, $next) {
            if (is_string($middleware)) {
                $middleware = app($middleware);
            }

            if (method_exists($middleware, 'handle')) {
                return $middleware->handle($request, $next);
            }

            return $next($request);
        };
    }
}
