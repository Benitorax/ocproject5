<?php

namespace Config\Security\RememberMe;

use App\Model\User;
use Config\Security\AbstractToken;

/**
 * This token is generated from the remember me cookie.
 */
class RememberMeToken extends AbstractToken
{
    public function __construct(User $user)
    {
        $this->setUser($user);
        $this->setAuthenticated(true);
    }
}
