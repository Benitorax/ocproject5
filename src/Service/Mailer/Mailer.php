<?php

namespace App\Service\Mailer;

use Swift_Mailer;
use Swift_Message;
use App\Model\User;
use Config\View\View;
use App\Form\ContactForm;

class Mailer
{
    private Swift_Mailer $mailer;
    private View $view;

    public function __construct(View $view)
    {
        $transport = (new \Swift_SmtpTransport())
            ->setHost($_ENV['MAILER_HOST'])
            ->setPort($_ENV['MAILER_PORT'])
            ->setEncryption($_ENV['MAILER_ENCRYPTION'])
            ->setUsername($_ENV['MAILER_USERNAME'])
            ->setPassword($_ENV['MAILER_PASSWORD'])
        ;

        $this->mailer = new Swift_Mailer($transport);
        $this->view = $view;
    }

    /**
     * Send email when a contact form is submitted.
     */
    public function notifyContact(ContactForm $form, User $recipient): int
    {
        $message = (new Swift_Message('You have receive a message'))
            ->setFrom(['example@mail.com' => 'MyWebsite'])
            ->setTo([$recipient->getEmail() => $recipient->getUsername()])
            ->setBody(
                $this->view->renderEmail('mail/contact.html.twig', [
                        'form' => $form,
                        'recipient' => $recipient
                    ]),
                'text/html'
            )
        ;

        return $this->mailer->send($message);
    }
}
