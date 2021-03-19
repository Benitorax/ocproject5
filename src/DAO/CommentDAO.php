<?php

namespace App\DAO;

use DateTime;
use App\Model\Comment;
use App\Service\Pagination\PaginationDAOInterface;
use Framework\DAO\AbstractDAO;

class CommentDAO extends AbstractDAO implements PaginationDAOInterface
{
    private string $sqlSelect;

    public function __construct(SQLGenerator $sqlGenerator)
    {
        $this->sqlSelect =  'SELECT ' . $sqlGenerator->generateStringWithAlias('c', Comment::SQL_COLUMNS)
                            . ' From Comment c';
    }

    public function buildObject(\stdClass $o): Comment
    {
        $comment = new Comment();
        $comment->setId($o->c_id)
            ->setContent($o->c_content)
            ->setCreatedAt(new DateTime($o->c_created_at))
            ->setUpdatedAt(new DateTime($o->c_updated_at))
            ->setIsValidated($o->c_is_validated)
            ->setUser($o->c_user_id)
            ->setPost($o->c_post_id);

        return $comment;
    }

    /**
     * @return null|object|Comment the object is instance of Comment class
     */
    public function getOneBy(array $parameters)
    {
        return $this->selectOneResultBy($this, $this->sqlSelect, $parameters);
    }

    /**
     * @return null|object[]|Comment[] Array of comments
     */
    public function getBy(array $parameters, array $orderBy = [], array $limit = [])
    {
        return $this->selectResultBy($this, $this->sqlSelect, $parameters);
    }

    /**
     * @return null|object[]|Comment[] Array of all comments
     */
    public function getAll()
    {
        return $this->selectAll($this, $this->sqlSelect);
    }

    public function add(Comment $comment): void
    {
        $this->insert('comment', [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'created_at' => ($comment->getCreatedAt())->format('Y-m-d H:i:s'),
            'updated_at' => ($comment->getUpdatedAt())->format('Y-m-d H:i:s'),
            'is_validated' => intval($comment->getIsValidated()),
            'user_id' => $comment->getUser()->getId(),
            'post_id' => $comment->getPost()->getId()
        ]);
    }

    /**
     * Returns the total count of comments.
     */
    public function getCountBy(array $parameters): int
    {
        $sql = 'SELECT COUNT(*) FROM comment';
        $stmt = $this->createQuery($sql, $parameters);
        $result = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int) $result;
    }
}
