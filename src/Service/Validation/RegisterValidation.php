<?php

namespace App\Service\Validation;

use App\Form\RegisterForm;
use App\Service\Validation\Validation;

class RegisterValidation extends Validation
{
    private const EMAIL = [
        ['notBlank'],
        ['minLength', 8],
        ['maxLength', 50],
        ['email'],
        ['unique', 'user:email']
    ];
    private const PASSWORD1 = [
        ['notBlank'],
        ['minLength', 6],
        ['maxLength', 50]
    ];
    private const USERNAME = [
        ['notBlank'],
        ['minLength', 3],
        ['maxLength', 50],
        ['unique', 'user:username']
    ];
    private const TERMS = [
        ['checkbox', true]
    ];

    public function validate(RegisterForm $form): void
    {
        $form->errors['email'] = $this->check(self::EMAIL, $form->email, 'email');
        $form->errors['password1'] = $this->check(self::PASSWORD1, $form->password1, 'password');
        $form->errors['username'] = $this->check(self::USERNAME, $form->username, 'username');
        $form->errors['terms'] = $this->check(self::TERMS, $form->terms, 'terms of use');
        $form->errors['csrf'] = $this->checkCsrfToken($form->csrfToken);

        if (!$form->errors['password1']) {
            $form->errors['password2'] = $this->checkIdentical($form->password1, $form->password2, 'password');
        }

        if (!$this->hasErrorMessages($form)) {
            $form->isValid = true;
        }
    }
}
