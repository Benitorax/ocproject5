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
    protected UserDAO $userDAO;
    protected PostDAO $postDAO;
    protected CommentDAO $commentDAO;

    public function setup(): void
    {
        $app = static::bootApp();
        $this->container = $app->getContainer();
        $this->cleanDatabase();
        $this->userDAO = $this->container->get(UserDAO::class);
        $this->postDAO = $this->container->get(PostDAO::class);
        $this->commentDAO = $this->container->get(CommentDAO::class);
    }

    public function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function cleanDatabase(): void
    {
        // cleans database after each test
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
    public function createUser(string $username, string $email, string $password, bool $isAdmin = false): User
    {
        $user = (new User())->setUuid(Uuid::uuid4())
            ->setEmail($email)
            ->setPassword((string) $this->container->get(PasswordHasher::class)->hash($password))
            ->setUsername($username)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
        ;

        if ($isAdmin) {
            $user->setRoles(['user', 'admin']);
        }

        $this->userDAO->add($user);
        /** @var User */
        return $this->userDAO->getOneByEmail($email); // returns User with Id
    }

    public function createPost(User $user, string $title, string $lead, string $content, bool $isPublished): Post
    {
        $post = (new Post())->setUuid(Uuid::uuid4())
            ->setTitle($title)
            ->setLead($lead)
            ->setContent($content)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
            ->setIsPublished($isPublished)
            ->setUser($user)
        ;

        if ($isPublished) {
            $post->setSlug($this->container->get(PostManager::class)->slugify($post->getTitle()));
        }

        $this->postDAO->add($post);
        /** @var Post */
        return $this->postDAO->getOneByUuid($post->getUuid()); // returns Post with Id
    }

    public function addCommentToPost(Post $post, User $user, string $comment): Comment
    {
        $comment =  (new Comment())->setUuid(Uuid::uuid4())
            ->setContent($comment)
            ->setPost($post)
            ->setUser($user)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
        ;

        if (in_array('admin', $user->getRoles())) {
            $comment->setIsValidated(true);
        }

        $this->commentDAO->add($comment);

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
