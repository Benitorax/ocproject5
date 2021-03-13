<?php
namespace App\DAO;

use PDO;
use DateTime;
use App\Model\Post;
use App\Model\User;
use Config\DAO\AbstractDAO;
use Config\DAO\DAOInterface;

class PostDAO extends AbstractDAO implements DAOInterface
{
    const SQL_SELECT = 'SELECT id, title, slug, short_text, text, created_at, updated_at, is_published, user_id 
                        FROM post';
    
    public function buildObject(\stdClass $object): Post
    {
        $post = new Post();
        $post->setId($object->id)
            ->setTitle($object->title)
            ->setSlug($object->slug)
            ->setShortText($object->short_text)
            ->setText($object->text)
            ->setCreatedAt(new DateTime($object->created_at))
            ->setUpdatedAt(new DateTime($object->updated_at))
            ->setIsPublished($object->is_published)
            ->setUser($this->getUserById($object->user_id));
            
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
     * @return null|object[]|Post[] the object is instance of Post class
     */
    public function getBy(array $parameters)
    {
        return $this->selectResultBy(self::SQL_SELECT, $parameters, $this);
    }

    /**
     * @return null|object[]|Post[] the object is instance of Post class
     */
    public function getAll()
    {
        return $this->selectAll(self::SQL_SELECT, $this);
    }

    public function getCountBySlug(string $slug): int
    {
        $sql = 'SELECT COUNT(*) AS count FROM post';
        $result = $this->createQuery($sql, ['slug' => $slug.'%']);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $result->closeCursor();

        return $row['count'];
    }

    public function add(Post $post): void
    {
        $sql = 'INSERT INTO post (id, title, slug, short_text, text, created_at, updated_at, is_published, user_id)'
            .'VALUES (:id, :title, :slug, :short_text, :text, :created_at, :updated_at, :is_published, :user_id)';
        $this->createQuery($sql, [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'short_text' => $post->getShortText(),
            'text' => $post->getText(),
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
                .'FROM user ORDER BY id DESC';
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
}
