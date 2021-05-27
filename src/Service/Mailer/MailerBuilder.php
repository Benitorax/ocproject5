<?php

namespace App\Service\Mailer;

use Swift_Mailer;

class MailerBuilder
{
    private ?Swift_Mailer $spoolMailer = null;
    private ?Swift_Mailer $smtpMailer = null;
    private TransportBuilder $transports;

    public function __construct(TransportBuilder $transports)
    {
        $this->transports = $transports;
    }

    /**
     * Returns a SwiftMailer with SpoolTransport.
     */
    public function getSpoolMailer(): Swift_Mailer
    {
        if (null !== $this->spoolMailer) {
            return $this->spoolMailer;
        }

        return $this->spoolMailer = new Swift_Mailer($this->transports->getSpoolTransport());
    }

    /**
     * Returns a SwiftMailer with SmtpTransport.
     */
    public function getSmtpMailer(): Swift_Mailer
    {
        if (null !== $this->smtpMailer) {
            return $this->smtpMailer;
        }

        return $this->smtpMailer = new Swift_Mailer($this->transports->getSmtpTransport());
    }
}
