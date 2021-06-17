<?php

namespace App\Validation;

use App\Form\LoginForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Constraint\Email;
use Framework\Validation\Constraint\Length;
use Framework\Validation\AbstractValidation;
use Framework\Validation\Constraint\NotBlank;

class LoginValidation extends AbstractValidation
{
    private const EMAIL = [
        NotBlank::class => ['label' => 'email'],
        Length::class => ['min' => 8, 'max' => 50, 'label' => 'email'],
        Email::class => ['label' => 'email']
    ];
    private const PASSWORD = [
        NotBlank::class => ['label' => 'password'],
        Length::class => ['min' => 6, 'max' => 50, 'label' => 'password'],
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var LoginForm $form */
        $form->addError('email', $this->check(self::EMAIL, $form->getEmail()));
        $form->addError('password', $this->check(self::PASSWORD, $form->getPassword()));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
