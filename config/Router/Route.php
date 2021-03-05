<?php
namespace Config\Router;

class Route
{
    private string $name;
    private string $path;
    private array $callable;
    private array $methods;

    public function __construct(string $path, string $callable, $methods, $name = null)
    {
        $this->path = $path;
        $this->name = $name;
        $this->setCallable($callable);
        $this->setMethods($methods);
    }

    public function getName()
    {
        return $this->name;
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

    /**
     * @param string|array $method
     */
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