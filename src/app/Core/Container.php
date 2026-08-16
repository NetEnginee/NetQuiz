<?php
declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionNamedType;
use RuntimeException;

/**
 * Lightweight, robust PSR-11 compliant Dependency Injection Container.
 * Supports auto-wiring, singleton binding, factory binding, and method invocation.
 */
class Container
{
    private static ?Container $instance = null;

    /**
     * Stored service bindings / factories.
     * @var array<string, array{concrete: mixed, singleton: bool}>
     */
    private array $bindings = [];

    /**
     * Stored singleton instances.
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Get or initialize the global container instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a singleton instance or factory.
     */
    public function singleton(string $abstract, callable|object|string|null $concrete = null): self
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        if (is_object($concrete) && !($concrete instanceof \Closure)) {
            $this->instances[$abstract] = $concrete;
        } else {
            $this->bindings[$abstract] = [
                'concrete' => $concrete,
                'singleton' => true,
            ];
        }

        return $this;
    }

    /**
     * Register a transient binding / factory.
     */
    public function bind(string $abstract, callable|string|null $concrete = null): self
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => false,
        ];

        return $this;
    }

    /**
     * Check if an abstract type or service is registered or can be resolved.
     */
    public function has(string $id): bool
    {
        return isset($this->instances[$id]) || isset($this->bindings[$id]) || class_exists($id);
    }

    /**
     * Resolve a service or class from the container.
     *
     * @template T
     * @param class-string<T>|string $id
     * @return T|object
     * @throws RuntimeException
     */
    public function get(string $id): object
    {
        // 1. Return already resolved singleton instance if exists
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // 2. Check registered bindings
        if (isset($this->bindings[$id])) {
            $binding = $this->bindings[$id];
            $concrete = $binding['concrete'];

            if ($concrete instanceof \Closure) {
                $object = $concrete($this);
            } elseif (is_string($concrete)) {
                $object = $this->build($concrete);
            } else {
                $object = $concrete;
            }

            if ($binding['singleton']) {
                $this->instances[$id] = $object;
            }

            return $object;
        }

        // 3. Fallback to auto-wiring if it is an existing class
        if (class_exists($id)) {
            return $this->build($id);
        }

        throw new RuntimeException("Target [{$id}] cannot be resolved by the Dependency Injection Container.");
    }

    /**
     * Instantiate a concrete class with auto-wired constructor dependencies.
     */
    public function build(string $concrete): object
    {
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new RuntimeException("Class [{$concrete}] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        // If no constructor, instantiate directly
        if ($constructor === null) {
            return new $concrete();
        }

        $dependencies = $this->resolveDependencies($constructor->getParameters());
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Call a method on a target object or class with auto-wired parameter injection.
     */
    public function call(object|string $target, string $method, array $parameters = []): mixed
    {
        $instance = is_string($target) ? $this->get($target) : $target;
        $reflector = new ReflectionMethod($instance, $method);

        $dependencies = $this->resolveDependencies($reflector->getParameters(), $parameters);
        return $reflector->invokeArgs($instance, $dependencies);
    }

    /**
     * Resolve reflection parameters to concrete dependency instances.
     *
     * @param ReflectionParameter[] $parameters
     * @param array<string, mixed> $customParameters
     * @return array<int, mixed>
     */
    private function resolveDependencies(array $parameters, array $customParameters = []): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();

            // 1. Check if parameter is explicitly provided in custom parameters
            if (array_key_exists($paramName, $customParameters)) {
                $dependencies[] = $customParameters[$paramName];
                continue;
            }

            // 2. Check typed parameter
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $className = $type->getName();
                $dependencies[] = $this->get($className);
                continue;
            }

            // 3. Check if parameter has a default value
            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
                continue;
            }

            // 4. Check if parameter is nullable
            if ($parameter->allowsNull()) {
                $dependencies[] = null;
                continue;
            }

            throw new RuntimeException("Cannot resolve parameter [{$paramName}] in [{$parameter->getDeclaringClass()?->getName()}].");
        }

        return $dependencies;
    }
}
