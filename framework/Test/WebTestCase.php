<?php

namespace Framework\Test;

use Framework\App;
use Framework\Dotenv\Dotenv;
use Framework\Test\HttpBrowser;
use Framework\Container\Container;
use PHPUnit\Framework\TestCase;

class WebTestCase extends TestCase
{
    use WebTestAssertionsTrait;

    public static ?App $app = null;
    public static HttpBrowser $client;

    /**
     * Creates a AppBrowser instance.
     */
    protected static function createClient(): HttpBrowser
    {
        $app = static::bootApp();
        self::$client = $app->getContainer()->get(HttpBrowser::class);
        self::$client->setApp($app);

        return self::$client;
    }

    /**
     * Boots the App for this test.
     */
    protected static function bootApp(): App
    {
        static::ensureAppShutdown();
        static::$app = static::createApp();

        return static::$app;
    }

    /**
     * Creates a App.
     */
    protected static function createApp(): App
    {
        $dotenv = new Dotenv();
        if (file_exists(dirname(__DIR__, 2) . '/.env.test.local')) {
            $dotenv->loadEnv(dirname(__DIR__, 2) . '/.env.test.local');
        } else {
            $dotenv->loadEnv(dirname(__DIR__, 2) . '/.env.test');
        }

        return new App($dotenv);
    }

    /**
     * Shuts down the App.
     */
    protected static function ensureAppShutdown(): void
    {
        if (null !== self::$app) {
            self::$app->shutDown();
        }
    }
}
