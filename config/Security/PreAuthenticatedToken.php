<?php

namespace Config\Security;

use App\Model\User;
use Config\Security\AbstractToken;

class PreAuthenticatedToken extends AbstractToken
{
    public function __construct(?User $user)
    {
        $this->setUser($user);
        $this->setAuthenticated(true);
    }
}
