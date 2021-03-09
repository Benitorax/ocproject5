<?php
namespace App\DAO;

use App\Model\User;
use App\Model\Comment;
use Config\DAO\AbstractDAO;
use Config\DAO\DAOInterface;

class CommentDAO extends AbstractDAO implements DAOInterface
{
    const SQL_SELECT = 'SELECT id, text, created_at, updated_at, is_validated, user_id, post_id FROM comment';

    public function buildObject(\stdClass $object): Comment
    {
        $comment = new Comment();
        $comment->setId($object->id)
            ->setText($object->text)
            ->setCreatedAt(new \DateTime($object->created_at))
            ->setUpdatedAt(new \DateTime($object->updated_at))
            ->setIsValidated($object->is_validated)
            ->setUser($object->user_id)
            ->setPost($object->post_id);

        return $comment;
    }

    public function getOneBy(array $parameters): User
    {
        return $this->selectOneResultBy(self::SQL_SELECT, $parameters, $this);
    }

    public function getBy(array $parameters): array
    {
        return $this->selectResultBy(self::SQL_SELECT, $parameters, $this);
    }

    public function getAll(): array
    {
        return $this->selectAll(self::SQL_SELECT, $this);
    }

    public function add(Comment $comment)
    {
        $sql = 'INSERT INTO user (id, text, created_at, updated_at, is_validated, user_id, post_id)'
            .'VALUES (:id, :text, :created_at, :updated_at, :is_validated, :user_id, :post_id)';
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
