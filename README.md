<h1>Project as part of OpenClassrooms training</h1>

<p>The project is developed with PHP but without any framework.</p>

<p>It's a blog where administrators can publish, edit and delete posts. Besides, they validate or invalidate comments before it were published. They can also block, unblock or delete users. Every pages for all these actions are only accessible by administrators.<br/>
Only logged in users can comment inside each post page, but an administrator has to validate them before it can be published. Logged in users can fill out and send a contact form: thus an email is sent to administrators.</p>

<p>There are a login page and a register page as well.</p>
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
  <li>Go to path "/fixtures" to load fixtures</li>
 </ul>
