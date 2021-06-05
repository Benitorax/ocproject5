<?php

namespace App\Tests\Controller;

use App\Tests\Controller\AppWebTestCase;

class AppControllerTest extends AppWebTestCase
{
    // public function testSomething(): void
    // {
    //     $user = $this->userDAO->getOneByEmail('sacha@mail.com');
    //     // This calls KernelTestCase::bootKernel(), and creates a
    //     // "client" that is acting as the browser
    //     $client = static::createClient();
    //     $client->loginUser($user);
        
    //     // Request a specific page
    //     $crawler = $client->request('GET', '/login');
    //     $crawler = $client->followRedirect();

    //     // $form = $crawler->getForm('contact');
    //     // $form->setValues([
    //     //     'subject' => 'Subject of my message',
    //     //     'content' => 'Content of my message. This is another sentence.'
    //     // ]);
    //     // $crawler = $client->submit($form);
    //     $crawler = $client->submitForm('contact', [
    //         'subject' => 'Subject of my message',
    //         'content' => 'Content of my message. This is another sentence.'
    //     ]);

    //     // $link = $crawler->selectLink('Posts');
    //     // $crawler = $client->click($link);

    //     $crawler = $client->clickLink('Posts');
    //     $this->assertSelectedTextContains('h4', 'Alice asked');
 
    //     // Validate a successful response and some content
    //     //$this->assertResponseIsSuccessful();
    //     //$this->assertResponseIsRedirect();
    //     $this->assertSelectedTextContains('h4', 'Alice asked');
    // }

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
        $this->assertSelectedTextContains('div', 'You register with success!');

        // fills each field wrong
        $client = static::createClient();
        $client->request('GET', '/register');
        $client->submitForm('register', [
            'email' => 'sacha@mail.com',
            'password1' => '123456',
            'password2' => '123455',
            'username' => 'Sacha',
        ]);
        $this->assertSelectedTextContains('div', 'The email "sacha@mail.com" already exists');
        $this->assertSelectedTextContains('div', 'The password should be the same in both field');
        $this->assertSelectedTextContains('div', 'The username "Sacha" already exists');
        $this->assertSelectedTextContains('div', 'The box "terms of use" must be checked');
    }

    public function testLogin(): void
    {
        $this->createUser('Sacha', 'sacha@mail.com', '123456');

        // wrong password
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '123455'
        ]);
        $this->assertSelectedTextContains('div', 'Email or password Invalid.');

        // right password
        $client->submitForm('login', [
            'email' => 'sacha@mail.com',
            'password' => '123456'
        ]);

        $this->assertResponseIsRedirect();
        $client->followRedirect();
        $this->assertSelectedTextContains('div', 'Welcome, Sacha!');
    }

    public function testHomepage(): void
    {
        // not logged in
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertSelectedTextNotContains('h4', 'Contact Me');

        // logged in
        $user = $this->createUser('Sacha', 'sacha@mail.com', '123456');
        $client->loginUser($user);
        $client->request('GET', '/');
        $this->assertSelectedTextContains('h4', 'Contact Me');

        // submit contact form wrongly

        // submit contact form rightly
    }

    public function testTermsOfUse(): void
    {
        $client = static::createClient();
        $client->request('GET', '/terms-of-use');
        $this->assertSelectedTextContains('h2', 'Terms of Use');
    }
}