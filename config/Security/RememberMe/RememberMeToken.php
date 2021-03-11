<?php
namespace Config\Security\RememberMe;

use App\Model\User;
use Config\Security\AbstractToken;

class RememberMeToken extends AbstractToken
{
    public function __construct(User $user)
    {
        $this->setUser($user);
        $this->setAuthenticated(false);
    }
}
