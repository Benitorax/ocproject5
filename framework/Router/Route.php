<?php

namespace Framework\Router;

class Route
{
    private string $name;
    private string $path;
    private array $callable;
    private array $methods;

    /**
     * @param null|string|array $methods
     */
    public function __construct(string $path, string $callable, $methods, string $name)
    {
        $this->path = $path;
        $this->name = $name;
        $this->setCallable($callable);
        $this->setMethods($methods);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCallable(): array
    {
        return $this->callable;
    }

    /**
     * @return string|array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param null|string|array $method
     */
    public function setMethods($method): void
    {
        if (null === $method) {
            $this->methods = ['GET'];

            return;
        } elseif (is_array($method)) {
            $this->methods = $method;

            return;
        }

        $this->methods = [$method];
    }

    public function setCallable(string $callable): void
    {
        [$class, $method] = explode('::', $callable, 2);
        $this->callable = [$class, $method];
    }
}
