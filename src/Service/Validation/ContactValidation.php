<?php

namespace App\Service\Validation;

use App\Form\ContactForm;
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
    private const TERMS = [
        ['checkbox', true]
    ];

    public function validate(ContactForm $form): void
    {
        $form->errors['subject'] = $this->check(self::SUBJECT, $form->subject, 'subject');
        $form->errors['content'] = $this->check(self::CONTENT, $form->content, 'content');
        $form->errors['csrf'] = $this->checkCsrfToken($form->csrfToken);

        if (!$this->hasErrorMessages($form)) {
            $form->isValid = true;
        }
    }
}
