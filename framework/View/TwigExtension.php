<?php

namespace Framework\View;

use Twig\TwigFunction;
use Framework\Router\UrlGenerator;
use Framework\Security\Csrf\CsrfTokenManager;
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
            new TwigFunction('queryWithParams', [$this, 'generateQueryStringWithParams'])
        ];
    }

    /**
     * Generates an absolute path without the scheme and host.
     */
    public function generatePath(string $routeName, array $routeParams = []): string
    {
        return $this->urlGenerator->generate($routeName, $routeParams);
    }

    /**
     * Generates an absolute url with the scheme and host.
     */
    public function generateUrl(string $routeName, array $routeParams = []): string
    {
        return $this->urlGenerator->generate($routeName, $routeParams, UrlGenerator::URL_TYPE);
    }

    /**
     * Generates a csrf token.
     */
    public function generateCsrfToken(): string
    {
        return $this->csrfTokenManager->generateToken();
    }

    /**
     * Generates a new query string with params.
     */
    public function generateQueryStringWithParams(string $queryString, array $params): string
    {
        parse_str($queryString, $queryArray);
        $queryArray = array_merge($queryArray, $params);

        return http_build_query($queryArray);
    }
}
