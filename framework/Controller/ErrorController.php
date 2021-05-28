<?php

namespace Framework\Controller;

use Throwable;
use Framework\Response\Response;

class ErrorController extends AbstractController
{
    /**
     * Displays error 403 page.
     */
    public function forbidden(): Response
    {
        return $this->render('error/error_403.html.twig');
    }

    /**
     * Displays error 404 page.
     */
    public function notFound(): Response
    {
        return $this->render('error/error_404.html.twig');
    }

    /**
     * Displays server error page.
     */
    public function server(): Response
    {
        return $this->render('error/error_500.html.twig');
    }

    /**
     * Displays debug page.
     */
    public function debug(Throwable $error): Response
    {
        return $this->render('error/debug.html.twig', [
            'error' => $error
        ]);
    }
}
