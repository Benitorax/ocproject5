<?php
namespace Config\View;

use Twig\Environment;
use Config\Request\Request;
use Config\Response\Response;
use App\Service\TwigExtension;
use Config\Security\TokenStorage;
use Twig\Loader\FilesystemLoader;

class View
{
    private $twig;
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage, TwigExtension $twigExtension)
    {
        $this->tokenStorage = $tokenStorage;

        $loader = new FilesystemLoader(\dirname(__DIR__, 2).'\templates');
        $this->twig = new Environment($loader, [
            'cache' => \dirname(__DIR__, 2).'\var\cache\twig',
        ]);
        $this->twig->addExtension($twigExtension);
    }

    public function render($template, $parameters = [], Response $response = null): Response
    {
        $content = $this->twig->render($template, $parameters);

        if (null === $response) {
            $response = new Response();
        }
        
        $response->setContent($content);

        return $response;
    }

    public function setRequest(Request $request)
    {
        $app = new AppVariable();
        $app->setRequest($request);
        $app->setTokenStorage($this->tokenStorage);
        $this->twig->addGlobal('app', $app);
    }
}
