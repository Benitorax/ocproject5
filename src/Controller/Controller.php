<?php
namespace App\Controller;

use Config\View\View;
use Config\Request\Request;
use Config\Session\Session;
use App\Service\UrlGenerator;
use Config\Request\Parameter;
use Config\Response\Response;
use Config\Container\Container;
use Config\Security\Csrf\CsrfTokenManager;

abstract class Controller
{
    protected View $view;
    protected Request $request;
    protected Parameter $query;
    protected Parameter $get;
    protected Parameter $post;
    protected ?Session $session;
    protected Container $container;

    public function __construct(View $view, Container $container)
    {
        $this->view = $view;
        $this->container = $container;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
        $this->query = $this->request->query;
        $this->post = $this->request->request;
        $this->session = $this->request->getSession();
        $this->view->setRequest($request);
    }

    public function get(string $name): object
    {
        return $this->container->getService($name);
    }

    public function render(string $viewPath, array $parameters = [], Response $response = null): Response
    {
        return $this->view->render($viewPath, $parameters, $response);
    }

    public function redirectToRoute(string $routeName, array $parameters = []): Response
    {
        /** @var UrlGenerator */
        $generator = $this->get(UrlGenerator::class);
        $url = $generator->generate($routeName, $parameters);
        
        $response = new Response('', 302);
        $response->headers->set('Location', $url);

        return $this->view->render('app/redirect.html.twig', ['url' => $url], $response);
        
        // header("Location: ".$this->get(UrlGenerator::class)->generate($routeName, $parameters));
        // exit();
    }

    public function isCsrfTokenValid(?string $token): bool
    {
        /** @var CsrfTokenManager */
        $tokenManager = $this->get(CsrfTokenManager::class);
        $isValid = $tokenManager->isTokenValid($token);

        if ($isValid) {
            return true;
        }
        return false;
    }
}
