<?php
namespace App\Service;

use App\DAO\UserDAO;
use App\Model\User;
use Config\Session\Session;

class Auth
{
    private $encoder;
    private $session;
    private $userDAO;

    public function __construct(UserDAO $userDAO, PasswordEncoder $encoder, Session $session)
    {
        $this->userDAO = $userDAO;
        $this->encoder = $encoder;
        $this->session = $session;
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userDAO->getOneBy(['email' => $email]);

        if ($user === null) {
            return null;
        }

        $isPasswordValid = $this->encoder->isPasswordValid($user, $password);

        if ($isPasswordValid) {
            return $user;
        }

        return null;
    }
}
