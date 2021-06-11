<?php

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\AppWebTestCase;

class PostControllerTest extends AppWebTestCase
{
    public function setup(): void
    {
        parent::setup();
        self::$client->loginUser(self::$users['admin']['Mike']);
    }

    public function testIndexWhenNotLogged(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/posts');
        $this->assertResponseIsError();
    }

    public function testIndexWhenNotAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser(self::$users['user']['Sacha']);
        $client->request('GET', '/admin/posts');
        $this->assertResponseIsError();
    }

    public function testIndex(): void
    {
        self::$client->request('GET', '/admin/posts');

        // should display only title and author of posts
        $this->assertTextContains('td', self::$posts['published'][0]->getTitle());
        $this->assertTextContains('td', self::$posts['published'][0]->getUser()->getUsername());
        $this->assertTextNotContains('td', self::$posts['published'][0]->getLead());
        $this->assertTextNotContains('td', self::$posts['published'][0]->getContent());

        // create button should be clickable
        self::$client->clickLink('Create a post');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateWithWrongData(): void
    {
        self::$client->request('GET', '/admin/post/create');
        self::$client->submitForm('post', [
            'title' => '',
            'lead' => 'Lead of the post',
            'content' => 'Content of the post',
            'author' => '12345-67890',
            'is_published' => '0'
        ]);
        $this->assertTextContains('div', 'The field "title" should not be empty');
        $this->assertTextContains('div', 'The field "lead" should contain at least 50 characters');
        $this->assertTextContains('div', 'The field "content" should contain at least 100 characters');
        $this->assertTextContains('div', 'Please select the author again if not already pre-selected.');
    }

    public function testCreateUnpublished(): void
    {
        self::$client->request('GET', '/admin/post/create');
        self::$client->submitForm('post', [
            'title' => $title = 'I write a title for the test',
            'lead' => $lead = 'I write a sentence to fill the lead of the post for the test.',
            'content' => 'I write a sentence to fill the content of the post for the test.'
                . 'I write another sentence to fill the content of the post for the test.',
            'author' => self::$users['admin']['Mike']->getUuid()->toString(),
            'is_published' => '0'
        ]);

        // should appear in admin page
        self::$client->followRedirect();
        $this->assertTextContains('td', $title);
        $this->assertTextNotContains('td', $lead);
        $this->assertTextContains('div', 'The post has been created with success!');

        // should not appear in public posts page
        self::$client->request('GET', '/posts');
        $this->assertTextNotContains('p', $lead);
    }

    public function testCreatePublished(): void
    {
        self::$client->request('GET', '/admin/post/create');
        self::$client->submitForm('post', [
            'title' => $title = 'I write a title for the test',
            'lead' => $lead = 'I write a sentence to fill the lead of the post for the test.',
            'content' => 'I write a sentence to fill the content of the post for the test.'
                . 'I write another sentence to fill the content of the post for the test.',
            'author' => self::$users['admin']['Mike']->getUuid()->toString(),
            'is_published' => '1'
        ]);

        // should appear in admin page
        self::$client->followRedirect();
        $this->assertTextContains('td', $title);
        $this->assertTextNotContains('td', $lead);
        $this->assertTextContains('div', 'The post has been created with success!');

        // should appear in public posts page
        self::$client->request('GET', '/posts');
        $this->assertTextContains('p', $lead);
    }

    public function testEditToPublish(): void
    {
        $post = self::$posts['unpublished'][0];

        // should not appear in public posts page
        self::$client->request('GET', '/posts');
        $this->assertTextNotContains('p', $post->getLead());

        self::$client->request('GET', '/admin/post/' . $post->getUuid() . '/edit');
        self::$client->submitForm('post', [
            'title' => $post->getTitle(),
            'lead' => $post->getLead(),
            'content' => $post->getContent(),
            'author' => $post->getUser()->getUuid()->toString(),
            'is_published' => '1'
        ]);

        // should appear in public posts page
        self::$client->request('GET', '/posts');
        $this->assertTextContains('p', $post->getLead());
    }

    public function testEditToUnpublish(): void
    {
        $post = self::$posts['published'][0];

        // should appear in public posts page
        self::$client->request('GET', '/posts');
        $this->assertTextContains('p', $post->getLead());

        self::$client->request('GET', '/admin/post/' . $post->getUuid() . '/edit');
        self::$client->submitForm('post', [
            'title' => $post->getTitle(),
            'lead' => $post->getLead(),
            'content' => $post->getContent(),
            'author' => $post->getUser()->getUuid()->toString(),
            'is_published' => '0'
        ]);

        // should not appear in public posts page
        self::$client->request('GET', '/posts');
        $this->assertTextNotContains('p', $post->getLead());

        // should not display the show page anymore
        self::$client->request('GET', '/post/' . $post->getSlug());
        $this->assertResponseIsError();
    }

    public function testDelete(): void
    {
        $post = self::$posts['published'][0];

        // should display the show page
        self::$client->request('GET', '/post/' . $post->getSlug());
        $this->assertTextContains('h1', $post->getTitle());

        // without csrf token
        self::$client->request('POST', '/admin/post/' . $post->getUuid() . '/delete');
        self::$client->followRedirect();
        $this->assertTextContains('td', $post->getTitle());

        // with csrf token
        self::$client->request('POST', '/admin/post/' . $post->getUuid() . '/delete', [
            'csrf_token' => self::$client->getCsrfToken()
        ]);
        self::$client->followRedirect();
        $this->assertTextContains('div', 'The post has been deleted with success!');

        // should not appear in admin area
        $this->assertTextNotContains('td', $post->getTitle());

        // should not appear in public posts page
        self::$client->request('POST', '/posts');
        $this->assertTextNotContains('td', $post->getTitle());

        // should not display the show page anymore
        self::$client->request('GET', '/post/' . $post->getSlug());
        $this->assertResponseIsError();
    }
}
