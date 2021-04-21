<?php

namespace App\Service;

use DateTime;
use App\Model\Post;
use App\Model\User;
use Ramsey\Uuid\Uuid;
use App\Model\Comment;
use App\DAO\CommentDAO;
use Framework\Security\TokenStorage;
use Framework\Security\AbstractToken;

class CommentManager
{
    private CommentDAO $commentDAO;
    private User $user;

    public function __construct(CommentDAO $commentDAO, TokenStorage $tokenStorage)
    {
        $this->commentDAO = $commentDAO;

        if (null !== $token = $tokenStorage->getToken()) {
            if (null !== $user = $token->getUser()) {
                /** @var User $user */
                $this->user = $user;
            }
        }
    }

    public function manageNewComment(Comment $comment, Post $post): Comment
    {
        $dateTime = new DateTime('now');
        $comment->setUuid(Uuid::uuid4())
            ->setPost($post)
            ->setUser($this->user)
            ->setUpdatedAt($dateTime)
            ->setCreatedAt($dateTime);

        if (in_array('admin', $this->user->getRoles())) {
            $comment->setIsValidated(true);
        }

        $this->commentDAO->add($comment);

        return $comment;
    }
}
