<?php

namespace Framework\Middleware;

abstract class Middleware {
    public abstract function handle($request, $next);
}
