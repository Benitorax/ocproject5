<?php

namespace App\Validation;

use App\Form\CommentForm;
use Framework\Form\AbstractForm;
use Framework\Validation\AbstractValidation;

class CommentValidation extends AbstractValidation
{
    private const CONTENT = [
        ['notBlank'],
        ['minLength', 10],
        ['maxLength', 1000],
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var CommentForm $form */
        $form->addError('content', $this->check(self::CONTENT, $form->getContent()));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
