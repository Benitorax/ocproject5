<?php

namespace App\Tests\Controller;

class CommentControllerTest extends AppWebTestCase
{
    public function testCreateForUnpublishedPost(): void
    {
        self::$client->request(
            'POST',
            '/post/' . self::$posts['unpublished'][0]->getUuid() . '/comment/create'
        );
        $this->assertResponseIsError();
    }

    public function testCreateForPublishedPostWhenNotLoggedIn(): void
    {
        self::$client->request(
            'POST',
            '/post/' . self::$posts['published'][0]->getUuid() . '/comment/create',
            [
                'content' => 'This is the content of my comment',
                'csrf_token' => self::$client->getCsrfToken()
            ]
        );
        $this->assertResponseIsError();
    }

    public function testCreateForPublishedPostWhenLoggedIn(): void
    {
        self::$client->loginUser(self::$users['user']['Sacha']);
        self::$client->request(
            'POST',
            '/post/' . self::$posts['published'][0]->getUuid() . '/comment/create',
            [
                'content' => 'This is the content of my comment',
                'csrf_token' => self::$client->getCsrfToken()
            ]
        );
        $this->assertResponseIsRedirect();
    }

    public function testCreateForPublishedPostWhenBlocked(): void
    {
        self::$client->loginUser(self::$users['user']['Monica']);
        self::$client->request(
            'POST',
            '/post/' . self::$posts['published'][0]->getUuid() . '/comment/create',
            [
                'content' => 'This is the content of my comment',
                'csrf_token' => self::$client->getCsrfToken()
            ]
        );
        $this->assertResponseIsError();
    }
}
