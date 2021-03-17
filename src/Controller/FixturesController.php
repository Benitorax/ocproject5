<?php

namespace App\Controller;

use DateTime;
use Faker\Factory;
use App\Model\Post;
use App\Model\User;
use App\DAO\UserDAO;
use App\Service\IdGenerator;
use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;
use Framework\Security\Encoder\PasswordEncoder;

class FixturesController extends AbstractController
{
    public function load(): Response
    {
        /** @var PasswordEncoder */ $encoder = $this->get(PasswordEncoder::class);
        /** @var PostManager */ $postManager = $this->get(PostManager::class);
        /** @var UserDAO */ $userDAO = $this->get(UserDAO::class);
        $faker = Factory::create('en_GB');

        for ($i = 0; $i < 3; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName;
            $dateTime = new DateTime();

            $user = new User();
            $user->setId(IdGenerator::generate())
                ->setEmail(strtolower($firstName . '.' . $lastName) . '@yopmail.com')
                ->setPassword((string) $encoder->encode('123456'))
                ->setUsername($firstName . ' ' . $lastName)
                ->setCreatedAt($dateTime)
                ->setUpdatedAt($dateTime);

            for ($j = 0; $j < 8; $j++) {
                $post = new Post();
                $post->setId(IdGenerator::generate())
                    ->setTitle($faker->realText(70, 5))
                    ->setLead($faker->realText(255, 3))
                    ->setContent($faker->paragraphs(3, true))
                    ->setIsPublished(true)
                    ->setUser($user);

                $postManager->createAndSave($post);
            }

            $userDAO->add($user);
        }

        $this->addFlash('success', 'Fixtures load with success! ');

        return $this->redirectToRoute('home');
    }
}
