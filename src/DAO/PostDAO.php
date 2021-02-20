<?php
namespace App\DAO;

use App\Model\Post;
use Config\DAO\DAO;

class PostDAO extends DAO
{
    private function buildObject($row)
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
        ->setUserId($row['user_id']);
        return $post;
    }

    public function getPosts()
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

    public function getPost($postId)
    {
        $sql = 'SELECT id, title, slug, short_text, text, created_at, updated_at, is_published, user_id FROM post WHERE id = ?';
        $result = $this->createQuery($sql, [$postId]);
        $article = $result->fetch();
        $result->closeCursor();

        return $this->buildObject($article);
    }

    public function addPost($post)
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
            'is_published' => $post->getIsPublished(),
            'user_id' => $post->getUserId(),
        ]);
    }
}