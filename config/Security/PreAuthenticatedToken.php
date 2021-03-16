<?php

namespace Config\Security;

use App\Model\User;
use Config\Security\AbstractToken;

/**
 * This token is generated from session.
 */
class PreAuthenticatedToken extends AbstractToken
{
    public function __construct(?User $user)
    {
        $this->setUser($user);
        $this->setAuthenticated(true);
    }
}
