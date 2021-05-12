<?php

namespace App\Form;

use App\Model\Post;
use App\Model\User;
use App\DAO\UserDAO;
use Framework\Form\AbstractForm;
use App\Validation\PostValidation;
use Framework\Security\TokenStorage;

class PostForm extends AbstractForm
{
    private Post $post;

    /**
     * @var User[]
     */
    private $adminUsers;

    private PostValidation $validation;
    private TokenStorage $tokenStorage;
    private UserDAO $userDAO;

    public function __construct(PostValidation $validation, TokenStorage $tokenStorage, UserDAO $userDAO)
    {
        $this->post = new Post();
        $this->tokenStorage = $tokenStorage;
        $this->validation = $validation;
        $this->userDAO = $userDAO;
        $this->adminUsers = $userDAO->getAllAdmin() ?: [];

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
        return new self($this->validation, $this->tokenStorage, $this->userDAO);
    }

    public function getTitle(): string
    {
        return $this->post->getTitle();
    }

    public function setTitle(string $title): self
    {
        if (null === $this->post->getSlug()) {
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

    /**
     * Used only to display the <select> menu in templates.
     *
     * @return User[]
     */
    public function getAuthors()
    {
        return $this->adminUsers;
    }

    /**
     * Used only to preselect the option in the <select> menu in templates.
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->post->getUser();
    }

    /**
     * Sets error message and invalidate the form if no User is set in Post.
     */
    public function setAuthor(string $uuid): self
    {
        foreach ($this->adminUsers as $user) {
            if ($uuid === $user->getUuid()->toString()) {
                $this->post->setUser($user);

                return $this;
            }
        }

        // if no admin user is found then sets error
        $this->isValid = false;
        $this->errors['author'] = 'Please select the author again if not already pre-selected. ';

        return $this;
    }

    public function getData(): Post
    {
        return $this->post;
    }

    public function hydrateForm(Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
