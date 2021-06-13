<?php

namespace App\Tests\Controller;

use Framework\Test\WebTestCase;
use App\Tests\Controller\Fixtures\FixturesLoader;

class AppWebTestCase extends WebTestCase
{
    protected FixturesLoader $loader;
    public static array $posts;
    public static array $users;

    public function setup(): void
    {
        $app = static::bootApp();
        $this->loader = new FixturesLoader($app->getContainer());
        $this->cleanDatabase();
        $this->loadFixtures();
        self::$client = static::createClient();
    }

    public function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function cleanDatabase(): void
    {
        // cleans database
        $this->loader->truncateTables();

        // cleans posts and users properties
        self::$posts = [];
        self::$users = [];
    }

    public function loadFixtures(): void
    {
        $data = $this->loader->loadFixtures();
        self::$posts = $data['posts'];
        self::$users = $data['users'];
    }
}
