<?php

namespace Framework\Mailer\Builder;

use Swift_MemorySpool;
use Swift_NullTransport;
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
     * @return Swift_SmtpTransport|Swift_NullTransport
     */
    public function getSmtpTransport()
    {
        if (count($this->config) === 0) {
            return $this->getNullTransport();
        }

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

    /**
     * Returns a NullTransport.
     */
    public function getNullTransport(): Swift_NullTransport
    {
        return new Swift_NullTransport();
    }
}
