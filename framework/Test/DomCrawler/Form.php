<?php

namespace Framework\Test\DomCrawler;

class Form
{
    private string $uri;
    private string $method;
    private array $parameters = [];

    public function __construct(string $method, string $uri = 'GET')
    {
        $this->uri = $uri;
        $this->method = $method;
    }

    public function setValue(string $name, string $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    public function setValues(array $parameters): self
    {
        foreach ($parameters as $name => $value) {
            $this->setValue($name, $value);
        }

        return $this;
    }

    public function removeValue(string $name): self
    {
        if (isset($this->parameters[$name])) {
            unset($this->parameters[$name]);
        }

        return $this;
    }

    public function removeValues(array $names): self
    {

        foreach ($names as $name) {
            $this->removeValue($name);
        }

        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
