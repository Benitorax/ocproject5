<?php
namespace Config\Router;

use Config\Router\Route;


class Routes
{
    private $routes = [
        '/login' => [
            'name' => 'login',
            'method' => ['GET', 'POST'],
            'callable' => 'App\Controller\AppController::login'
        ],
        '/register' => [
            'name' => 'register',
            'method' => ['GET', 'POST'],
            'callable' => 'App\Controller\AppController::register'
        ],
        '/post/{slug}/author/{username}' => [
            'name' => 'post',
            'method' => 'GET',
            'callable' => 'App\Controller\AppController::post'
        ],
        '/' => [
            //'method' => 'GET',
            'name' => 'home',
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
            $route = new Route($key, $value['callable'], $value['method'], $value['name']);

            $this->routeObjects[] = $route;
        }
    }

    public function getRoutes()
    {
        return $this->routeObjects;
    }
}