<?php

namespace Config;

use Exception;
use App\Model\User;
use Config\Cookie\Cookie;
use Config\Request\Request;
use Config\Session\Session;
use Config\Response\Response;
use Config\Container\Container;
use Config\Security\TokenStorage;
use Config\Router\RequestContext;
use Config\Security\AbstractToken;
use Config\Security\RememberMe\RememberMeManager;

class App
{
    private Container $container;
    private Session $session;

    public function handle(Request $request): Response
    {
        $this->boot($request);
        $router = $this->container->getRouter();
        $response = $router->run($request);

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

        /** @var RequestContext */
        $context = $this->container->get(RequestContext::class);
        $context->fromRequest($request);

        /** @var Session */
        $session = $this->container->get(Session::class);
        $this->session = $session;
        $request->setSession($this->session);

        $this->authenticate($request);
    }

    public function authenticate(Request $request): void
    {
        /** @var TokenStorage */
        $tokenStorage = $this->container->get(TokenStorage::class);

        // check User from session
        if ($this->session->get('user') instanceof User) {
            $tokenStorage->setUserFromSession($this->session);
            return;
        }

        // check rememberme cookie
        /** @var RememberMeManager */
        $rememberMeManager = $this->container->get(RememberMeManager::class);
        try {
            $token = $rememberMeManager->autoLogin($request);
        } catch (Exception $e) {
            $token = null;
        }

        if ($token instanceof AbstractToken) {
            $tokenStorage->setToken($token);
            $this->session->set('user', $token->getUser());
        }
    }

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
