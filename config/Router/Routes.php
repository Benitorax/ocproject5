<?php
namespace Config\Router;

use Config\Router\Route;


class Routes
{
    private $routes = [
        '/post/{slug}/comment/{id}' => [
            'method' => 'GET',
            'callable' => 'App\Controller\AppController::post'
        ],
        '/' => [
            //'method' => 'GET',
            'callable' => 'App\Controller\AppController::home'
        ]
    ];

    private $routeObjects = [];

    public function __construct()
    {
        $this->initializeRoutes();
    }

    public function initializeRoutes()
    {
        foreach($this->routes as $key => $value) 
        {
            $value['method'] = isset($value['method']) ? $value['method'] : null;
            $route = new Route($key, $value['callable'], $value['method']);

            $this->routeObjects[] = $route;
        }
    }

    public function getRoutes()
    {
        return $this->routeObjects;
    }
}