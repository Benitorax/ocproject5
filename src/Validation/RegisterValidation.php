<?php

namespace App\Validation;

use App\Form\RegisterForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Constraint\Email;
use Framework\Validation\Constraint\IsTrue;
use Framework\Validation\Constraint\Length;
use Framework\Validation\Constraint\Unique;
use Framework\Validation\AbstractValidation;
use Framework\Validation\Constraint\NotBlank;
use Framework\Validation\Constraint\IdenticalTo;

class RegisterValidation extends AbstractValidation
{
    private const EMAIL = [
        NotBlank::class => ['label' => 'email'],
        Length::class => ['min' => 6, 'max' => 50, 'label' => 'email'],
        Email::class => ['label' => 'email'],
        Unique::class => ['tableColumn' => 'user:email', 'label' => 'email']
    ];
    private const PASSWORD1 = [
        NotBlank::class => ['label' => 'password'],
        Length::class => ['min' => 6, 'max' => 50, 'label' => 'password'],
    ];
    private const PASSWORD2 = [
        IdenticalTo::class => ['label' => 'password']
    ];
    private const USERNAME = [
        NotBlank::class => ['label' => 'username'],
        Length::class => ['min' => 3, 'max' => 50, 'label' => 'username'],
        Unique::class => ['tableColumn' => 'user:username', 'label' => 'username']
    ];
    private const TERMS = [
        IsTrue::class => ['message' => 'The box "terms of use" must be checked']
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var RegisterForm $form */
        $form->addError('email', $this->check(self::EMAIL, $form->getEmail()));
        $form->addError('password1', $this->check(self::PASSWORD1, $form->getPassword1()));
        $form->addError('username', $this->check(self::USERNAME, $form->getUsername()));
        $form->addError('terms', $this->check(self::TERMS, $form->getTerms()));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));

        if (!$form->getErrors()['password1']) {
            $form->addError(
                'password2',
                $this->check(self::PASSWORD2, [$form->getPassword1(), $form->getPassword2()])
            );
        }
    }
}
