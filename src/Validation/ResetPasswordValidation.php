<?php

namespace App\Validation;

use App\Form\ResetPasswordForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Constraint\Length;
use Framework\Validation\AbstractValidation;
use Framework\Validation\Constraint\NotBlank;
use Framework\Validation\Constraint\IdenticalTo;

class ResetPasswordValidation extends AbstractValidation
{
    private const PASSWORD1 = [
        NotBlank::class => ['label' => 'password'],
        Length::class => ['min' => 6, 'max' => 50, 'label' => 'password'],
    ];
    private const PASSWORD2 = [
        IdenticalTo::class => ['label' => 'password']
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var ResetPasswordForm $form */
        $form->addError('password1', $this->check(self::PASSWORD1, $form->getPassword1()));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));

        if (!$form->getErrors()['password1']) {
            $form->addError(
                'password2',
                $this->check(self::PASSWORD2, [$form->getPassword1(), $form->getPassword2()])
            );
        }
    }
}
