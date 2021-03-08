<?php
namespace App\DAO;

use PDO;
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
            ->setCreatedAt(new \DateTime($object->created_at))
            ->setUpdatedAt(new \DateTime($object->updated_at))
            ->setIsPublished($object->is_published)
            ->setUser($this->getUserById($object->user_id));
            
        return $post;
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

    public function getCountBySlug($slug): int
    {
        $sql = 'SELECT COUNT(*) AS count FROM post';
        $result = $this->createQuery($sql, ['slug' => $slug.'%']);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $result->closeCursor();

        return $row['count'];
    }

    public function add(Post $post)
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

    // TODO Create a function to attach user to each post
    public function getUserById($userId)
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
            ->setIsAdmin(json_decode($row['roles']))
            ->setIsBlocked($row['is_blocked']);

        return $user;
    }
}
