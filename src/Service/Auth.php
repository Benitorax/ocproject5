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
    private Session $session;
    private UserDAO $userDAO;
    private PasswordEncoder $encoder;
    private RememberMeManager $rememberMeManager;

    public function __construct(
        Session $session,
        UserDAO $userDAO,
        PasswordEncoder $encoder,
        RememberMeManager $rememberMeManager
    ) {
        $this->userDAO = $userDAO;
        $this->encoder = $encoder;
        $this->session = $session;
        $this->rememberMeManager = $rememberMeManager;
    }

    public function authenticate(string $email, string $password): ?User
    {
        /** @var User|null */
        $user = $this->userDAO->getOneBy(['email' => $email]);

        if (null === $user) {
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
        $user = $this->authenticate($form->email, $form->password);

        if (!$user instanceof User) {
            return null;
        }

        if ((bool) $form->rememberme) {
            $this->rememberMeManager->createNewToken($user, $request);
        }

        return $user;
    }

    public function handleLogout(Request $request): void
    {
        if ($request->cookies->has(RememberMeManager::COOKIE_NAME)) {
            $this->rememberMeManager->deleteToken($request);
        }

        $this->session->clear();
    }
}
