<?php
namespace Config\Security;

use App\Model\User;

class RememberMeToken extends AbstractToken
{
    public function __construct(User $user)
    {
        $this->setUser($user);
        $this->setAuthenticated(false);
    }
}
