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

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->session = $this->request->getSession();
        $this->loader = new FilesystemLoader(\dirname(\dirname(__DIR__)).'\templates');
        $this->twig = new Environment($this->loader, [
            'cache' => \dirname(\dirname(__DIR__)).'\var\cache\twig',
        ]);
    }

    public function render($template, $data = [])
    {
        // add session to have session data inside Twig template
        // $data = array_merge($data, $this->session->toArray());
        $data = array_merge($data, []);
        echo $this->twig->render($template, $data);
    }
}