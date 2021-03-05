<?php
namespace Config\View;

use Twig\Environment;
use Config\Request\Request;
use Config\Response\Response;
use App\Service\TwigExtension;
use Twig\Loader\FilesystemLoader;

class View
{
    private $request;
    private $session;
    private $twig;

    public function __construct(TwigExtension $twigExtension)
    {
        $loader = new FilesystemLoader(\dirname(__DIR__, 2).'\templates');
        $this->twig = new Environment($loader, [
            'cache' => \dirname(__DIR__, 2).'\var\cache\twig',
        ]);
        $this->twig->addExtension($twigExtension);
    }

    public function render($template, $parameters = [], Response $response = null): Response
    {
        // add session to have session data inside Twig template
        // $parameters = array_merge($parameters, $this->session->toArray());
        $parameters = array_merge($parameters, []);
        $content = $this->twig->render($template, $parameters);

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        $this->session = $this->request->session;
    }
}
