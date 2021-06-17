<?php

namespace App\Validation;

use App\Form\EmailForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Constraint\Email;
use Framework\Validation\Constraint\Length;
use Framework\Validation\AbstractValidation;
use Framework\Validation\Constraint\NotBlank;

class EmailValidation extends AbstractValidation
{
    private const EMAIL = [
        NotBlank::class => ['label' => 'email'],
        Length::class => ['min' => 8, 'max' => 50, 'label' => 'email'],
        Email::class => ['label' => 'email']
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var EmailForm $form */
        $form->addError('email', $this->check(self::EMAIL, $form->getEmail()));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
