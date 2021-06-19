<?php

namespace Framework\Mailer\Subscriber;

use Swift_MemorySpool;
use Swift_TransportException;
use Swift_Transport_SpoolTransport;
use Framework\Mailer\Event\MailEvent;
use Framework\Mailer\Builder\MailerBuilder;
use Framework\Mailer\Builder\TransportBuilder;
use Framework\EventDispatcher\Event\ExceptionEvent;
use Framework\EventDispatcher\Event\TerminateEvent;
use Framework\EventDispatcher\Subscriber\EventSubscriberInterface;
use Framework\Mailer\MailLogger;

class MailerSubscriber implements EventSubscriberInterface
{
    private MailerBuilder $mailerBuilder;
    private TransportBuilder $transportBuilder;
    private MailLogger $logger;
    private bool $wasExceptionThrown = false;

    public function __construct(
        MailerBuilder $mailerBuilder,
        TransportBuilder $transportBuilder,
        MailLogger $logger
    ) {
        $this->mailerBuilder = $mailerBuilder;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
    }

    /**
     * Sends emails from SpoolTransport.
     */
    public function onTerminate(): void
    {
        if ($this->wasExceptionThrown) {
            return;
        }

        $transport = $this->mailerBuilder->getSpoolMailer()->getTransport();
        if ($transport instanceof Swift_Transport_SpoolTransport) {
            $spool = $transport->getSpool();
            if ($spool instanceof Swift_MemorySpool) {
                try {
                    $spool->flushQueue($this->transportBuilder->getSmtpTransport());
                } catch (Swift_TransportException $exception) {
                    // Nothing to do
                }
            }
        }
    }

    public function onMail(MailEvent $event): void
    {
        $this->logger->log($event);
    }

    public function onException(): void
    {
        $this->wasExceptionThrown = true;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TerminateEvent::class => 'onTerminate',
            ExceptionEvent::class => 'onException',
            MailEvent::class => 'onMail'
        ];
    }
}
