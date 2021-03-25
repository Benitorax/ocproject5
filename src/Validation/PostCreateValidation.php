<?php

namespace App\Validation;

use App\Form\PostCreateForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Validation;

class PostCreateValidation extends Validation
{
    private const TITLE = [
        ['notBlank'],
        ['minLength', 10],
        ['maxLength', 100],
    ];
    private const LEAD = [
        ['notBlank'],
        ['minLength', 50],
        ['maxLength', 255],
    ];
    private const CONTENT = [
        ['notBlank'],
        ['minLength', 100],
        ['maxLength', 1500],
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var PostCreateForm $form */
        $form->addError('title', $this->check(self::TITLE, $form->getTitle(), 'title'));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));

        if ($form->getIsPublished()) {
            $form->addError('lead', $this->check(self::LEAD, $form->getLead(), 'lead'));
            $form->addError('content', $this->check(self::CONTENT, $form->getContent(), 'content'));
        }
    }
}
