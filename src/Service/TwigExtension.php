<?php
namespace App\Service;

use Twig\TwigFunction;
use App\Service\UrlGenerator;
use Config\Security\Csrf\CsrfTokenManager;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
    private $urlGenerator;
    private $csrfTokenManager;

    public function __construct(UrlGenerator $urlGenerator, CsrfTokenManager $csrfTokenManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('url', [$this, 'generateUrl']),
            new TwigFunction('csrf_token', [$this, 'generateCsrfToken']),
        ];
    }

    public function generateUrl(string $routeName, array $routeParams = []): string
    {
        return $this->urlGenerator->generate($routeName, $routeParams);
    }

    public function generateCsrfToken(): string
    {
        return $this->csrfTokenManager->generateToken();
    }
}
