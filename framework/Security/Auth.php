<?php

namespace Framework\Security;

use Exception;
use Framework\Request\Request;
use Framework\Session\Session;
use Framework\DAO\UserDAOInterface;
use Framework\Security\TokenStorage;
use Framework\Security\User\UserInterface;
use Framework\Security\RememberMe\RememberMeManager;

class Auth
{
    private UserDAOInterface $userDAO;
    private RememberMeManager $rememberMeManager;
    private TokenStorage $tokenStorage;

    public function __construct(
        TokenStorage $tokenStorage,
        UserDAOInterface $userDAO,
        RememberMeManager $rememberMeManager
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userDAO = $userDAO;
        $this->rememberMeManager = $rememberMeManager;
    }

    public function authenticate(Request $request, Session $session): void
    {
        // checks User from session
        try {
            $user = $session->get('user');

            if (!$user instanceof UserInterface) {
                throw new Exception('User from session does not implements UserInterface');
            }
            // gets a fresh User from database
            $user = $this->userDAO->loadByIdentifier((string) $user->getId());
        } catch (Exception $e) {
            $user = null;
        }

        if ($user instanceof UserInterface) {
            $this->tokenStorage->setUser($user);
            return;
        }

        // checks remember me cookie
        try {
            $token = $this->rememberMeManager->autoLogin($request);
        } catch (Exception $e) {
            $token = null;
        }

        if ($token instanceof AbstractToken) {
            $this->tokenStorage->setToken($token);
            $session->set('user', $token->getUser());
        }
    }
}
