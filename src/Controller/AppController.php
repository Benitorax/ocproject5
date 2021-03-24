<?php

namespace App\Controller;

use App\Form\ContactForm;
use App\Form\RegisterForm;
use App\Service\UserManager;
use App\Service\Mailer\Notification;
use Framework\Response\Response;
use Framework\Controller\AbstractController;
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
     * Displays the Terms of use page.
     */
    public function termsOfUse(): Response
    {
        return $this->render('app/terms_of_use.html.twig');
    }
}
