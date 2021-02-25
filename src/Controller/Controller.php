<?php
namespace App\Controller;

use App\DAO\DAO;
use App\Model\View;
use Config\Container\Container;
use Config\Router\Request;

abstract class Controller
{
    protected $view;
    protected $request;
    protected $get;
    protected $post;
    protected $session;
    protected $container;

    public function __construct(View $view, Container $container)
    {
        $this->view = $view;
        $this->container = $container;
    }

    public function setRequest(Request $request) 
    {
        $this->request = $request;
        $this->get = $this->request->getGet();
        $this->post = $this->request->getPost();
        $this->session = $this->request->getSession();
        $this->view->setRequest($request);
    }

    public function get(string $name)
    {
        if(preg_match('#DAO#', $name)) {
            return $this->container->getService('App\\DAO\\'.$name);
        }
        
        return $this->container->getService('App\\Service\\'.$name);
    }
}