<?php

namespace App\Validation;

use App\Form\PostForm;
use Framework\Form\AbstractForm;
use Framework\Validation\Constraint\Length;
use Framework\Validation\AbstractValidation;
use Framework\Validation\Constraint\NotBlank;

class PostValidation extends AbstractValidation
{
    private const TITLE = [
        NotBlank::class => ['label' => 'title'],
        Length::class => ['min' => 10, 'max' => 150, 'label' => 'title'],
    ];
    private const LEAD = [
        NotBlank::class => ['label' => 'lead'],
        Length::class => ['min' => 50, 'max' => 300, 'label' => 'lead'],
    ];
    private const CONTENT = [
        NotBlank::class => ['label' => 'content'],
        Length::class => ['min' => 100, 'max' => 1500, 'label' => 'content'],
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var PostForm $form */
        $form->addError('title', $this->check(self::TITLE, $form->getTitle()));
        $form->addError('lead', $this->check(self::LEAD, $form->getLead()));
        $form->addError('content', $this->check(self::CONTENT, $form->getContent()));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
