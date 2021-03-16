<?php

namespace Config\View;

use Exception;
use App\Model\User;
use Config\Request\Request;
use Config\Session\Session;
use Config\Security\TokenStorage;

class AppVariable
{
    private ?Request $request = null;
    private ?TokenStorage $tokenStorage = null;

    public function setTokenStorage(TokenStorage $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser(): ?User
    {
        if (null === $tokenStorage = $this->tokenStorage) {
            throw new Exception('The "app.user" variable is not available.');
        }

        $token = $tokenStorage->getToken();

        if (empty($token)) {
            return null;
        }

        return $token->getUser();
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        if (null === $this->request) {
            throw new Exception('The "app.request" variable is not available.');
        }

        return $this->request;
    }

    public function getSession(): ?Session
    {
        if (null === $this->request) {
            throw new Exception('The "app.session" variable is not available.');
        }

        return $this->request->hasSession() ? $this->request->getSession() : null;
    }

    /**
     * Returns some or all the existing flash messages:
     *  * getFlashes() returns all the flash messages
     *  * getFlashes('notice') returns a simple array with flash messages of that type
     *  * getFlashes(['notice', 'error']) returns a nested array of type => messages.
     *
     * @param string|array $types
     * @return null|array
     */
    public function getFlashes($types = null)
    {
        try {
            if (null === $session = $this->getSession()) {
                return [];
            }
        } catch (\RuntimeException $e) {
            return [];
        }

        if (null === $types || '' === $types || [] === $types) {
            return $session->getFlashes()->all();
        }

        if (\is_string($types)) {
            return $session->getFlashes()->get($types);
        }

        $result = [];
        foreach ($types as $type) {
            $result[$type] = $session->getFlashes()->get($type);
        }

        return $result;
    }
}
