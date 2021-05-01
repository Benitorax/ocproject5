<a href="https://codeclimate.com/github/Benitorax/ocproject5/maintainability"><img src="https://api.codeclimate.com/v1/badges/d6c4613ad1927f13e5a8/maintainability" /></a>
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
    Create a database and those tables:
    <ul>
      <li>user: id, email, password, username, roles, is_blocked</li>
      <li>post: id, title, slug, lead, content, is_published, user_id</li>
      <li>comment: id, content, is_validated, user_id, post_id</li>
      <li>rememberme_token: class, username, series, value, last_used</li>
      <li>reset_password_token: id, user_id, selector, hashed_token, requested_at, expired_at</li>
    </ul>
    <br>
    You can execute those SQL lines in your database:<br><br>
    <code>
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_validated` tinyint(1) NOT NULL,
  `user_id` int(4) UNSIGNED NOT NULL,
  `post_id` int(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=429 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `title` varchar(150) NOT NULL,
  `slug` varchar(160) DEFAULT NULL,
  `lead` text NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_published` tinyint(1) NOT NULL,
  `user_id` int(4) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rememberme_token`;
CREATE TABLE IF NOT EXISTS `rememberme_token` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `series` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `last_used` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `reset_password_token`;
CREATE TABLE IF NOT EXISTS `reset_password_token` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(4) NOT NULL,
  `selector` varchar(20) NOT NULL,
  `hashed_token` varchar(50) NOT NULL,
  `requested_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `selector` (`selector`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `email` varchar(70) NOT NULL,
  `password` varchar(300) NOT NULL,
  `username` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `roles` json NOT NULL,
  `is_blocked` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`);

ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;
    </code><br>
  </li>
  <li>Go to path "/fixtures" to load fixtures.</li>
 </ul>
