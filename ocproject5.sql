SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database :  `ocproject5`
--

-- --------------------------------------------------------

--
-- Table structure `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_validated` tinyint(1) NOT NULL,
  `user_id` int(4) UNSIGNED DEFAULT NULL,
  `post_id` int(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `post`
--

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
  `user_id` int(4) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `rememberme_token`
--

DROP TABLE IF EXISTS `rememberme_token`;
CREATE TABLE IF NOT EXISTS `rememberme_token` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `series` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `last_used` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `reset_password_token`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
--Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`);

--
--Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;