<?php

namespace App\Form;

use App\Model\User;
use Framework\Form\AbstractForm;
use App\Validation\RegisterValidation;

class RegisterForm extends AbstractForm
{
    private User $user;
    private string $password2 = '';
    private bool $terms = false;

    private RegisterValidation $validation;

    public function __construct(RegisterValidation $validation)
    {
        $this->user = new User();
        $this->validation = $validation;
    }

    public function getValidation(): RegisterValidation
    {
        return $this->validation;
    }

    public function newInstance(): self
    {
        return new self($this->validation);
    }

    public function getEmail(): string
    {
        return $this->user->getEmail();
    }

    public function setEmail(string $email): self
    {
        $this->user->setEmail($email);

        return $this;
    }

    public function getPassword1(): string
    {
        return $this->user->getPassword();
    }

    public function setPassword1(string $password): self
    {
        $this->user->setPassword($password);

        return $this;
    }

    public function getPassword2(): string
    {
        return $this->password2;
    }

    public function setPassword2(string $password): self
    {
        $this->password2 = $password;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->user->getUsername();
    }

    public function setUsername(string $username): self
    {
        $this->user->setUsername($username);

        return $this;
    }

    public function getTerms(): bool
    {
        return $this->terms;
    }

    public function setTerms(bool $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    public function getData(): User
    {
        return $this->user;
    }
}
