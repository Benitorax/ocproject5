<?php

namespace App\Service\Validation;

use App\Form\LoginForm;
use App\Form\AbstractForm;
use App\Service\Validation\Validation;

class LoginValidation extends Validation
{
    private const EMAIL = [
        ['notBlank'],
        ['minLength', 8],
        ['maxLength', 50],
        ['email']
    ];
    private const PASSWORD = [
        ['notBlank'],
        ['minLength', 6],
        ['maxLength', 50]
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var LoginForm $form */
        $form->addError('email', $this->check(self::EMAIL, $form->getEmail(), 'email'));
        $form->addError('password', $this->check(self::PASSWORD, $form->getPassword(), 'password'));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
