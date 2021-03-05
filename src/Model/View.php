<?php
namespace App\Model;

use Twig\Environment;
use Config\Request\Request;
use App\Service\TwigExtension;
use Twig\Loader\FilesystemLoader;

class View
{
    private $request;
    private $session;
    private $loader;
    private $twig;

    public function __construct(TwigExtension $twigExtension)
    {
        $this->loader = new FilesystemLoader(\dirname(__DIR__, 2).'\templates');
        $this->twig = new Environment($this->loader, [
            'cache' => \dirname(__DIR__, 2).'\var\cache\twig',
        ]);
        $this->twig->addExtension($twigExtension);
    }

    public function render($template, $data = [])
    {
        // add session to have session data inside Twig template
        // $data = array_merge($data, $this->session->toArray());
        $data = array_merge($data, []);
        echo $this->twig->render($template, $data);
    }

    public function setRequest(Request $request) 
    {
        $this->request = $request;
        $this->session = $this->request->session;
    }
}