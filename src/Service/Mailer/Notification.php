<?php

namespace App\Service\Mailer;

use App\Model\User;
use App\DAO\UserDAO;
use App\Service\Mailer\Mailer;
use App\Form\ContactForm;
use App\Model\ResetPasswordToken;

class Notification
{
    private UserDAO $userDAO;
    private Mailer $mailer;

    public function __construct(UserDAO $userDAO, Mailer $mailer)
    {
        $this->userDAO = $userDAO;
        $this->mailer = $mailer;
    }

    /**
     * Send email to every admin users when a contact form is submitted.
     */
    public function notifyContact(ContactForm $form): int
    {
        /** @var User[] */
        $admins = $this->userDAO->getAllAdmin();

        $count = 0;

        foreach ($admins as $admin) {
            $count += $this->mailer->notifyContact($form, $admin);
        }

        return $count;
    }

    /**
     * Send email to user to reset password.
     */
    public function notifyResetPasswordRequest(User $user, ResetPasswordToken $token): int
    {
        $count = $this->mailer->notifyResetPasswordRequest($user, $token);

        return $count;
    }
}
