<?php

namespace App\Form;

use App\Model\Post;
use App\Model\User;
use Framework\Form\AbstractForm;
use App\Validation\PostValidation;
use Framework\Security\TokenStorage;

class PostForm extends AbstractForm
{
    private Post $post;

    private PostValidation $validation;
    private TokenStorage $tokenStorage;

    public function __construct(PostValidation $validation, TokenStorage $tokenStorage)
    {
        $this->post = new Post();
        $this->tokenStorage = $tokenStorage;
        $this->validation = $validation;

        if (null !== $token = $tokenStorage->getToken()) {
            if (null !== $user = $token->getUser()) {
                /** @var User $user */
                $this->post->setUser($user);
            }
        }
    }

    public function getValidation(): PostValidation
    {
        return $this->validation;
    }

    public function newInstance(): self
    {
        return new self($this->validation, $this->tokenStorage);
    }

    public function getTitle(): string
    {
        return $this->post->getTitle();
    }

    public function setTitle(string $title): self
    {
        if ($this->post->getCreatedAt()->format('Y-m-d H:i:s') === $this->post->getUpdatedAt()->format('Y-m-d H:i:s')) {
            $this->post->setTitle($title);
        }

        return $this;
    }

    public function getLead(): string
    {
        return $this->post->getLead();
    }

    public function setLead(string $lead): self
    {
        $this->post->setLead($lead);

        return $this;
    }

    public function getContent(): string
    {
        return $this->post->getContent();
    }

    public function setContent(string $content): self
    {
        $this->post->setContent($content);

        return $this;
    }

    public function getIsPublished(): bool
    {
        return $this->post->getIsPublished();
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->post->setIsPublished($isPublished);

        return $this;
    }

    public function getData(): Post
    {
        return $this->post;
    }

    public function setData(Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
