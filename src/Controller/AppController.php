<?php

namespace App\Controller;

use DateTime;
use App\Model\Post;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\Service\Auth;
use App\Form\LoginForm;
use App\Form\ContactForm;
use App\Form\RegisterForm;
use App\Service\UserManager;
use App\Service\Mailer\Notification;
use Config\Response\Response;
use Config\Controller\Controller;
use App\Service\Validation\LoginValidation;
use App\Service\Validation\ContactValidation;
use App\Service\Validation\RegisterValidation;

class AppController extends Controller
{
    /**
     * Displays the home page with contact form visible only by logged users.
     */
    public function home(): Response
    {
        $form = new ContactForm($this->get(ContactValidation::class));
        $form->handleRequest($this->request);

        if ($form->isSubmitted && $form->isValid) {
            $mailCount = $this->get(Notification::class)->notifyContact($form);

            if (0 === $mailCount) {
                $this->addFlash('danger', 'The messaging service has technical problems. Please try later.');
            } else {
                $form->clear();
                $this->addFlash('success', 'Your message has been sent with success!');
            }
        }

        return $this->render('app/home.html.twig', ['form' => $form]);
    }

    public function post(string $slug, string $username): Response
    {
        $user = new User();
        $userId = (string) rand(10000, 99999);
        $user->setId($userId)
            ->setEmail($username . $userId . '@mail.com')
            ->setPassword('123456')
            ->setUsername($username . $userId)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $post = new Post();
        $postId = (string) rand(10000, 99999);
        $post->setId($postId)
        ->setTitle($slug)
        ->setSlug('mon-titre-de-la-mort' . rand(100, 999))
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

    /**
     * Displays the login page.
     */
    public function login(): Response
    {
        if ($this->isGranted(['user'])) {
            return $this->redirectToRoute('home');
        }

        $form = new LoginForm($this->get(LoginValidation::class));
        $form->handleRequest($this->request);

        if ($form->isSubmitted && $form->isValid) {
            $user = $this->get(Auth::class)->authenticateLoginForm($form, $this->request);

            // if user exists then redirect to homepage
            if (!empty($user)) {
                $this->addFlash('success', 'Welcome, ' . $user->getUsername() . '!');

                return $this->redirectToRoute('home');
            }

            // if user not exists then display invalid credentials
            $this->addFlash('danger', 'Email or password Invalid.');
        }

        return $this->render('app/login.html.twig', ['form' => $form]);
    }

    /**
     * Displays the register page.
     */
    public function register(): Response
    {
        $form = new RegisterForm($this->get(RegisterValidation::class));
        $form->handleRequest($this->request);

        if ($form->isSubmitted && $form->isValid) {
            $this->get(UserManager::class)->saveNewUser($form);
            $this->addFlash('success', 'You register with success!');

            return $this->redirectToRoute('login');
        }

        return $this->render('app/register.html.twig', ['form' => $form]);
    }

    /**
     * Logs out the user and redirect to homepage.
     */
    public function logout(): Response
    {
        if ($this->isCsrfTokenValid($this->request->request->get('csrf_token'))) {
            $this->get(Auth::class)->handleLogout($this->request);
            $this->addFlash('success', 'You logout with success!');
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Displays the Terms of use page.
     */
    public function termsOfUse(): Response
    {
        return $this->render('app/terms_of_use.html.twig');
    }
}
