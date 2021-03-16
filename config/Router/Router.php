<?php

namespace Config\Router;

use Exception;
use ReflectionMethod;
use Config\Router\Route;
use Config\Request\Request;
use Config\Response\Response;
use Config\Container\Container;
use Config\Controller\Controller;
use Config\Controller\ErrorController;

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
        $requestUri = $this->request->getRequestUri();

        try {
            $route = $this->match($requestUri, $this->request->getMethod());
            $arguments = $this->resolveControllerArguments($route->getCallable(), $route->getPath(), $requestUri);

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
     * Returns a Route from the uri and the method
     */
    public function match(string $requestUri, string $requestMethod): Route
    {
        $matchedRoute = null;

        foreach ($this->routes as $route) {
            $routePath = $route->getPath();
            $pattern = preg_replace('#\{\w+\}#', '[\w\-]+', $routePath);

            if (preg_match('#^' . $pattern . '$#', $requestUri, $matches)) {
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
        } else {
            return false;
        }
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
        [$classname, $method] = $callable;

        /** @var Controller abstract class of Controller */
        $object = $this->container->create($classname);
        $object->setRequest($this->request);
        $controller = [$object, $method];

        if (is_callable($controller)) {
            if (is_array($arguments)) {
                return $controller(...$arguments);
            } else {
                return $controller($arguments);
            }
        } else {
            throw new Exception(
                sprintf('Can\'t execute controller %s.', get_class($object)),
                500
            );
        }
    }

    /**
     * Retrieves the route parameters of the Controller, hydrate them and return them in a array.
     * e.g. for 'showPost($slug)' it returns '[$slug]' from '/post/{slug}'
     */
    public function resolveControllerArguments(array $callable, string $routePath, string $requestUri): array
    {
        $pathElements = explode('/', $routePath);
        $uriElements = explode('/', $requestUri);

        // Find every route params in url and make them variables
        foreach ($pathElements as $key => $element) {
            if (preg_match('#\{\w+\}#', $pathElements[$key], $matches0)) {
                $paramName = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$2', $element);
                $start = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$1', $element);
                $end = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$3', $element);
                preg_match('#^' . $start . '([-\w]+)' . $end . '$#', $uriElements[$key], $matches);

                if (count($matches)) {
                    $value = $matches[1];
                }

                if (!empty($value)) {
                    $routeParams[$paramName] = $value;
                    $this->request->attributes->set((string) $paramName, $value);
                }
            }
        }

        if (isset($routeParams)) {
            $this->request->attributes->set('route_params', $routeParams);
        } else {
            $routeParams = [];
        }

        $reflection = new ReflectionMethod($callable[0], $callable[1]);

        // Hydrates all the parameters
        $arguments = [];
        foreach ($reflection->getParameters() as $param) {
            if (array_key_exists($param->name, $routeParams)) {
                $arguments[] = $routeParams[$param->name];
            } else {
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
        }

        return $arguments;
    }

    public function initializeRoutes(): void
    {
        $routes = require __DIR__ . '/routes.php';
        foreach ($routes as $path => $data) {
            $this->routes[] = new Route($path, $data['callable'], $data['method'] ?? null, $data['name']);
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
