<?php
namespace App\Service;

use Swift_Mailer;
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

    public function notifyContact(ContactForm $form, User $receiver): int
    {
        $message = (new \Swift_Message('You have receive a message'))
            ->setFrom([$form->user->getEmail() => $form->user->getUsername()])
            ->setTo([$receiver->getEmail() => $receiver->getUsername()])
            ->setBody($this->view->renderEmail('mail/contact.html.twig', ['form' => $form]), 'text/html')
        ;

        return $this->mailer->send($message);
    }
}
