<?php

namespace App\Tests\Controller;

class PostControllerTest extends AppWebTestCase
{
    public function testIndex(): void
    {
        self::$client->request('GET', '/posts');
        $post = self::$posts['published'][0];

        // should display the published post and only its lead
        $this->assertTextContains('h4', $post->getTitle());
        $this->assertTextContains('p', $post->getLead());
        $this->assertTextNotContains('p', $post->getContent());

        // should not display the unpublished post
        $this->assertTextNotContains('h4', self::$posts['unpublished'][0]->getTitle());

        // post title should be clickable to see the content of the post
        self::$client->clickLink($post->getTitle());
        $this->assertTextContains('p', $post->getContent());
    }

    public function testUnpublishedPost(): void
    {
        self::$client->request('GET', '/post/' . self::$posts['unpublished'][0]->getSlug());
        $this->assertResponseIsError();
    }

    public function testShowWhenNotLoggedIn(): void
    {
        $post = self::$posts['published'][0];

        self::$client->request('GET', '/post/' . $post->getSlug());
        $this->assertTextContains('h1', $post->getTitle());
        $this->assertTextContains('p', $post->getLead());
        $this->assertTextContains('p', $post->getContent());
        $this->assertTextContains('p', 'This is the content of the comment 3');
        $this->assertTextNotContainsForm('comment');
    }

    public function testShowWhenLoggedIn(): void
    {
        self::$client->loginUser(self::$users['user']['Sacha']);
        self::$client->request('GET', '/post/' . self::$posts['published'][0]->getSlug());
        $this->assertTextContainsForm('comment');
    }
}
