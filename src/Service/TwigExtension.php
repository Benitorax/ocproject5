<?php

namespace App\Service;

use Twig\TwigFunction;
use Config\Router\UrlGenerator;
use Config\Security\Csrf\CsrfTokenManager;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
    private UrlGenerator $urlGenerator;
    private CsrfTokenManager $csrfTokenManager;

    public function __construct(UrlGenerator $urlGenerator, CsrfTokenManager $csrfTokenManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'generatePath']),
            new TwigFunction('url', [$this, 'generateUrl']),
            new TwigFunction('csrf_token', [$this, 'generateCsrfToken']),
        ];
    }

    public function generatePath(string $routeName, array $routeParams = []): string
    {
        return $this->urlGenerator->generate($routeName, $routeParams);
    }

    public function generateUrl(string $routeName, array $routeParams = []): string
    {
        return $this->urlGenerator->generate($routeName, $routeParams, UrlGenerator::URL_TYPE);
    }

    public function generateCsrfToken(): string
    {
        return $this->csrfTokenManager->generateToken();
    }
}
