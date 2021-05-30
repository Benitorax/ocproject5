<?php

namespace App\Service\Mailer\Builder;

use Swift_Message;
use App\Model\User;
use Framework\View\View;
use App\Form\ContactForm;
use App\Model\ResetPasswordToken;

/**
 * MessageBuilder method names are identical to Mailer's.
 */
class MessageBuilder
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * Prepares message for submitting contact form.
     */
    public function createContact(ContactForm $form, User $recipient): Swift_Message
    {
        return $this->prepareMessage('You have receive a message')
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
    }

    /**
     * Prepares message for reset password request.
     */
    public function createResetPasswordRequest(User $user, ResetPasswordToken $token): Swift_Message
    {
        return $this->prepareMessage('Reset password request')
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
    }

    /**
     * Prepares message for reset password.
     */
    public function createResetPassword(User $user): Swift_Message
    {
        return $this->prepareMessage('Reset password')
            ->setTo([$user->getEmail() => $user->getUsername()])
            ->setBody(
                $this->view->renderEmail('mail/reset_password.html.twig', ['recipient' => $user]),
                'text/html'
            )
        ;
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
