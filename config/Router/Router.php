<?php
namespace Config\Router;

use Exception;
use Config\Router\Routes;
use App\Controller\ErrorController;
use Config\Container\Container;

class Router
{
    private $errorController;
    private $request;
    private $routes;
    private $container;

    public function __construct(Routes $routes, ErrorController $errorController, Container $container)
    {
        $this->errorController = $errorController;
        $this->routes = $routes->getRoutes();
        $this->container = $container;
    }

    public function run(Request $request)
    {
        $this->request = $request;
        
        try{
            $this->match($this->request->getRequestUri());
        }
        catch (Exception $e)
        {
            $this->errorController->errorServer($e);
        }
    }

    public function match($requestUri)
    {
        $route = $this->matchRoute($requestUri);

        if(!$route) {
            $this->errorController->errorNotFound();
        } else {
            $isMethodValid = $this->matchMethod($this->request->getMethod(), $route->getMethods());
            if(!$isMethodValid) {
                $this->errorController->errorNotFound();
            } else {
                $arguments = $this->resolveControllerArguments($route->getCallable(), $route->getPath(), $requestUri);
                $this->executeController($route->getCallable(), $arguments);
            }
        }

    }

    public function matchRoute($requestUri)
    {
        foreach($this->routes as $route) {
            $routePath = $route->getPath();
            $pattern = preg_replace('#\{\w+\}#', '[\w\-]+', $routePath);

            if(preg_match('#^'.$pattern.'$#', $requestUri, $matches)) {

                return $route;
            } 
        }

        return false;
    }

    public function matchMethod($method, $requiredMethods)
    {
        if(in_array($method, $requiredMethods)) {
            return true;
        } else {
            return false;
        }
    }

    public function executeController($callable, $arguments)
    {
        [$classname, $method] = $callable;
        $object = $this->container->createService($classname);
        $object->setRequest($this->request);
        $controller = [$object, $method];

        if(is_callable($controller)) {
            $controller(...$arguments);
        } else {
            $this->errorController->errorNotFound();
        }
    }

    public function resolveControllerArguments($callable, $routePath, $requestUri)
    {
        $pathElements = explode('/', $routePath);
        $uriElements = explode('/', $requestUri);

        foreach($pathElements as $key => $element) {
            if(preg_match('#\{\w+\}#', $pathElements[$key], $matches0)) {
                $paramName = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$2', $element);
                $start = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$1', $element);
                $end = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$3', $element);
                preg_match('#^'.$start.'([-\w]+)'.$end.'$#', $uriElements[$key], $matches);

                if(count($matches)) {
                    $value = $matches[1];
                }

                $routeParams[$paramName] = $value;
                $this->request->getAttributes()->set($paramName, $value);
            }            
        }
        if(isset($routeParams)) {
            $this->request->getAttributes()->set('route_params', $routeParams);
        }
        
        $reflection = new \ReflectionMethod($callable[0], $callable[1]);

        $arguments = [];
        foreach ($reflection->getParameters() as $param) {
            if(array_key_exists($param->name, $routeParams)) {
                $arguments[] = $routeParams[$param->name];
            } else {
                throw new \Exception(sprintf("The parameter '%s' for %s::%s doesn't exist inside your route", $param->name, $callable[0], $callable[1]));
            }
        }

        return $arguments;
    }
}