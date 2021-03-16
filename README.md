# Project as part of OpenClassrooms training
<br/>
The project is developed with PHP but without any framework.<br/>
It's a blog where administrators can publish, edit and delete posts. Besides, they validate or invalidate comments before it were published. They can also block, unblock or delete users. Every pages for all these actions are only accessible by administrators.<br/>
Only logged in users can comment inside each post page, but an administrator has to validate them before it can be published. Logged in users can fill out and send a contact form: thus an email is sent to administrators.<br/>
There are a login page and a register page as well.<br/>
<br/>
## Librairies
<br/>
Twig for the frontend.<br/>
Swiftmail to send email.<br/>
<br/>
The code is cleaned with PHPStan (level 8) and PHPCS (PSR1 and PSR12).<br/>
<br/>
You have to create a .env.local file or configure the .env file without committing it.<br/>
