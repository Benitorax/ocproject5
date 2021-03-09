<?php
namespace Config\Security;

use App\Model\User;
use Config\Session\Session;

class TokenStorage
{
    private $token;

    public function getToken()
    {
        return $this->token;
    }

    public function setToken(AbstractToken $token = null)
    {
        $this->initializer = null;
        $this->token = $token;
    }

    public function reset()
    {
        $this->setToken(null);
    }

    public function setUserFromSession(Session $session)
    {
        $user = $session->get('user');

        if ($user instanceof User) {
            $token = new PreAuthenticatedToken($user);
            $this->setToken($token);
        }
    }
}
