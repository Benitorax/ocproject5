<?php

namespace Framework\Test\DomCrawler;

class Crawler
{
    private string $content;
    private string $uri;

    public function __construct(string $content, string $uri)
    {
        $this->content = $content;
        $this->uri = $uri;
    }

    /**
     * Returns a Form instance.
     *
     * $name is the value of "name" attribute of <form> tag.
     */
    public function getForm(string $name): Form
    {
        // matches form
        $pattern = '#<form[ "\'-=\w\s]*name="' . $name . '"[ "\'-=\w\s]*>#';
        if (!preg_match($pattern, $this->content, $form)) {
            throw new \Exception(sprintf('Form with name "%s" does not exist.', $name));
        }

        // matches method
        if (preg_match('#method=[\'"]([-\w]*)[\'"]#', $form[0], $match)) {
            $method = strtoupper($match[1]);
        }

        // matches action
        if (preg_match('#action=[\'"]([-\/\w]*)[\'"]#', $form[0], $match)) {
            $uri = $match[1];
        }

        $form = new Form($method ?? 'GET', $uri ?? $this->uri);

        // matches input with name="csrf_token".
        $pattern = '#<input[ "\'-=\w]*name="csrf_token"[ "\'-=\w]*>#';
        if (preg_match($pattern, $this->content, $input)) {
            if (preg_match('#value=[\'"]([-\w]*)[\'"]#', $input[0], $match)) {
                $form->setValue('csrf_token', $match[1]);
            }
        }

        return $form;
    }

    /**
     * Returns a Link instance.
     */
    public function selectLink(string $text, ?int $counter = null): Link
    {
        // matches form
        $pattern = '#<a [@&.!?,;:\-=\'"\\\/\s\w]*>[.-<>\'"=\/\w\s]*' . $text . '[@&.!?,;:\-<>\'"=\/\w\s]*<\/a>#';
        if (!preg_match_all($pattern, $this->content, $links)) {
            throw new \Exception(sprintf('Link with text "%s" does not exist.', $text));
        }

        $link = null === $counter ? $links[0] : $links[$counter];

        // matches href
        $uri = null;
        if (preg_match('#href=[\'"]([@&.!?,;:\-<>=\/\w]*)[\'"]#', $link[0], $match)) {
            $uri = $match[1];
        }

        if (null === $uri) {
            throw new \Exception(sprintf('Link with text "%s" has no href attribute.', $text));
        }

        return new Link($uri);
    }

    /**
     * Returns a text within an element tag.
     * e.g.: $selector = 'h1'
     */
    public function getTextByTag(string $selector, int $counter = null): array
    {
        // matches tag element
        $pattern = '#<' . $selector . '(>|[@&.!?,;:\-=\'"\\\/\s\w\#]*>)[@&.!?,;:\-<>=\'"\\\/\w\s\#]*<\/' . $selector . '>#';
        if (preg_match_all($pattern, $this->content, $texts)) {
            if ($counter) {
                return [$texts[0][$counter]];
            }

            return $texts[0];
        }

        return [];
    }
}
