<?php
namespace App\Controller;

use App\Model\Post;
use App\Model\User;
use App\Model\UserDTO;
use App\Controller\Controller;

class AppController extends Controller
{  
    public function home()
    {
        $user = new User();
        $userId = rand(10000, 99999);
        $user->setId($userId)
            ->setEmail('name'.$userId)
            ->setPassword('123456')
            ->setUsername('Martouflette'.$userId.'@mail.com')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());
        $titles = [
            "C'est la casse du siècle !",
            "Pandémie ici et là-bas ?!",
            "Word of the month",
            "¿Qué tenemos en nuestro plato? "
        ];
        $post = new Post();
        $postId = rand(10000, 99999);
        $post->setId($postId)
        ->setTitle($titles[array_rand($titles)])
        ->setShortText('Mon introduction')
        ->setText('Le texte complètement vide')
        ->setIsPublished(true)
        ->setUser($user);

        $this->get('PostManager')->createAndSave($post);

        $this->get('PostDAO')->add($post);
        $this->get('UserDAO')->add($user);

        return $this->view->render('app/home.html.twig', [
            'post' => $post,
            'user' => $user
        ]);
    }

    public function post($slug, $username)
    {
        $user = new User();
        $userId = rand(10000, 99999);
        $user->setId($userId)
            ->setEmail($username.$userId.'@mail.com')
            ->setPassword('123456')
            ->setUsername($username.$userId)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());
            
        $post = new Post();
        $postId = rand(10000, 99999);
        $post->setId($postId)
        ->setTitle($slug)
        ->setSlug('mon-titre-de-la-mort'.rand(100, 999))
        ->setShortText('Mon introduction')
        ->setText('Le texte complètement vide')
        ->setCreatedAt(new \DateTime())
        ->setUpdatedAt(new \DateTime())
        ->setIsPublished(true)
        ->setUser($user);

        $this->get('PostDAO')->add($post);
        $this->get('UserDAO')->add($user);
        
        return $this->view->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    public function login()
    {

        
        return $this->view->render('app/login.html.twig', [
        ]);
    }

    public function register()
    {
        $userDTO = new UserDTO();

        if($this->request->getMethod() === 'POST') {
            $userManager = $this->get('UserManager');
            $userDTO = $userManager->hydrateUserDTO($userDTO, $this->request->request);
            $userDTO = $this->get('UserValidation')->validate($userDTO);

            if($userDTO->isValid) {
                // $userManager->saveNewUser($userDTO);
                $this->redirectToRoute('login');
            }
        }

        return $this->view->render('app/register.html.twig', [
            'form' => $userDTO
        ]);
    }
}