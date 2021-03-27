<?php

namespace App\Service;

use DateTime;
use App\Model\User;
use App\DAO\UserDAO;
use App\Form\RegisterForm;
use Framework\Security\Encoder\PasswordEncoder;

class UserManager
{
    private UserDAO $userDAO;
    private PasswordEncoder $encoder;

    public function __construct(
        UserDAO $userDAO,
        PasswordEncoder $encoder
    ) {
        $this->userDAO = $userDAO;
        $this->encoder = $encoder;
    }

    public function saveNewUser(User $user): User
    {
        $dateTime = new DateTime();
        $user->setId(IdGenerator::generate())
            ->setPassword((string) $this->encoder->encode($user->getPassword()))
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
        ;

        $this->userDAO->add($user);

        return $user;
    }
}
