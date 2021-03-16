<?php

namespace Config\Request;

use Config\Session\Session;
use Config\Request\Parameter;

class Request
{
    public Parameter $query;
    public Parameter $request;
    public Parameter $cookies;
    public Parameter $server;
    private ?Session $session;
    public Parameter $attributes;
    private ?string $requestUri = null;
    private ?string $pathInfo = null;

    public function create(): self
    {
        $this->query = new Parameter($_GET ?: []);
        $this->request = new Parameter($_POST ?: []);
        $this->cookies = new Parameter($_COOKIE ?: []);
        $this->server = new Parameter($_SERVER ?: []);
        $this->attributes = new Parameter([]);

        return $this;
    }

    public function getMethod(): string
    {
        /** @var string */
        return $this->server->get('REQUEST_METHOD', 'GET');
    }

    public function getRequestUri(): string
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }

        return $this->requestUri;
    }

    public function prepareRequestUri(): string
    {
        $requestUri = '';

        if ('1' === $this->server->get('IIS_WasUrlRewritten') && '' !== $this->server->get('UNENCODED_URL')) {
            // IIS7 with URL Rewrite: make sure we get the unencoded URL (double slash problem)
            $requestUri = $this->server->get('UNENCODED_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');

            if ('' !== $requestUri && '/' === $requestUri[0]) {
                // To only use path and query remove the fragment.
                if (false !== $pos = strpos($requestUri, '#')) {
                    $requestUri = substr($requestUri, 0, $pos);
                }
            } else {
                // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path,
                // only use URL path.
                $uriComponents = (array) parse_url($requestUri);

                if (isset($uriComponents['path'])) {
                    $requestUri = $uriComponents['path'];
                }

                if (isset($uriComponents['query'])) {
                    $requestUri .= '?' . $uriComponents['query'];
                }
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ('' !== $this->server->get('QUERY_STRING')) {
                $requestUri .= '?' . $this->server->get('QUERY_STRING');
            }
            $this->server->remove('ORIG_PATH_INFO');
        }

        // normalize the request URI to ease creating sub-requests from this request
        $this->server->set('REQUEST_URI', $requestUri);

        return $requestUri;
    }

    public function getPathInfo(): string
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }

        return $this->pathInfo;
    }

    protected function preparePathInfo(): string
    {
        $requestUri = $this->getRequestUri();

        // Remove the query string from REQUEST_URI
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/' . $requestUri;
        }

        return (string) $requestUri;
    }

    public function getSession(): ?Session
    {
        if ($this->hasSession()) {
            return $this->session;
        }
        return null;
    }

    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

    public function hasSession(): bool
    {
        return null !== $this->session;
    }

    public function isSecure(): bool
    {
        $https = $this->server->get('HTTPS');

        return !empty($https) && 'off' !== strtolower($https);
    }

    public function getScheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function getHost(): string
    {
        return $this->server->get('HTTP_HOST');
    }

    public function getPort(): int
    {
        return $this->server->get('SERVER_PORT');
    }

    public function getQueryString(): string
    {
        $qs = $this->server->get('QUERY_STRING');

        if ('' === ($qs ?? '')) {
            return '';
        }

        parse_str($qs, $qs);
        ksort($qs);

        $qs = http_build_query($qs, '', '&', \PHP_QUERY_RFC3986);

        return $qs;
    }
}
