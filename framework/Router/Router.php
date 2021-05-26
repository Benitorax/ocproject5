<?php

namespace Framework\Router;

use Exception;
use ReflectionMethod;
use Framework\Router\Route;
use Framework\Request\Request;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\Controller\ErrorController;

class Router
{
    private Request $request;
    private Container $container;
    private array $routes;

    public function __construct(Container $container)
    {
        $this->initializeRoutes();

        $this->container = $container;
    }

    public function run(Request $request): Response
    {
        $this->request = $request;
        $pathInfo = $this->request->getPathInfo();

        try {
            $route = $this->match($pathInfo, $this->request->getMethod());
            $arguments = $this->resolveControllerArguments($route->getCallable(), $route->getPath(), $pathInfo);
            $this->request->attributes->set('route', $route->getName());

            return $this->executeController($route->getCallable(), $arguments);
        } catch (Exception $e) {
            return $this->errorServer($e);
            // if (5 === (int) substr($e->getCode(), 0, 1)) {
            //     return $this->errorServer($e);
            // } else {
            //     return $this->errorNotFound();
            // }
        }
    }

    /**
     * Returns a Route from the path info and the method.
     */
    public function match(string $pathInfo, string $requestMethod): Route
    {
        $matchedRoute = null;

        foreach ($this->routes as $route) {
            $routePath = $route->getPath();

            // creates a pattern for the preg_match
            // which replaces every "{wildcard}" of the route,
            // e.g. '/path/{info}/user/{id}' will return '/path/[\w\-]+/user/[\w\-]+'.
            $pattern = preg_replace('#\{\w+\}#', '[\w\-]+', $routePath);

            // checks if the path info matches with the pattern of the route
            if (preg_match('#^' . $pattern . '$#', $pathInfo)) {
                // checks if the method matches with a method of the route
                if ($this->isMethodValid($requestMethod, $route->getMethods())) {
                    $matchedRoute = $route;
                }
            }
        }

        if (!$matchedRoute) {
            throw new Exception('No route found.', 404);
        }

        return $matchedRoute;
    }

    public function isMethodValid(string $method, array $requiredMethods): bool
    {
        if (in_array($method, $requiredMethods)) {
            return true;
        }

        return false;
    }

    /**
     * Returns an error 404 page.
     *
     * @param Exception $error
     */
    public function errorNotFound($error = null): Response
    {
        return $this->executeController([ErrorController::class, 'notFound'], $error);
    }

    /**
     * Returns an error server page.
     *
     * @param Exception $error
     */
    public function errorServer($error = null): Response
    {
        return $this->executeController([ErrorController::class, 'server'], $error);
    }

    /**
     * @param string|array|object $arguments
     */
    public function executeController(array $callable, $arguments = null): Response
    {
        [$className, $method] = $callable;

        $controllerClass = $this->container->create($className); // @phpstan-ignore-line
        $controllerClass->setContainer($this->container);
        $controllerClass->setRequest($this->request);
        $controller = [$controllerClass, $method];

        if (is_callable($controller)) {
            if (is_array($arguments)) {
                return $controller(...$arguments);
            } else {
                return $controller($arguments);
            }
        } else {
            throw new Exception(
                sprintf('Can\'t execute controller "%s" of class "%s".', $method, $className),
                500
            );
        }
    }

    /**
     * Retrieves the route parameters of the Controller, hydrate them and return them in a array.
     * e.g. for 'showPost($slug)' it returns '[$slug]' from '/post/{slug}'
     */
    public function resolveControllerArguments(array $callable, string $routePath, string $pathInfo): array
    {
        $pathElements = explode('/', $routePath);
        $urlElements = explode('/', $pathInfo);
        $routeParams = [];

        // retrieves every route's params and hydrates them with values of the url
        foreach ($pathElements as $key => $element) {
            // checks if there is a {wildcard}
            if (preg_match('#\{\w+\}#', $pathElements[$key])) {
                // get the name of the param
                // e.g. 'post-{id}' will return 'id'.
                $paramName = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$2', $element);

                // prepares the pattern for the preg_match
                // e.g. 'post-{id}-2021' will returns 'post-([-\w]+)-2021'
                $start = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$1', $element);
                $end = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$3', $element);
                preg_match('#^' . $start . '([-\w]+)' . $end . '$#', $urlElements[$key], $matches);

                if (count($matches)) {
                    $value = $matches[1];
                }

                if (!empty($value)) {
                    // add the value to the param
                    $routeParams[$paramName] = $value;
                    $this->request->attributes->set((string) $paramName, $value);
                }
            }
        }

        // add $routeParams in the Request's attributes
        if (!empty($routeParams)) {
            $this->request->attributes->set('route_params', $routeParams);
        }

        // Hydrates the parameters of the controller with the params of the route
        $reflection = new ReflectionMethod($callable[0], $callable[1]);
        $arguments = [];

        foreach ($reflection->getParameters() as $param) {
            if (array_key_exists($param->name, $routeParams)) {
                $arguments[] = $routeParams[$param->name];
                continue;
            }

            throw new Exception(
                sprintf(
                    "The parameter {%s} for %s::%s doesn't exist inside your route",
                    $param->name,
                    $callable[0],
                    $callable[1]
                ),
                500
            );
        }

        return $arguments;
    }

    public function initializeRoutes(): void
    {
        $routes = require dirname(__DIR__, 2) . '\config\routes.php';
        foreach ($routes as $path => $data) {
            $this->routes[] = new Route($path, $data['callable'], $data['method'] ?? null, $data['name']);
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
