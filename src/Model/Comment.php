<?php

namespace App\Model;

use App\Model\Post;
use App\Model\User;
use App\Model\TimestampTrait;

class Comment
{
    use TimestampTrait;

    public const SQL_COLUMNS = ['id', 'content', 'created_at', 'updated_at', 'is_validated', 'user_id', 'post_id'];

    private string $id;
    private string $content;
    private bool $isValidated;
    private User $user;
    private Post $post;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

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

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
