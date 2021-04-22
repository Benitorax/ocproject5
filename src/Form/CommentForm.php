<?php

namespace App\Form;

use App\Model\Comment;
use Framework\Form\AbstractForm;
use App\Validation\CommentValidation;

class CommentForm extends AbstractForm
{
    private Comment $comment;
    private CommentValidation $validation;

    public function __construct(CommentValidation $validation)
    {
        $this->validation = $validation;
        $this->comment = new Comment();
    }

    public function getValidation(): CommentValidation
    {
        return $this->validation;
    }

    public function newInstance(): self
    {
        return new self($this->validation);
    }

    public function getData(): Comment
    {
        return $this->comment;
    }

    public function getContent(): string
    {
        return $this->comment->getContent();
    }

    public function setContent(string $content): self
    {
        $this->comment->setContent($content);

        return $this;
    }

    public function clear(): void
    {
        $this->comment->setContent('');
    }
}
