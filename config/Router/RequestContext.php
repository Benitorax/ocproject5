<?php
namespace Config\Router;

use Config\Request\Request;

class RequestContext
{
    private string $method;
    private string $host;
    private string $scheme;
    private int $port;
    private string $pathInfo;
    private string $queryString;

    public function __construct()
    {
        $this->method = 'GET';
        $this->host = 'localhost';
        $this->scheme = 'http';
        $this->port = 80;
        $this->pathInfo = '/';
        $this->queryString = '';
    }

    public function fromRequest(Request $request): self
    {
        $this->method = $request->getMethod();
        $this->host = $request->getHost();
        $this->scheme = $request->getScheme();
        $this->port = $request->getPort();
        $this->pathInfo = $request->getPathInfo();
        $this->queryString = $request->getQueryString();

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getpathInfo(): string
    {
        return $this->pathInfo;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getSchemeAndHost(): string
    {
        return $this->scheme.'://'.$this->host;
    }
}
