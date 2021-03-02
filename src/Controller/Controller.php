<?php
namespace App\Controller;

use App\Model\View;
use Config\Request\Request;
use Config\Container\Container;

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
        $this->query = $this->request->query;
        $this->post = $this->request->request;
        $this->session = $this->request->session;
        $this->view->setRequest($request);
    }

    public function get(string $name)
    {
        if(preg_match('#DAO$#', $name)) {
            return $this->container->getService('App\\DAO\\'.$name);
        }

        if(preg_match('#Validation$#', $name)) {
            return $this->container->getService('App\\Service\\Validation\\'.$name);
        }
        
        return $this->container->getService('App\\Service\\'.$name);
    }

    public function redirectToRoute(string $routeName, array $parameters = null) 
    {
        header("Location: ".$this->get('UrlGenerator')->generate($routeName, $parameters));
        exit();
        
        // return $this->view->render('app/redirect.html.twig', [
        //     'url' => $this->get('UrlGenerator')->generate($routeName, $parameters)
        // ]); 
    }
}