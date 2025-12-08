<?php

namespace App\Core;

use ReflectionClass;
use ReflectionParameter;
use ReflectionUnionType;
use Closure;
use Exception;

class Container
{
    /**
     * Registered bindings
     *
     * @var array<string, mixed>
     */
    protected array $bindings = [];

    /**
     * Singleton instances
     *
     * @var array<string, mixed>
     */
    protected array $instances = [];

    /**
     * Aliases map
     *
     * @var array<string, string>
     */
    protected array $aliases = [];

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
     * Register a binding only if it has not been registered.
     */
    public function bindIf(string $abstract, mixed $concrete = null): void
    {
        if (!$this->has($abstract)) {
            $this->bind($abstract, $concrete);
        }
    }

    /**
     * Register a singleton
     */
    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null; // mark as singleton
    }

    /**
     * Register an existing instance
     */
    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Register an alias
     */
    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    /**
     * Resolve alias to real abstract
     */
    protected function getAbstract(string $abstract): string
    {
        return $this->aliases[$abstract] ?? $abstract;
    }

    /**
     * Resolve a class or interface from the container
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        $abstract = $this->getAbstract($abstract);

        // Return existing singleton instance
        if (array_key_exists($abstract, $this->instances) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // Get concrete implementation (binding or same as abstract)
        $concrete = $this->bindings[$abstract] ?? $abstract;

        // Build the instance
        $instance = $this->build($concrete, $parameters);

        // Store singleton instance
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build a concrete instance
     */
    protected function build(mixed $concrete, array $parameters = []): mixed
    {
        // If concrete is a closure, execute it (can receive container & params)
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        // If it's a class string, resolve via reflection
        if (is_string($concrete)) {
            return $this->resolve($concrete, $parameters);
        }

        // If it's already an object, just return it
        if (is_object($concrete)) {
            return $concrete;
        }

        throw new Exception("Unable to build [" . print_r($concrete, true) . "]");
    }

    /**
     * Resolve a class with its dependencies via reflection
     */
    public function resolve(string $class, array $parameters = []): mixed
    {
        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$class} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class;
        }

        $params = $constructor->getParameters();
        $deps = $this->resolveDependencies($params, $parameters);

        return $reflector->newInstanceArgs($deps);
    }

    /**
     * Resolve constructor dependencies
     */
    protected function resolveDependencies(array $parameters, array $overrides = []): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();

            // Parameter override by name (for scalars, config, etc.)
            if (array_key_exists($name, $overrides)) {
                $dependencies[] = $overrides[$name];
                continue;
            }

            $dependencies[] = $this->resolveDependency($parameter);
        }

        return $dependencies;
    }

    /**
     * Resolve a single dependency
     */
    protected function resolveDependency(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        // No type hint
        if ($type === null) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new Exception("Cannot resolve parameter \${$parameter->getName()} (no type and no default)");
        }

        // Union type: pick first non-builtin
        if ($type instanceof ReflectionUnionType) {
            $chosen = null;
            foreach ($type->getTypes() as $t) {
                if (!$t->isBuiltin()) {
                    $chosen = $t;
                    break;
                }
            }

            if ($chosen === null) {
                // all builtin
                if ($parameter->isDefaultValueAvailable()) {
                    return $parameter->getDefaultValue();
                }
                throw new Exception("Cannot resolve union type for \${$parameter->getName()}");
            }

            $typeName = $chosen->getName();
            return $this->make($typeName);
        }

        $typeName = $type->getName();

        // Built-in type (int, string, array, bool, etc.)
        if ($type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new Exception("Cannot resolve built-in type {$typeName} for \${$parameter->getName()}");
        }

        // Class/interface: resolve from container
        return $this->make($typeName);
    }

    /**
     * Check if a binding or instance exists
     */
    public function has(string $abstract): bool
    {
        $abstract = $this->getAbstract($abstract);

        return isset($this->bindings[$abstract]) || array_key_exists($abstract, $this->instances);
    }

    /**
     * Get all bindings
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
}
