<?php

namespace Framework\Test;

use Framework\Cookie\Cookie;
use Framework\Response\Response;
use Framework\Test\DomCrawler\Crawler;

trait WebTestAssertionsTrait
{
    public static function assertResponseIsSuccessful(): void
    {
        self::assertThat(self::getStatusCode(), self::logicalAnd(
            self::greaterThanOrEqual(200),
            self::lessThan(300)
        ), sprintf('Failed: status code %d should be between 200 and 299', self::getStatusCode()));
    }

    public static function assertResponseIsRedirect(): void
    {
        self::assertThat(self::getStatusCode(), self::logicalAnd(
            self::greaterThanOrEqual(300),
            self::lessThan(400)
        ), sprintf('Failed: status code %d should be between 300 and 399', self::getStatusCode()));
    }

    public static function assertResponseIsError(): void
    {
        self::assertThat(self::getStatusCode(), self::logicalAnd(
            self::greaterThanOrEqual(400),
            self::lessThan(600)
        ), sprintf('Failed: status code %d should be between 400 and 599', self::getStatusCode()));
    }

    /**
     * The selector is the tag element
     * e.g.: $selector = "h1"
     */
    public static function assertTextContains(string $selector, string $text): void
    {
        $selectedTexts = implode('', self::getCrawler()->getTextByTag($selector));
        self::assertStringContainsString($text, $selectedTexts, sprintf(
            'Failed asserting that text within <%s> contains "%s"',
            $selector,
            $text
        ));
    }

    /**
     * The selector is the tag element
     * e.g.: $selector = "h1"
     */
    public static function assertTextNotContains(string $selector, string $text): void
    {
        $selectedTexts = implode('', self::getCrawler()->getTextByTag($selector));
        self::assertStringNotContainsString($text, $selectedTexts, sprintf(
            'Failed asserting that text within <%s> does not contain "%s"',
            $selector,
            $text
        ));
    }

    public static function assertTextContainsForm(string $text): void
    {
        $selectedTexts = implode('', self::getCrawler()->getTextByTag('form'));
        self::assertStringContainsString($text, $selectedTexts, sprintf(
            'Failed asserting that text contains <form> with name="%s"',
            $text
        ));
    }

    public static function assertTextNotContainsForm(string $text): void
    {
        $selectedTexts = implode('', self::getCrawler()->getTextByTag('form'));
        self::assertStringNotContainsString(htmlspecialchars($text, ENT_QUOTES), $selectedTexts, sprintf(
            'Failed asserting that text within contains <form> with name="%s"',
            $text
        ));
    }

    public static function assertCookiesHasName(string $name): void
    {
        $cookieNames = [];
        foreach (self::getCookies() as $cookie) {
            $cookieNames[] = $cookie->getName();
        }

        self::assertContains($name, $cookieNames, sprintf(
            'Failed asserting that client has cookie "%s',
            $name
        ));
    }

    public static function assertCookiesCount(int $count): void
    {
        $expectedCount = count(self::getCookies());
        self::assertSame($expectedCount, $count, sprintf(
            'Failed asserting that client should have %d cookies, but %s given',
            $expectedCount,
            $count
        ));
    }

    public static function getStatusCode(): int
    {
        return self::getResponse()->getStatusCode();
    }

    public static function getResponse(): Response
    {
        return self::$client->getResponse();
    }

    public static function getCrawler(): Crawler
    {
        return self::$client->getCrawler();
    }

    /**
     * @return Cookie[]
     */
    public static function getCookies()
    {
        return self::$client->getCookies();
    }
}
