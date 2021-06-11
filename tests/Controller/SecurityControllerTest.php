<?php

namespace App\Tests\Controller;

use Framework\Test\DomCrawler\Link;
use Framework\Test\DomCrawler\Crawler;

class SecurityControllerTest extends AppWebTestCase
{
    public function testLogin(): void
    {
        self::$client->request('GET', '/login');

        // login with wrong password
        self::$client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '123455'
        ]);
        $this->assertTextContains('div', 'Email or password invalid.');

        // login with right password
        self::$client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '123456'
        ]);
        self::$client->followRedirect();
        $this->assertTextContains('div', 'Welcome, Sacha!');
        $this->assertCookiesCount(0);
    }

    public function testLogout(): void
    {
        self::$client->loginUser(self::$users['user']['Sacha']);
        self::$client->request('GET', '/');
        self::$client->submitForm('logout');
        $this->assertCookiesCount(0);
        self::$client->followRedirect();
        $this->assertTextContains('p', 'Social Media');
        $this->assertTextNotContainsForm('contact');
    }

    public function testRememberme(): void
    {
        // login with remember me
        self::$client->request('GET', '/login');
        self::$client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '123456',
            'rememberme' => 'on'
        ]);
        $this->assertCookiesHasName('REMEMBERME');
        $this->assertCookiesCount(1);
        self::$client->followRedirect();
        $this->assertTextContains('div', 'Welcome, Sacha!');

        // logout
        self::$client->submitForm('logout');
        self::$client->followRedirect();
        $this->assertTextContains('p', 'Social Media');
        $this->assertTextNotContainsForm('contact');
    }

    public function testResetPasswordRequest(): void
    {
        self::$client->request('GET', '/password/reset-request');

        // submits invalid email format
        self::$client->submitForm('email', ['email' => 'qsdfghjk']);
        $this->assertTextContains('div', 'The field "email" has not a valid format');

        // submits wrong email
        self::$client->submitForm('email', ['email' => 'unknown@mail.com']);
        $this->assertEmailCount(0);
        self::$client->followRedirect();
        $this->assertTextContains(
            'div',
            'If you\'re registered with unknown@mail.com, then an email has been sent to reset your password.'
        );

        // submit right email
        self::$client->request('GET', '/password/reset-request');
        self::$client->submitForm('email', ['email' => 'sacha@mail.com']);
        $this->assertEmailCount(1);
        $link = $this->getResetPasswordLinkFromEmail();
        self::$client->followRedirect();
        $this->assertTextContains(
            'div',
            'If you\'re registered with sacha@mail.com, then an email has been sent to reset your password.'
        );

        //// clicks email link to reset password
        self::$client->click($link);

        // submits 2 different passwords
        self::$client->submitForm('password', [
            'password1' => '789789',
            'password2' => '123456'
        ]);
        $this->assertEmailCount(0);
        $this->assertTextContains('div', 'The password should be the same in both field');

        // submits correctly
        self::$client->submitForm('password', [
            'password1' => '789789',
            'password2' => '789789'
        ]);
        $this->assertEmailCount(1);
        self::$client->followRedirect();
        $this->assertTextContains('div', 'The password has been reset with success!');

        // clicks expired link again
        self::$client->click($link);
        self::$client->followRedirect();
        $this->assertTextContains(
            'div',
            'The reset password link is invalid. Please try to reset your password again.'
        );

        // login with new password
        self::$client->request('GET', '/login');
        self::$client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '789789'
        ]);
        self::$client->followRedirect();
        $this->assertTextContains('div', 'Welcome, Sacha!');

        // clicks expired link when logged in
        self::$client->click($link);
        self::$client->followRedirect();
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
