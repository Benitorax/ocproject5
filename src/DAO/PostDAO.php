<?php

namespace App\DAO;

use PDO;
use DateTime;
use App\Model\Post;
use App\Model\User;
use Ramsey\Uuid\Uuid;
use Framework\DAO\AbstractDAO;
use Framework\DAO\QueryExpression;
use App\Service\Pagination\PaginationDAOInterface;

class PostDAO extends AbstractDAO implements PaginationDAOInterface
{
    public const SQL_TABLE = 'post';
    public const SQL_COLUMNS = [
        'id', 'uuid', 'title', 'slug', 'lead', 'content', 'created_at', 'updated_at', 'is_published', 'user_id'
    ];

    private QueryExpression $query;

    /**
     * Returns a Post object from stdClass.
     */
    public function buildObject(\stdClass $o): Post
    {
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
            ->setUser($user);

        return $post;
    }

    /**
     * @return null|object|Post the object is instance of Post class
     */
    public function getOneBySlug(string $slug)
    {
        $this->prepareQuery()
            ->where('slug = :slug')
            ->setParameter('slug', $slug);

        return $this->getOneResult($this, $this->query);
    }

    /**
     * @return null|object|Post the object is instance of Post class
     */
    public function getOneByUuid(string $uuid)
    {
        $this->prepareQuery()
            ->where('p.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        return $this->getOneResult($this, $this->query);
    }

    /**
     * Setting the query without executing it.
     */
    public function setAllPostsQuery(?string $search): void
    {
        $this->prepareQuery();

        if (null !== $search && '' !== $search) {
            $this->query->addWhere(
                'title LIKE :search'
                    . ' OR lead LIKE :search'
                    . ' OR content LIKE :search'
                    . ' OR u.username LIKE :search'
            )
            ->setParameter('search', '%' . $search . '%');
        }
    }

    /**
     * Setting the query without executing it.
     */
    public function setIsPublishedAndSearchQuery(?string $search): void
    {
        $this->prepareQuery()
            ->where('is_published = :is_published')
            ->setParameter('is_published', true);

        if (null !== $search && '' !== $search) {
            $this->query->addWhere(
                'title LIKE :search'
                    . ' OR lead LIKE :search'
                    . ' OR content LIKE :search'
            )
            ->setParameter('search', '%' . $search . '%');
        }
    }

    /**
     * Sets the select, table and jointure for the sql query.
     */
    private function prepareQuery(): QueryExpression
    {
        return $this->query = (new QueryExpression())
            ->select(self::SQL_COLUMNS, 'p')
            ->addSelect(UserDAO::SQL_COLUMNS, 'u')
            ->from(self::SQL_TABLE, 'p')
            ->leftOuterJoin(UserDAO::SQL_TABLE, 'u', 'user_id = u.id')
            ->orderBy('p.updated_at', 'DESC');
    }

    /**
     * Inserts a new row in the database.
     */
    public function updatePost(Post $post): void
    {
        $this->update(
            'post',
            [
                'title' => $post->getTitle(),
                'slug' => $post->getSlug(),
                'lead' => $post->getLead(),
                'content' => $post->getContent(),
                'updated_at' => ($post->getUpdatedAt())->format('Y-m-d H:i:s'),
                'is_published' => intval($post->getIsPublished()),
                'user_id' => $post->getUser()->getId(),
            ],
            ['id' => $post->getId()]
        );
    }

    /**
     * Inserts a new row in the database.
     */
    public function add(Post $post): void
    {
        $this->insert('post', [
            'uuid' => $post->getUuid(),
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'lead' => $post->getLead(),
            'content' => $post->getContent(),
            'created_at' => ($post->getCreatedAt())->format('Y-m-d H:i:s'),
            'updated_at' => ($post->getUpdatedAt())->format('Y-m-d H:i:s'),
            'is_published' => intval($post->getIsPublished()),
            'user_id' => $post->getUser()->getId(),
        ]);
    }

    /**
     * Deletes a Post by id.
     */
    public function deleteByUuid(string $uuid): void
    {
        $this->delete(self::SQL_TABLE, ['uuid' => $uuid]);
    }

    /**
     * Returns the list of slugs by slug of a SQL command.
     */
    public function getSlugsBy(string $value): ?array
    {
        $sql = 'SELECT slug FROM post WHERE slug LIKE :slug';
        $stmt = $this->createQuery($sql, ['slug' => $value . '%']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * Returns the total count of posts.
     */
    public function getPaginationCount(): int
    {
        $stmt = $this->createQuery($this->query->generateCountSQL(), $this->query->getParameters());
        $result = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int) $result;
    }

    /**
     * @return null|object[]|Post[] Array of posts
     */
    public function getPaginationResult(int $offset, int $range)
    {
        $this->query->limit($offset, $range);

        return $this->getResult($this, $this->query);
    }
}
