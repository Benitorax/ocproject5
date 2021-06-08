<?php

namespace App\Tests\Controller;

use App\Tests\Controller\AppWebTestCase;

class AppControllerTest extends AppWebTestCase
{
    public function testRegister(): void
    {
        // register correctly
        $client = static::createClient();
        $client->request('GET', '/register');
        $client->submitForm('register', [
            'email' => 'sacha@mail.com',
            'password1' => '123456',
            'password2' => '123456',
            'username' => 'Sacha',
            'terms' => 'on'
        ]);
        $this->assertResponseIsRedirect();
        $client->followRedirect();
        $this->assertTextContains('div', 'You register with success!');

        // fills each field wrong
        $client->request('GET', '/register');
        $client->submitForm('register', [
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
        // create an admin user who can receive message from contact form
        $this->createUser('Mike', 'mike@mail.com', '123456', true);
        $this->createUser('John', 'john@mail.com', '123456', true);

        // not logged in
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertTextNotContains('h4', 'Contact Me');

        // logged in
        $user = $this->createUser('Sacha', 'sacha@mail.com', '123456');
        $client->loginUser($user);
        $client->request('GET', '/');
        $this->assertTextContains('h4', 'Contact Me');

        // submit contact form wrongly
        $client->submitForm('contact', [
            'subject' => '',
            'content' => 'My content'
        ]);
        $this->assertTextContains('div', 'The field "subject" should not be empty');
        $this->assertTextContains('div', 'The field "content" should contain at least 20 characters');

        // submit contact form correctly
        $client->submitForm('contact', [
            'subject' => 'My super subject',
            'content' => 'My super content which has enough characters.'
        ]);
        $this->assertTextContains('div', 'Your message has been sent with success!');
        $this->assertQueuedEmailCount(2);
    }

    public function testTermsOfUse(): void
    {
        $client = static::createClient();
        $client->request('GET', '/terms-of-use');
        $this->assertTextContains('h2', 'Terms of Use');
    }
}
