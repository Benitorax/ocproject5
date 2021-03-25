<?php

namespace App\Controller;

use App\Service\Auth;
use App\Form\LoginForm;
use Framework\View\View;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\Controller\AbstractController;

class SecurityController extends AbstractController
{
    private Auth $auth;

    public function __construct(
        View $view,
        Container $container,
        Auth $auth
    ) {
        parent::__construct($view, $container);
        $this->auth = $auth;
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

        /** @var LoginForm */
        $form = $this->createForm(LoginForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->auth->authenticateLoginForm($form, $this->request);

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
     * Logs out the user and redirect to homepage.
     *
     * Always redirects to homepage whether or not the csrf is valid.
     */
    public function logout(): Response
    {
        // checks if the csrf token is valid to execute the logout
        if ($this->isCsrfTokenValid($this->request->request->get('csrf_token'))) {
            $this->auth->handleLogout($this->request);
            $this->addFlash('success', 'You logout with success!');
        }

        return $this->redirectToRoute('home');
    }
}
