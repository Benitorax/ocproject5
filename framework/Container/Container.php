<?php

namespace Framework\Container;

use ReflectionType;
use ReflectionMethod;
use Framework\Router\Router;

class Container
{
    /**
     * @template T
     * @var T[] $services
     * */
    private array $services = [];

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function get(string $className)
    {
        if ($this->has($className)) {
            /** @var T */
            return $this->services[$className];
        } else {
            return $this->create($className);
        }
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function create(string $className)
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

        $this->set($service);

        return $service;
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return bool
     */
    public function has(string $className): bool
    {
        if (isset($this->services[$className])) {
            return true;
        }
        return false;
    }

    public function set(object $service): void
    {
        $this->services[get_class($service)] = $service;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        $className = Router::class;
        if ($this->has($className)) {
            /** @var Router */
            return $this->get($className);
        }
        /** @var Router */
        return $this->create($className);
    }

    /**
     * Retrieve parameters of the class's constructor, instantiate them and return them inside an array
     * @template T
     * @param class-string<T> $className
     * @return T[]|null
     */
    public function resolveArguments(string $className)
    {
        if (method_exists($className, '__construct')) {
            $reflection = new ReflectionMethod($className, '__construct');
            $arguments = null;

            foreach ($reflection->getParameters() as $param) {
                /** @var ReflectionType */
                $reflectionType = $param->getType();

                if (method_exists($reflectionType, 'getName')) {
                    $serviceClassName = $reflectionType->getName();
                }

                if (!empty($serviceClassName)) {
                    if ($this->has($serviceClassName)) {
                        $arguments[] = $this->get($serviceClassName); // @phpstan-ignore-line
                    } else {
                        $service = $this->create($serviceClassName); // @phpstan-ignore-line
                        $arguments[] = $service;
                    }
                }
            }

            return $arguments;
        }

        return null;
    }
}
