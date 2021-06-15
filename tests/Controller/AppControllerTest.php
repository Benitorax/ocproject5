<?php

namespace App\Tests\Controller;

class AppControllerTest extends AppWebTestCase
{
    public function testRegister(): void
    {
        // register correctly
        self::$client->request('GET', '/register');
        self::$client->submitForm('register', [
            'email' => 'roger@mail.com',
            'password1' => '123456',
            'password2' => '123456',
            'username' => 'Roger',
            'terms' => 'on'
        ]);
        $this->assertResponseIsRedirect();
        self::$client->followRedirect();
        $this->assertTextContains('div', 'You register with success!');

        // fills each field wrong
        self::$client->request('GET', '/register');
        self::$client->submitForm('register', [
            'email' => 'sacha@mail.com',
            'password1' => '123456',
            'password2' => '123455',
            'username' => 'Sacha',
        ]);
        $this->assertTextContains('div', 'The email "sacha@mail.com" already exists');
        $this->assertTextContains('div', 'The password should be the same in both field');
        $this->assertTextContains('div', 'The username "Sacha" already exists');
        $this->assertTextContains('div', 'The box "terms of use" must be checked');
    }

    public function testHomepage(): void
    {
        // not logged in
        self::$client->request('GET', '/');
        $this->assertTextNotContainsForm('contact');

        // logged in
        self::$client->loginUser(self::$users['user']['Sacha']);
        self::$client->request('GET', '/');
        $this->assertTextContainsForm('contact');

        // submit contact form wrongly
        self::$client->submitForm('contact', [
            'subject' => '',
            'content' => 'My content'
        ]);
        $this->assertTextContains('div', 'The field "subject" should not be empty');
        $this->assertTextContains('div', 'The field "content" should contain at least 20 characters');

        // submit contact form correctly
        self::$client->submitForm('contact', [
            'subject' => 'My super subject',
            'content' => 'My super content which has enough characters.'
        ]);
        $this->assertTextContains('div', 'Your message has been sent with success!');
        $this->assertQueuedEmailCount(3);
    }

    public function testTermsOfUse(): void
    {
        self::$client->request('GET', '/terms-of-use');
        $this->assertTextContains('h2', 'Terms of Use');
    }
}
