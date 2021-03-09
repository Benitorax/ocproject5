<?php
namespace App\Controller;

use Config\View\View;
use Config\Request\Request;
use Config\Session\Session;
use App\Service\UrlGenerator;
use Config\Response\Response;
use Config\Container\Container;

abstract class Controller
{
    protected $view;
    protected $request;
    protected $get;
    protected $post;
    /** @var Session */
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
        $this->session = $this->container->getService(Session::class);
        $this->view->setRequest($request);
    }

    public function get(string $name)
    {
        return $this->container->getService($name);
    }

    public function render(string $viewPath, array $parameters = [], Response $response = null)
    {
        return $this->view->render($viewPath, $parameters, $response);
    }

    public function redirectToRoute(string $routeName, array $parameters = [])
    {
        $url = $this->get(UrlGenerator::class)->generate($routeName, $parameters);
        $response = new Response('', 302);
        $response->headers->set('Location', $url);

        return $this->view->render('app/redirect.html.twig', ['url' => $url], $response);
        
        // header("Location: ".$this->get(UrlGenerator::class)->generate($routeName, $parameters));
        // exit();
    }
}
