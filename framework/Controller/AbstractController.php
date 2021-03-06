<?php

namespace Framework\Controller;

use Exception;
use Framework\View\View;
use Framework\Request\Request;
use Framework\Session\Session;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\Router\UrlGenerator;
use Framework\Response\JsonResponse;
use Framework\Security\TokenStorage;
use Framework\Security\User\UserInterface;
use Framework\Security\Csrf\CsrfTokenManager;

abstract class AbstractController
{
    protected Container $container;
    protected Request $request;

    /**
     * Sets the container.
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * Set request to have some shortcuts and hydrate the AppVariable of the View renderer.
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
        $this->container->get(View::class)->setRequest($request);
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
        return $this->container->get(View::class)->render($viewPath, $parameters, $response);
    }

    /**
     * Redirects to another route from the route's name and its parameters.
     */
    public function redirectToRoute(string $routeName, array $parameters = []): Response
    {
        $generator = $this->container->get(UrlGenerator::class);
        $url = $generator->generate($routeName, $parameters);

        $response = new Response('', 302);
        $response->headers->set('Location', $url);

        return $this->container->get(View::class)->render('app/redirect.html.twig', ['url' => $url], $response);

        // header("Location: ".$this->container->get(UrlGenerator::class)->generate($routeName, $parameters));
        // exit();
    }

    /**
     * Redirects to another route from the route's name and its parameters.
     */
    public function redirectToUrl(string $url): Response
    {
        $response = new Response('', 302);
        $response->headers->set('Location', $url);

        return $this->container->get(View::class)->render('app/redirect.html.twig', ['url' => $url], $response);
    }

    /**
     * Checks if the csrf token is valid.
     */
    public function isCsrfTokenValid(): bool
    {
        $tokenManager = $this->container->get(CsrfTokenManager::class);
        $token = $this->request->request->get('csrf_token');

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
        if (null !== $user = $this->getUser()) {
            foreach ($roles as $role) {
                if (in_array($role, $user->getRoles())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Throws an exception if the user does not have access.
     */
    protected function denyAccessUnlessGranted(array $roles): void
    {
        if ($this->isGranted($roles)) {
            return;
        }

        throw new Exception(sprintf('Access Denied. Required roles: %s.', implode(', ', $roles)), 403);
    }

    /**
     * Returns an user object if authenticated, otherwise null.
     */
    public function getUser(): ?UserInterface
    {
        if (null !== $token = $this->container->get(TokenStorage::class)->getToken()) {
            return $token->getUser();
        }

        return null;
    }

    /**
     * Returns an instantiate form.
     *
     * The $className must be the name of the form class which is instantiated.
     *
     * @template T
     * @param class-string<T> $className
     * @param null|mixed $object
     * @return T
     */
    public function createForm(string $className, $object = null)
    {
        $form = $this->container->get($className)->newInstance();

        if (null !== $object) {
            $form->hydrateForm($object);
        }

        return $form;
    }

    /**
     * Returns a JsonResponse that uses json_encode if $data is not encoded yet.
     * @param mixed $data
     */
    protected function json($data, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $json);
    }
}
