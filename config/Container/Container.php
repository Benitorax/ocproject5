<?php
namespace Config\Container;

use App\DAO\DAO;
use App\Model\View;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DAO\CommentDAO;
use Config\Router\Router;
use Config\Router\Routes;
use Config\Router\Request;
use App\Controller\ErrorController;

class Container
{
    private $router;
    private $request;
    private $DAO;
    private $view;
    private $controllers = [];
    private $errorController;

    public function getDAO()
    {
        if ($this->DAO === null) $this->DAO = new DAO(new UserDAO(), new PostDAO(), new CommentDAO());

        return $this->DAO;
    }

    public function getView()
    {
        if ($this->view === null) $this->view = new View($this->getRequest());

        return $this->view;
    }

    public function getErrorController()
    {
        if ($this->errorController === null) {
            $this->errorController = new ErrorController($this->getView(), $this->getDAO(), $this->getRequest());
        }
        return $this->errorController;
    }

    public function getController($className)
    {
        if (!isset($this->controllers[$className])) {
            $controller = new $className($this->getView(), $this->getDAO(), $this->getRequest());
            $this->controllers[$className] = $controller;
        }
        return $this->controllers[$className];
    }

    public function getRouter()
    {
        if ($this->router === null) $this->router = new Router($this->getRequest(), new Routes(), $this->getErrorController());

        return $this->router;
    }

    public function getRequest()
    {
        if ($this->request === null) $this->request = new Request();

        return $this->request;
    }
}