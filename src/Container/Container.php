<?php

namespace Framework\Container;

use Closure;
use ReflectionClass;
use ReflectionParameter;

/**
 * Service Container for dependency injection
 * 
 * Manages service bindings, singletons, and automatic resolution
 */
class Container
{
    /**
     * The registered bindings in the container
     *
     * @var array
     */
    protected array $bindings = [];

    /**
     * The registered singletons in the container
     *
     * @var array
     */
    protected array $instances = [];

    /**
     * The resolved instances cache
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * Bind a service to the container
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @return void
     */
    public function bind(string $abstract, $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        if (!$concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => false,
        ];
    }

    /**
     * Bind a singleton to the container
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @return void
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete);
        $this->bindings[$abstract]['singleton'] = true;
    }

    /**
     * Register an instance as singleton
     *
     * @param string $abstract
     * @param mixed $instance
     * @return void
     */
    public function instance(string $abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve a service from the container
     *
     * @param string $abstract
     * @return mixed
     */
    public function make(string $abstract)
    {
        // Check if already instantiated as singleton
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Check if binding exists
        if (!isset($this->bindings[$abstract])) {
            // Try to auto-resolve class
            return $this->resolve($abstract);
        }

        $binding = $this->bindings[$abstract];
        $concrete = $binding['concrete'];

        // Call the binding's concrete
        $instance = $concrete($this);

        // If singleton, cache the instance
        if ($binding['singleton']) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Get a closure for a binding
     *
     * @param string $abstract
     * @param string|Closure $concrete
     * @return Closure
     */
    protected function getClosure(string $abstract, $concrete): Closure
    {
        return function ($container) use ($abstract, $concrete) {
            if ($concrete === $abstract) {
                return $container->resolve($abstract);
            }
            return $container->make($concrete);
        };
    }

    /**
     * Resolve a class instance
     *
     * @param string $class
     * @return mixed
     */
    public function resolve(string $class)
    {
        try {
            $reflection = new ReflectionClass($class);

            if (!$reflection->isInstantiable()) {
                throw new \RuntimeException("Class {$class} cannot be instantiated");
            }

            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                return new $class();
            }

            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $dependency = $parameter->getType();

                if ($dependency === null) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $dependencies[] = $parameter->getDefaultValue();
                    } else {
                        throw new \RuntimeException("Cannot resolve parameter {$parameter->getName()} in {$class}");
                    }
                } else {
                    $dependencyName = $dependency->getName();
                    $dependencies[] = $this->make($dependencyName);
                }
            }

            return new $class(...$dependencies);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException("Cannot resolve class {$class}: " . $e->getMessage());
        }
    }

    /**
     * Check if a binding exists
     *
     * @param string $abstract
     * @return bool
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Get all bindings
     *
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Call a function with dependency injection
     *
     * @param callable $callback
     * @param array $parameters
     * @return mixed
     */
    public function call(callable $callback, array $parameters = [])
    {
        if (is_array($callback)) {
            [$instance, $method] = $callback;
            return $this->callMethod($instance, $method, $parameters);
        }

        return $callback($this, ...$parameters);
    }

    /**
     * Call a method with dependency injection
     *
     * @param object $instance
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function callMethod($instance, string $method, array $parameters = [])
    {
        $reflection = new ReflectionClass($instance);
        $reflectionMethod = $reflection->getMethod($method);

        $methodParameters = $reflectionMethod->getParameters();
        $dependencies = [];

        foreach ($methodParameters as $parameter) {
            if (isset($parameters[$parameter->getName()])) {
                $dependencies[] = $parameters[$parameter->getName()];
            } else {
                $dependency = $parameter->getType();
                if ($dependency !== null) {
                    $dependencyName = $dependency->getName();
                    $dependencies[] = $this->make($dependencyName);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                }
            }
        }

        return $reflectionMethod->invokeArgs($instance, $dependencies);
    }
}
