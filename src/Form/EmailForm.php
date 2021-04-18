<?php

namespace App\Form;

use Framework\Form\AbstractForm;
use App\Validation\EmailValidation;

class EmailForm extends AbstractForm
{
    private string $email = '';

    private EmailValidation $validation;

    public function __construct(EmailValidation $validation)
    {
        $this->validation = $validation;
    }

    public function getValidation(): EmailValidation
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
}
