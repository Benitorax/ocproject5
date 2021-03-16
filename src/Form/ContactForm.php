<?php

namespace App\Form;

use DateTime;
use App\Model\User;
use App\Form\AbstractForm;
use App\Service\Validation\ContactValidation;

class ContactForm extends AbstractForm
{
    private User $user;
    private string $subject = '';
    private string $content = '';
    private DateTime $createdAt;

    private ContactValidation $validation;

    public function __construct(ContactValidation $validation, ?User $user)
    {
        $this->validation = $validation;
        $this->createdAt = new DateTime('now');

        if (null !== $user) {
            $this->user = $user;
        }
    }

    public function getValidation(): ContactValidation
    {
        return $this->validation;
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
