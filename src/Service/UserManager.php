<?php

namespace App\Service;

use DateTime;
use App\Model\User;
use App\DAO\UserDAO;
use Ramsey\Uuid\Uuid;
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
        $user->setUuid(Uuid::uuid4())
            ->setPassword((string) $this->encoder->encode($user->getPassword()))
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
        ;

        $this->userDAO->add($user);

        return $user;
    }
}
