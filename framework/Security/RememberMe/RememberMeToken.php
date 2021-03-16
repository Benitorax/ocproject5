<?php

namespace Framework\Security\RememberMe;

use Framework\Security\AbstractToken;
use Framework\Security\User\UserInterface;

/**
 * This token is generated from the remember me cookie.
 */
class RememberMeToken extends AbstractToken
{
    public function __construct(UserInterface $user)
    {
        $this->setUser($user);
        $this->setAuthenticated(true);
    }
}
