<?php

namespace App\Model;

use Framework\Security\User\UserInterface;

class User implements UserInterface
{
    use IdentifierTrait;
    use TimestampTrait;

    private string $email = '';
    private string $password = '';
    private string $username = '';
    private array $roles = ['user'];
    private bool $isBlocked = false;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string|array $roles
     */
    public function addRoles($roles): self
    {
        if (is_string($roles)) {
            $this->roles[] = $roles;

            return $this;
        }

        foreach ($roles as $role) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function getIsBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }
}
