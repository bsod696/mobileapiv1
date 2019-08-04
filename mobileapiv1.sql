-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               10.3.10-MariaDB-log - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5332
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for mobileapiv1
DROP DATABASE IF EXISTS `mobileapiv1`;
CREATE DATABASE IF NOT EXISTS `mobileapiv1` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `mobileapiv1`;

-- Dumping structure for table mobileapiv1.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mobileapiv1.migrations: ~2 rows (approximately)
DELETE FROM `migrations`;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table mobileapiv1.password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mobileapiv1.password_resets: ~0 rows (approximately)
DELETE FROM `password_resets`;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table mobileapiv1.stream_det
DROP TABLE IF EXISTS `stream_det`;
CREATE TABLE IF NOT EXISTS `stream_det` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `strlink` text DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table mobileapiv1.stream_det: ~0 rows (approximately)
DELETE FROM `stream_det`;
/*!40000 ALTER TABLE `stream_det` DISABLE KEYS */;
INSERT INTO `stream_det` (`id`, `strlink`, `status`, `created_at`, `updated_at`) VALUES
	(1, '192.168.1.2:8000', 'INACTIVE', '2019-07-16 15:46:15', '2019-07-24 23:48:16');
/*!40000 ALTER TABLE `stream_det` ENABLE KEYS */;

-- Dumping structure for table mobileapiv1.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `studentid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `studentmail` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PIN` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `temporaryPIN` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_type` enum('U','G') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'G',
  `token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fullname` (`studentmail`),
  UNIQUE KEY `username` (`studentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mobileapiv1.users: ~3 rows (approximately)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `studentid`, `studentmail`, `password`, `PIN`, `temporaryPIN`, `access_type`, `token`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'AAABBBBCCCDDDD', 'fai@hotmail.com', '$2y$10$4VsoZjXhob5R60ImKaoTpu43j4jea.sTJRymYv6Mk/zRrP2ulANMa', '555', '', 'U', '1b548d70e55aad89947e75d8c0e1227f0bda96ed4092959dada8631258ca88f6', NULL, '2019-07-16 23:54:22', '2019-07-16 23:54:28'),
	(2, 'AAABBBBCCCDDDD1', 'firdaushakul@gmail.com', '$2y$10$3Dwm3JrdgGPn/qxfzTNb/O/s/ufU5QiicusjYleyVjFBECV4XHf6i', '000', '', 'U', 'ca006823be181b588d7d13d88e2bc8b0ac92684892cdd4db4ec9b3d96f09c0a1', NULL, '2019-07-16 17:08:49', '2019-07-16 17:27:17');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
