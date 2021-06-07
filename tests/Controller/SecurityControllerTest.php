<?php

namespace App\Tests\Controller;

use Framework\Test\DomCrawler\Link;
use Framework\Test\DomCrawler\Crawler;

class SecurityControllerTest extends AppWebTestCase
{
    public function testLoginAndLogout(): void
    {
        $this->createUser('Sacha', 'sacha@mail.com', '123456');
        $client = static::createClient();
        $client->request('GET', '/login');

        // login with wrong password
        $client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '123455'
        ]);
        $this->assertTextContains('div', 'Email or password Invalid.');

        // login with right password
        $client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '123456'
        ]);
        $client->followRedirect();
        $this->assertTextContains('div', 'Welcome, Sacha!');
        $this->assertCookiesCount(0);

        // logout
        $client->submitForm('logout');
        $this->assertCookiesCount(0);
        $client->followRedirect();
        $this->assertTextContains('p', 'Social Media');
        $this->assertTextNotContains('h4', 'Contact Me');
    }

    public function testRememberme(): void
    {
        $this->createUser('Sacha', 'sacha@mail.com', '123456');
        $client = static::createClient();

        // login with remember me
        $client->request('GET', '/login');
        $client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '123456',
            'rememberme' => 'on'
        ]);
        $this->assertCookiesHasName('REMEMBERME');
        $this->assertCookiesCount(1);
        $client->followRedirect();
        $this->assertTextContains('div', 'Welcome, Sacha!');

        // logout
        $client->submitForm('logout');
        $client->followRedirect();
        $this->assertTextContains('p', 'Social Media');
        $this->assertTextNotContains('h4', 'Contact Me');
    }

    public function testResetPasswordRequest(): void
    {
        $this->createUser('Sacha', 'sacha@mail.com', '123456');
        $client = static::createClient();
        $client->request('GET', '/password/reset-request');

        // submits invalid email format
        $client->submitForm('email', ['email' => 'qsdfghjk']);
        $this->assertTextContains('div', 'The field "email" has not a valid format');

        // submits wrong email
        $client->submitForm('email', ['email' => 'unknown@mail.com']);
        $this->assertEmailCount(0);
        $client->followRedirect();
        $this->assertTextContains(
            'div',
            'If you\'re registered with unknown@mail.com, then an email has been sent to reset your password.'
        );

        // submit right email
        $client->request('GET', '/password/reset-request');
        $client->submitForm('email', ['email' => 'sacha@mail.com']);
        $this->assertEmailCount(1);
        $link = $this->getResetPasswordLinkFromEmail();
        $client->followRedirect();
        $this->assertTextContains(
            'div',
            'If you\'re registered with sacha@mail.com, then an email has been sent to reset your password.'
        );

        //// clicks email link to reset password
        $client->click($link);

        // submits 2 different passwords
        $client->submitForm('password', [
            'password1' => '789789',
            'password2' => '123456'
        ]);
        $this->assertEmailCount(0);
        $this->assertTextContains('div', 'The password should be the same in both field');

        // submit corretly
        $client->submitForm('password', [
            'password1' => '789789',
            'password2' => '789789'
        ]);
        $this->assertEmailCount(1);
        $client->followRedirect();
        $this->assertTextContains('div', 'The password has been reset with success!');

        // clicks expired link again
        $client->click($link);
        $client->followRedirect();
        $this->assertTextContains(
            'div',
            'The reset password link is invalid. Please try to reset your password again.'
        );

        // login with new password
        $client->request('GET', '/login');
        $client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '789789'
        ]);
        $client->followRedirect();
        $this->assertTextContains('div', 'Welcome, Sacha!');

        // clicks expired link when logged in
        $client->click($link);
        $client->followRedirect();
        $this->assertTextContains(
            'div',
            'You have been redirected from reset password page because you\'re already logged in.'
        );
    }

    public function getResetPasswordLinkFromEmail(): Link
    {
        $mailsEvent = $this->getMailEvents()[0];
        $crawler = new Crawler($mailsEvent->getMessage()->getBody(), '');

        return $crawler->selectLink('/password/reset/');
    }
}
