<?php

namespace Framework;

use Exception;
use Framework\Cookie\Cookie;
use Framework\Request\Request;
use Framework\Session\Session;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\DAO\UserDAOInterface;
use Framework\Router\RequestContext;
use Framework\Security\TokenStorage;
use Framework\Security\AbstractToken;
use Framework\Security\User\UserInterface;
use Framework\Security\RememberMe\RememberMeManager;

class App
{
    private Container $container;
    private Session $session;

    public function handle(Request $request): Response
    {
        $this->boot($request);
        $response = $this->container->getRouter()->run($request);

        // Add rememberme cookie into Response if exists
        if ($request->attributes->has(RememberMeManager::COOKIE_ATTR_NAME)) {
            /** @var Cookie */
            $cookie = $request->attributes->get(RememberMeManager::COOKIE_ATTR_NAME);
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function boot(Request $request): void
    {
        $this->container = new Container();
        $this->container->set($this);

        $context = $this->container->get(RequestContext::class);
        $context->fromRequest($request);

        // adds the session in the request
        $session = $this->container->get(Session::class);
        $this->session = $session;
        $request->setSession($this->session);

        $this->authenticate($request);
    }

    /**
     * Tries to authenticate from the session or a remember me cookie
     */
    public function authenticate(Request $request): void
    {
        $tokenStorage = $this->container->get(TokenStorage::class);

        // checks User from session
        try {
            $user = $this->session->get('user');

            if (!$user instanceof UserInterface) {
                throw new Exception('User from session does not implements UserInterface');
            }
            // gets a fresh User from database
            $user = $this->container->get(UserDAOInterface::class)->getOneByUsername($user->getUsername());
        } catch (Exception $e) {
            $user = null;
        }

        if ($user instanceof UserInterface) {
            $tokenStorage->setUser($user);
            return;
        }

        // checks remember me cookie
        try {
            $rememberMeManager = $this->container->get(RememberMeManager::class);
            $token = $rememberMeManager->autoLogin($request);
        } catch (Exception $e) {
            $token = null;
        }

        if ($token instanceof AbstractToken) {
            $tokenStorage->setToken($token);
            $this->session->set('user', $token->getUser());
        }
    }

    /**
     * Adds environment variables from env file
     */
    public function addEnvVariables(string $path): void
    {
        $env = (string) file_get_contents($path);
        $data = (array) json_decode($env);

        foreach ($data as $key => $value) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    public function terminate(): void
    {
        // TODO: send email
    }
}
