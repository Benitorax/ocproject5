<?php

namespace Config\Container;

use ReflectionType;
use ReflectionMethod;
use Config\Router\Router;

class Container
{
    /** @var object[] $services */
    private array $services = [];

    public function get(string $className): object
    {
        if ($this->has($className)) {
            return $this->services[$className];
        } else {
            return $this->create($className);
        }
    }

    public function create(string $className): object
    {
        if (get_class($this) === $className) {
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

    public function getRouter(): Router
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
     * @return object[]
     */
    public function resolveArguments(string $className): ?array
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
                        $arguments[] = $this->get($serviceClassName);
                    } else {
                        $service = $this->create($serviceClassName);
                        $arguments[] = $service;
                    }
                }
            }

            return $arguments;
        }

        return null;
    }
}
