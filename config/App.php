<?php
namespace Config;

use Config\Router\Router;
use Config\Request\Request;
use Config\Response\Response;
use Config\Container\Container;

class App
{
    private $request;
    private $container;

    public function handle(Request $request): Response
    {
        $this->container = new Container();
        $this->container->setService($this);
        $this->request = $request;

        /** @var Router */
        $router = $this->container->getRouter();
        
        return $router->run($request);
    }

    public function terminate()
    {
        // TODO: send email
    }
}
