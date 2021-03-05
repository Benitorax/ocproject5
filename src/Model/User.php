<?php
namespace App\Model;

class User
{
    use TimestampTrait;
    
    private string $id;
    private string $email;
    private string $password;
    private string $username;
    private bool $isAdmin = false;
    private bool $isBlocked = false;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername($username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getIsBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked($isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }
}
