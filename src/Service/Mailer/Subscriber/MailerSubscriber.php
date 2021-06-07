<?php

namespace App\Service\Mailer\Subscriber;

use Swift_MemorySpool;
use Swift_TransportException;
use Swift_Transport_SpoolTransport;
use App\Service\Mailer\Event\MailEvent;
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

    /**
     * @var MailEvent[]
     */
    private array $mailEvents = [];

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
                    // Nothing to do
                }
            }
        }
    }

    public function onMail(MailEvent $event): void
    {
        $this->mailEvents[] = $event;
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

    /**
     * @return MailEvent[]
     */
    public function getMailEvents()
    {
        return $this->mailEvents;
    }
}
