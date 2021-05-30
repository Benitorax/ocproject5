<?php

namespace App\Service\Mailer;

use Swift_MemorySpool;
use Swift_TransportException;
use Swift_Transport_SpoolTransport;
use App\Service\Mailer\Builder\MailerBuilder;
use App\Service\Mailer\Builder\TransportBuilder;
use Framework\EventDispatcher\Event\ExceptionEvent;
use Framework\EventDispatcher\Event\TerminateEvent;
use Framework\EventDispatcher\Subscriber\EventSubscriberInterface;

class MailerSubscriber implements EventSubscriberInterface
{
    private MailerBuilder $mailerBuilder;
    private TransportBuilder $transportBuilder;
    private bool $wasExceptionThrown = false;

    public function __construct(MailerBuilder $mailerBuilder, TransportBuilder $transportBuilder)
    {
        $this->mailerBuilder = $mailerBuilder;
        $this->transportBuilder = $transportBuilder;
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
                    // TODO: possibly log the exception
                    // if (null !== $this->logger) {
                    //     $this->logger->error(sprintf(
                    //         'Exception occurred while flushing email queue: %s',
                    //          $exception->getMessage()
                    //     ));
                    // }
                }
            }
        }
    }

    public function onException(): void
    {
        $this->wasExceptionThrown = true;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TerminateEvent::class => 'onTerminate',
            ExceptionEvent::class => 'onException'
        ];
    }
}
