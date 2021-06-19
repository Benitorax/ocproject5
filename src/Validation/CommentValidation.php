<?php

namespace App\Validation;

use App\Form\CommentForm;
use Framework\Form\AbstractForm;
use Framework\Validation\AbstractValidation;
use Framework\Validation\Constraint\Length;
use Framework\Validation\Constraint\NotBlank;

class CommentValidation extends AbstractValidation
{
    public const CONTENT = [
        NotBlank::class => [],
        Length::class => ['min' => 10, 'max' => 1000]
    ];

    public function validate(AbstractForm $form): void
    {
        /** @var CommentForm $form */
        $form->addError('content', $this->check(self::CONTENT, $form->getContent()));
        $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
    }
}
