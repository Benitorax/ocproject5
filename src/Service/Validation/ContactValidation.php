<?php

namespace App\Service\Validation;

use App\Form\ContactForm;
use App\Form\AbstractForm;
use App\Service\Validation\Validation;

class ContactValidation extends Validation
{
    private const SUBJECT = [
        ['notBlank'],
        ['minLength', 5],
        ['maxLength', 50]
    ];
    private const CONTENT = [
        ['notBlank'],
        ['minLength', 20],
        ['maxLength', 1500],
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var ContactForm $form */
        $form->addError('subject', $this->check(self::SUBJECT, $form->getSubject(), 'subject'));
        $form->addError('content', $this->check(self::CONTENT, $form->getContent(), 'content'));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
