<?php
namespace App\Controller;

class ErrorController extends Controller
{
    public function notFound()
    {
        return $this->view->render('error/error_404.html.twig');
    }

    public function server($e)
    {
        return $this->view->render('error/error_500.html.twig', [
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ]);
    }
}
