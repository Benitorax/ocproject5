<?php

namespace App\Form;

use DateTime;
use App\Model\TimestampTrait;
use App\Model\User;
use Framework\Form\AbstractForm;
use Framework\Security\TokenStorage;
use App\Validation\PostCreateValidation;

class PostCreateForm extends AbstractForm
{
    use TimestampTrait;

    private string $title = '';
    private string $lead = '';
    private string $content = '';
    private bool $isPublished = false;
    private User $user;

    private PostCreateValidation $validation;
    private TokenStorage $tokenStorage;

    public function __construct(PostCreateValidation $validation, TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->validation = $validation;

        $dateTime = new DateTime('now');
        $this->createdAt = $dateTime;
        $this->updatedAt = $dateTime;

        if (null !== $token = $tokenStorage->getToken()) {
            if (null !== $user = $token->getUser()) {
                /** @var User $user */
                $this->user = $user;
            }
        }
    }

    public function getValidation(): PostCreateValidation
    {
        return $this->validation;
    }

    public function newInstance(): self
    {
        return new self($this->validation, $this->tokenStorage);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLead(): string
    {
        return $this->lead;
    }

    public function setLead(string $lead): self
    {
        $this->lead = $lead;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
