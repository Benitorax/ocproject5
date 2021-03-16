<?php

namespace App\Controller;

use Exception;
use Config\Response\Response;

class ErrorController extends Controller
{
    public function notFound(): Response
    {
        return $this->view->render('error/error_404.html.twig');
    }

    public function server(Exception $e): Response
    {
        return $this->view->render('error/error_500.html.twig', [
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ]);
    }
}
