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

    public function saveNewUser(RegisterForm $form): User
    {
        $dateTime = new DateTime();
        $user = new User();
        $user->setId(IdGenerator::generate())
            ->setEmail($form->getEmail())
            ->setPassword((string) $this->encoder->encode($form->getPassword1()))
            ->setUsername($form->getUsername())
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
        ;

        $this->userDAO->add($user);

        return $user;
    }
}
