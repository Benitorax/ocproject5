<?php

namespace App\Service\Mailer\Event;

use Swift_Message;

class MailEvent
{
    private Swift_Message $message;

    public function __construct(Swift_Message $message)
    {
        $this->message = $message;
    }

    public function getMessage(): Swift_Message
    {
        return $this->message;
    }
}
