<?php

namespace Framework\Router;

use Framework\Router\RequestContext;
use Exception;
use Framework\Router\Router;

class UrlGenerator
{
    public const PATH_TYPE = 1;
    public const URL_TYPE = 2;

    private RequestContext $context;
    private array $routes;

    public function __construct(RequestContext $context, Router $router)
    {

        $this->context = $context;
        $this->routes = $router->getRoutes();
    }

    /**
     * Generates the url from route name and route parameters.
     *
     * @param int $type indicates if it should generate an absolute url (with scheme and host) or absolute path
     */
    public function generate(string $routeName, array $routeParams = null, int $type = self::PATH_TYPE): string
    {
        $url = '';

        foreach ($this->routes as $route) {
            if ($route->getName() === $routeName) {
                if (count((array) $routeParams)) {
                    $url = $this->hydrateRouteParams($route->getPath(), (array) $routeParams);
                    break;
                }

                $url = $route->getPath();
                break;
            }
        }

        if (strlen($url) > 0) {
            if (self::PATH_TYPE === $type) {
                return $url;
            }

            return $this->context->getSchemeAndHost() . $url;
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
                    '#\{' . $paramName . '\}#',
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
