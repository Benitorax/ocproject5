<?php
namespace Config\Router;

use Exception;
use Config\Router\Routes;
use Config\Router\Request;
use App\Controller\ErrorController;
use Config\Container\Container;

class Router
{
    private $errorController;
    private $request;
    private $routes;
    private $container;

    public function __construct(Request $request, Routes $routes, ErrorController $errorController)
    {
        $this->errorController = $errorController;
        $this->request = $request;
        $this->routes = $routes->getRoutes();
    }

    public function run()
    {
        try{
            $this->match();
        }
        catch (Exception $e)
        {
            $this->errorController->errorServer($e);
        }
    }

    public function match()
    {
        $requestUri = $this->request->getRequestUri();
        $route = $this->matchRoute($requestUri);

        if(!$route) {
            $this->errorController->errorNotFound();
        } else {
            $isMethodValid = $this->matchMethod($this->request->getMethod(), $route->getMethods());
            if(!$isMethodValid) {
                $this->errorController->errorNotFound();
            } else {
                $this->executeController($route->getCallable());
            }
        }

    }

    public function matchRoute($requestUri)
    {
        foreach($this->routes as $route) {
            $routePath = $route->getPath();
            $pattern = preg_replace('#\{\w+\}#', '[_\w\-]+', $routePath);

            if(preg_match('#^'.$pattern.'$#', $requestUri, $matches1)) {
                $pathElements = explode('/', $routePath);
                $uriElements = explode('/', $requestUri);

                foreach($pathElements as $key => $element) {
                    if(preg_match('#\{\w+\}#', $element, $matches)) {
                        $routeParameters[$element] = $uriElements[$key];
                        $this->request->getAttributes()->set($element, $uriElements[$key]);
                    }
                }

                if(isset($routeParameters)) {
                    $this->request->getAttributes()->set('route_parameters', $routeParameters);
                }

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

    public function executeController($callable)
    {
        [$classname, $method] = $callable;
        // TODO Get the controller from Container;
        $class = $this->container->getController($classname);
        $controller = [$class, $method];
        if(is_callable($controller)) {
            $controller();
        } else {
            $this->errorController->errorNotFound();
        }
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}