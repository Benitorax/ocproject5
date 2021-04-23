<?php

namespace App\DAO;

use DateTime;
use stdClass;
use App\Model\Post;
use App\Model\User;
use Ramsey\Uuid\Uuid;
use App\Model\Comment;
use Framework\DAO\AbstractDAO;
use Framework\DAO\QueryExpression;

class CommentDAO extends AbstractDAO
{
    public const SQL_TABLE = 'comment';
    public const SQL_COLUMNS = [
        'id', 'uuid', 'content', 'created_at', 'updated_at', 'is_validated', 'user_id', 'post_id'
    ];

    private QueryExpression $query;

    public function __construct()
    {
        $this->query = new QueryExpression();
    }

    /**
     * Returns a Comment object from stdClass.
     */
    public function buildObject(stdClass $o): Comment
    {
        $post = new Post();
        $user = new User();

        if (!empty($o->u_id)) {
            $user->setId($o->u_id)
            ->setUuid(Uuid::fromString($o->u_uuid))
            ->setEmail($o->u_email)
            ->setPassword($o->u_password)
            ->setUsername($o->u_username)
            ->setCreatedAt(new DateTime($o->u_created_at))
            ->setUpdatedAt(new DateTime($o->u_updated_at))
            ->setRoles(json_decode($o->u_roles))
            ->setIsBlocked($o->u_is_blocked);
        }

        $comment = new Comment();
        $comment->setId($o->c_id)
            ->setUuid(Uuid::fromString($o->c_uuid))
            ->setContent($o->c_content)
            ->setCreatedAt(new DateTime($o->c_created_at))
            ->setUpdatedAt(new DateTime($o->c_updated_at))
            ->setIsValidated($o->c_is_validated)
            ->setUser($user)
            ->setPost($post);

        return $comment;
    }

    /**
     * Get comments by Post id.
     * @return null|Comment[]
     */
    public function getCommentsByPostId(int $postId)
    {
        $this->prepareQuery()
            ->where('post_id = :post_id')
            ->setParameter('post_id', $postId);

        /** @var null|Comment[] */
        $comments = $this->getResult($this, $this->query);

        return $comments;
    }

    /**
     * Sets the select, table and jointure for the sql query.
     */
    private function prepareQuery(): QueryExpression
    {
        return $this->query = (new QueryExpression())
            ->select(self::SQL_COLUMNS, 'c')
            ->addSelect(UserDAO::SQL_COLUMNS, 'u')
            ->addSelect(PostDAO::SQL_COLUMNS, 'p')
            ->from(self::SQL_TABLE, 'c')
            ->leftOuterJoin(UserDAO::SQL_TABLE, 'u', 'user_id = u.id')
            ->addLeftOuterJoin(PostDAO::SQL_TABLE, 'p', 'post_id = p.id')
            ->orderBy('c.updated_at', 'DESC');
    }

    /**
     * Inserts a new row in the database.
     */
    public function add(Comment $comment): void
    {
        $this->insert(self::SQL_TABLE, [
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
