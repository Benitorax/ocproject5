<?php

namespace App\Validation;

use App\Form\ResetPasswordForm;
use Framework\Form\AbstractForm;
use Framework\Validation\AbstractValidation;

class ResetPasswordValidation extends AbstractValidation
{
    private const PASSWORD1 = [
        ['notBlank'],
        ['minLength', 6],
        ['maxLength', 50]
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var ResetPasswordForm $form */
        $form->addError('password1', $this->check(self::PASSWORD1, $form->getPassword1(), 'password'));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));

        if (!$form->getErrors()['password1']) {
            $form->addError(
                'password2',
                $this->checkIdentical($form->getPassword1(), $form->getPassword2(), 'password')
            );
        }
    }
}
