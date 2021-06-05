<?php

namespace App\Controller;

use App\DAO\CommentDAO;
use Faker\Factory;
use App\Model\Post;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\Model\Comment;
use Faker\Generator;
use Ramsey\Uuid\Uuid;
use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;
use Framework\Security\Hasher\PasswordHasher;

class FixturesController extends AbstractController
{
    /**
     * User[]: array of users and admins.
     */
    private array $users;

    private PasswordHasher $hasher;
    private PostManager $postManager;
    private PostDAO $postDAO;
    private UserDAO $userDAO;
    private CommentDAO $commentDAO;

    private Generator $faker;

    public function __construct(
        PasswordHasher $hasher,
        PostManager $postManager,
        PostDAO $postDAO,
        UserDAO $userDAO,
        CommentDAO $commentDAO
    ) {
        $this->hasher = $hasher;
        $this->postManager = $postManager;
        $this->postDAO = $postDAO;
        $this->userDAO = $userDAO;
        $this->commentDAO = $commentDAO;

        $this->faker = Factory::create('en_GB');
    }

    /**
     * Loads fixtures
     */
    public function load(): Response
    {
        $this->createUsers(20);

        for ($i = 0; $i < 3; $i++) {
            $user = $this->createAdminUser();
            $this->createPosts($user, 15);
        }

        $this->addFlash('success', 'Fixtures load with success! ');

        return $this->redirectToRoute('home');
    }

    /**
     * Creates and saves a number of posts.
     */
    public function createPosts(User $user, int $numberOfPosts): void
    {
        for ($i = 0; $i < $numberOfPosts; $i++) {
            $post = $this->createPost($user);

            if ($post->getIsPublished()) {
                $commentCount = mt_rand(5, 10);

                for ($j = 0; $j < $commentCount; $j++) {
                    $this->addCommentToPost($post);
                }
            }
        }
    }


    /**
     * Creates and saves a number of users.
     */
    public function createUsers(int $numberOfUsers): void
    {
        for ($i = 0; $i < $numberOfUsers; $i++) {
            $this->createUser();
        }
    }

    /**
     * Returns a created and saved admin user.
     */
    public function createAdminUser(): User
    {
        return $this->createUser(true);
    }

    /**
     * Returns a created and saved user.
     */
    public function createUser(bool $isAdmin = false): User
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName;
        $dateTime = $this->faker->dateTimeBetween('-2 years', '-10 months');
        $email = strtolower($firstName . '.' . $lastName) . '@yopmail.com';

        $user = (new User())->setUuid(Uuid::uuid4())
            ->setEmail($email)
            ->setPassword((string) $this->hasher->hash('123456'))
            ->setUsername($firstName . ' ' . $lastName)
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
        ;

        if ($isAdmin) {
            $user->setRoles(['user', 'admin']);
        }

        $this->userDAO->add($user);
        /** @var User */
        $user = $this->userDAO->getOneByEmail($email); // returns User with Id
        $this->users[] = $user;

        return $user;
    }

    /**
     * Returns a created and saved post.
     */
    public function createPost(User $user): Post
    {
        $isPublished = random_int(0, 100) < 70;
        $dateTime1 = $this->faker->dateTimeBetween('-1 years', 'now');

        // sets randomly updatedAt different from createdAt
        if (mt_rand(0, 1) === 1) {
            $dateTime2 = $this->faker->dateTimeBetween($dateTime1->format('Y-m-d H:i:s'), 'now');
        } else {
            $dateTime2 = $dateTime1;
        }

        $post = (new Post())->setUuid(Uuid::uuid4())
            ->setTitle($this->faker->realText(70, 5))
            ->setLead($this->faker->realText(255, 3))
            ->setContent($this->faker->paragraphs(3, true))
            ->setCreatedAt($dateTime1)
            ->setUpdatedAt($dateTime2)
            ->setIsPublished($isPublished)
            ->setUser($user)
        ;

        if ($isPublished) {
            $post->setSlug($this->postManager->slugify($post->getTitle()));
        }

        $this->postDAO->add($post);

        return $this->postDAO->getOneByUuid($post->getUuid()); // returns Post with Id
    }

    /**
     * Adds comment attached to post.
     */
    public function addCommentToPost(Post $post): Comment
    {
        $dateTime1 = $this->faker->dateTimeBetween('-2 years', '-10 months');
        $user = $this->getRandomUser();

        $comment =  (new Comment())->setUuid(Uuid::uuid4())
            ->setContent($this->faker->realText(mt_rand(200, 800), 3))
            ->setPost($post)
            ->setUser($user)
            ->setCreatedAt($dateTime1)
            ->setUpdatedAt($dateTime1)
        ;

        if (in_array('admin', $user->getRoles())) {
            $comment->setIsValidated(true);
        } else {
            // sets randomly updatedAt different from createdAt
            // if updatedAt !== createdAt then isValidated = true
            if (mt_rand(1, 6) > 1) {
                $dateTime2 = $this->faker->dateTimeBetween($dateTime1->format('Y-m-d H:i:s'), 'now');
                $comment->setIsValidated(true)
                    ->setUpdatedAt($dateTime2);
            }
        }

        $this->commentDAO->add($comment);

        return $comment;
    }

    /**
     * Returns a random user.
     */
    public function getRandomUser(): User
    {
        return $this->users[array_rand($this->users)];
    }
}
