<?php

namespace App\Service\Mailer;

use App\Model\User;
use App\DAO\UserDAO;
use App\Service\Mailer\Mailer;
use App\Form\ContactForm;

class Notification
{
    private UserDAO $userDAO;
    private Mailer $mailer;

    public function __construct(UserDAO $userDAO, Mailer $mailer)
    {
        $this->userDAO = $userDAO;
        $this->mailer = $mailer;
    }

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
}
