<?php

namespace Framework\Test;

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

    /**
     * The selector is the tag element
     * e.g.: $selector = "h1"
     */
    public static function assertSelectedTextContains(string $selector, string $text): void
    {
        $selectedTexts = implode('', self::getCrawler()->getTextByTag($selector));
        self::assertStringContainsString(htmlspecialchars($text), $selectedTexts);
    }

    /**
     * The selector is the tag element
     * e.g.: $selector = "h1"
     */
    public static function assertSelectedTextNotContains(string $selector, string $text): void
    {
        $selectedTexts = implode('', self::getCrawler()->getTextByTag($selector));
        self::assertStringNotContainsString(htmlspecialchars($text), $selectedTexts);
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
}
