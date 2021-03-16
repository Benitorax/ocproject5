<?php

namespace App\Controller;

use Config\View\View;
use Config\Request\Request;
use Config\Session\Session;
use Config\Request\Parameter;
use Config\Response\Response;
use Config\Container\Container;
use Config\Router\UrlGenerator;
use Config\Security\TokenStorage;
use Config\Security\Csrf\CsrfTokenManager;

abstract class Controller
{
    protected View $view;
    protected Container $container;

    protected Request $request;
    protected Parameter $query;
    protected Parameter $post;

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
        $this->view->setRequest($request);
    }

    public function get(string $name): object
    {
        return $this->container->get($name);
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

    public function addFlash(string $type, string $message): void
    {
        if ($this->container->has(Session::class)) {
            /** @var Session */
            $session = $this->container->get(Session::class);
            $session->getFlashes()->add($type, $message);
        }
    }

    /**
     * Check is the user has a role
     * e.g. isGrand(['user']) or isGranted(['user', 'admin'])
     */
    public function isGranted(array $roles): bool
    {
        /** @var TokenStorage */
        $tokenStorage = $this->get(TokenStorage::class);

        if (!$token = $tokenStorage->getToken()) {
            return false;
        }

        if (!$user = $token->getUser()) {
            return false;
        }

        foreach ($roles as $role) {
            if (in_array($role, $user->getRoles())) {
                return true;
            }
        }

        return false;
    }
}
