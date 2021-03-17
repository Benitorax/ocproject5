<?php

namespace App\DAO;

use PDO;
use DateTime;
use App\Model\Post;
use App\Model\User;
use Framework\DAO\AbstractDAO;
use Framework\DAO\DAOInterface;

class PostDAO extends AbstractDAO implements DAOInterface
{
    private const SQL_SELECT = 'SELECT'
        . ' p.id p_id, p.title p_title, p.slug p_slug, p.lead p_lead, p.content p_content,'
        . ' p.created_at p_createdAt, p.updated_at p_updatedAt, p.is_published p_isPublished,'
        . ' u.id u_id, u.email u_email, u.password u_password, u.username u_username,'
        . ' u.created_at u_createdAt, u.updated_at u_updatedAt, u.roles u_roles, u.is_blocked u_isBlocked'
        . ' FROM post p'
        . ' LEFT OUTER JOIN user u ON user_id = u.id';

    public function buildObject(\stdClass $o): Post
    {
        $user = new User();
        $user->setId($o->u_id)
            ->setEmail($o->u_email)
            ->setPassword($o->u_password)
            ->setUsername($o->u_username)
            ->setCreatedAt(new DateTime($o->u_createdAt))
            ->setUpdatedAt(new DateTime($o->u_updatedAt))
            ->setRoles(json_decode($o->u_roles))
            ->setIsBlocked($o->u_isBlocked);

        $post = new Post();
        $post->setId($o->p_id)
            ->setTitle($o->p_title)
            ->setSlug($o->p_slug)
            ->setLead($o->p_lead)
            ->setContent($o->p_content)
            ->setCreatedAt(new DateTime($o->p_createdAt))
            ->setUpdatedAt(new DateTime($o->p_updatedAt))
            ->setIsPublished($o->p_isPublished)
            ->setUser($user);

        return $post;
    }

    /**
     * @return null|object|Post the object is instance of Post class
     */
    public function getOneBy(array $parameters)
    {
        return $this->selectOneResultBy(self::SQL_SELECT, $parameters, $this);
    }

    /**
     * @return null|object[]|Post[] Array of posts
     */
    public function getBy(array $parameters)
    {
        return $this->selectResultBy(self::SQL_SELECT, $parameters, $this);
    }

    /**
     * @return null|object[]|Post[] Array of all posts
     */
    public function getAll()
    {
        return $this->selectAll(self::SQL_SELECT, $this);
    }

    public function getCountBySlug(string $slug): int
    {
        $sql = 'SELECT COUNT(*) AS count FROM post';
        $result = $this->createQuery($sql, ['slug' => $slug . '%']);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $result->closeCursor();

        return $row['count'];
    }

    public function getListBySlug(string $slug): int
    {
        $sql = 'SELECT COUNT(*) AS count FROM post';
        $result = $this->createQuery($sql, ['slug' => $slug . '%']);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $result->closeCursor();

        return $row['count'];
    }

    public function add(Post $post): void
    {
        $this->insert('post', [
            'id' => $post->getId(),
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
     * @param string|int $userId
     */
    public function getUserById($userId): User
    {
        $sql = 'SELECT id, email, password, username, created_at, updated_at, roles, is_blocked'
                . 'FROM user ORDER BY id DESC';
        $result = $this->createQuery($sql, [$userId]);
        $row = $result->fetch();
        $result->closeCursor();

        $user = new User();
        $user->setId($row['id'])
            ->setEmail($row['email'])
            ->setPassword($row['password'])
            ->setUsername($row['username'])
            ->setCreatedAt($row['created_at'])
            ->setUpdatedAt($row['updated_at'])
            ->setRoles(json_decode($row['roles']))
            ->setIsBlocked($row['is_blocked']);

        return $user;
    }

    /**
     * returns the list of slugs by slug of a SQL command.
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
}
