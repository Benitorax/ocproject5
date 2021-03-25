<?php

namespace App\Form;

use Framework\Form\AbstractForm;
use App\Validation\LoginValidation;

class LoginForm extends AbstractForm
{
    private string $email = '';
    private string $password = '';
    private bool $rememberme = false;

    private LoginValidation $validation;

    public function __construct(LoginValidation $validation)
    {
        $this->validation = $validation;
    }

    public function getValidation(): LoginValidation
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRememberme(): bool
    {
        return $this->rememberme;
    }

    public function setRememberme(bool $rememberme): self
    {
        $this->rememberme = $rememberme;

        return $this;
    }
}
