-- Adminer 4.8.1 MySQL 8.0.21 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `client_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `auth_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `secret_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `access_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `refresh_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tokens_create` int DEFAULT NULL,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_client_id` (`user_id`,`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `auth` (`id`, `user_id`, `client_id`, `auth_code`, `secret_key`, `access_token`, `refresh_token`, `tokens_create`, `created_at`, `updated_at`) VALUES
(28,	84,	'5uo11TYSp3Qh3SSjH-a8sIZL-l9Ze8WM',	'4F5bGTrQZDe_YzP7cENX64J5XtVjcKtS',	'Fn2UUN32CBDNpXeartIU5qlQgDtdERJA',	'6894298f9c77a5c222da7fcf672a39d4bf0885e2a7b660239dfedb226e58256e',	'26QyC4SElxzSQGAS-6-_s5sqV2jxRitz',	1658924650,	1658240603,	1658924650);

DROP TABLE IF EXISTS `client`;
CREATE TABLE `client` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `client_secret` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(255) DEFAULT NULL,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_name` (`client_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `client` (`id`, `client_name`, `client_id`, `client_secret`, `redirect_uri`, `created_at`, `updated_at`) VALUES
(25,	'Admin-app',	'5uo11TYSp3Qh3SSjH-a8sIZL-l9Ze8WM',	'Y1I30lLAvK2M071gaIz-nZ-FySAoO8Df',	'localhost',	1658235774,	1658362859),
(26,	'app1',	'l2pFzeb01g1loapGxey-whIWCmkWx8ei',	'2SFmLdCmXz0i1VtX31IPNgRqI6pQS47K',	'local',	1658236001,	1658236001),
(27,	'app2',	'v_Lfp6gBSWPi-stG2J2QEJz_8NmT0owy',	'ouGnydr2wtPeF3C6yQQCQCV1oKI93xS6',	'loc',	1658236013,	1658236013),
(28,	'app3',	'thiP8_Ief7EhqQUqKgPlrmanJKcRdiTX',	'f3pN19Cakv7ACQm_Dh4tYVC9PIkgatwo',	'locs',	1658236034,	1658236034);

DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base',	1656082772),
('m220625_131619_create_user_table',	1656386431),
('m220628_022631_create_review_table',	1656386431),
('m220711_142833_create_client_table',	1657551403),
('m220717_110113_create_auth_table',	1658089739);

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `status` varchar(8) NOT NULL DEFAULT '0',
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-review-user_id` (`user_id`),
  CONSTRAINT `fk-review-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `review` (`id`, `user_id`, `title`, `body`, `status`, `created_at`, `updated_at`) VALUES
(11,	85,	'Lorem Ipsum 2',	'Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum ',	'0',	1658317305,	1658847609),
(12,	86,	'Lorem Ipsum 3',	'Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum ',	'0',	1658317371,	1658317371),
(13,	87,	'Lorem Ipsum 1',	'Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum ',	'0',	1658317450,	1658317450),
(18,	84,	'Ipsum',	'Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem Lorem ',	'0',	1658925026,	1658925066);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_old` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `auth_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `first_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `surname` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `patronymic` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `date_birth` date DEFAULT NULL,
  `city` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `phone_number` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `avatar_initial` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `password` (`password`),
  UNIQUE KEY `auth_key` (`auth_key`),
  UNIQUE KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `username`, `password`, `password_old`, `auth_key`, `first_name`, `surname`, `patronymic`, `date_birth`, `city`, `phone_number`, `avatar`, `avatar_initial`, `created_at`, `updated_at`) VALUES
(84,	'admin',	'$2y$13$dJ63kaisVEInRQCWgf/Ltud6Ky53xU7D5085/XfuVx9z2/QAPD8Hu',	NULL,	'Sf-YfddAqrLraMLfkpCwmATclgXr5ska',	'Администратор',	NULL,	NULL,	NULL,	'Казань',	'79998986677',	NULL,	NULL,	1658858710,	1658858925),
(85,	'user1',	'$2y$13$V0mrAEPMMjZUbXjTHo3vyuMobfAxdfzT1UeqZ5HpYdZxIPxL84A3G',	NULL,	'QuEkTm4mzNLzgW2Vc9Bg2_SaabnqnFQM',	'Алексей',	'Кулик',	'Сергеевич',	'1999-12-31',	'Москва',	'79008087761',	'62e0032506707.jpg',	'29a82067b71bd9e3df95e1c0ba5c4daf.jpg',	1658234863,	1658848037),
(86,	'user2',	'$2y$13$y1pveE.rR.SkJX6ecol3BuQwc9V36OMP/6.04S.OWE3WGRu5jXEHu',	NULL,	'sK9WmL86cTM7lpR2kYtY1EsNp9YSmRnV',	'Иван',	'Васильев',	'Александрович',	'1998-12-31',	'Краснодар',	'79008087762',	'62e007bb2266a.jpg',	'e113f64f714bcf8a32d0b183727e8f38.jpg',	1658234874,	1658849211),
(87,	'user3',	'$2y$13$U4R0Ti4rqDcEjHhsxLF2muy7mG8fiOqeiC71bPkszkcMP.5t0Wi4y',	NULL,	'X-F0qqDojJTxZ7H1g5cGDARSGPU5AtYC',	'Александр',	'Петров',	'Сергеевич',	'2002-07-08',	'Рязань',	'79008087765',	'62e008063e70b.jpg',	'628f49798bba23a996bbb0fe8aad174e--avatar-james-cameron-avatar-fan-art.jpg',	1658234886,	1658849286);

-- 2022-07-27 14:04:47
