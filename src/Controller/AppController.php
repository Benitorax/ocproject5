<?php
namespace App\Controller;

use App\Model\Post;
use App\Model\User;
use App\Form\LoginForm;
use App\Controller\Controller;
use App\Form\RegisterForm;

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

        $this->get('PostManager')->createAndSave($post);
        $this->get('UserDAO')->add($user);

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

        $this->get('PostDAO')->add($post);
        $this->get('UserDAO')->add($user);
        
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    public function login()
    {
        $loginForm = new LoginForm();

        if ($this->request->getMethod() === 'POST') {
            $loginForm = $this->get('UserManager')->hydrateLoginForm($loginForm, $this->request->request);
            $loginForm = $this->get('LoginValidation')->validate($loginForm);

            if ($loginForm->isValid) {
                $user = $this->get('UserDAO')->getOneBy(['email' => $loginForm->email]);
                $isPasswordValid = $this->get('PasswordEncoder')->isPasswordValid($user, $loginForm->password);

                if ($isPasswordValid) {
                    // TODO Session
                    // Flash messages
                    // $this->redirectToRoute('home');
                }
            }
        }

        // TO DO: Session and Flashmessages

        return $this->render('app/login.html.twig', [
            'form' => $loginForm
        ]);
    }

    public function register()
    {
        $registerForm = new RegisterForm();

        if ($this->request->getMethod() === 'POST') {
            $userManager = $this->get('UserManager');
            $registerForm = $userManager->hydrateRegisterForm($registerForm, $this->request->request);
            $registerForm = $this->get('RegisterValidation')->validate($registerForm);

            if ($registerForm->isValid) {
                $userManager->saveNewUser($registerForm);
                $this->redirectToRoute('login');
            }
        }

        // TO DO: Flashmessages

        return $this->render('app/register.html.twig', [
            'form' => $registerForm
        ]);
    }
}
