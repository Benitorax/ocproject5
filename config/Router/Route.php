<?php
namespace Config\Router;

class Route
{
    private string $path;
    private array $callable;
    private array $methods;

    public function __construct(string $path, string $callable, $methods)
    {
        $this->path = $path;
        $this->setCallable($callable);
        $this->setMethods($methods);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCallable()
    {
        return $this->callable;
    }
    
    public function getMethods()
    {
        return $this->methods;
    }

    public function setMethods($method)
    {
        if(!$method) {
            $this->methods = ['GET'];
        } elseif(!is_array($method)) {
            $this->methods = [$method];
        } else {
            $this->methods = $method;
        }
    }

    public function setCallable(string $callable)
    {
        [$class, $method] = explode('::', $callable, 2);
        $this->callable = [$class, $method];
    }
}