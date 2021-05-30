<?php

namespace Framework\EventDispatcher\Subscriber;

interface EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     * e.g.:
     *  ['eventName' => 'methodName']
     *  ['eventName' => ['methodName', $priority]]
     *  ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents();
}
