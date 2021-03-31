<?php

namespace App\Validation;

use App\Form\PostForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Validation;

class PostValidation extends Validation
{
    private const TITLE = [
        ['notBlank'],
        ['minLength', 10],
        ['maxLength', 100],
    ];
    private const LEAD = [
        ['notBlank'],
        ['minLength', 50],
        ['maxLength', 300],
    ];
    private const CONTENT = [
        ['notBlank'],
        ['minLength', 100],
        ['maxLength', 1500],
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var PostForm $form */
        $form->addError('title', $this->check(self::TITLE, $form->getTitle(), 'title'));
        $form->addError('lead', $this->check(self::LEAD, $form->getLead(), 'lead'));
        $form->addError('content', $this->check(self::CONTENT, $form->getContent(), 'content'));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
