<?php

namespace Framework\Container;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionParameter;

class Container implements ArrayAccess {
    protected $bindings = [];
    protected $instances = [];
    protected $resolved = [];

    public function bind($abstract, $concrete = null, $shared = false) {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        if ($concrete instanceof Closure) {
            $this->bindings[$abstract] = compact('concrete', 'shared');
        } else {
            $this->bindings[$abstract] = [
                'concrete' => $concrete,
                'shared' => $shared,
            ];
        }
    }

    public function singleton($abstract, $concrete = null) {
        $this->bind($abstract, $concrete, true);
    }

    public function instance($abstract, $instance) {
        $this->instances[$abstract] = $instance;
    }

    public function make($abstract, $parameters = []) {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            return $this->resolve($abstract, $parameters);
        }

        $binding = $this->bindings[$abstract];
        $concrete = $binding['concrete'];

        if ($concrete instanceof Closure) {
            $instance = call_user_func($concrete, $this, $parameters);
        } else {
            $instance = $this->resolve($concrete, $parameters);
        }

        if ($binding['shared'] ?? false) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    public function resolve($abstract, $parameters = []) {
        try {
            $reflection = new ReflectionClass($abstract);
        } catch (\ReflectionException $e) {
            throw new \Exception("Unable to resolve: $abstract");
        }

        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class is not instantiable: $abstract");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $abstract();
        }

        $params = $this->resolveParameters($constructor->getParameters(), $parameters);

        return $reflection->newInstanceArgs($params);
    }

    protected function resolveParameters($parameters, $givenParams = []) {
        $resolved = [];

        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter->getName(), $givenParams)) {
                $resolved[] = $givenParams[$parameter->getName()];
                continue;
            }

            $type = $parameter->getType();

            if ($type && !$type->isBuiltin()) {
                $className = $type->getName();
                if ($this->has($className)) {
                    $resolved[] = $this->make($className);
                } else {
                    $resolved[] = $this->resolve($className);
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
            }
        }

        return $resolved;
    }

    public function has($abstract) {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    public function get($abstract) {
        return $this->make($abstract);
    }

    public function offsetExists($offset): bool {
        return $this->has($offset);
    }

    public function offsetGet($offset): mixed {
        return $this->make($offset);
    }

    public function offsetSet($offset, $value): void {
        if ($value instanceof Closure) {
            $this->bind($offset, $value);
        } else {
            $this->instance($offset, $value);
        }
    }

    public function offsetUnset($offset): void {
        unset($this->bindings[$offset], $this->instances[$offset]);
    }
}
