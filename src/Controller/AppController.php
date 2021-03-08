<?php
namespace App\Controller;

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
use Config\Security\PersistentToken;
use Config\Security\RememberMeManager;

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
            ->setUpdatedAt(new \DateTime())
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

        $this->get(PostDAO::class)->add($post);
        $this->get(UserDAO::class)->add($user);
        
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    public function login()
    {
        $loginForm = new LoginForm();

        if ($this->request->getMethod() === 'POST') {
            $loginForm = $this->get(UserManager::class)->manageLoginForm($loginForm, $this->request);

            if ($loginForm->isValid) {
                $user = $this->get(Auth::class)->authenticate($loginForm->email, $loginForm->password);

                if ($user) {
                    $this->session->set('user', $user);
                    $this->session->getFlashes()->add('success', 'Welcome, '.$user->getUsername().'!');
                    if ($loginForm->rememberme) {
                        $this->get(RememberMeManager::class)->createNewToken($user, $this->request);
                    }
                    
                    //return $this->redirectToRoute('home');
                }
            } else {
                $this->session->getFlashes()->add('danger', 'Invalid credentials.');
            }
        }

        return $this->render('app/login.html.twig', [
            'form' => $loginForm
        ]);
    }

    public function register()
    {
        $registerForm = new RegisterForm();
        $this->session->clear();
        $this->session->getFlashes()->all();
        if ($this->request->getMethod() === 'POST') {
            $userManager = $this->get(UserManager::class);
            $registerForm = $userManager->manageRegisterForm($registerForm, $this->request);

            if ($registerForm->isValid) {
                $userManager->saveNewUser($registerForm);
                $this->session->getFlashes()->add('success', 'You register with success!');
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('app/register.html.twig', [
            'form' => $registerForm
        ]);
    }

    public function logout()
    {
        $this->session->stop();
        $this->session->getFlashes()->add('success', 'You log out with success!');
        return $this->redirectToRoute('login');
    }
}
