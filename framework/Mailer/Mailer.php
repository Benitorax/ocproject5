<?php

namespace Framework\Mailer;

use Exception;
use Swift_Mailer;
use Framework\Mailer\Event\MailEvent;
use Framework\Mailer\Builder\MailerBuilder;
use Framework\EventDispatcher\EventDispatcher;
use Swift_Message;
use Throwable;

class Mailer
{
    public const SMTP = 0;
    public const SPOOL_MEMORY = 1;

    private MailerBuilder $builder;
    private ?Swift_Mailer $mailer;
    private EventDispatcher $dispatcher;

    public function __construct(MailerBuilder $builder, EventDispatcher $dispatcher)
    {
        $this->builder = $builder;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Sets the type to use spool transport or smtp trnasport.
     *
     * @param int $type const self::SMTP or self::SPOOL_MEMORY.
     */
    public function setType(int $type): self
    {
        if (self::SMTP === $type) {
            $this->mailer = $this->builder->getSmtpMailer();
            return $this;
        }

        if (self::SPOOL_MEMORY === $type) {
            $this->mailer = $this->builder->getSpoolMailer();
            return $this;
        }

        throw new Exception(sprintf('Mailer type "%s" does not exist', $type));
    }

    /**
     * Return the number of successful recipients. Can be 0 which indicates failure
     */
    public function send(Swift_Message $message): int
    {
        if (!$this->mailer instanceof Swift_Mailer) {
            throw new Exception(
                'Mailer has no transport type: ' .
                'setType() must be called once before calling send().'
            );
        }

        $this->dispatcher->dispatch(new MailEvent($message));

        return $this->mailer->send($message);
    }
}
