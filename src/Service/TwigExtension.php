<?php
namespace App\Service;

use Twig\TwigFunction;
use App\Service\UrlGenerator;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
    private $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('url', [$this, 'generateUrl']),
        ];
    }

    public function generateUrl(string $routeName, array $routeParams = []): string
    {
        return $this->urlGenerator->generate($routeName, $routeParams);
    }
}
