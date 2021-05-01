<?php

namespace App\Service;

use App\Model\User;
use App\DAO\UserDAO;
use App\Form\LoginForm;
use Framework\Request\Request;
use Framework\Session\Session;
use Framework\Security\Hasher\PasswordHasher;
use Framework\Security\RememberMe\RememberMeManager;

class Auth
{
    private Session $session;
    private UserDAO $userDAO;
    private PasswordHasher $hasher;
    private RememberMeManager $rememberMeManager;

    public function __construct(
        Session $session,
        UserDAO $userDAO,
        PasswordHasher $hasher,
        RememberMeManager $rememberMeManager
    ) {
        $this->userDAO = $userDAO;
        $this->hasher = $hasher;
        $this->session = $session;
        $this->rememberMeManager = $rememberMeManager;
    }

    public function authenticate(string $email, string $password): ?User
    {
        /** @var User|null */
        $user = $this->userDAO->getOneByEmail($email);

        if (null === $user) {
            return null;
        }

        if (!$this->hasher->isPasswordValid($user, $password)) {
            return null;
        }

        $this->session->set('user', $user);

        return $user;
    }

    /**
     * Authenticate from login form.
     */
    public function authenticateLoginForm(LoginForm $form, Request $request): ?User
    {
        $user = $this->authenticate($form->getEmail(), $form->getPassword());

        if (!$user instanceof User) {
            return null;
        }

        // if rememberme is checked then create a rememberme token
        if ((bool) $form->getRememberme()) {
            $this->rememberMeManager->createNewToken($user, $request);
        }

        return $user;
    }

    public function handleLogout(Request $request): void
    {
        // if there is a rememberme cookie then delete the rememberme token
        if ($request->cookies->has(RememberMeManager::COOKIE_NAME)) {
            $this->rememberMeManager->deleteToken($request);
        }

        $this->session->clear();
    }
}
