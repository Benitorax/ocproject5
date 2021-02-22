<?php
namespace App\Controller;

use App\Model\Post;
use App\Model\User;
use App\Controller\Controller;

class AppController extends Controller
{
    public function home()
    {
        $user = new User();
        $userId = rand(10000, 99999);
        $user->setId($userId)
            ->setEmail('name'.rand(1000, 9999))
            ->setPassword('123456')
            ->setUsername('Martouflette'.rand(1000, 9999))
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setIsAdmin(false)
            ->setIsBlocked(false);

        $post = new Post();
        $postId = rand(10000, 99999);
        $post->setId($postId)
        ->setTitle('Mon titre de la mort')
        ->setSlug('mon-titre-de-la-mort'.rand(100, 999))
        ->setShortText('Mon introduction')
        ->setText('Le texte complètement vide')
        ->setCreatedAt(new \DateTime())
        ->setUpdatedAt(new \DateTime())
        ->setIsPublished(true)
        ->setUser($user);

        $this->postDAO->add($post);
        $this->userDAO->add($user);

        return $this->view->render('home/home.html.twig', [
            'post' => $post,
            'user' => $user
        ]);
    }

    public function post()
    {
        $user = new User();
        $userId = rand(10000, 99999);
        $user->setId($userId)
            ->setEmail('name'.rand(1000, 9999))
            ->setPassword('123456')
            ->setUsername('Martouflette')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setIsAdmin(true)
            ->setIsBlocked(false);

        $post = new Post();
        $postId = rand(10000, 99999);
        $post->setId($postId)
        ->setTitle('Mon titre de la mort')
        ->setSlug('mon-titre-de-la-mort'.rand(100, 999))
        ->setShortText('Mon introduction')
        ->setText('Le texte complètement vide')
        ->setCreatedAt(new \DateTime())
        ->setUpdatedAt(new \DateTime())
        ->setIsPublished(true)
        ->setUser($user);
        
        $this->postDAO->add($post);

        return $this->view->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}