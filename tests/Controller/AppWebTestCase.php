<?php

namespace App\Tests\Controller;

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
use Framework\Test\WebTestCase;
use Framework\Container\Container;
use App\Service\Mailer\Event\MailEvent;
use Framework\Security\Hasher\PasswordHasher;
use App\Service\Mailer\Subscriber\MailerSubscriber;

class AppWebTestCase extends WebTestCase
{
    protected Container $container;
    public static array $posts;
    public static array $users;

    public function setup(): void
    {
        $app = static::bootApp();
        $this->container = $app->getContainer();
        $this->cleanDatabase();
        $this->loadFixtures();
        self::$client = static::createClient();
    }

    public function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function cleanDatabase(): void
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

        // cleans posts and users properties
        self::$posts = [];
        self::$users = [];
    }

    public function loadFixtures(): void
    {
        $data = new FixturesData();

        // loads user
        if (!empty($data::USERS)) {
            foreach ($data::USERS as $user) {
                self::$users['user'][$user[0]] = $this->createUser($user[0], $user[1], $user[2], $user[3], false);
            }
        }

        // loads admin users
        if (!empty($data::ADMIN_USERS)) {
            foreach ($data::ADMIN_USERS as $user) {
                self::$users['admin'][$user[0]] = $this->createUser($user[0], $user[1], $user[2], $user[3], true);
            }
        }

        self::$users['all'] = array_merge(self::$users['user'], self::$users['admin']);

        // loads published posts
        if (!empty($data::PUBLISHED_POSTS)) {
            foreach ($data::PUBLISHED_POSTS as $post) {
                self::$posts['published'][] = $this->createPost(
                    self::$users['admin'][array_rand(self::$users['admin'])],
                    $post[0],
                    $post[1],
                    $post[2],
                    true
                );
            }
        }

        // loads unpublished posts
        if (!empty($data::UNPUBLISHED_POSTS)) {
            foreach ($data::UNPUBLISHED_POSTS as $post) {
                self::$posts['unpublished'][] = $this->createPost(
                    self::$users['admin'][array_rand(self::$users['admin'])],
                    $post[0],
                    $post[1],
                    $post[2],
                    false
                );
            }
        }

        // loads invalidated comments
        if (!empty($data::COMMENTS)) {
            // adds comments only to first published post
            foreach ($data::COMMENTS as $comment) {
                $this->addCommentToPost(
                    self::$posts['published'][0],
                    self::$users['user'][array_rand(self::$users['user'])],
                    $comment[0],
                    $comment[1]
                );
            }
        }
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

    public function addCommentToPost(Post $post, User $user, string $comment, bool $isValidated): Comment
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

        return $comment;
    }

    public function assertEmailCount(int $expectedCount): void
    {
        $count = count(self::getMailEvents());
        self::assertSame($expectedCount, $count, sprintf(
            'The email count should be %d, but %d given.',
            $expectedCount,
            $count
        ));
    }

    public function assertQueuedEmailCount(int $expectedCount): void
    {
        $count = count(self::getMailEvents());
        self::assertSame($expectedCount, $count, sprintf(
            'The queued email count should be %d, but %d given.',
            $expectedCount,
            $count
        ));
    }

    /**
     * @return MailEvent[]
     */
    public static function getMailEvents()
    {
        return self::$client->getContainer()->get(MailerSubscriber::class)->getMailEvents();
    }
}
