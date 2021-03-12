<?php
namespace App\Service;

use Exception;
use Config\Router\Router;

class UrlGenerator
{
    private array $routes;
    
    public function __construct(Router $router)
    {
        $this->routes = $router->getRoutes();
    }

    public function generate(string $routeName, array $routeParams = null): string
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $routeName) {
                if (count((array) $routeParams)) {
                    return $this->hydrateRouteParams($route->getPath(), (array) $routeParams);
                } else {
                    return $route->getPath();
                }
            }
        }

        throw new Exception(sprintf("Route with name '%s' cannot be found.", $routeName), 500);
    }

    private function hydrateRouteParams(string $routePath, array $routeParams): string
    {
        preg_match_all('#{[-\w]+}#', $routePath, $matches);

        foreach ($matches[0] as $match) {
            $paramName = (string) preg_replace('#^\{(\w+)\}$#', '$1', $match);

            if (array_key_exists($paramName, $routeParams)) {
                $routePath = (string) preg_replace(
                    '#\{'.$paramName.'\}#',
                    $routeParams[$paramName],
                    (string) $routePath
                );
            } else {
                throw new Exception(sprintf("The route parameter '%s' cannot be found.", $paramName), 500);
            }
        }

        return $routePath;
    }
}
