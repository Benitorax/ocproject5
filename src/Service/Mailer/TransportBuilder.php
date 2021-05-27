<?php

namespace App\Service\Mailer;

use Swift_MemorySpool;
use Swift_SmtpTransport;
use Swift_Events_SimpleEventDispatcher;
use Swift_Transport_SpoolTransport;

class TransportBuilder
{
    private ?Swift_Transport_SpoolTransport $spoolTransport = null;
    private ?Swift_SmtpTransport $smtpTransport = null;

    /**
     * Returns a SpoolTransport.
     */
    public function getSpoolTransport(): Swift_Transport_SpoolTransport
    {
        if (null !== $this->spoolTransport) {
            return $this->spoolTransport;
        }

        return $this->spoolTransport = new Swift_Transport_SpoolTransport(
            new Swift_Events_SimpleEventDispatcher(),
            new Swift_MemorySpool()
        );
    }

    /**
     * Returns a SmtpTransport.
     */
    public function getSmtpTransport(): Swift_SmtpTransport
    {
        if (null !== $this->smtpTransport) {
            return $this->smtpTransport;
        }

        return $this->smtpTransport = (new Swift_SmtpTransport())
            ->setHost($_ENV['MAILER_HOST'])
            ->setPort($_ENV['MAILER_PORT'])
            ->setEncryption($_ENV['MAILER_ENCRYPTION'])
            ->setUsername($_ENV['MAILER_USERNAME'])
            ->setPassword($_ENV['MAILER_PASSWORD'])
        ;
    }
}
