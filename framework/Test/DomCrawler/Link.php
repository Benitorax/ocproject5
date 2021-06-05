<?php

namespace Framework\Test\DomCrawler;

class Link
{
    private string $method = 'GET';
    private string $uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
