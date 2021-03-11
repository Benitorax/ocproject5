<?php
namespace App\Controller;

use DateTime;
use App\Model\Post;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\Service\Auth;
use App\Form\LoginForm;
use App\Form\RegisterForm;
use App\Service\PostManager;
use App\Service\UserManager;
use App\Controller\Controller;
use Config\Security\TokenStorage;
use App\Service\Validation\LoginValidation;
use App\Service\Validation\RegisterValidation;
use Config\Security\RememberMe\RememberMeManager;

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
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
        ;
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
            ->setUser($user)
        ;

        $this->get(PostManager::class)->createAndSave($post);
        $this->get(UserDAO::class)->add($user);

        return $this->render('app/home.html.twig', [
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
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());
            
        $post = new Post();
        $postId = rand(10000, 99999);
        $post->setId($postId)
        ->setTitle($slug)
        ->setSlug('mon-titre-de-la-mort'.rand(100, 999))
        ->setShortText('Mon introduction')
        ->setText('Le texte complètement vide')
        ->setCreatedAt(new DateTime())
        ->setUpdatedAt(new DateTime())
        ->setIsPublished(true)
        ->setUser($user);

        $this->get(PostDAO::class)->add($post);
        $this->get(UserDAO::class)->add($user);
        
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    public function login()
    {
        if (!empty($this->get(TokenStorage::class)->getToken())) {
            return $this->redirectToRoute('home');
        }

        $form = new LoginForm($this->get(LoginValidation::class));
        $form->handleRequest($this->request);

        if ($form->isSubmitted && $form->isValid) {
            $user = $this->get(Auth::class)->authenticateLoginForm($form, $this->request);

            if (!empty($user)) {
                $this->session->getFlashes()->add('success', 'Welcome, '.$user->getUsername().'!');
                return $this->redirectToRoute('home');
            }
            $this->session->getFlashes()->add('danger', 'Email or password Invalid.');
        }

        return $this->render('app/login.html.twig', ['form' => $form]);
    }

    public function register()
    {
        $form = new RegisterForm($this->get(RegisterValidation::class));
        $form->handleRequest($this->request);

        if ($form->isSubmitted && $form->isValid) {
            $this->get(UserManager::class)->saveNewUser($form);
            $this->session->getFlashes()->add('success', 'You register with success!');

            return $this->redirectToRoute('login');
        }

        return $this->render('app/register.html.twig', ['form' => $form]);
    }

    public function logout()
    {
        if ($this->isCsrfTokenValid($this->request->request->get('csrf_token'))) {
            $this->get(Auth::class)->handleLogout($this->request);
        }

        return $this->redirectToRoute('home');
    }

    public function termsOfUse()
    {
        return $this->render('app/terms_of_use.html.twig');
    }
}
