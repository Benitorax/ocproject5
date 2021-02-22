<?php
namespace App\Controller;

use App\DAO\DAO;
use App\Model\View;
use Config\Router\Request;

abstract class Controller
{
    protected $view;
    protected $DAO;
    protected $userDAO;
    protected $postDAO;
    protected $commentDAO;
    protected $request;
    protected $get;
    protected $post;
    protected $session;

    public function __construct(View $view, DAO $DAO, Request $request)
    {
        $this->view = $view;
        $this->DAO = $DAO;
        $this->userDAO = $DAO->getUserDAO();
        $this->postDAO = $DAO->getPostDAO();
        $this->commentDAO = $DAO->getCommentDAO();
        $this->request = $request;
        $this->get = $this->request->getGet();
        $this->post = $this->request->getPost();
        $this->session = $this->request->getSession();
    }
}