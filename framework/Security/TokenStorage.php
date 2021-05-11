<?php

namespace Framework\Security;

use Framework\Security\User\UserInterface;

/**
 * Contains an user object if the user is authenticated
 */
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

    public function setUser(UserInterface $user): void
    {
        $token = new PreAuthenticatedToken($user);
        $this->setToken($token);
    }
}
