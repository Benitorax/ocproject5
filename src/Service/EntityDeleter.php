<?php

namespace App\Service;

use App\Model\Post;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DAO\CommentDAO;

/**
 * Manages the deletion of entities and any relationships.
 */
class EntityDeleter
{
    private UserDAO $userDAO;
    private PostDAO $postDAO;
    private CommentDAO $commentDAO;

    public function __construct(
        UserDAO $userDAO,
        PostDAO $postDAO,
        CommentDAO $commentDAO
    ) {
        $this->userDAO = $userDAO;
        $this->postDAO = $postDAO;
        $this->commentDAO = $commentDAO;
    }

    /**
     * Deletes comment by uuid.
     */
    public function deleteCommentByUuid(string $uuid): void
    {
        $this->commentDAO->deleteByUuid($uuid);
    }

    /**
     * Deletes post and its comments by post id.
     */
    public function deletePostByUuid(string $uuid): void
    {
        $post = $this->postDAO->getOneByUuid($uuid);

        if ($post instanceof Post) {
            $this->commentDAO->deleteByPostId($post->getId());
            $this->postDAO->deleteById($post->getId());
        }
    }

    /**
     * Deletes user in database by id.
     */
    public function deleteUserByUuid(string $uuid): void
    {
        $user = $this->userDAO->getOneByUuid($uuid);

        if (!$user instanceof User) {
            return;
        }

        $this->commentDAO->setAuthorToNull($user);

        // if User is admin then set author to null in user's Posts
        if (in_array('admin', $user->getRoles())) {
            $this->postDAO->setAuthorToNull($user);
        }

        $this->userDAO->deleteUser($user);
    }
}
