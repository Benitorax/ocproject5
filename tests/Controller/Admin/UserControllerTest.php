<?php

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\AppWebTestCase;

class UserControllerTest extends AppWebTestCase
{
    public function setup(): void
    {
        parent::setup();
        self::$client->loginUser(self::$users['admin']['Mike']);
    }

    public function testIndexWhenNotLogged(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/users');
        $this->assertResponseIsError();
    }

    public function testIndexWhenNotAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser(self::$users['user']['Sacha']);
        $client->request('GET', '/admin/users');
        $this->assertResponseIsError();
    }

    public function testIndex(): void
    {
        self::$client->request('GET', '/admin/users');
        $this->assertTextContains('td', 'John');
        $this->assertTextContains('td', 'Matthew');
        $this->assertTextContains('td', 'Mike');
    }

    public function testBlock(): void
    {
        // blocks John
        self::$client->request('GET', '/admin/users');
        self::$client->submitForm('block-john');
        self::$client->followRedirect();
        $this->assertTextContains('div', 'The user has been blocked with success!');

        // unblocks John
        self::$client->submitForm('unblock-john');
        self::$client->followRedirect();
        $this->assertTextContains('div', 'The user has been unblocked with success!');
    }

    public function testUnblock(): void
    {
        // unblocks Monica
        self::$client->request('GET', '/admin/users');
        self::$client->submitForm('unblock-monica');
        self::$client->followRedirect();
        $this->assertTextContains('div', 'The user has been unblocked with success!');

        // blocks Monica
        self::$client->submitForm('block-monica');
        self::$client->followRedirect();
        $this->assertTextContains('div', 'The user has been blocked with success!');
    }

    public function testDelete(): void
    {
        // submits without csrf token
        self::$client->request(
            'POST',
            '/admin/user/' . self::$users['user']['Monica']->getUuid() . '/delete'
        );
        self::$client->followRedirect();
        $this->assertTextNotContains('div', 'The user has been deleted with success!');
        $this->assertTextContains('td', 'Monica');

        // submits with csrf token
        self::$client->request(
            'POST',
            '/admin/user/' . self::$users['user']['Monica']->getUuid() . '/delete',
            ['csrf_token' => self::$client->getCsrfToken()]
        );
        self::$client->followRedirect();
        $this->assertTextContains('div', 'The user has been deleted with success!');
        $this->assertTextNotContains('td', 'Monica');

        // deletes yourself
        self::$client->request(
            'POST',
            '/admin/user/' . self::$users['admin']['Mike']->getUuid() . '/delete',
            ['csrf_token' => self::$client->getCsrfToken()]
        );
        self::$client->followRedirect();
        $this->assertTextContains('div', 'You can\'t delete your own account!');
        $this->assertTextContains('td', 'Mike');
    }
}
