<?php

namespace App\Form;

use DateTime;
use Framework\Form\AbstractForm;
use Framework\Security\TokenStorage;
use App\Validation\ContactValidation;
use Framework\Security\User\UserInterface;

class ContactForm extends AbstractForm
{
    private TokenStorage $tokenStorage;
    private UserInterface $user;
    private string $subject = '';
    private string $content = '';
    private DateTime $createdAt;

    private ContactValidation $validation;

    public function __construct(ContactValidation $validation, TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->validation = $validation;
        $this->createdAt = new DateTime('now');

        if (null !== $token = $tokenStorage->getToken()) {
            if (null !== $user = $token->getUser()) {
                $this->user = $user;
            }
        }
    }


    public function getValidation(): ContactValidation
    {
        return $this->validation;
    }

    public function newInstance(): self
    {
        return new self($this->validation, $this->tokenStorage);
    }

    public function clear(): void
    {
        $this->subject = '';
        $this->content = '';
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

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

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
