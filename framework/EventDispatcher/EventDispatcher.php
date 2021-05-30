<?php

namespace Framework\EventDispatcher;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Framework\EventDispatcher\Subscriber\EventSubscriberInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * List of event listeners.
     * e.g.:
     * [
     *     'event-name1' => [
     *         priority1 => [
     *            'listener1',
     *            'listener2'
     *         ]
     *     ]
     * ]
     */
    private array $listeners = [];

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event Object to process.
     * @return object Event that was passed, now modified by listeners.
     */
    public function dispatch(object $event, string $eventName = null): object
    {
        $eventName = $eventName ?? \get_class($event);
        $listeners = $this->getListeners($eventName);

        if ($listeners) {
            $this->callListeners($listeners, $eventName, $event);
        }

        return $event;
    }

    /**
     * Returns an array of listeners.
     */
    public function getListeners(string $eventName = null): ?array
    {
        if (null !== $eventName) {
            if (empty($this->listeners[$eventName])) {
                return [];
            }

            ksort($this->listeners[$eventName]);
            $listeners = [];

            // merges every listeners in one table by removing priority tables
            foreach ($this->listeners[$eventName] as $listenersByPriority) {
                foreach ($listenersByPriority as $listener) {
                    $listeners[] = $listener;
                }
            }

            return $listeners;
        }

        return null;
    }

    /**
     * Triggers the listeners of an event.
     */
    protected function callListeners(iterable $listeners, string $eventName, object $event): void
    {
        foreach ($listeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $listener($event, $eventName, $this);
        }
    }

    /**
     * @param callable|array $listener
     */
    public function addListener(string $eventName, $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][$priority][] = $listener;
    }

    /**
     * @param callable|array $listener
     */
    public function removeListener(string $eventName, $listener): void
    {
        if (empty($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $priority => &$listeners) {
            foreach ($listeners as $key => &$value) {
                if ($value === $listener) {
                    unset($listeners[$key]);
                }
            }

            if (!$listeners) {
                unset($this->listeners[$eventName][$priority]);
            }
        }
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, [$subscriber, $params]);
            } elseif (is_string($params[0])) {
                $this->addListener($eventName, [$subscriber, $params[0]], $params[1] ?? 0);
            } elseif (is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->addListener($eventName, [$subscriber, $listener[0]], $listener[1] ?? 0);
                }
            }
        }
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, [$subscriber, $listener[0]]);
                }
                return;
            }

            $this->removeListener($eventName, [$subscriber, is_string($params) ? $params : $params[0]]);
        }
    }
}
