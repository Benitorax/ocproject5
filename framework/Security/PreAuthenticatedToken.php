<?php

namespace Framework\Security;

use Framework\Security\AbstractToken;
use Framework\Security\User\UserInterface;

/**
 * This token is generated from session.
 */
class PreAuthenticatedToken extends AbstractToken
{
    public function __construct(?UserInterface $user)
    {
        $this->setUser($user);
        $this->setAuthenticated(true);
    }
}
