<?php

namespace Framework\Controller;

use Exception;
use Framework\View\View;
use Framework\Request\Request;
use Framework\Session\Session;
use Framework\Request\Parameter;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\Form\AbstractForm;
use Framework\Form\FormInterface;
use Framework\Router\UrlGenerator;
use Framework\Security\TokenStorage;
use Framework\Security\Csrf\CsrfTokenManager;
use Framework\Security\User\UserInterface;

abstract class AbstractController
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

    /**
     * Set request to have some shortcuts and hydrate the AppVariable of the View renderer.
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
        $this->query = $this->request->query;
        $this->post = $this->request->request;
        $this->view->setRequest($request);
    }

    /**
     * Returns an instantiate service.
     *
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function get(string $className)
    {
        return $this->container->get($className);
    }

    /**
     * Returns a response with content rendered by View.
     */
    public function render(string $viewPath, array $parameters = [], Response $response = null): Response
    {
        return $this->view->render($viewPath, $parameters, $response);
    }

    /**
     * Redirects to another route from the route's name and its parameters.
     */
    public function redirectToRoute(string $routeName, array $parameters = []): Response
    {
        $generator = $this->get(UrlGenerator::class);
        $url = $generator->generate($routeName, $parameters);

        $response = new Response('', 302);
        $response->headers->set('Location', $url);

        return $this->view->render('app/redirect.html.twig', ['url' => $url], $response);

        // header("Location: ".$this->get(UrlGenerator::class)->generate($routeName, $parameters));
        // exit();
    }

    /**
     * Checks if the csrf token is valid.
     */
    public function isCsrfTokenValid(?string $token): bool
    {
        $tokenManager = $this->get(CsrfTokenManager::class);

        if ($tokenManager->isTokenValid($token)) {
            return true;
        }

        return false;
    }

    /**
     * Shortcut to add flash message.
     */
    public function addFlash(string $type, string $message): void
    {
        if ($this->container->has(Session::class)) {
            $session = $this->container->get(Session::class);
            $session->getFlashes()->add($type, $message);
        }
    }

    /**
     * Check whether the user has a role
     * e.g. isGranted(['user']) or isGranted(['user', 'admin'])
     */
    public function isGranted(array $roles): bool
    {
        if (null === $user = $this->getUser()) {
            return false;
        }

        foreach ($roles as $role) {
            if (in_array($role, $user->getRoles())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an user object if authenticated, otherwise null.
     */
    public function getUser(): ?UserInterface
    {
        $tokenStorage = $this->get(TokenStorage::class);

        if (null === $token = $tokenStorage->getToken()) {
            return null;
        }

        if (null === $user = $token->getUser()) {
            return null;
        }

        return $user;
    }

    /**
     * Returns an instantiate form.
     *
     * The $className must be the name of the form class which is instantiated.
     *
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function createForm(string $className)
    {
        return $this->get($className)->newInstance();
    }
}
