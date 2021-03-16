<?php

namespace Config\View;

use Twig\Environment;
use Config\Request\Request;
use Config\Response\Response;
use App\Service\TwigExtension;
use Config\Security\TokenStorage;
use Twig\Loader\FilesystemLoader;

/**
 * This is the renderer class for controller template and email template
 */
class View
{
    private Environment $twig;
    private TokenStorage $tokenStorage;

    public function __construct(TokenStorage $tokenStorage, TwigExtension $twigExtension)
    {
        $this->tokenStorage = $tokenStorage;

        $loader = new FilesystemLoader(\dirname(__DIR__, 2) . '\templates');
        $this->twig = new Environment($loader, /*['cache' => \dirname(__DIR__, 2).'\var\cache\twig']*/);
        $this->twig->addExtension($twigExtension);
    }

    public function render(string $template, ?array $parameters = [], ?Response $response = null): Response
    {
        $content = $this->twig->render($template, (array) $parameters);

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    public function renderEmail(string $template, ?array $parameters = []): string
    {
        return $this->twig->render($template, (array) $parameters);
    }

    /**
     * Hydrates the AppVariable for Twig template.
     */
    public function setRequest(Request $request): void
    {
        $app = new AppVariable();
        $app->setRequest($request);
        $app->setTokenStorage($this->tokenStorage);
        $this->twig->addGlobal('app', $app);
    }
}
