<?php

namespace Framework;

use Exception;
use Framework\Cookie\Cookie;
use Framework\Request\Request;
use Framework\Session\Session;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\Security\TokenStorage;
use Framework\Router\RequestContext;
use Framework\Security\AbstractToken;
use Framework\Security\RememberMe\RememberMeManager;
use Framework\Security\User\UserInterface;

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

        // check User from session
        if ($this->session->get('user') instanceof UserInterface) {
            $tokenStorage->setUserFromSession($this->session);
            return;
        }

        // check remember me cookie
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
     * Add environment variables from env file
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
