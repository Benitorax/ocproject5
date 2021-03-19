<?php

namespace App\Controller;

use App\Service\Auth;
use App\Form\LoginForm;
use App\Form\ContactForm;
use App\Form\RegisterForm;
use App\Service\UserManager;
use App\Service\Mailer\Notification;
use Framework\Response\Response;
use Framework\Controller\AbstractController;
use App\Service\Validation\LoginValidation;
use App\Service\Validation\ContactValidation;
use App\Service\Validation\RegisterValidation;

class AppController extends AbstractController
{
    /**
     * Displays the home page with contact form visible only by logged users.
     */
    public function home(): Response
    {
        // creates the form and handles the request
        /** @var ContactValidation */
        $validation = $this->get(ContactValidation::class);

        $form = new ContactForm($validation, $this->getUser());
        $form->handleRequest($this->request);

        // if the form is valid, then send email
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Notification */
            $notification = $this->get(Notification::class);
            $mailCount = $notification->notifyContact($form);

            // checks if at least one mail has been sent
            if (0 === $mailCount) {
                $this->addFlash('danger', 'The messaging service has technical problems. Please try later.');
            } else {
                $form->clear();
                $this->addFlash('success', 'Your message has been sent with success!');
            }
        }

        return $this->render('app/home.html.twig', ['form' => $form]);
    }

    /**
     * Displays the login page.
     */
    public function login(): Response
    {
        // if the user is already authenticated, then redirects to home page
        if ($this->isGranted(['user'])) {
            return $this->redirectToRoute('home');
        }

        /** @var LoginValidation */
        $validation = $this->get(LoginValidation::class);

        $form = new LoginForm($validation);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Auth */
            $auth = $this->get(Auth::class);
            $user = $auth->authenticateLoginForm($form, $this->request);

            // if user exists then redirect to homepage
            if (!empty($user)) {
                $this->addFlash('success', 'Welcome, ' . $user->getUsername() . '!');

                return $this->redirectToRoute('home');
            }

            // if user does not exist then displays invalid credentials
            $this->addFlash('danger', 'Email or password Invalid.');
        }

        return $this->render('app/login.html.twig', ['form' => $form]);
    }

    /**
     * Displays the register page.
     */
    public function register(): Response
    {
        /** @var RegisterValidation */
        $validation = $this->get(RegisterValidation::class);

        $form = new RegisterForm($validation);
        $form->handleRequest($this->request);

        // if the form is valid, then persists the user in the database
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UserManager */
            $manager = $this->get(UserManager::class);
            $manager->saveNewUser($form);

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
        // checks if the csrf token is valid to execute the logout
        if ($this->isCsrfTokenValid($this->request->request->get('csrf_token'))) {

            /** @var Auth */
            $auth = $this->get(Auth::class);
            $auth->handleLogout($this->request);

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
