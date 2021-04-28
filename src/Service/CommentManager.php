<?php

namespace App\Service;

use DateTime;
use App\Model\Post;
use App\Model\User;
use Ramsey\Uuid\Uuid;
use App\Model\Comment;
use App\DAO\CommentDAO;
use App\Service\Pagination\Paginator;
use Framework\Security\TokenStorage;

class CommentManager
{
    private CommentDAO $commentDAO;
    private Paginator $paginator;
    private User $user;

    public function __construct(
        CommentDAO $commentDAO,
        Paginator $paginator,
        TokenStorage $tokenStorage
    ) {
        $this->commentDAO = $commentDAO;
        $this->paginator = $paginator;

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

    /**
     * Returns Paginator
     */
    public function getPaginationForCommentsToValidate(int $pageNumber): Paginator
    {
        // sets the query for the pagination
        $this->commentDAO->setCommentsToValidateQuery();

        // creates the pagination for the template
        return $this->paginator->paginate(
            $this->commentDAO,
            $pageNumber < 1 ? 1 : $pageNumber,
            15
        );
    }
}
