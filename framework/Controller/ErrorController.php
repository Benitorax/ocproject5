<?php

namespace Framework\Controller;

use Exception;
use Framework\Response\Response;

class ErrorController extends AbstractController
{
    /**
     * Displays error 404 page.
     */
    public function notFound(): Response
    {
        return $this->view->render('error/error_404.html.twig');
    }

    /**
     * Displays server error page.
     */
    public function server(Exception $e): Response
    {
        return $this->view->render('error/error_500.html.twig', [
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ]);
    }
}
