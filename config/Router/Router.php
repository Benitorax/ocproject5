<?php
namespace Config\Router;

use Exception;
use Config\Router\Request;
use App\Controller\AppController;
use App\Controller\ErrorController;

class Router
{
    private $errorController;
    private $appController;

    public function __construct()
    {
        $this->appController = new AppController();
        $this->errorController = new ErrorController();
        $this->request = new Request();
    }

    public function run()
    {
        try{
            if(isset($_GET['route']))
            {
                if($_GET['route'] === 'home'){
                    $this->appController->home();
                }
                // elseif($_GET['route'] === 'post'){
                //     $this->frontController->article($this->request->getGet()->get('articleId'));
                // }
                // elseif($_GET['route'] === 'addArticle'){
                //     $this->backController->addArticle($this->request->getPost());
                // }
                else{
                    $this->errorController->errorNotFound();
                }
            }
            else{
                $this->appController->home();
            }
        }
        catch (Exception $e)
        {
            $this->errorController->errorServer($e);
        }
    }
}