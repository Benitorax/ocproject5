<?php
namespace App\Controller;

class ErrorController extends Controller
{
    public function errorNotFound()
    {
        return $this->view->render('error/error_404.html.twig');
    }

    public function errorServer($e)
    {
        return $this->view->render('error/error_500.html.twig', [
            'error' => $e
        ]);
    }
}