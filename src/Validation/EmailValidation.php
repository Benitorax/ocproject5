<?php

namespace App\Validation;

use App\Form\EmailForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Validation;

class EmailValidation extends Validation
{
    private const EMAIL = [
        ['notBlank'],
        ['minLength', 8],
        ['maxLength', 50],
        ['email']
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var EmailForm $form */
        $form->addError('email', $this->check(self::EMAIL, $form->getEmail(), 'email'));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
