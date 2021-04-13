<?php

namespace App\Form;

use Framework\Form\AbstractForm;
use App\Validation\ResetPasswordValidation;

class ResetPasswordForm extends AbstractForm
{
    private string $password1 = '';
    private string $password2 = '';

    private ResetPasswordValidation $validation;

    public function __construct(ResetPasswordValidation $validation)
    {
        $this->validation = $validation;
    }

    public function getValidation(): ResetPasswordValidation
    {
        return $this->validation;
    }

    public function newInstance(): self
    {
        return new self($this->validation);
    }

    public function getPassword1(): string
    {
        return $this->password1;
    }

    public function setPassword1(string $password1): self
    {
        $this->password1 = $password1;

        return $this;
    }

    public function getPassword2(): string
    {
        return $this->password2;
    }

    public function setPassword2(string $password2): self
    {
        $this->password2 = $password2;

        return $this;
    }
}
