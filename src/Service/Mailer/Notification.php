<?php

namespace App\Service\Mailer;

use App\Model\User;
use App\DAO\UserDAO;
use App\Form\ContactForm;
use App\Model\ResetPasswordToken;
use App\Service\Mailer\Builder\MessageBuilder;
use Framework\Mailer\Mailer;

class Notification
{
    private UserDAO $userDAO;
    private Mailer $mailer;
    private MessageBuilder $messageBuilder;

    public function __construct(
        UserDAO $userDAO,
        Mailer $mailer,
        MessageBuilder $messageBuilder
    ) {
        $this->userDAO = $userDAO;
        $this->mailer = $mailer;
        $this->messageBuilder = $messageBuilder;
    }

    /**
     * Send email to every admin users when a contact form is submitted.
     */
    public function notifyContact(ContactForm $form): int
    {
        /** @var User[] */
        $admins = $this->userDAO->getAllAdmin();
        $mailer = $this->mailer->setType(Mailer::SPOOL_MEMORY);
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
        $mailer = $this->mailer->setType(Mailer::SMTP);

        return $mailer->send($message);
    }

    /**
     * Send email to user to reset password.
     */
    public function notifyResetPassword(User $user): int
    {
        $message = $this->messageBuilder->createResetPassword($user);
        $mailer = $this->mailer->setType(Mailer::SMTP);

        return $mailer->send($message);
    }
}
