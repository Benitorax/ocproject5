<?php
namespace App\Service;

use Twig\TwigFunction;
use Config\Router\Routes;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('url', [$this, 'generateUrl']),
        ];
    }

    public function generateUrl(string $routeName, array $routeParams = [])
    {
        $routes = (new Routes())->getRoutes();

        foreach($routes as $route) {
            if($route->getName() === $routeName) {
                if(count($routeParams)) {
                    return $this->hydrateRouteParams($route->getPath(), $routeParams);
                } else {
                    return $route->getPath();
                }
            }
        }

        throw new \Exception(sprintf("Route with name '%s' cannot be found.", $routeName), 500);
    }

    private function hydrateRouteParams(string $routePath, array $routeParams)
    {
        preg_match_all('#{[-\w]+}#', $routePath, $matches);

        foreach($matches[0] as $match) {
            $paramName = preg_replace('#^\{(\w+)\}$#', '$1', $match);

            if(array_key_exists($paramName, $routeParams)) {
                $routePath = preg_replace('#\{'.$paramName.'\}#', $routeParams[$paramName], $routePath);
            } else {
                throw new \Exception(sprintf("The route parameter '%s' cannot be found.", $paramName), 500);
                break;
            }
        }

        return $routePath;
    }
}


