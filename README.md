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
    <li>can see the contact form on the home page.</li>
    <li>fill out and submit the contact form, then an email is sent to administrators.</li>
  </ul>
</p>

<p>There are a register page and a login page as well.</p>
<br/>

<h2>Librairies</h2>
<ul>
  <li>Twig for the frontend.</li>
  <li>SwiftMailer to send email.</li>
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
    Create a database and tables
    <ul>
      <li>user: id, email, password, username, roles, is_blocked</li>
      <li>post: id, title, slug, lead, content, is_published, user_id</li>
      <li>comment: id, content, is_validated, user_id, post_id</li>
    </ul>
  </li>
  <li>Go to path "/fixtures" to load fixtures.</li>
 </ul>
