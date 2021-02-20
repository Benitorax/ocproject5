<?php
namespace App\Controller;

use App\Model\View;
use App\DAO\PostDAO;
use Config\Router\Request;

abstract class Controller
{
    protected $view;
    protected $postDAO;
    private $request;
    protected $get;
    protected $post;
    protected $session;

    public function __construct()
    {
        $this->view = new View();
        $this->postDAO = new PostDAO();
        $this->request = new Request();
        $this->get = $this->request->getGet();
        $this->post = $this->request->getPost();
        $this->session = $this->request->getSession();
    }
}