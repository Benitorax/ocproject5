<?php
namespace App\DAO;

use App\Model\Post;
use App\Model\User;
use Config\DAO\AbstractDAO;

class PostDAO extends AbstractDAO
{
    private function buildObject($row): Post
    {
        $post = new Post();
        $post->setId($row['id'])
            ->setTitle($row['title'])
            ->setSlug($row['slug'])
            ->setShortText($row['short_text'])
            ->setText($row['text'])
            ->setCreatedAt($row['created_at'])
            ->setUpdatedAt($row['updated_at'])
            ->setIsPublished($row['is_published'])
            ->setUser($this->getUserById($row['user_id']));
            
        return $post;
    }

    public function getAll()
    {
        $sql = 'SELECT id, title, slug, short_text, text, created_at, updated_at, is_published, user_id FROM post ORDER BY id DESC';
        $result = $this->createQuery($sql);
        $posts = [];
        foreach ($result as $row){
            $postId = $row['id'];
            $posts[$postId] = $this->buildObject($row);
        }
        $result->closeCursor();

        return $posts;
    }

    public function getById($postId): Post
    {
        $sql = 'SELECT id, title, slug, short_text, text, created_at, updated_at, is_published, user_id FROM post WHERE id = ?';
        $result = $this->createQuery($sql, [$postId]);
        $post = $result->fetch();
        $result->closeCursor();

        return $this->buildObject($post);
    }

    public function add(Post $post)
    {
        $sql = 'INSERT INTO post (id, title, slug, short_text, text, created_at, updated_at, is_published, user_id) 
            VALUES (:id, :title, :slug, :short_text, :text, :created_at, :updated_at, :is_published, :user_id)';
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
    public function getUserById($userId) {
        $sql = 'SELECT id, email, password, username, created_at, updated_at, is_admin, is_blocked FROM user ORDER BY id DESC';
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
            ->setIsAdmin($row['is_admin'])
            ->setIsBlocked($row['is_blocked']);

        return $user;
    }
}