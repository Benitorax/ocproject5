<?php
namespace Config\Security;

use App\Model\User;

abstract class AbstractToken
{
    private ?User $user = null;
    private bool $authenticated = false;

    public function getUsername(): string
    {
        return $this->user->getUsername();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
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

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    public function setAuthenticated(bool $authenticated): void
    {
        $this->authenticated = $authenticated;
    }
}
