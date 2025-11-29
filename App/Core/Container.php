<?php

namespace App\Core;

use ReflectionClass;
use ReflectionParameter;
use Exception;

class Container
{
    /**
     * Registered bindings
     */
    protected array $bindings = [];

    /**
     * Singleton instances
     */
    protected array $instances = [];

    /**
     * Register a binding
     */
    public function bind(string $abstract, mixed $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Register a singleton
     */
    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null; // Mark as singleton
    }

    /**
     * Resolve a class or interface from the container
     */
    public function make(string $abstract): mixed
    {
        // Check if singleton instance exists
        if (array_key_exists($abstract, $this->instances) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // Get concrete implementation
        $concrete = $this->bindings[$abstract] ?? $abstract;

        // Build the instance
        $instance = $this->build($concrete);

        // Store singleton instance
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build a concrete instance
     */
    protected function build(mixed $concrete): mixed
    {
        // If concrete is a closure, execute it
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }

        // Use reflection to resolve dependencies
        return $this->resolve($concrete);
    }

    /**
     * Resolve a class with its dependencies via reflection
     */
    public function resolve(string $class): mixed
    {
        $reflector = new ReflectionClass($class);

        // Check if class is instantiable
        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$class} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        // No constructor means no dependencies
        if ($constructor === null) {
            return new $class;
        }

        // Get constructor parameters
        $parameters = $constructor->getParameters();

        // Resolve each dependency
        $dependencies = $this->resolveDependencies($parameters);

        // Create instance with dependencies
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve constructor dependencies
     */
    protected function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $this->resolveDependency($parameter);
            $dependencies[] = $dependency;
        }

        return $dependencies;
    }

    /**
     * Resolve a single dependency
     */
    protected function resolveDependency(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        // No type hint, check for default value
        if ($type === null) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new Exception("Cannot resolve parameter {$parameter->getName()}");
        }

        // Handle union types (PHP 8.0+)
        if ($type instanceof \ReflectionUnionType) {
            $types = $type->getTypes();
            $typeName = $types[0]->getName();
        } else {
            $typeName = $type->getName();
        }

        // If it's a built-in type, check for default value
        if ($type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new Exception("Cannot resolve built-in type {$typeName}");
        }

        // Resolve the class from the container
        return $this->make($typeName);
    }

    /**
     * Check if a binding exists
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Get all bindings
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
}
