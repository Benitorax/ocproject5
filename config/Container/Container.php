<?php
namespace Config\Container;

use ReflectionMethod;
use Config\Router\Router;

class Container
{
    /** @var object[] $services */
    private array $services = [];

    public function getService(string $className): object
    {
        if ($this->hasService($className)) {
            return $this->services[$className];
        } else {
            return $this->createService($className);
        }
    }

    public function createService(string $className): object
    {
        if ($className === get_class($this)) {
            return $this;
        }

        $arguments = $this->resolveServiceArguments($className);

        if (is_array($arguments)) {
            $service = new $className(...$arguments);
        } else {
            $service = new $className($arguments);
        }

        $this->setService($service);
        
        return $service;
    }

    public function hasService(string $className): bool
    {
        if (isset($this->services[$className])) {
            return true;
        }
        return false;
    }

    public function setService(object $service): void
    {
        $this->services[get_class($service)] = $service;
    }

    public function getRouter(): Router
    {
        $className = Router::class;
        if ($this->hasService($className)) {
            return $this->getService($className);
        }

        return $this->createService($className);
    }

    /**
     * @return object[]
     */
    public function resolveServiceArguments(string $className): ?array
    {
        if (method_exists($className, '__construct')) {
            $reflection = new ReflectionMethod($className, '__construct');
            $arguments = null;
            
            foreach ($reflection->getParameters() as $param) {
                $reflectionType = $param->getType();

                if (method_exists($reflectionType, 'getName')) {
                    $serviceClassName = $reflectionType->getName();
                }

                if (!empty($serviceClassName)) {
                    if ($this->hasService($serviceClassName)) {
                        $arguments[] = $this->getService($serviceClassName);
                    } else {
                        $service = $this->createService($serviceClassName);
                        $arguments[] = $service;
                    }
                }
            }
            
            return $arguments;
        }

        return null;
    }
}
