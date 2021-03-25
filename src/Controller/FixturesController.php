<?php

namespace App\Controller;

use Faker\Factory;
use App\Model\Post;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use Framework\View\View;
use App\Service\IdGenerator;
use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\Controller\AbstractController;
use Framework\Security\Encoder\PasswordEncoder;

class FixturesController extends AbstractController
{
    private $encoder;
    private $postManager;
    private $postDAO;
    private $userDAO;
    private $faker;

    public function __construct(
        View $view,
        Container $container,
        PasswordEncoder $encoder, 
        PostManager $postManager, 
        PostDAO $postDAO, 
        UserDAO $userDAO
    )
    {
        parent::__construct($view, $container);
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
        $dateTime = $this->faker->dateTimeBetween('-2 years', '1 year');

        $user = (new User())->setId(IdGenerator::generate())
            ->setEmail(strtolower($firstName . '.' . $lastName) . '@yopmail.com')
            ->setPassword((string) $this->encoder->encode('123456'))
            ->setUsername($firstName . ' ' . $lastName)
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
        ;

        if($isAdmin) {
            $user->setRoles(['user', 'admin']);
        }

        $this->userDAO->add($user);

        return $user;
    }

    /**
     * Returns a created and saved post.
     */
    public function createPost(User $user): Post
    {
        $dateTime = $this->faker->dateTimeBetween('-1 years', 'now');
        $isPublished = random_int(0, 100) > 70;

        $post = new Post();
        $post->setId(IdGenerator::generate())
            ->setTitle($this->faker->realText(70, 5))
            ->setSlug($this->postManager->slugify($post->getTitle()))
            ->setLead($this->faker->realText(255, 3))
            ->setContent($this->faker->paragraphs(3, true))
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
            ->setIsPublished($isPublished)
            ->setUser($user)
        ;

        $this->postDAO->add($post);

        return $post;
    }
}
