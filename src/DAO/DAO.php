<?php
namespace App\DAO;

use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DAO\CommentDAO;

class DAO
{
    private $userDAO;
    private $postDAO;
    private $commentDAO;

    public function __construct(UserDAO $userDAO, PostDAO $postDAO, CommentDAO $commentDAO)
    {
        $this->userDAO = $userDAO;
        $this->postDAO = $postDAO;
        $this->commentDAO = $commentDAO;
    }

    public function getUserDAO()
    {
        return $this->userDAO;
    }

    public function getPostDAO()
    {
        return $this->postDAO;
    }

    public function getCommentDAO()
    {
        return $this->commentDAO;
    }
}