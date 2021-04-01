<?php

namespace App\Controller;

use Faker\Factory;
use App\Model\Post;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use Faker\Generator;
use Ramsey\Uuid\Uuid;
use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;
use Framework\Security\Encoder\PasswordEncoder;

class FixturesController extends AbstractController
{
    private PasswordEncoder $encoder;
    private PostManager $postManager;
    private PostDAO $postDAO;
    private UserDAO $userDAO;
    private Generator $faker;

    public function __construct(
        PasswordEncoder $encoder,
        PostManager $postManager,
        PostDAO $postDAO,
        UserDAO $userDAO
    ) {
        $this->encoder = $encoder;
        $this->postManager = $postManager;
        $this->postDAO = $postDAO;
        $this->userDAO = $userDAO;

        $this->faker = Factory::create('en_GB');
    }

    /**
     * Loads fixtures
     */
    public function load(): Response
    {
        $this->createUsers(4);

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
            $this->createPost($user);
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

        $user = (new User())->setUuid(Uuid::uuid4())
            ->setEmail(strtolower($firstName . '.' . $lastName) . '@yopmail.com')
            ->setPassword((string) $this->encoder->encode('123456'))
            ->setUsername($firstName . ' ' . $lastName)
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
        ;

        if ($isAdmin) {
            $user->setRoles(['user', 'admin']);
        }

        $this->userDAO->add($user);
        $user = $this->userDAO->getOneByUsername($firstName . ' ' . $lastName);

        /** @var User */
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

        $post = new Post();
        $post->setUuid(Uuid::uuid4())
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

        return $post;
    }
}
