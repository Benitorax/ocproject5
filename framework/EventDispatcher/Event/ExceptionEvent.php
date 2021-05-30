<?php

namespace Framework\EventDispatcher\Event;

use Throwable;
use Framework\Request\Request;
use Framework\Response\Response;
use Framework\Container\Container;

class ExceptionEvent extends Event
{
    private Request $request;
    private Container $container;
    private Response $response;
    private Throwable $throwable;

    public function __construct(Container $container, Request $request, Throwable $throwable)
    {
        $this->container = $container;
        $this->request = $request;
        $this->throwable = $throwable;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Sets a response and stops event propagation.
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    public function hasResponse(): bool
    {
        return null !== $this->response;
    }
}
