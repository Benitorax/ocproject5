<?php

namespace App\Service\Mailer;

use Swift_MemorySpool;
use Swift_TransportException;
use Swift_Transport_SpoolTransport;
use App\Service\Mailer\Builder\MailerBuilder;
use App\Service\Mailer\Builder\TransportBuilder;
use Framework\EventDispatcher\Event\TerminateEvent;
use Framework\EventDispatcher\Subscriber\EventSubscriberInterface;

class MailerSubscriber implements EventSubscriberInterface
{
    private MailerBuilder $mailerBuilder;
    private TransportBuilder $transportBuilder;

    public function __construct(MailerBuilder $mailerBuilder, TransportBuilder $transportBuilder)
    {
        $this->mailerBuilder = $mailerBuilder;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Sends emails from SpoolTransport.
     */
    public function onTerminateEvent(TerminateEvent $event): void
    {
        $transport = $this->mailerBuilder->getSpoolMailer()->getTransport();
        if ($transport instanceof Swift_Transport_SpoolTransport) {
            $spool = $transport->getSpool();
            if ($spool instanceof Swift_MemorySpool) {
                try {
                    $spool->flushQueue($this->transportBuilder->getSmtpTransport());
                } catch (Swift_TransportException $exception) {
                    // TODO: possibly log the exception
                    // if (null !== $this->logger) {
                    //     $this->logger->error(sprintf('Exception occurred while flushing email queue: %s', $exception->getMessage()));
                    // }
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TerminateEvent::class => ['onTerminateEvent']
        ];
    }
}
