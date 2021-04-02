<?php

namespace App\DAO;

use DateTime;
use Ramsey\Uuid\Uuid;
use App\Model\Comment;
use Framework\DAO\AbstractDAO;
use Framework\DAO\QueryExpression;

class CommentDAO extends AbstractDAO
{
    public const SQL_TABLE = 'comment';
    public const SQL_COLUMNS = ['id', 'uuid', 'content', 'created_at', 'updated_at', 'is_validated', 'user_id', 'post_id'];

    private QueryExpression $query;

    public function __construct()
    {
        $this->query = new QueryExpression();
    }

    /**
     * Returns a Comment object from stdClass.
     */
    public function buildObject(\stdClass $o): Comment
    {
        $comment = new Comment();
        $comment->setId($o->c_id)
            ->setUuid(Uuid::fromString($o->c_uuid))
            ->setContent($o->c_content)
            ->setCreatedAt(new DateTime($o->c_created_at))
            ->setUpdatedAt(new DateTime($o->c_updated_at))
            ->setIsValidated($o->c_is_validated)
            ->setUser($o->c_user_id)
            ->setPost($o->c_post_id);

        return $comment;
    }

    /**
     * Inserts a new row in the database.
     */
    public function add(Comment $comment): void
    {
        $this->insert('comment', [
            'id' => $comment->getId(),
            'uuid' => $comment->getUuid(),
            'content' => $comment->getContent(),
            'created_at' => ($comment->getCreatedAt())->format('Y-m-d H:i:s'),
            'updated_at' => ($comment->getUpdatedAt())->format('Y-m-d H:i:s'),
            'is_validated' => intval($comment->getIsValidated()),
            'user_id' => $comment->getUser()->getId(),
            'post_id' => $comment->getPost()->getId()
        ]);
    }
}
