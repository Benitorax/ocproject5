<?php
namespace App\Service;

use App\Model\User;
use App\DAO\UserDAO;
use App\Form\LoginForm;
use Config\Request\Request;
use Config\Session\Session;
use Config\Security\RememberMe\RememberMeManager;

class Auth
{
    private $encoder;
    private $session;
    private $userDAO;
    private $rememberMeManager;

    public function __construct(
        UserDAO $userDAO, 
        PasswordEncoder $encoder, 
        Session $session,
        RememberMeManager $rememberMeManager
    )
    {
        $this->userDAO = $userDAO;
        $this->encoder = $encoder;
        $this->session = $session;
        $this->rememberMeManager = $rememberMeManager;
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userDAO->getOneBy(['email' => $email]);

        if ($user === null) {
            return null;
        }

        $isPasswordValid = $this->encoder->isPasswordValid($user, $password);

        if (!$isPasswordValid) {
            return null;
        }

        $this->session->set('user', $user);
        return $user;
    }

    public function authenticateLoginForm(LoginForm $form, Request $request): ?User
    {
        $user = $this->userDAO->getOneBy(['email' => $form->email]);

        if ($user === null) {
            return null;
        }

        $isPasswordValid = $this->encoder->isPasswordValid($user, $form->password);

        if (!$isPasswordValid) {
            return null;
        }

        if ($form->rememberme) {
            $this->rememberMeManager->createNewToken($user, $request);
        }
        
        $this->session->set('user', $user);
        return $user;
    }
}
