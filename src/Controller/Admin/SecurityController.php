<?php

namespace App\Controller\Admin;

use App\Service\Auth;
use App\Form\LoginForm;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class SecurityController extends AbstractController
{
    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Displays the login page for admin users.
     */
    public function login(): Response
    {
        // if the user is already authenticated, then redirects to home page
        if ($this->isGranted(['admin'])) {
            return $this->redirectToRoute('admin_post_index');
        }

        /** @var LoginForm */
        $form = $this->createForm(LoginForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->auth->authenticateAdminLoginForm($form, $this->request);

            // if user exists then redirect to homepage
            if (!empty($user)) {
                $this->addFlash('success', 'Welcome, ' . $user->getUsername() . '!');

                return $this->redirectToRoute('admin_post_index');
            }

            // if user does not exist then displays invalid credentials
            $this->addFlash('danger', 'Email or password Invalid.');
        }

        return $this->render('admin/security/login.html.twig', ['form' => $form]);
    }
}
