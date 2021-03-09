<?php
namespace Config\View;

use App\Model\User;
use Config\Request\Request;
use Config\Security\TokenStorage;

class AppVariable
{
    private $request;
    private $tokenStorage;

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser(): ?User
    {
        if (null === $tokenStorage = $this->tokenStorage) {
            throw new \Exception('The "app.user" variable is not available.');
        }

        if (!$token = $tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();

        return $user instanceof User ? $user : null;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        if (null === $this->request) {
            throw new \Exception('The "app.request" variable is not available.');
        }

        return $this->request;
    }

    public function getSession()
    {
        if (null === $this->request) {
            throw new \Exception('The "app.session" variable is not available.');
        }

        return $this->request->hasSession() ? $this->request->getSession() : null;
    }

        /**
     * Returns some or all the existing flash messages:
     *  * getFlashes() returns all the flash messages
     *  * getFlashes('notice') returns a simple array with flash messages of that type
     *  * getFlashes(['notice', 'error']) returns a nested array of type => messages.
     *
     * @return array
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
