<?php

namespace Framework;

use Throwable;
use Framework\Dotenv\Dotenv;
use Framework\Router\Router;
use Framework\Security\Auth;
use Framework\Request\Request;
use Framework\Session\Session;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\DAO\UserDAOInterface;
use Framework\Router\RequestContext;
use Framework\EventDispatcher\EventDispatcher;
use Framework\EventDispatcher\Event\ExceptionEvent;
use Framework\EventDispatcher\Event\TerminateEvent;
use Framework\Security\RememberMe\RememberMeManager;

class App
{
    private Container $container;

    public function __construct(Dotenv $dotenv)
    {
        $this->container = new Container();
        $this->container->set($this);
        $this->container->set($dotenv);
    }

    public function handle(Request $request): Response
    {
        try {
            $this->boot($request);
            $response = $this->container->get(Router::class)->run($request);

            return $this->addCookiesToResponse($request, $response);
        } catch (Throwable $throwable) {
            $eventDispatcher = $this->container->get(EventDispatcher::class);
            $event = new ExceptionEvent($this->container, $request, $throwable);
            $eventDispatcher->dispatch($event);

            /** @var Response */
            return $event->getResponse();
        }
    }

    public function boot(Request $request): void
    {
        $context = $this->container->get(RequestContext::class);
        $context->fromRequest($request);

        // adds the session in the request
        $session = $this->container->get(Session::class);
        $request->setSession($session);

        // authenticates only if a class implements UserDAOInterface
        if (!empty($this->container->getAliases()[UserDAOInterface::class])) {
            $this->container->get(Auth::class)->authenticate($request, $session);
        }
    }

    /**
     * Adds Cookies to Response.
     */
    public function addCookiesToResponse(Request $request, Response $response): Response
    {
        // Add rememberme cookie into Response if exists in Request attributes
        if ($request->attributes->has(RememberMeManager::COOKIE_ATTR_NAME)) {
            $cookie = $request->attributes->get(RememberMeManager::COOKIE_ATTR_NAME);
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function terminate(Request $request, Response $response): void
    {
        $eventDispatcher = $this->container->get(EventDispatcher::class);
        $eventDispatcher->dispatch(new TerminateEvent($request, $response));
    }
}
