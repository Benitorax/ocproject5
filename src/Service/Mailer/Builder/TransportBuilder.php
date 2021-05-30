<?php

namespace App\Service\Mailer\Builder;

use Swift_MemorySpool;
use Swift_SmtpTransport;
use Framework\Dotenv\Dotenv;
use Swift_Transport_SpoolTransport;
use Swift_Events_SimpleEventDispatcher;

class TransportBuilder
{
    private ?Swift_Transport_SpoolTransport $spoolTransport = null;
    private ?Swift_SmtpTransport $smtpTransport = null;
    private array $config = [];

    public function __construct(Dotenv $dotenv)
    {
        // loads config from environment variables
        foreach ($dotenv->all() as $key => $value) {
            if (0 === strpos($key, 'MAILER_')) {
                $this->config[substr($key, 7)] = $value;
            }
        }
    }

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
            ->setHost($this->config['HOST'])
            ->setPort($this->config['PORT'])
            ->setEncryption($this->config['ENCRYPTION'])
            ->setUsername($this->config['USERNAME'])
            ->setPassword($this->config['PASSWORD'])
        ;
    }
}
