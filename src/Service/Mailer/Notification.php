<?php

namespace App\Service\Mailer;

use App\Model\User;
use App\DAO\UserDAO;
use App\Form\ContactForm;
use App\Model\ResetPasswordToken;

class Notification
{
    private UserDAO $userDAO;
    private MailerBuilder $mailerBuilder;
    private MessageBuilder $messageBuilder;

    public function __construct(UserDAO $userDAO, MailerBuilder $mailerBuilder, MessageBuilder $messageBuilder)
    {
        $this->userDAO = $userDAO;
        $this->mailerBuilder = $mailerBuilder;
        $this->messageBuilder = $messageBuilder;
    }

    /**
     * Send email to every admin users when a contact form is submitted.
     */
    public function notifyContact(ContactForm $form): int
    {
        /** @var User[] */
        $admins = $this->userDAO->getAllAdmin();
        $mailer = $this->mailerBuilder->getSpoolMailer();
        $count = 0;

        foreach ($admins as $admin) {
            $message = $this->messageBuilder->createContact($form, $admin);
            $count += $mailer->send($message);
        }

        return $count;
    }

    /**
     * Send email to user to reset password.
     */
    public function notifyResetPasswordRequest(User $user, ResetPasswordToken $token): int
    {
        $message = $this->messageBuilder->createResetPasswordRequest($user, $token);
        $mailer = $this->mailerBuilder->getSmtpMailer();

        return $mailer->send($message);
    }

    /**
     * Send email to user to reset password.
     */
    public function notifyResetPassword(User $user): int
    {
        $message = $this->messageBuilder->createResetPassword($user);
        $mailer = $this->mailerBuilder->getSmtpMailer();

        return $mailer->send($message);
    }
}
