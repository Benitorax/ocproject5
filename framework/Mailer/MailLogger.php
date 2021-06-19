<?php

namespace Framework\Mailer;

use Framework\Mailer\Event\MailEvent;

class MailLogger
{
    /**
     * @var MailEvent[]
     */
    private array $mailEvents = [];

    public function log(MailEvent $event): void
    {
        $this->mailEvents[] = $event;
    }

    /**
     * @return MailEvent[]
     */
    public function getEvents()
    {
        return $this->mailEvents;
    }
}
