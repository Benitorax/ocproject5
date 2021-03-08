<?php
namespace Config\Security;

use App\Model\User;

abstract class AbstractToken
{
    private $user;

    public function getUsername()
    {
        if ($this->user instanceof User) {
            return $this->user->getUsername();
        }

        return (string) $this->user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(?User $user)
    {
        if (null === $this->user) {
            $changed = false;
        } else {
            $changed = $this->user !== $user;
        }

        if ($changed) {
            $this->setAuthenticated(false);
        }

        $this->user = $user;
    }

    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    public function setAuthenticated($authenticated)
    {
        $this->authenticated = (bool) $authenticated;
    }
}
