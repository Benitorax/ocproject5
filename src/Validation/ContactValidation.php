<?php

namespace App\Validation;

use App\Form\ContactForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Constraint\Length;
use Framework\Validation\AbstractValidation;
use Framework\Validation\Constraint\NotBlank;

class ContactValidation extends AbstractValidation
{
    private const SUBJECT = [
        NotBlank::class => ['label' => 'subject'],
        Length::class => ['min' => 5, 'max' => 50, 'label' => 'subject']
    ];
    private const CONTENT = [
        NotBlank::class => ['label' => 'content'],
        Length::class => ['min' => 20, 'max' => 1500, 'label' => 'content']
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var ContactForm $form */
        $form->addError('subject', $this->check(self::SUBJECT, $form->getSubject()));
        $form->addError('content', $this->check(self::CONTENT, $form->getContent()));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
