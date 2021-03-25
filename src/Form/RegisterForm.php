<?php

namespace App\Form;

use Framework\Form\AbstractForm;
use App\Validation\RegisterValidation;

class RegisterForm extends AbstractForm
{
    public string $email = '';
    public string $password1 = '';
    public string $password2 = '';
    public string $username = '';
    public bool $terms = false;

    private RegisterValidation $validation;

    public function __construct(RegisterValidation $validation)
    {
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
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword1(): string
    {
        return $this->password1;
    }

    public function setPassword1(string $password): self
    {
        $this->password1 = $password;

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
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
}
