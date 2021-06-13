<?php

namespace App\Tests\Controller\Fixtures;

use DateTime;
use App\Model\Post;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use Ramsey\Uuid\Uuid;
use App\Model\Comment;
use Framework\DAO\DAO;
use App\DAO\CommentDAO;
use App\Service\PostManager;
use Framework\Container\Container;
use Framework\Security\Hasher\PasswordHasher;
use App\Tests\Controller\Fixtures\FixturesData;

class FixturesLoader
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function truncateTables(): void
    {
        // cleans database
        $this->container->get(DAO::class)->makeQuery(
            "
            SET FOREIGN_KEY_CHECKS=0;
            TRUNCATE TABLE comment;
            TRUNCATE TABLE post;
            TRUNCATE TABLE user;
            TRUNCATE TABLE rememberme_token;
            TRUNCATE TABLE reset_password_token;
            SET FOREIGN_KEY_CHECKS=1;
            "
        );
    }

    /**
     *  returns [
     *      'users' => [
     *          'admin' => [],
     *          'user' => []
     *      ],
     *      'posts' => [
     *          'published' => [],
     *          'unpublished' => []
     *      ],
     *  ]
     */
    public function loadFixtures(): array
    {
        $data = new FixturesData();
        $values = [];

        // loads user
        foreach ($data::USERS as $user) {
            $values['users'][$user[4] ? 'admin' : 'user'][$user[0]] = $this->createUser(
                $user[0],
                $user[1],
                $user[2],
                $user[3],
                $user[4]
            );
        }

        // loads published posts
        foreach ($data::POSTS as $post) {
            $values['posts'][$post[3] ? 'published' : 'unpublished'][] = $this->createPost(
                $values['users']['admin'][array_rand($values['users']['admin'])],
                $post[0],
                $post[1],
                $post[2],
                $post[3]
            );
        }

        // adds comments only to first published post
        foreach ($data::COMMENTS as $comment) {
            $this->addCommentToPost(
                $values['posts']['published'][0],
                $values['users']['user'][array_rand($values['users']['user'])],
                $comment[0],
                $comment[1]
            );
        }

        return $values;
    }

    public function createUser(
        string $username,
        string $email,
        string $password,
        bool $isBlocked,
        bool $isAdmin = false
    ): User {
        $user = (new User())->setUuid(Uuid::uuid4())
            ->setEmail($email)
            ->setPassword((string) $this->container->get(PasswordHasher::class)->hash($password))
            ->setUsername($username)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
            ->setIsBlocked($isBlocked)
        ;

        if ($isAdmin) {
            $user->setRoles(['user', 'admin']);
        }

        $this->container->get(UserDAO::class)->add($user);
        /** @var User */
        return $this->container->get(UserDAO::class)->getOneByEmail($email); // returns User with Id
    }

    public function createPost(User $user, string $title, string $lead, string $content, bool $isPublished): Post
    {
        $post = (new Post())->setUuid(Uuid::uuid4())
            ->setTitle($title)
            ->setLead($lead)
            ->setContent($content)
            ->setCreatedAt(new DateTime('-5 months'))
            ->setUpdatedAt(new DateTime('-4 months'))
            ->setIsPublished($isPublished)
            ->setUser($user)
        ;

        if ($isPublished) {
            $post->setSlug($this->container->get(PostManager::class)->slugify($post->getTitle()));
        }

        $this->container->get(PostDAO::class)->add($post);
        /** @var Post */
        return $this->container->get(PostDAO::class)->getOneByUuid($post->getUuid()); // returns Post with Id
    }

    public function addCommentToPost(Post $post, User $user, string $comment, bool $isValidated): void
    {
        $comment =  (new Comment())->setUuid(Uuid::uuid4())
            ->setContent($comment)
            ->setPost($post)
            ->setUser($user)
            ->setCreatedAt(new DateTime('-4 months'))
            ->setUpdatedAt(new DateTime('- 3 months'))
        ;

        if ($isValidated || in_array('admin', $user->getRoles())) {
            $comment->setIsValidated(true);
        }

        $this->container->get(CommentDAO::class)->add($comment);
    }
}
