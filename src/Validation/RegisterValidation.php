<?php

namespace App\Validation;

use App\Form\RegisterForm;
use Framework\Form\AbstractForm;
use Framework\Validation\AbstractValidation;

class RegisterValidation extends AbstractValidation
{
    private const EMAIL = [
        ['notBlank'],
        ['minLength', 8],
        ['maxLength', 70],
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

    public function validate(AbstractForm $form): void
    {
        /** @var RegisterForm $form */
        $form->addError('email', $this->check(self::EMAIL, $form->getEmail(), 'email'));
        $form->addError('password1', $this->check(self::PASSWORD1, $form->getPassword1(), 'password'));
        $form->addError('username', $this->check(self::USERNAME, $form->getUsername(), 'username'));
        $form->addError('terms', $this->check(self::TERMS, $form->getTerms(), 'terms of use'));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));

        if (!$form->getErrors()['password1']) {
            $form->addError(
                'password2',
                $this->checkIdentical($form->getPassword1(), $form->getPassword2(), 'password')
            );
        }
    }
}
