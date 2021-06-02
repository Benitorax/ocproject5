<?php

namespace Framework\Controller;

use Framework\Dotenv\Dotenv;
use Framework\Controller\ErrorController;
use Framework\EventDispatcher\Event\ExceptionEvent;
use Framework\EventDispatcher\Subscriber\EventSubscriberInterface;

class ControllerSubscriber implements EventSubscriberInterface
{
    private ErrorController $controller;
    private bool $debug;

    public function __construct(
        ErrorController $controller,
        Dotenv $dotenv
    ) {
        $this->controller = $controller;
        $this->debug = $dotenv->get('APP_DEBUG', false);
    }

    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $this->controller->setContainer($event->getContainer());
        $this->controller->setRequest($event->getRequest());

        if ($this->debug) {
            $event->setResponse($this->controller->debug($throwable));
            return;
        }

        $code = (int) $throwable->getCode();
        $codeNumber = (int) substr($throwable->getCode(), 0, 1);

        if (403 === $code) {
            $event->setResponse($this->controller->forbidden());
        } elseif (4 === $codeNumber) {
            $event->setResponse($this->controller->notFound());
        }

        $event->setResponse($this->controller->server());
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => ['onExceptionEvent', 100]
        ];
    }
}
