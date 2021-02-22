<?php
namespace App\DAO;

use App\Model\User;
use App\Model\Comment;
use Config\DAO\AbstractDAO;

class CommentDAO extends AbstractDAO
{
    private function buildObject($row): Comment
    {
        $comment = new Comment();
        $comment->setId($row['id'])
            ->setText($row['text'])
            ->setCreatedAt($row['created_at'])
            ->setUpdatedAt($row['updated_at'])
            ->setIsValidated($row['is_validated'])
            ->setUser($row['user_id'])
            ->setPost($row['post_id']);

        return $comment;
    }

    public function getAll()
    {
        $sql = 'SELECT id, text, created_at, updated_at, is_validated, user_id, post_id FROM comment ORDER BY id DESC';
        $result = $this->createQuery($sql);
        $comments = [];
        foreach ($result as $row){
            $commentId = $row['id'];
            $comments[$commentId] = $this->buildObject($row);
        }
        $result->closeCursor();

        return $comments;
    }

    public function getById($commentId): Comment
    {
        $sql = 'SELECT id, text, created_at, updated_at, is_validated, user_id, post_id FROM comment ORDER BY id DESC';
        $result = $this->createQuery($sql, [$commentId]);
        $comment = $result->fetch();
        $result->closeCursor();

        return $this->buildObject($comment);
    }

    public function add(Comment $comment)
    {
        $sql = 'INSERT INTO user (id, text, created_at, updated_at, is_validated, user_id, post_id) 
            VALUES (:id, :text, :created_at, :updated_at, :is_validated, :user_id, :post_id)';
        $this->createQuery($sql, [
            'id' => $comment->getId(),
            'text' => $comment->getText(),
            'created_at' => ($comment->getCreatedAt())->format('Y-m-d H:i:s'),
            'updated_at' => ($comment->getUpdatedAt())->format('Y-m-d H:i:s'),
            'is_validated' => intval($comment->getIsValidated()),
            'user_id' => $comment->getUser()->getId(),
            'post_id' => $comment->getPost()->getId()
        ]);
    }
}