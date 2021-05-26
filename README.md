<a href="https://codeclimate.com/github/Benitorax/ocproject5/maintainability"><img src="https://api.codeclimate.com/v1/badges/d6c4613ad1927f13e5a8/maintainability" /></a>
<a href="https://www.codacy.com/gh/Benitorax/ocproject5/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Benitorax/ocproject5&amp;utm_campaign=Badge_Grade"><img src="https://app.codacy.com/project/badge/Grade/8a6e2a716ac04a6087353ccb101791d3"/></a>

# Project as part of OpenClassrooms training

The project is developed with PHP but without any framework.

It's a blog where administrators can: 
- publish, edit and delete posts.
- validate or invalidate a comment for publication.
- block, unblock or delete users.

All these pages are only accessible by administrators.


Only logged users can:
- submit a comment below each post.
- can see the contact form in the home page.
- fill out and submit the contact form, then an email is sent to administrators.

There are a register page and a login page as well.


## Getting started
### Step 1: Configure environment variables
Copy the `.env file`, rename it to `.env.local` and configure the following variables for:
- the database:

```
"DB_HOST": "mysql:host=localhost;dbname=my_blog;charset=utf8"
"DB_USERNAME": "root"
"DB_PASSWORD": ""
```

- the emailing:

```
"MAILER_HOST": "smtp.example.org"
"MAILER_PORT": 25
"MAILER_ENCRYPTION": "ssl"
"MAILER_USERNAME": "example@email.com"
"MAILER_PASSWORD": "password"
```

### Step 2: Create database
Create a database, then run the commands from the SQL file `ocproject5.sql` located in the project's root to create those tables:
- user: id, email, password, username, roles, is_blocked
- post: id, title, slug, lead, content, is_published, user_id
- comment: id, content, is_validated, user_id, post_id
- rememberme_token: class, username, series, value, last_used
- reset_password_token: id, user_id, selector, hashed_token, requested_at, expired_at
    
### Step 3: Launch the server
- Run the command in your terminal from the root project:

```
php -S 127.0.0.1:8000 -t public
```

- Or if you use Symfony CLI you can execute:
```
symfony serve -d
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

## Clean 
  - PHPStan: level 8
  - PHPCS: PSR1 and PSR12

