<?php

namespace App\Controller;

use Exception;
use App\Service\Auth;
use App\Form\EmailForm;
use App\Form\LoginForm;
use App\Form\ResetPasswordForm;
use Framework\Response\Response;
use App\Service\ResetPasswordManager;
use Framework\Controller\AbstractController;

class SecurityController extends AbstractController
{
    private const REDIRECT_MESSAGE =
        'You have been redirected from reset password page'
        . ' because you\'re already logged in.';
    private Auth $auth;
    private ResetPasswordManager $resetPasswordManager;

    public function __construct(Auth $auth, ResetPasswordManager $resetPasswordManager)
    {
        $this->auth = $auth;
        $this->resetPasswordManager = $resetPasswordManager;
    }

    /**
     * Displays the login page.
     */
    public function login(): Response
    {
        if ($this->isGranted(['user'])) {
            return $this->redirectToRoute('home');
        }

        /** @var LoginForm */
        $form = $this->createForm(LoginForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->auth->authenticateLoginForm($form, $this->request);

            // checks if user exists
            if (!empty($user)) {
                $this->addFlash('success', 'Welcome, ' . $user->getUsername() . '!');

                if (in_array('admin', $user->getRoles())) {
                    return $this->redirectToRoute('admin_dashboard');
                }

                return $this->redirectToRoute('home');
            }

            // if user does not exist then displays invalid credentials
            $this->addFlash('danger', 'Email or password invalid.');
        }

        return $this->render('security/login.html.twig', ['form' => $form]);
    }

    /**
     * Logs out the user and redirect to homepage.
     *
     * Always redirects to homepage whether or not the csrf is valid.
     */
    public function logout(): Response
    {
        // checks if the csrf token is valid to execute the logout
        if ($this->isCsrfTokenValid()) {
            $this->auth->handleLogout($this->request);

            return $this->redirectToRoute('home');
        }

        if ($this->isGranted(['admin'])) {
            return $this->redirectToRoute('admin_post_index');
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Displays the page to request a reset password.
     */
    public function resetPasswordRequest(): Response
    {
        if ($this->isGranted(['user'])) {
            $this->addFlash('danger', self::REDIRECT_MESSAGE);
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(EmailForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordManager->manageResetRequest($form->getEmail());

            return $this->redirectToRoute('login');
        }

        return $this->render('security/request_reset_password.html.twig', ['form' => $form]);
    }

    /**
     * Displays the page to reset password.
     */
    public function resetPassword(string $token): Response
    {
        if ($this->isGranted(['user'])) {
            $this->addFlash('danger', self::REDIRECT_MESSAGE);

            return $this->redirectToRoute('home');
        }

        try {
            $user = $this->resetPasswordManager->validateTokenAndFetchUser($token);
        } catch (Exception $exception) {
            return $this->redirectToRoute('password_reset_request');
        }

        $form = $this->createForm(ResetPasswordForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordManager->manageReset($user, $form->getPassword1());

            return $this->redirectToRoute('login');
        }

        return $this->render('security/reset_password.html.twig', ['form' => $form]);
    }
}
