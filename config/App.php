<?php
namespace Config;

use App\Model\User;
use Config\Cookie\Cookie;
use Config\Request\Request;
use Config\Session\Session;
use Config\Response\Response;
use Config\Container\Container;
use Config\Security\TokenStorage;
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
        $this->container->setService($this);
        
        /** @var Session */
        $session = $this->container->getService(Session::class);
        $this->session = $session;
        $request->setSession($this->session);

        $this->authenticate($request);
    }

    public function authenticate(Request $request): void
    {
        /** @var TokenStorage */
        $tokenStorage = $this->container->getService(TokenStorage::class);

        // check User from session
        if ($this->session->get('user') instanceof User) {
            $tokenStorage->setUserFromSession($this->session);
            return;
        }

        // check rememberme cookie
        /** @var RememberMeManager */
        $rememberMeManager = $this->container->getService(RememberMeManager::class);
        $token = $rememberMeManager->autoLogin($request);

        if ($token instanceof AbstractToken) {
            $tokenStorage->setToken($token);
        }
    }

    public function terminate(): void
    {
        // TODO: send email
    }
}
