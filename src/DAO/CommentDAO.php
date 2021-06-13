<?php

namespace App\DAO;

use DateTime;
use stdClass;
use App\Model\Post;
use App\Model\User;
use Ramsey\Uuid\Uuid;
use App\Model\Comment;
use Framework\DAO\Connection;
use Framework\DAO\AbstractDAO;
use Framework\DAO\QueryExpression;
use App\Service\Pagination\PaginationDAOInterface;

class CommentDAO extends AbstractDAO implements PaginationDAOInterface
{
    public const SQL_TABLE = 'comment';
    public const SQL_COLUMNS = [
        'id', 'uuid', 'content', 'created_at', 'updated_at', 'is_validated', 'user_id', 'post_id'
    ];

    private QueryExpression $query;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * Returns a Comment object from stdClass.
     */
    public function buildObject(stdClass $o): Comment
    {
        $post = new Post();
        $post->setId($o->p_id)
        ->setUuid(Uuid::fromString($o->p_uuid))
        ->setTitle($o->p_title)
        ->setSlug($o->p_slug)
        ->setLead($o->p_lead)
        ->setContent($o->p_content)
        ->setCreatedAt(new DateTime($o->p_created_at))
        ->setUpdatedAt(new DateTime($o->p_updated_at))
        ->setIsPublished($o->p_is_published)
        ->setUser(new User());

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
                ->setIsBlocked($o->u_is_blocked)
            ;
        }

        $comment = new Comment();
        $comment->setId($o->c_id)
            ->setUuid(Uuid::fromString($o->c_uuid))
            ->setContent($o->c_content)
            ->setCreatedAt(new DateTime($o->c_created_at))
            ->setUpdatedAt(new DateTime($o->c_updated_at))
            ->setIsValidated($o->c_is_validated)
            ->setUser($user)
            ->setPost($post)
        ;

        return $comment;
    }

    public function setCommentsToValidateQuery(): void
    {
        $this->prepareQuery()
            ->where('is_validated IS FALSE');
    }

    /**
     * Get comments which haven't been validated yet.
     * @return null|Comment[]
     */
    public function getCommentsToValidate()
    {
        $this->setCommentsToValidateQuery();

        /** @var null|Comment[] */
        $comments = $this->getResult($this, $this->query);

        return $comments;
    }

    /**
     * Get comments by Post id.
     * @return null|Comment[]
     */
    public function getValidatedCommentsByPostId(int $postId)
    {
        $this->prepareQuery()
            ->where('post_id = :post_id')
            ->addWhere('is_validated = :is_validated')
            ->setParameters([
                'post_id' => $postId,
                'is_validated' => true
            ]);

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
            ->orderBy('c.updated_at', 'ASC');
    }

    /**
     * Validates comment by uuid in database.
     */
    public function validateByUuid(string $uuid): void
    {
        $this->update(self::SQL_TABLE, ['is_validated' => true], ['uuid' => $uuid]);
    }

    /**
     * Deletes comment by uuid.
     */
    public function deleteByUuid(string $uuid): void
    {
        $this->delete(self::SQL_TABLE, ['uuid' => $uuid]);
    }

    /**
     * Deletes Comment by post id.
     */
    public function deleteByPostId(int $id): void
    {
        $this->delete(self::SQL_TABLE, ['post_id' => $id]);
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

    /**
     * Sets user_id column to null for Comments of the user.
     */
    public function setAuthorToNull(User $user): void
    {
        $sql = 'UPDATE ' . self::SQL_TABLE . ' SET user_id = NULL WHERE user_id = :user_id';
        $stmt = $this->createQuery($sql, ['user_id' => $user->getId()]);
        $stmt->closeCursor();
    }

    /**
     * Returns the total count of comments.
     */
    public function getPaginationCount(): int
    {
        $stmt = $this->createQuery($this->query->generateCountSQL(), $this->query->getParameters());
        $result = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int) $result;
    }

    /**
     * @return null|object[]|Comment[] Array of comments
     */
    public function getPaginationResult(int $offset, int $range)
    {
        $this->query->limit($offset, $range);

        return $this->getResult($this, $this->query);
    }
}
