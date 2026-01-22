-- Active: 1769076099731@@127.0.0.1@3306@job_dating_youcode
-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour job_dating_youcode
CREATE DATABASE IF NOT EXISTS `job_dating_youcode` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `job_dating_youcode`;

-- Listage de la structure de table job_dating_youcode. announcements
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `contract_type` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `skills` text,
  `image` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table job_dating_youcode.announcements : ~5 rows (environ)
DELETE FROM `announcements`;
INSERT INTO `announcements` (`id`, `company_id`, `title`, `description`, `contract_type`, `location`, `skills`, `image`, `deleted`, `created_at`, `updated_at`) VALUES
	(7, 1, 'Aut tempor eaque eum', 'Tenetur dolorem fugi', 'CDI', 'Dolores ullam nostru', 'Qui et iste enim qui', NULL, 0, '2026-01-22 10:09:11', '2026-01-22 10:09:11'),
	(8, 6, 'maroua', 'hjkjkhkdksd', 'Stage', 'meknes', 'javascript', NULL, 1, '2026-01-22 10:44:48', '2026-01-22 10:45:02'),
	(9, 7, 'Consequat Quibusdam', 'Illum ex illo ea et', 'CDI', 'Sunt autem dolorum ', 'Qui delectus conseq', NULL, 0, '2026-01-22 10:46:32', '2026-01-22 10:46:32');

-- Listage de la structure de table job_dating_youcode. companies
CREATE TABLE IF NOT EXISTS `companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table job_dating_youcode.companies : ~3 rows (environ)
DELETE FROM `companies`;
INSERT INTO `companies` (`id`, `name`, `sector`, `location`, `email`, `phone`, `avatar`, `created_at`) VALUES
	(1, 'Tech Solutions', 'Technologie', 'Casablanca', 'contact@techsolutions.ma', '0522123456', NULL, '2026-01-21 09:57:44'),
	(4, 'Garrison Fletcher', NULL, 'Exercitationem eaque', 'xorig@mailinator.com', '+1 (195) 396-6486', NULL, '2026-01-22 10:10:04'),
	(5, 'Heidi Mueller', 'Est eu at illo odio ', 'Assumenda voluptatem', 'fody@mailinator.com', '+1 (924) 106-1437', NULL, '2026-01-22 10:10:34'),
	(6, 'maroua kourdi', 'maroc', 'raba', 'maroua@gmail.com', '+1 (695) 814-7558', NULL, '2026-01-22 10:12:08'),
	(7, 'Roary Bell', 'Sit voluptatem ipsam', 'Voluptas dolore ulla', 'rizuv@mailinator.com', '+1 (698) 884-3843', NULL, '2026-01-22 10:42:10');

-- Listage de la structure de table job_dating_youcode. login_attempts
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `attempted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_attempted_at` (`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table job_dating_youcode.login_attempts : ~0 rows (environ)
DELETE FROM `login_attempts`;
INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `success`, `attempted_at`) VALUES
	(1, 'med@med.com', '::1', 0, '2026-01-21 16:19:43'),
	(2, 'med@med.com', '::1', 0, '2026-01-21 16:19:56'),
	(3, 'hewura@mailinator.com', '::1', 1, '2026-01-21 16:22:56'),
	(4, 'hewura@mailinator.com', '::1', 1, '2026-01-21 16:23:21'),
	(5, 'hewura@mailinator.com', '::1', 1, '2026-01-21 16:23:56'),
	(6, 'hewura@mailinator.com', '::1', 1, '2026-01-21 16:28:06'),
	(7, 'hewura@mailinator.com', '::1', 1, '2026-01-22 08:05:00'),
	(8, 'hewura@mailinator.com', '::1', 1, '2026-01-22 08:30:13');

--@block Listage de la structure de table job_dating_youcode.applications
CREATE TABLE IF NOT EXISTS `applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `announcement_id` int NOT NULL,
  `cover_letter` text,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `applied_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `announcement_id` (`announcement_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE applications
ADD COLUMN `cover_letter` text AFTER `announcement_id`;
-- Listage des données de la table job_dating_youcode.applications : ~0 rows (environ)
DELETE FROM `applications`;

-- Listage de la structure de table job_dating_youcode. students
CREATE TABLE IF NOT EXISTS `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `promotion` varchar(50) DEFAULT NULL,
  `specialisation` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table job_dating_youcode.students : ~0 rows (environ)
DELETE FROM `students`;
INSERT INTO `students` (`id`, `user_id`, `promotion`, `specialisation`) VALUES
	(1, 5, '2025-2026', 'informatique');

-- Listage de la structure de table job_dating_youcode. users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table job_dating_youcode.users : ~5 rows (environ)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
	(1, 'Ahmed Mohammed', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2026-01-21 09:57:44'),
	(2, 'Fatima Zahra', 'fatima@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2026-01-21 09:57:44'),
	(3, 'Youssef Ali', 'youssef@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2026-01-21 09:57:44'),
	(4, 'med', 'med@med.com', 'facebook', 'admin', '2026-01-21 16:11:37'),
	(5, 'Camille Bolton', 'hewura@mailinator.com', '$2y$10$GJWgdO.fR85x.VBPa/1Eqe7OqemRu4LJBO6RdcyTtm9vR6ztWP552', 'admin', '2026-01-21 16:22:25');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
