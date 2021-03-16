<?php

namespace Config\Security;

use App\Model\User;
use Config\Session\Session;

class TokenStorage
{
    private ?AbstractToken $token = null;

    public function getToken(): ?AbstractToken
    {
        return $this->token;
    }

    public function setToken(AbstractToken $token = null): void
    {
        $this->token = $token;
    }

    public function reset(): void
    {
        $this->setToken(null);
    }

    public function setUserFromSession(Session $session): void
    {
        $user = $session->get('user');

        if ($user instanceof User) {
            $token = new PreAuthenticatedToken($user);
            $this->setToken($token);
        }
    }
}
