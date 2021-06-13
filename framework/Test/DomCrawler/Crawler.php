<?php

namespace Framework\Test\DomCrawler;

class Crawler
{
    private string $content;
    private string $uri;
    private \DOMXPath $finder;
    private \DOMDocument $document;

    public function __construct(string $content, string $uri)
    {
        $this->content = $content;
        $this->uri = $uri;
        $this->document = new \DOMDocument();
        @$this->document->loadHTML($this->content);

        $this->finder = new \DOMXPath($this->document);
    }

    /**
     * Returns a Form instance.
     *
     * $name is the value of "name" attribute of <form> tag.
     */
    public function getForm(string $name): Form
    {
        // looks for <form> with name="$name"
        /** @var \DOMNodeList */
        $nodes = $this->finder->query('//form[@name=\'' . $name . '\']');
        $method = strtoupper($nodes[0]->getAttribute('method'));
        $uri = $nodes[0]->getAttribute('action');

        $form = new Form(
            strlen($method) > 0 ? $method : 'GET',
            strlen($uri) > 0 ? $uri : $this->uri
        );

        // looks for input with name="csrf_token"
        /** @var \DOMNodeList */
        $nodes = $this->finder->query('//input[@name=\'csrf_token\']');
        $form->setValue('csrf_token', $nodes[0]->getAttribute('value'));

        return $form;
    }

    /**
     * Returns a Link instance.
     */
    public function selectLink(string $text, ?int $counter = null): Link
    {
        // looks for every <a>
        /** @var \DOMNodeList */
        $nodes = $this->finder->query('//a');

        // looks for every <a> which contains $text
        $links = [];
        foreach ($nodes as $node) {
            $nodeHTML = $this->document->saveHTML($node);
            if (preg_match('#' . $text . '#', (string) $nodeHTML)) {
                $links[] = $node;
            }
        }

        if (count($links) === 0) {
            throw new \Exception(sprintf('No tag <a> with %s was found', $text));
        }

        $link = null !== $counter ? $links[$counter] : $links[0];

        return new Link($link->getAttribute('href')); // @phpstan-ignore-line
    }

    /**
     * Returns a text within an element tag.
     * e.g.: $selector = 'h1'
     */
    public function getTextByTag(string $selector, int $counter = null): array
    {
        /** @var \DOMNodeList */
        $nodes = $this->finder->query('//' . $selector);

        if (null !== $counter) {
            return [$this->document->saveHTML($nodes[$counter])];
        }

        $texts = [];
        foreach ($nodes as $node) {
            $texts[] = $this->document->saveHTML($node);
        }

        return $texts;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
