<?php

namespace App\Service\Mailer;

use Swift_Mailer;
use Swift_Message;
use App\Model\User;
use Framework\View\View;
use App\Form\ContactForm;
use App\Model\ResetPasswordToken;

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
        $message = $this->prepareMessage('You have receive a message')
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

    /**
     * Send email to user to reset password.
     */
    public function notifyResetPasswordRequest(User $user, ResetPasswordToken $token): int
    {
        $message = $this->prepareMessage('Reset password request')
            ->setFrom(['example@mail.com' => 'MyWebsite'])
            ->setTo([$user->getEmail() => $user->getUsername()])
            ->setBody(
                $this->view->renderEmail('mail/reset_password_request.html.twig', [
                        'token' => $token,
                        'recipient' => $user
                ]),
                'text/html'
            )
        ;

        return $this->mailer->send($message);
    }

    /**
     * Send email to user to reset password.
     */
    public function notifyResetPassword(User $user): int
    {
        $message = $this->prepareMessage('Reset password')
            ->setTo([$user->getEmail() => $user->getUsername()])
            ->setBody(
                $this->view->renderEmail('mail/reset_password.html.twig', ['recipient' => $user]),
                'text/html'
            )
        ;

        return $this->mailer->send($message);
    }

    /**
     * Creates an instance of Swift_Message and adds sender email address.
     */
    private function prepareMessage(string $title): Swift_Message
    {
        return (new Swift_Message($title))
            ->setFrom(['example@mail.com' => 'MyWebsite']);
    }
}
