<?php

namespace Framework\Test;

use Framework\App;
use Framework\Cookie\Cookie;
use Framework\Request\Request;
use Framework\Session\Session;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\Test\DomCrawler\Form;
use Framework\Test\DomCrawler\Link;
use Framework\Security\TokenStorage;
use Framework\Test\DomCrawler\Crawler;
use Framework\Security\User\UserInterface;
use Framework\Security\Csrf\CsrfTokenManager;

class HttpBrowser
{
    private App $app;
    private Request $request;
    private Response $response;
    private Crawler $crawler;
    private Session $session;
    /**
     * @var Cookie[]
     */
    private $cookies = [];

    private ?string $redirect = null;

    private const DEFAULT_VALUES = [
            'HTTPS' => 'On',
            'HTTP_HOST' => '127.0.0.1:8000',
            'SERVER_PORT' => '8000'
    ];

    public function setApp(App $app): void
    {
        $this->app = $app;
        $this->session = $this->getContainer()->get(Session::class);
    }

    public function request(string $method, string $uri, array $parameters = null): Crawler
    {
        $this->hydrateServer($method, $uri);
        $this->rebootApp();

        $this->request = (new Request())->create($parameters);
        $this->response = $this->app->handle($this->request);

        $code = $this->response->getStatusCode();
        if ($code >= 300 && $code < 400) {
            $this->redirect = $this->response->getHeader('Location');
        }

        $this->session = $this->app->getContainer()->get(Session::class);
        $this->cookies = $this->response->getCookies();

        return $this->crawler = new Crawler($this->response->getContent(), $uri);
    }

    /**
     * Makes a request with the redirect url.
     */
    public function followRedirect(): Crawler
    {
        if (null === $this->redirect) {
            throw new \Exception('HttpBrowser can\'t redirect because there is no redirect.');
        }

        $crawler = $this->request('GET', $this->redirect);
        $this->redirect = null;

        return $crawler;
    }

    /**
     * Makes a request from Form.
     */
    public function submit(Form $form): Crawler
    {
        return $this->request($form->getMethod(), $form->getUri(), $form->getParameters());
    }

    /**
     * Makes a request from Form.
     */
    public function submitForm(string $formName, ?array $parameters = null): Crawler
    {
        $form = $this->crawler->getForm($formName);
        if ($parameters) {
            $form->setValues($parameters);
        }

        return $this->submit($form);
    }

    /**
     * Makes a request from Link.
     */
    public function click(Link $link): Crawler
    {
        return $this->request($link->getMethod(), $link->getUri());
    }

    /**
     * Makes a request from Link.
     */
    public function clickLink(string $text, int $counter = null): Crawler
    {
        $link = $this->crawler->selectLink($text, $counter);

        return $this->click($link);
    }

    /**
     * Sets values to superglobale $_SERVER.
     */
    public function hydrateServer(string $method, string $uri): void
    {
        $defaultValues = array_merge(self::DEFAULT_VALUES, [
            'REQUEST_URI' => $this->getAbsoluteUri($uri),
            'REQUEST_METHOD' => $method
        ]);

        foreach ($defaultValues as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }

    public function getAbsoluteUri(string $uri): string
    {
        // already absolute?
        if (0 === strpos($uri, 'http://') || 0 === strpos($uri, 'https://')) {
            return $uri;
        }

        $currentUri = sprintf(
            'http%s://%s',
            isset(self::DEFAULT_VALUES['HTTPS']) ? 's' : '',
            self::DEFAULT_VALUES['HTTP_HOST'] ?? 'localhost'
        );

        return $currentUri . $uri;
    }

    /**
     * Logs in a User.
     */
    public function loginUser(UserInterface $user): self
    {
        $this->getContainer()->get(Session::class)->set('user', $user);
        $this->getContainer()->get(TokenStorage::class)->setUser($user);

        return $this;
    }

    /**
     * Reboots the App instance
     */
    private function rebootApp(): void
    {
        $this->app->shutDown();
        $this->getContainer()->set($this->session);
    }

    public function getContainer(): Container
    {
        return $this->app->getContainer();
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getCrawler(): Crawler
    {
        return $this->crawler;
    }

    /**
     * @return Cookie[]
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    public function getCsrfToken(): string
    {
        return $this->getContainer()->get(CsrfTokenManager::class)->generateToken();
    }
}
