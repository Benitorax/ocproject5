<?php
namespace Config\Router;

use Exception;
use ReflectionMethod;
use Config\Router\Route;
use Config\Request\Request;
use Config\Response\Response;
use Config\Container\Container;
use App\Controller\ErrorController;

class Router
{
    private Request $request;
    private array $routes;
    private Container $container;

    public function __construct(Container $container)
    {
        $this->initializeRoutes();

        $this->container = $container;
    }

    public function run(Request $request): Response
    {
        $this->request = $request;
        
        try {
            return $this->match($this->request->getRequestUri(), $this->request->getMethod());
        } catch (Exception $e) {
            return $this->errorServer($e);
            // if (substr($e->getCode(), 0, 1) == 5) {
            //     return $this->errorServer($e);
            // } else {
            //     return $this->errorNotFound();
            // }
        }
    }

    public function match(string $requestUri, string $requestMethod): Response
    {
        $route = $this->matchRoute($requestUri, $requestMethod);

        if (!$route) {
            throw new Exception('No route found.', 404);
        } else {
            $arguments = $this->resolveControllerArguments($route->getCallable(), $route->getPath(), $requestUri);
            return $this->executeController($route->getCallable(), $arguments);
        }
    }

    public function matchRoute(string $requestUri, string $requestMethod): ?Route
    {
        foreach ($this->routes as $route) {
            $routePath = $route->getPath();
            $pattern = preg_replace('#\{\w+\}#', '[\w\-]+', $routePath);

            if (preg_match('#^'.$pattern.'$#', $requestUri, $matches)) {
                $isMethodValid = $this->matchMethod($requestMethod, $route->getMethods());
                
                if ($isMethodValid) {
                    return $route;
                }
            }
        }

        return null;
    }

    public function matchMethod(string $method, array $requiredMethods): bool
    {
        if (in_array($method, $requiredMethods)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Exception $error
     */
    public function errorNotFound($error = null): Response
    {
        return $this->executeController([ErrorController::class, 'notFound'], $error);
    }

    /**
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
        $object = $this->container->createService($classname);
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

    public function resolveControllerArguments(array $callable, string $routePath, string $requestUri): array
    {
        $pathElements = explode('/', $routePath);
        $uriElements = explode('/', $requestUri);
        
        // find every route params in url and make them variables
        foreach ($pathElements as $key => $element) {
            if (preg_match('#\{\w+\}#', $pathElements[$key], $matches0)) {
                $paramName = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$2', $element);
                $start = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$1', $element);
                $end = preg_replace('#([-\w]*)\{(\w+)\}([-\w]*)#', '$3', $element);
                preg_match('#^'.$start.'([-\w]+)'.$end.'$#', $uriElements[$key], $matches);

                if (count($matches)) {
                    $value = $matches[1];
                }

                if (!empty($value)) {
                    $routeParams[$paramName] = $value;
                    $this->request->attributes->set($paramName, $value);
                }
            }
        }

        if (isset($routeParams)) {
            $this->request->attributes->set('route_params', $routeParams);
        } else {
            $routeParams = [];
        }
        
        $reflection = new ReflectionMethod($callable[0], $callable[1]);

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
        $routes = require __DIR__.'/routes.php';
        foreach ($routes as $path => $data) {
            $this->routes[] = new Route($path, $data['callable'], $data['method'] ?? null, $data['name']);
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
