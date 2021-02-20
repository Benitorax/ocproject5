<?php
namespace App\Model;

use Twig\Environment;
use Config\Router\Request;
use Twig\Loader\FilesystemLoader;

class View
{
    private $request;
    private $session;
    private $loader;
    private $twig;

    public function __construct()
    {
        $this->request = new Request();
        $this->session = $this->request->getSession();
        $this->loader = new FilesystemLoader(\dirname(\dirname(__DIR__)).'\templates');
        $this->twig = new Environment($this->loader, [
            'cache' => \dirname(\dirname(__DIR__)).'\var\cache\twig',
        ]);
    }

    public function render($template, $data = [])
    {
        // $data = array_merge($data, $this->session->toArray());
        echo $this->twig->render($template, $data);
    }
}