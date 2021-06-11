<a href="https://codeclimate.com/github/Benitorax/ocproject5/maintainability"><img src="https://api.codeclimate.com/v1/badges/d6c4613ad1927f13e5a8/maintainability" /></a>
<a href="https://www.codacy.com/gh/Benitorax/ocproject5/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Benitorax/ocproject5&amp;utm_campaign=Badge_Grade"><img src="https://app.codacy.com/project/badge/Grade/8a6e2a716ac04a6087353ccb101791d3"/></a>

# Project as part of OpenClassrooms training

The project is developed with PHP and without any existing framework. However, I developed a tiny one for the project that I explained in detail at the [end of the page](#about-the-framework).

It's a blog where administrators can: 
- publish, edit and delete posts.
- validate or invalidate a comment for publication.
- block, unblock or delete users.

*All these pages are only accessible by administrators*.


Only logged users can:
- submit a comment below each post.
- can see the contact form in the home page.
- fill out and submit the contact form, then an email is sent to administrators.

There are a register page and a login page as well.


## Getting started
### Step 1: Configure environment variables
Copy the `.env file` in the root folder, rename it to `.env.local` and configure the following variables for:
- the database:
  ```
  DB_HOST=mysql:host=localhost;dbname=my_blog;charset=utf8
  DB_USERNAME=root
  DB_PASSWORD=
  ```

- and the emailing:
  ```
  MAILER_HOST=smtp.example.org
  MAILER_PORT=25
  MAILER_ENCRYPTION=ssl
  MAILER_USERNAME=example@email.com
  MAILER_PASSWORD=password
  ```

### Step 2: Create database
Create a database, then run the SQL commands from the SQL file `ocproject5.sql` located in the project root to create those tables:
- user: id, email, password, username, roles, is_blocked
- post: id, title, slug, lead, content, is_published, user_id
- comment: id, content, is_validated, user_id, post_id
- rememberme_token: class, username, series, value, last_used
- reset_password_token: id, user_id, selector, hashed_token, requested_at, expired_at
    
Or import `ocproject5.sql` from phpMyAdmin if you have access.

### Step 3: Launch the server
- Run the command in your terminal from the project root:
  ```
  php -S 127.0.0.1:8000 -t public
  ```

### Step 4: Load some data
Go to url http://127.0.0.1:8000/fixtures to load fixtures. It redirects to homepage, so you can navigate on the website after loading data.

### Step 5: Access to admin area
Find a user who has admin role in your database. Then log in with this user.

## Librairies
- [Ramsey/Uuid](https://github.com/ramsey/uuid) for uuid inside model classes. 
- [Twig](https://github.com/twigphp/Twig) for the template engine.
- [SwiftMailer](https://github.com/swiftmailer/swiftmailer) to send emails.
- [Faker](https://github.com/fzaninotto/Faker) to load fixtures.
- [PSR/EventDispatcher](https://github.com/php-fig/event-dispatcher) to respect PSR-14 (Event Dispatcher).
- [PHPUnit](https://github.com/sebastianbergmann/phpunit) to run tests.

## Clean code
- [PHPStan](https://github.com/phpstan/phpstan): level 8
- [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer): PSR1 and PSR12

## About the framework
For the project, I couldn't use any existing framework. However, I was already used to develop with Symfony. So as for me, it would be tedious to develop a blog with this constraint. That's why I have created a tiny one to have a better DX (Developer Experience).

The drawback is huge - *spending your time to develop the framework instead of the blog* - but it was an interesting challenge because, *from my level at that time*, I didn't know if I was able to do it.

The framework is inspired a lot by Symfony:
- The App handles a `Request` class and returns a `Response` class.
- The AbstractController has methods similar to Symfony's controller helpers (`render()`, `addFlash()`, `isGranted()`, `json()`, etc).
- The Form class has `handleRequest()`, `isSubmitted()` and `isValid()` methods.
- The Twig Renderer:
  - has access to current user, current route and flash messages.
  - has `url` and `path` functions to generate url.

Therefore, the appearance of controllers and templates remind of Symfony but the internal code is different (very simple and less complex).

### Other informations
- Routes are defined in routes config file:

  ```php
  // config/routes.php
  
  return [
    // Security
    '/login' => [
        'name' => 'login',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\SecurityController::login'
    ],
    '/logout' => [
        'name' => 'logout',
        'method' => 'POST',
        'callable' => 'App\Controller\SecurityController::logout'
    ],
    // ...
  ];
  ```
- A Form class must extend `AbstractForm`.

  Setter method name must match form field name in snake_case:
  
  ```php
  class Contact extends AbstractForm
  {
      private string $username;
      private string $phoneNumber;
      
      public function setUsername(string $username)
      {
          $this->username = $username;
      }
      
      public function setPhoneNumber(string $phoneNumber)
      {
          $this->phoneNumber = $phoneNumber;
      }
  }
  ```
  
  ```html
  <form method="POST" action="/contact/create">
    <input type="text" name="username">
    <input type="text" name="phone_number">
    <button>Submit</button>
  </form>
  ```
  
  The field name `phone_number` is in snake_case whereas the setter name `setPhoneNumber` is in camelCase (or PascalCase without the prefix `set`).
  
- A Validation class must extend `AbstractValidation`.

  Property constraints are set as properties, then they're used in `validate()` method. They're combined with `check([constraints], $value, ?'fieldName')` method which returns error message when it's not validated. And finally the error message is set in form with `$form->addError($key, $message)`.
  
  ```php
  class EmailValidation extends AbstractValidation
  {
      private const EMAIL = [
          ['notBlank'],
          ['minLength', 8],
          ['maxLength', 50],
          ['email']
      ];

      public function validate(AbstractForm $form): void
      {
          /** @var EmailForm $form */
          $form->addError('email', $this->check(self::EMAIL, $form->getEmail(), 'email'));
          $form->addError('csrf', $this->checkCsrfToken($form->getCsrfToken()));
      }
  }
  ```
  
- A DAO class must extend `AbstractDAO`.

  You can make query expression like [Doctrine/ORM](https://github.com/doctrine/orm):

    ```php
    // src/DAO/UserDAO.php

    private function getOneByEmailQuery(string $email)
    {
        return (new QueryExpression())
            ->select(self::SQL_COLUMNS, 'u')
            ->from(self::SQL_TABLE, 'u')
            ->where('email = :email')
            ->setParameter('email', $email);
    }
    ```

- EventDispatcher

  The framework has 2 events (`TerminateEvent` and `ExceptionEvent`) and 1 subscriber (`ControllerSubscriber`).
  Moreover, you can create your own events, event listeners or subscribers:
  - Event class must extend `Event`. 
  - Subscriber class must extend `EventSubscriberInterface`.
  - Listener class must implement `__invoke` method.
  
  Registration: event must be set in `events` key, listener in `listeners` key and subscriber in `subscribers` key.

  ```php
  // config/services.php

   return [ 'event' => [
      'events' => [
          TerminateEvent::class => [
              'listeners' => [
                  // [listener::class, priority],
                  // [EntityListener::class, 10],
              ]
          ],
          ExceptionEvent::class => [
              'listeners' => [
                  // [listener::class, priority],
              ]
          ]
      ],
      'subscribers' => [
          Framework\Controller\ControllerSubscriber::class,
          App\Service\Mailer\MailerSubscriber::class
      ]
  ]];
  ```

- Debug

  If `APP_DEBUG` is set to true in `.env.local`, the browser will display a beautifier error when a bug occurs. Otherwise, it will show an error page (403, 404, 500) that you can customize.

- Dotenv
 
  This class is responsible for loading environment variable from `.env.local`. So you can add your own variables, then variables are retrieved from Dotenv with dependency injection:

  ```php
  // Mailer/Builder/TransportBuilder.php
  
      public function __construct(Dotenv $dotenv)
    {
        // loads config from environment variables
        foreach ($dotenv->all() as $key => $value) {
        // Mailer variables start with "MAILER_" like "MAILER_HOST", "MAILER_PORT", etc
            if (0 === strpos($key, 'MAILER_')) {
                $this->config[substr($key, 7)] = $value;
            }
        }
    }
  ```

- Security

  User class must implement `UserInterface` and UserDAO class must implement `UserDAOInterface` (the application needs these implementations to authenticate the user). Then, the alias of UserDAOInterface must be set in the services config:

     ```php
     // config/services.php

     return [ 'alias' => [
        // Security
        // Define the DAO class to fetch user for authentication
        Framework\DAO\UserDAOInterface::class => App\DAO\UserDAO::class
    ]];
     ```

  - The remember me system with the [split token strategy](https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels) is also inspired by [Symfony's](https://github.com/symfony/security-http).
  - Extra: the reset password system with split token strategy (*not included in the framework but only for the app*) is inspired by [SymfonyCasts/ResetPasswordBundle](https://github.com/SymfonyCasts/reset-password-bundle).

- Test

  The test service allows functional tests using [PHPUnit](https://github.com/sebastianbergmann/phpunit). 
  
  Controller test class must extend `WebTestCase` class:
  
  ```php
  // tests/Controller/AppControllerTest.php
  use Framework\Test\WebTestCase;

  class AppControllerTest extends WebTestCase
  {
      public function testSomething(): void
      {
          // This calls WebTestCase::bootApp(), and creates a
          // "client" that is acting as the browser
          $client = static::createClient();

          // Request a specific page
          $client->request('GET', '/');

          // Validate a successful response and some content
          $this->assertResponseIsSuccessful();
          $this->assertTextContains('h1', 'Hello World');
          
          // Click a link on the page
          $client->clickLink('Register');
          
          // Submit a form on the page
          $client->submitForm('register', [
              'email' => 'roger@mail.com',
              'password1' => '123456',
              'password2' => '123456',
              'username' => 'Roger',
              'terms' => 'on'
          ]);
          
          // Validate and follow redirection 
          $this->assertResponseIsRedirect();
          $client->followRedirect();
          $this->assertTextContains('div', 'You register with success!');

          // Log in a user before accessing a private page
          $user = $userDAO->getOneByMail('roger@mail.com');
          $client->loginUser($user);
          $client->request('GET', '/private-page');
          
          // $crawler is always returned after calling these methods
          $crawler = $client->request('GET', '/');
          $crawler = $client->clickLink('Login');
          $crawler = $client->submitForm('login', []);
          $crawler = $client->followRedirect();
          
          // some built-in assertion methods:
          $this->assertResponseIsSuccessful(); // should match status code from 200 to 299
          $this->assertResponseIsRedirect(); // should match status code from 300 to 399
          $this->assertResponseIsError(); // should match status code from 400 to 599
          $this->assertTextContains('div', 'I should be display');
          $this->assertTextNotContains('h1', 'I should not be display');
          $this->assertTextContainsForm('login'); // <form name="login">
          $this->assertTextNotContainsForm('register'); // <form name="register">
          $this->assertCookiesHasName('REMEMBERME');
      }
  }
  ```

## Others

### Mailer

Thanks to SwiftMailer, EventDispatcher and MailerSubscriber, the Mailer can have 2 types of transport:
- SMTP transport: emails are sent immediately but it can slow down the sending of the HTTP response. 
- Spool transport: emails are sent after returning the HTTP response.
