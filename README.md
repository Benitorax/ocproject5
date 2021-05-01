<a href="https://codeclimate.com/github/Benitorax/ocproject5/maintainability"><img src="https://api.codeclimate.com/v1/badges/d6c4613ad1927f13e5a8/maintainability" /></a>
<a href="https://www.codacy.com/gh/Benitorax/ocproject5/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Benitorax/ocproject5&amp;utm_campaign=Badge_Grade"><img src="https://app.codacy.com/project/badge/Grade/8a6e2a716ac04a6087353ccb101791d3"/></a>

<h1>Project as part of OpenClassrooms training</h1>

<p>The project is developed with PHP but without any framework.</p>

<p>
  It's a blog where administrators can: 
  <ul>
    <li>publish, edit and delete posts.</li>
    <li>validate or invalidate a comment for publication.</li>
    <li>block, unblock or delete users</li>
  </ul>
  All these pages are only accessible by administrators.
</p>

<p>
  Only logged users can:
  <ul>
    <li>submit a comment below each post.</li>
    <li>can see the contact form in the home page.</li>
    <li>fill out and submit the contact form, then an email is sent to administrators.</li>
  </ul>
</p>

<p>There are a register page and a login page as well.</p>
<br/>

<h2>Librairies</h2>
<ul>
  <li>Ramsey/Uuid for uuid inside model classes.</li> 
  <li>Twig for the template engine.</li>
  <li>SwiftMailer to send emails.</li>
  <li>Faker to load fixtures.</li>
</ul>
<br/>

<h2>Clean code</h2>
<ul>
  <li>PHPStan: level 8</li>
  <li> PHPCS: PSR1 and PSR12</li>
</ul>
<br/>

<h2>Getting started</h2>
<ul>
  <li>Create a .env.local file or configure the .env file but don't commit it.</li>
  <li>
    Create a database, then execute the SQL file <b>ocproject5.sql</b> located in the project's root to create schemas and those tables:
    <ul>
      <li>user: id, email, password, username, roles, is_blocked</li>
      <li>post: id, title, slug, lead, content, is_published, user_id</li>
      <li>comment: id, content, is_validated, user_id, post_id</li>
      <li>rememberme_token: class, username, series, value, last_used</li>
      <li>reset_password_token: id, user_id, selector, hashed_token, requested_at, expired_at</li>
    </ul>
  </li>
  <li>Execute in your terminal:<br>
    <code>php -S 127.0.0.1:8000 -t public</code><br><br>
    Or if you use Symfony CLI you can execute:<br>
    <code>symfony serve -d</code>
  </li>
  <li>And finally, go to url "/fixtures" to load fixtures.</li>
 </ul>
