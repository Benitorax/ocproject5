<?php

namespace Framework\Container;

use ReflectionMethod;
use ReflectionNamedType;
use Framework\Router\Router;

class Container
{
    /**
     * Array of instanciated services.
     */
    private array $services = [];

    /**
     * Array of services aliases.
     */
    private array $aliases = [];

    public function __construct()
    {
        // loads service aliases
        $config = require dirname(__DIR__, 2) . '\config\services.php';

        foreach ($config['alias'] as $className => $alias) {
            $this->aliases[$className] = $alias;
        }
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function get($className)
    {
        if ($this->has($className)) {
            /** @var T */
            return $this->services[$className];
        }

        // checks if the service className has an alias in config
        if (array_key_exists($className, $this->aliases)) {
            $alias = $className;
            $className = $this->aliases[$className];

            // checks again with alias
            if ($this->has($className)) {
                /** @var T */
                return $this->services[$className];
            }

            return $this->create($className, $alias);
        }

        return $this->create($className);
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @param class-string<T> $alias
     * @return T
     */
    public function create($className, $alias = null)
    {
        if (get_class($this) === $className) {
            /** @var T */
            return $this;
        }

        $arguments = $this->resolveArguments($className);

        if (is_array($arguments)) {
            $service = new $className(...$arguments);
        } else {
            $service = new $className($arguments);
        }

        $this->set($service, $alias);

        return $service;
    }

    /**
     * @template T
     * @param class-string<T> $className
     */
    public function has($className): bool
    {
        if (isset($this->services[$className])) {
            return true;
        }

        return false;
    }

    /**
     * @template T
     * @param class-string<T> $alias
     */
    public function set(object $service, $alias = null): void
    {
        $this->services[get_class($service)] = $service;

        if (null !== $alias) {
            $this->services[$alias] = $service;
        }
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        $className = Router::class;
        if ($this->has($className)) {
            return $this->get($className);
        }

        return $this->create($className);
    }

    /**
     * Retrieve parameters of the class's constructor, instantiate them and return them inside an array
     *
     * @template T
     * @param class-string<T> $className
     * @return T[]|null
     */
    public function resolveArguments($className)
    {
        if (method_exists($className, '__construct')) {
            $reflection = new ReflectionMethod($className, '__construct');
            $arguments = null;

            foreach ($reflection->getParameters() as $param) {
                /** @var ReflectionNamedType */
                $reflectionType = $param->getType();
                /** @var class-string<T> */
                $serviceClassName = $reflectionType->getName();

                if (!empty($serviceClassName)) {
                    $arguments[] = $this->get($serviceClassName);
                }
            }

            return $arguments;
        }

        return null;
    }
}
