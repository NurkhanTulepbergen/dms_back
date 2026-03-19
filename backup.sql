-- MySQL dump 10.13  Distrib 9.5.0, for macos26.1 (arm64)
--
-- Host: metro.proxy.rlwy.net    Database: railway
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `buildings`
--

DROP TABLE IF EXISTS `buildings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buildings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_floors` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buildings`
--

LOCK TABLES `buildings` WRITE;
/*!40000 ALTER TABLE `buildings` DISABLE KEYS */;
INSERT INTO `buildings` VALUES (1,'Ислама Каримова, 70 к1',5,'2026-02-24 05:49:26','2026-03-18 15:35:42','Ислама Каримова, 70 к1',43.2525200,76.8823370),(2,'Ислама Каримова, 70 к2',5,'2026-02-24 05:49:38','2026-03-18 15:36:14','Ислама Каримова, 70 к2',43.2525200,76.8823370),(3,'Ислама Каримова, 70 к3',9,'2026-02-24 05:49:45','2026-03-18 15:36:41','Ислама Каримова, 70 к3',43.2525200,76.8823370),(4,'Ислама Каримова, 70 к4',9,'2026-02-25 15:31:04','2026-03-18 15:37:57','Ислама Каримова, 70 к4',43.2522840,76.8823410),(5,'Тургут Озала, 80',6,'2026-02-25 15:31:26','2026-03-18 15:38:56','Тургут Озала, 80',43.2522020,76.8808850);
/*!40000 ALTER TABLE `buildings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-livewire-rate-limiter:47b52c3e722b853668ed73f346f9f8ac21f4b1fb','i:1;',1773916911),('laravel-cache-livewire-rate-limiter:47b52c3e722b853668ed73f346f9f8ac21f4b1fb:timer','i:1773916911;',1773916911),('laravel-cache-livewire-rate-limiter:e0c62e0f21394a9dcbb732eb19d97aeceb06fd20','i:1;',1773850135),('laravel-cache-livewire-rate-limiter:e0c62e0f21394a9dcbb732eb19d97aeceb06fd20:timer','i:1773850135;',1773850135);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `charges`
--

DROP TABLE IF EXISTS `charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `settlement_id` bigint unsigned DEFAULT NULL,
  `gym_plan_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'KZT',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'semester_rent',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `charges_settlement_type_unique` (`settlement_id`,`type`),
  KEY `charges_user_id_foreign` (`user_id`),
  KEY `charges_gym_plan_id_foreign` (`gym_plan_id`),
  CONSTRAINT `charges_gym_plan_id_foreign` FOREIGN KEY (`gym_plan_id`) REFERENCES `gym_plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `charges_settlement_id_foreign` FOREIGN KEY (`settlement_id`) REFERENCES `settlements` (`id`) ON DELETE SET NULL,
  CONSTRAINT `charges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `charges`
--

LOCK TABLES `charges` WRITE;
/*!40000 ALTER TABLE `charges` DISABLE KEYS */;
INSERT INTO `charges` VALUES (1,6,1,NULL,700000.00,'KZT','semester_rent','2026-02-01','2026-07-31','pending','2026-02-25 16:05:52','2026-02-25 16:05:52'),(2,3,2,NULL,1000000.00,'KZT','semester_rent','2026-02-01','2026-07-31','pending','2026-02-25 16:36:19','2026-02-25 16:36:19'),(3,8,3,NULL,800000.00,'KZT','semester_rent','2026-02-01','2026-07-31','pending','2026-02-26 05:45:32','2026-02-26 05:45:32'),(4,4,4,NULL,800000.00,'KZT','semester_rent','2026-02-01','2026-07-31','pending','2026-02-26 06:32:07','2026-02-26 06:32:07'),(5,9,5,NULL,950000.00,'KZT','semester_rent','2026-03-01','2026-08-31','paid','2026-03-12 15:43:10','2026-03-12 17:03:01'),(6,9,NULL,1,10000.00,'KZT','gym_membership','2026-03-14','2026-04-13','paid','2026-03-14 20:33:15','2026-03-14 20:33:46'),(7,3,NULL,1,10000.00,'KZT','gym_membership','2026-03-14','2026-04-13','pending','2026-03-14 20:54:40','2026-03-14 20:54:40'),(8,1,6,NULL,500000.00,'KZT','semester_rent','2026-03-01','2026-08-31','paid','2026-03-16 12:09:01','2026-03-16 12:10:02'),(9,1,NULL,1,10000.00,'KZT','gym_membership','2026-03-16','2026-04-15','pending','2026-03-16 13:38:29','2026-03-16 13:38:29'),(10,10,7,NULL,700000.00,'KZT','semester_rent','2026-03-01','2026-08-31','pending','2026-03-17 17:35:20','2026-03-17 17:35:20'),(11,11,8,NULL,700000.00,'KZT','semester_rent','2026-03-01','2026-08-31','pending','2026-03-17 18:06:43','2026-03-17 18:06:43'),(12,13,NULL,NULL,4000.00,'KZT','gym_membership','2026-03-18','2026-04-01','cancelled','2026-03-18 14:02:06','2026-03-18 15:07:54'),(13,13,NULL,4,100000.00,'KZT','gym_membership','2026-03-18','2027-03-18','paid','2026-03-18 14:54:12','2026-03-18 14:54:37'),(14,13,9,NULL,950000.00,'KZT','semester_rent','2026-03-01','2026-08-31','paid','2026-03-18 15:44:05','2026-03-18 15:51:06');
/*!40000 ALTER TABLE `charges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_request_id_foreign` (`request_id`),
  CONSTRAINT `documents_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `request_lives` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dorm_students`
--

DROP TABLE IF EXISTS `dorm_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dorm_students` (
  `user_id` bigint unsigned NOT NULL,
  `warning_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `dorm_students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dorm_students`
--

LOCK TABLES `dorm_students` WRITE;
/*!40000 ALTER TABLE `dorm_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `dorm_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `floors`
--

DROP TABLE IF EXISTS `floors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `floors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `building_id` bigint unsigned NOT NULL,
  `floor_number` int NOT NULL,
  `gender_policy` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mixed',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `floors_building_id_foreign` (`building_id`),
  CONSTRAINT `floors_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `floors`
--

LOCK TABLES `floors` WRITE;
/*!40000 ALTER TABLE `floors` DISABLE KEYS */;
INSERT INTO `floors` VALUES (1,1,1,'mixed',1,'2026-02-24 05:50:47','2026-02-24 07:47:39'),(2,1,2,'mixed',1,'2026-02-24 05:50:56','2026-02-24 07:47:39'),(3,1,3,'mixed',1,'2026-02-24 05:51:01','2026-02-24 07:47:39'),(4,1,4,'mixed',1,'2026-02-24 05:51:07','2026-02-24 07:47:39'),(5,1,5,'mixed',1,'2026-02-24 05:51:12','2026-02-24 07:47:39'),(9,2,1,'female',1,'2026-02-24 06:25:51','2026-02-24 07:47:39'),(10,2,2,'female',1,'2026-02-24 06:26:17','2026-02-24 07:47:39'),(11,2,3,'female',1,'2026-02-24 06:26:25','2026-02-24 07:47:39'),(12,2,4,'female',1,'2026-02-24 06:26:31','2026-02-24 07:47:39'),(13,2,5,'female',1,'2026-02-24 06:26:36','2026-02-24 07:47:39'),(15,3,1,'male',1,'2026-02-24 06:28:32','2026-02-24 07:47:39'),(16,3,2,'male',1,'2026-02-24 06:28:39','2026-02-24 07:47:39'),(17,3,3,'male',1,'2026-02-24 06:28:46','2026-02-24 07:47:39'),(18,3,4,'male',1,'2026-02-24 06:28:53','2026-02-24 07:47:39'),(19,3,5,'male',1,'2026-02-24 06:28:59','2026-02-24 07:47:39'),(20,3,6,'mixed',1,'2026-02-25 15:36:21','2026-02-25 15:36:21'),(21,3,7,'mixed',1,'2026-02-25 15:36:50','2026-02-25 15:36:50'),(22,3,8,'mixed',1,'2026-02-25 15:36:56','2026-02-25 15:36:56'),(23,3,9,'mixed',1,'2026-02-25 15:37:05','2026-02-25 15:37:05'),(25,4,1,'mixed',1,'2026-02-25 15:43:52','2026-02-25 15:43:52'),(26,4,2,'mixed',1,'2026-02-25 15:44:59','2026-02-25 15:44:59'),(27,4,3,'mixed',1,'2026-02-25 15:45:06','2026-02-25 15:45:06'),(28,4,4,'mixed',1,'2026-02-25 15:45:13','2026-02-25 15:45:13'),(29,4,5,'mixed',1,'2026-02-25 15:45:21','2026-02-25 15:45:21'),(30,4,6,'mixed',1,'2026-02-25 15:45:40','2026-02-25 15:45:40'),(31,4,7,'mixed',1,'2026-02-25 15:45:45','2026-02-25 15:45:45'),(32,4,8,'mixed',1,'2026-02-25 15:45:55','2026-02-25 15:45:55'),(33,4,9,'mixed',1,'2026-02-25 15:46:07','2026-02-25 15:46:07'),(34,5,1,'mixed',1,'2026-02-25 15:46:28','2026-02-25 15:46:28'),(35,5,2,'mixed',1,'2026-02-25 15:46:33','2026-02-25 15:46:33'),(36,5,3,'mixed',1,'2026-02-25 15:46:38','2026-02-25 15:46:38'),(37,5,4,'mixed',1,'2026-02-25 15:46:44','2026-02-25 15:46:44'),(38,5,5,'mixed',1,'2026-02-25 15:46:49','2026-02-25 15:46:49'),(39,5,6,'mixed',1,'2026-02-25 15:47:08','2026-02-25 15:47:08');
/*!40000 ALTER TABLE `floors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gym_memberships`
--

DROP TABLE IF EXISTS `gym_memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_memberships` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `charge_id` bigint unsigned NOT NULL,
  `total_sessions` int NOT NULL,
  `remaining_sessions` int NOT NULL,
  `started_at` date NOT NULL,
  `expires_at` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gym_memberships_charge_id_unique` (`charge_id`),
  KEY `gym_memberships_plan_id_foreign` (`plan_id`),
  KEY `gym_memberships_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `gym_memberships_charge_id_foreign` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gym_memberships_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `gym_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gym_memberships_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_memberships`
--

LOCK TABLES `gym_memberships` WRITE;
/*!40000 ALTER TABLE `gym_memberships` DISABLE KEYS */;
INSERT INTO `gym_memberships` VALUES (1,9,1,6,12,8,'2026-03-14','2026-04-13','active','2026-03-14 20:33:46','2026-03-18 13:36:50'),(2,13,4,13,400,399,'2026-03-18','2027-03-18','active','2026-03-18 14:54:37','2026-03-18 15:00:10');
/*!40000 ALTER TABLE `gym_memberships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gym_plans`
--

DROP TABLE IF EXISTS `gym_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_sessions` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_days` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_plans`
--

LOCK TABLES `gym_plans` WRITE;
/*!40000 ALTER TABLE `gym_plans` DISABLE KEYS */;
INSERT INTO `gym_plans` VALUES (1,'Месячный абонемент',12,10000.00,30,1,'2026-03-14 20:30:57','2026-03-14 20:30:57'),(3,'Полугодовой абонемент',120,50000.00,180,1,'2026-03-18 14:02:04','2026-03-18 14:02:04'),(4,'Годовой абонемент',400,100000.00,365,1,'2026-03-18 14:47:17','2026-03-18 14:47:17'),(5,'Расширенный абонемент',24,18000.00,60,1,'2026-03-18 14:47:30','2026-03-18 14:47:30');
/*!40000 ALTER TABLE `gym_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gym_visits`
--

DROP TABLE IF EXISTS `gym_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `membership_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `visit_date` date NOT NULL,
  `check_in_at` timestamp NOT NULL,
  `check_out_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int DEFAULT NULL,
  `sessions_used` int unsigned NOT NULL DEFAULT '1',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gym_visits_user_id_status_index` (`user_id`,`status`),
  KEY `gym_visits_membership_id_visit_date_index` (`membership_id`,`visit_date`),
  CONSTRAINT `gym_visits_membership_id_foreign` FOREIGN KEY (`membership_id`) REFERENCES `gym_memberships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gym_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_visits`
--

LOCK TABLES `gym_visits` WRITE;
/*!40000 ALTER TABLE `gym_visits` DISABLE KEYS */;
INSERT INTO `gym_visits` VALUES (1,1,9,'2026-03-14','2026-03-14 22:46:49','2026-03-14 22:49:01',2,1,'completed','2026-03-14 22:46:49','2026-03-14 22:49:01'),(2,1,9,'2026-03-14','2026-03-14 23:01:59','2026-03-14 23:02:20',0,1,'completed','2026-03-14 23:01:59','2026-03-14 23:02:20'),(3,1,9,'2026-03-14','2026-03-14 23:02:48','2026-03-14 23:03:05',0,1,'completed','2026-03-14 23:02:48','2026-03-14 23:03:05'),(4,1,9,'2026-03-18','2026-03-18 13:36:50','2026-03-18 13:37:52',1,1,'completed','2026-03-18 13:36:50','2026-03-18 13:37:52'),(5,2,13,'2026-03-18','2026-03-18 15:00:10',NULL,NULL,1,'active','2026-03-18 15:00:10','2026-03-18 15:00:10');
/*!40000 ALTER TABLE `gym_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_11_27_190218_create_personal_access_tokens_table',1),(5,'2025_12_04_074159_create_news_table',1),(6,'2025_12_04_084757_add_custom_fields_to_users_table',1),(7,'2025_12_22_135741_create_buildings_table',1),(8,'2025_12_22_135757_create_floors_table',1),(9,'2025_12_22_135803_create_rooms_table',1),(10,'2025_12_22_141751_create_dorm_students_table',1),(11,'2025_12_22_145224_create_request_lives_table',1),(12,'2025_12_22_150808_create_request_change_rooms_table',1),(13,'2026_01_21_135852_create_settlements_table',1),(14,'2026_02_09_000001_create_documents_table',1),(15,'2026_02_09_061500_add_gender_to_users_table',1),(16,'2026_02_09_061501_add_gender_policy_and_is_active_to_floors_table',1),(17,'2026_02_09_061502_add_is_active_to_rooms_table',1),(18,'2026_02_09_061503_add_preferred_room_id_to_request_lives_table',1),(19,'2026_02_09_061504_rebuild_settlements_table',1),(20,'2026_02_09_061505_rebuild_dorm_students_table',1),(21,'2026_02_09_061506_rebuild_request_lives_table',1),(22,'2026_02_12_064152_create_room_types_tables',1),(23,'2026_02_12_064220_create_room_types_id_tables',1),(24,'2026_02_12_064233_create_charges_tables',1),(25,'2026_02_12_064248_create_payments_tables',1),(26,'2026_02_12_141000_add_room_type_id_to_rooms_table',1),(27,'2026_02_12_141100_add_unique_constraint_to_charges_table',1),(28,'2026_02_12_141200_drop_room_types_id_table',1),(29,'2026_02_12_200000_change_users_gender_to_enum',1),(30,'2026_02_19_000001_create_penalty_rules_table',1),(31,'2026_02_19_000002_create_penalties_table',1),(32,'2026_02_19_000003_create_penalty_evidences_table',1),(33,'2026_02_19_000004_create_penalty_redemptions_table',1),(34,'2026_02_19_000005_add_discipline_limit_to_users_table',1),(35,'2026_02_24_120000_drop_capacity_from_room_types_table',2),(36,'2026_02_24_130100_create_gym_plans_table',3),(37,'2026_02_24_130200_create_gym_memberships_table',3),(38,'2026_02_24_130300_create_gym_visits_table',3),(39,'2026_03_15_120000_update_charges_for_gym_payments',4),(40,'2026_03_15_130000_update_gym_memberships_and_visits_for_checkins',5),(41,'2026_03_18_120000_add_name_and_coordinates_to_buildings_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (3,'Открытие новой лаборатории искусственного интеллекта','В университете состоялось торжественное открытие лаборатории искусственного интеллекта, оснащённой современным оборудованием для исследований в области машинного обучения и анализа данных. Студенты смогут участвовать в научных проектах и стажировках совместно с IT-компаниями',NULL,'2026-02-24 07:49:33','2026-02-25 18:41:09'),(4,'Студенты представили стартапы на ежегодной выставке проектов','Прошла ежегодная выставка студенческих стартапов, где участники презентовали проекты в сферах EdTech, FinTech и экологии. Лучшие команды получили гранты на дальнейшее развитие своих идей.',NULL,'2026-02-24 07:49:45','2026-02-24 07:49:45'),(5,'Университет подписал меморандум о сотрудничестве с международным вузом','Подписано соглашение о сотрудничестве с зарубежным университетом, предусматривающее обмен студентами, совместные исследования и академическую мобильность преподавателей.',NULL,'2026-02-24 07:49:59','2026-02-24 07:49:59'),(6,'Запуск программы стажировок для студентов IT-направлений','Университет совместно с ведущими IT-компаниями запустил программу оплачиваемых стажировок. Участники смогут получить практический опыт разработки и DevOps в реальных проектах.',NULL,'2026-02-24 07:50:11','2026-02-24 07:50:11'),(7,'Проведена научная конференция молодых исследователей','В университете прошла конференция, на которой студенты и магистранты представили доклады по актуальным научным направлениям: от анализа больших данных до устойчивого развития городов.',NULL,'2026-02-24 07:50:24','2026-02-24 07:50:24'),(8,'Обновление общежитий и создание новых коворкинг-зон','Завершена модернизация общежитий: обновлены комнаты, созданы зоны коворкинга и пространства для совместной учебы. Улучшения направлены на повышение комфорта студентов.',NULL,'2026-02-24 07:50:37','2026-02-24 07:50:37'),(9,'fcvgbhnjm,','drfghjkl,',NULL,'2026-03-18 18:16:14','2026-03-18 18:16:14');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `charge_id` bigint unsigned NOT NULL,
  `stripe_session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_payment_intent_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `raw_payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_charge_id_foreign` (`charge_id`),
  CONSTRAINT `payments_charge_id_foreign` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,5,'cs_test_a19t2ZjjkXIZMhqCoSrX5jhBaxMoBLcRLFRLCBckW4UGnLFgpiMfm7rxEo',NULL,950000.00,'pending',NULL,NULL,'2026-03-12 15:43:25','2026-03-12 15:43:25'),(2,5,'cs_test_a1LgFF7k6Q47qSMjRbMW011dEF3CH6ZvhkFO15i59k8g8hsVtFDcbJYYhE',NULL,950000.00,'pending',NULL,NULL,'2026-03-12 15:49:08','2026-03-12 15:49:08'),(3,5,'cs_test_a1v47yVNOswr6G0pyqySqvKlkNbwcDQlDGJuLVU24wGWKe9QARf6iQ9Iqu',NULL,950000.00,'pending',NULL,NULL,'2026-03-12 16:13:25','2026-03-12 16:13:25'),(4,5,'cs_test_a1Z1V01gavTtRNpeG7AqP4xWicwjZQSgq9am4G4ERL5Bjp6hMRaf79tUEF',NULL,950000.00,'pending',NULL,NULL,'2026-03-12 16:17:58','2026-03-12 16:17:58'),(5,5,'cs_test_a1zjGbPGrmum5hq1A85nQus4hhFab2u61LsL1LDyT8sWe7ZKGmjA16ISP5','pi_3TACZrJhH8az6DoF002Mje6K',950000.00,'succeeded','2026-03-12 17:58:55','{\"id\": \"cs_test_a1zjGbPGrmum5hq1A85nQus4hhFab2u61LsL1LDyT8sWe7ZKGmjA16ISP5\", \"url\": null, \"mode\": \"payment\", \"locale\": null, \"object\": \"checkout.session\", \"status\": \"complete\", \"consent\": null, \"created\": 1773334718, \"invoice\": null, \"ui_mode\": \"hosted\", \"currency\": \"kzt\", \"customer\": null, \"livemode\": false, \"metadata\": {\"charge_id\": \"5\"}, \"discounts\": [], \"cancel_url\": \"http://localhost:5173/payment-cancel\", \"expires_at\": 1773421118, \"custom_text\": {\"submit\": null, \"after_submit\": null, \"shipping_address\": null, \"terms_of_service_acceptance\": null}, \"permissions\": null, \"submit_type\": null, \"success_url\": \"http://localhost:5173/payment-success\", \"amount_total\": 95000000, \"payment_link\": null, \"setup_intent\": null, \"subscription\": null, \"automatic_tax\": {\"status\": null, \"enabled\": false, \"provider\": null, \"liability\": null}, \"client_secret\": null, \"custom_fields\": [], \"shipping_cost\": null, \"total_details\": {\"amount_tax\": 0, \"amount_discount\": 0, \"amount_shipping\": 0}, \"customer_email\": null, \"origin_context\": null, \"payment_intent\": \"pi_3TACZrJhH8az6DoF002Mje6K\", \"payment_status\": \"paid\", \"recovered_from\": null, \"wallet_options\": null, \"amount_subtotal\": 95000000, \"adaptive_pricing\": {\"enabled\": true}, \"after_expiration\": null, \"customer_account\": null, \"customer_details\": {\"name\": \"Nurkhan Tulepbegren\", \"email\": \"n_tulepbegren@kbtu.kz\", \"phone\": null, \"address\": {\"city\": null, \"line1\": null, \"line2\": null, \"state\": null, \"country\": \"KZ\", \"postal_code\": null}, \"tax_ids\": [], \"tax_exempt\": \"none\", \"business_name\": null, \"individual_name\": null}, \"invoice_creation\": {\"enabled\": false, \"invoice_data\": {\"footer\": null, \"issuer\": null, \"metadata\": [], \"description\": null, \"custom_fields\": null, \"account_tax_ids\": null, \"rendering_options\": null}}, \"shipping_options\": [], \"branding_settings\": {\"icon\": null, \"logo\": null, \"font_family\": \"default\", \"border_style\": \"rounded\", \"button_color\": \"#0074d4\", \"display_name\": \"New business sandbox\", \"background_color\": \"#ffffff\"}, \"customer_creation\": \"if_required\", \"consent_collection\": null, \"client_reference_id\": null, \"currency_conversion\": null, \"payment_method_types\": [\"card\"], \"allow_promotion_codes\": null, \"collected_information\": null, \"integration_identifier\": null, \"payment_method_options\": {\"card\": {\"request_three_d_secure\": \"automatic\"}}, \"phone_number_collection\": {\"enabled\": false}, \"payment_method_collection\": \"if_required\", \"billing_address_collection\": null, \"shipping_address_collection\": null, \"saved_payment_method_options\": null, \"payment_method_configuration_details\": null}','2026-03-12 16:58:39','2026-03-12 17:58:55'),(6,5,'cs_test_a13Qj3y23NztdwU5KLF83JLeHI2sXyA0Ba3dA6zZkx770ddznoZNsLVRYH','pi_3TACdTJhH8az6DoF1U5oS3eA',950000.00,'succeeded','2026-03-12 17:03:01','{\"id\": \"cs_test_a13Qj3y23NztdwU5KLF83JLeHI2sXyA0Ba3dA6zZkx770ddznoZNsLVRYH\", \"url\": null, \"mode\": \"payment\", \"locale\": null, \"object\": \"checkout.session\", \"status\": \"complete\", \"consent\": null, \"created\": 1773334954, \"invoice\": null, \"ui_mode\": \"hosted\", \"currency\": \"kzt\", \"customer\": null, \"livemode\": false, \"metadata\": {\"charge_id\": \"5\"}, \"discounts\": [], \"cancel_url\": \"http://localhost:5173/payment-cancel\", \"expires_at\": 1773421354, \"custom_text\": {\"submit\": null, \"after_submit\": null, \"shipping_address\": null, \"terms_of_service_acceptance\": null}, \"permissions\": null, \"submit_type\": null, \"success_url\": \"http://localhost:5173/payment-success\", \"amount_total\": 95000000, \"payment_link\": null, \"setup_intent\": null, \"subscription\": null, \"automatic_tax\": {\"status\": null, \"enabled\": false, \"provider\": null, \"liability\": null}, \"client_secret\": null, \"custom_fields\": [], \"shipping_cost\": null, \"total_details\": {\"amount_tax\": 0, \"amount_discount\": 0, \"amount_shipping\": 0}, \"customer_email\": null, \"origin_context\": null, \"payment_intent\": \"pi_3TACdTJhH8az6DoF1U5oS3eA\", \"payment_status\": \"paid\", \"recovered_from\": null, \"wallet_options\": null, \"amount_subtotal\": 95000000, \"adaptive_pricing\": {\"enabled\": true}, \"after_expiration\": null, \"customer_account\": null, \"customer_details\": {\"name\": \"Nurkhan Tulepbergen\", \"email\": \"n_tulepbergen@kbtu.kz\", \"phone\": null, \"address\": {\"city\": null, \"line1\": null, \"line2\": null, \"state\": null, \"country\": \"KZ\", \"postal_code\": null}, \"tax_ids\": [], \"tax_exempt\": \"none\", \"business_name\": null, \"individual_name\": null}, \"invoice_creation\": {\"enabled\": false, \"invoice_data\": {\"footer\": null, \"issuer\": null, \"metadata\": [], \"description\": null, \"custom_fields\": null, \"account_tax_ids\": null, \"rendering_options\": null}}, \"shipping_options\": [], \"branding_settings\": {\"icon\": null, \"logo\": null, \"font_family\": \"default\", \"border_style\": \"rounded\", \"button_color\": \"#0074d4\", \"display_name\": \"New business sandbox\", \"background_color\": \"#ffffff\"}, \"customer_creation\": \"if_required\", \"consent_collection\": null, \"client_reference_id\": null, \"currency_conversion\": null, \"payment_method_types\": [\"card\"], \"allow_promotion_codes\": null, \"collected_information\": null, \"integration_identifier\": null, \"payment_method_options\": {\"card\": {\"request_three_d_secure\": \"automatic\"}}, \"phone_number_collection\": {\"enabled\": false}, \"payment_method_collection\": \"if_required\", \"billing_address_collection\": null, \"shipping_address_collection\": null, \"saved_payment_method_options\": null, \"payment_method_configuration_details\": null}','2026-03-12 17:02:34','2026-03-12 17:03:01'),(7,6,'cs_test_a1Prqsh9JoxYZqnFMHe5bnPzXRLCw8AEf1gFPOT7acuOaFByn9O4baMI71','pi_3TAysXJhH8az6DoF1ZJJBI58',10000.00,'succeeded','2026-03-14 20:33:46','{\"id\": \"cs_test_a1Prqsh9JoxYZqnFMHe5bnPzXRLCw8AEf1gFPOT7acuOaFByn9O4baMI71\", \"url\": null, \"mode\": \"payment\", \"locale\": null, \"object\": \"checkout.session\", \"status\": \"complete\", \"consent\": null, \"created\": 1773520396, \"invoice\": null, \"ui_mode\": \"hosted\", \"currency\": \"kzt\", \"customer\": null, \"livemode\": false, \"metadata\": {\"source\": \"gym\", \"charge_id\": \"6\", \"gym_plan_id\": \"1\"}, \"discounts\": [], \"cancel_url\": \"http://localhost:5173/payment-cancel?source=gym\", \"expires_at\": 1773606795, \"custom_text\": {\"submit\": null, \"after_submit\": null, \"shipping_address\": null, \"terms_of_service_acceptance\": null}, \"permissions\": null, \"submit_type\": null, \"success_url\": \"http://localhost:5173/payment-success?source=gym\", \"amount_total\": 1000000, \"payment_link\": null, \"setup_intent\": null, \"subscription\": null, \"automatic_tax\": {\"status\": null, \"enabled\": false, \"provider\": null, \"liability\": null}, \"client_secret\": null, \"custom_fields\": [], \"shipping_cost\": null, \"total_details\": {\"amount_tax\": 0, \"amount_discount\": 0, \"amount_shipping\": 0}, \"customer_email\": null, \"origin_context\": null, \"payment_intent\": \"pi_3TAysXJhH8az6DoF1ZJJBI58\", \"payment_status\": \"paid\", \"recovered_from\": null, \"wallet_options\": null, \"amount_subtotal\": 1000000, \"adaptive_pricing\": {\"enabled\": true}, \"after_expiration\": null, \"customer_account\": null, \"customer_details\": {\"name\": \"Test\", \"email\": \"n_tulepbergen@kbtu.kz\", \"phone\": null, \"address\": {\"city\": null, \"line1\": null, \"line2\": null, \"state\": null, \"country\": \"KZ\", \"postal_code\": null}, \"tax_ids\": [], \"tax_exempt\": \"none\", \"business_name\": null, \"individual_name\": null}, \"invoice_creation\": {\"enabled\": false, \"invoice_data\": {\"footer\": null, \"issuer\": null, \"metadata\": [], \"description\": null, \"custom_fields\": null, \"account_tax_ids\": null, \"rendering_options\": null}}, \"shipping_options\": [], \"branding_settings\": {\"icon\": null, \"logo\": null, \"font_family\": \"default\", \"border_style\": \"rounded\", \"button_color\": \"#0074d4\", \"display_name\": \"New business sandbox\", \"background_color\": \"#ffffff\"}, \"customer_creation\": \"if_required\", \"consent_collection\": null, \"client_reference_id\": null, \"currency_conversion\": null, \"payment_method_types\": [\"card\"], \"allow_promotion_codes\": null, \"collected_information\": null, \"integration_identifier\": null, \"payment_method_options\": {\"card\": {\"request_three_d_secure\": \"automatic\"}}, \"phone_number_collection\": {\"enabled\": false}, \"payment_method_collection\": \"if_required\", \"billing_address_collection\": null, \"shipping_address_collection\": null, \"saved_payment_method_options\": null, \"payment_method_configuration_details\": null}','2026-03-14 20:33:16','2026-03-14 20:33:46'),(8,7,'cs_test_a1y8WkQvTCOjORdNpwlutZ1lxYx4aJUTVLYf1D29OnIWilLhcTuYQb4uxD',NULL,10000.00,'pending',NULL,NULL,'2026-03-14 20:54:40','2026-03-14 20:54:40'),(9,8,'cs_test_a1JiN2biURQzzSz7TZy8cjpI3TrSqO9k06pPEJk3bl8OxvqpYYf8nz3oOY','pi_3TBZy9JhH8az6DoF18tvVPMh',500000.00,'succeeded','2026-03-16 12:10:02','{\"id\": \"cs_test_a1JiN2biURQzzSz7TZy8cjpI3TrSqO9k06pPEJk3bl8OxvqpYYf8nz3oOY\", \"url\": null, \"mode\": \"payment\", \"locale\": null, \"object\": \"checkout.session\", \"status\": \"complete\", \"consent\": null, \"created\": 1773662978, \"invoice\": null, \"ui_mode\": \"hosted\", \"currency\": \"kzt\", \"customer\": null, \"livemode\": false, \"metadata\": {\"charge_id\": \"8\"}, \"discounts\": [], \"cancel_url\": \"http://localhost:5173/payment-cancel\", \"expires_at\": 1773749377, \"custom_text\": {\"submit\": null, \"after_submit\": null, \"shipping_address\": null, \"terms_of_service_acceptance\": null}, \"permissions\": null, \"submit_type\": null, \"success_url\": \"http://localhost:5173/payment-success\", \"amount_total\": 50000000, \"payment_link\": null, \"setup_intent\": null, \"subscription\": null, \"automatic_tax\": {\"status\": null, \"enabled\": false, \"provider\": null, \"liability\": null}, \"client_secret\": null, \"custom_fields\": [], \"shipping_cost\": null, \"total_details\": {\"amount_tax\": 0, \"amount_discount\": 0, \"amount_shipping\": 0}, \"customer_email\": null, \"origin_context\": null, \"payment_intent\": \"pi_3TBZy9JhH8az6DoF18tvVPMh\", \"payment_status\": \"paid\", \"recovered_from\": null, \"wallet_options\": null, \"amount_subtotal\": 50000000, \"adaptive_pricing\": {\"enabled\": true}, \"after_expiration\": null, \"customer_account\": null, \"customer_details\": {\"name\": \"tetst\", \"email\": \"jdas@example.com\", \"phone\": null, \"address\": {\"city\": null, \"line1\": null, \"line2\": null, \"state\": null, \"country\": \"KZ\", \"postal_code\": null}, \"tax_ids\": [], \"tax_exempt\": \"none\", \"business_name\": null, \"individual_name\": null}, \"invoice_creation\": {\"enabled\": false, \"invoice_data\": {\"footer\": null, \"issuer\": null, \"metadata\": [], \"description\": null, \"custom_fields\": null, \"account_tax_ids\": null, \"rendering_options\": null}}, \"shipping_options\": [], \"branding_settings\": {\"icon\": null, \"logo\": null, \"font_family\": \"default\", \"border_style\": \"rounded\", \"button_color\": \"#0074d4\", \"display_name\": \"New business sandbox\", \"background_color\": \"#ffffff\"}, \"customer_creation\": \"if_required\", \"consent_collection\": null, \"client_reference_id\": null, \"currency_conversion\": null, \"payment_method_types\": [\"card\"], \"allow_promotion_codes\": null, \"collected_information\": null, \"integration_identifier\": null, \"payment_method_options\": {\"card\": {\"request_three_d_secure\": \"automatic\"}}, \"phone_number_collection\": {\"enabled\": false}, \"payment_method_collection\": \"if_required\", \"billing_address_collection\": null, \"shipping_address_collection\": null, \"saved_payment_method_options\": null, \"payment_method_configuration_details\": null}','2026-03-16 12:09:38','2026-03-16 12:10:02'),(10,9,'cs_test_a1iXnadwKzAnE3b7g26ZPSYCDKLaLkaHCukWXJFdJrGaPB1Uxl3dRTY6t0',NULL,10000.00,'pending',NULL,NULL,'2026-03-16 13:38:29','2026-03-16 13:38:29'),(11,12,'cs_test_a1DIw9FOjbywWcj5lcbOlItSHUNN73dAnaOOaNFqBOQTdHOcXtqXBe4f34',NULL,4000.00,'cancelled',NULL,NULL,'2026-03-18 14:02:08','2026-03-18 15:07:54'),(12,13,'cs_test_a1OJmcoYelP8v7sebV2Pek2mnOxlcITeQTtj293RVERyR6MF12Kk9gB8A9','pi_3TCLUVJhH8az6DoF1gBolLa2',100000.00,'succeeded','2026-03-18 14:54:37','{\"id\": \"cs_test_a1OJmcoYelP8v7sebV2Pek2mnOxlcITeQTtj293RVERyR6MF12Kk9gB8A9\", \"url\": null, \"mode\": \"payment\", \"locale\": null, \"object\": \"checkout.session\", \"status\": \"complete\", \"consent\": null, \"created\": 1773845653, \"invoice\": null, \"ui_mode\": \"hosted\", \"currency\": \"kzt\", \"customer\": null, \"livemode\": false, \"metadata\": {\"source\": \"gym\", \"charge_id\": \"13\", \"gym_plan_id\": \"4\"}, \"discounts\": [], \"cancel_url\": \"http://localhost:5173/payment-cancel?source=gym\", \"expires_at\": 1773932052, \"custom_text\": {\"submit\": null, \"after_submit\": null, \"shipping_address\": null, \"terms_of_service_acceptance\": null}, \"permissions\": null, \"submit_type\": null, \"success_url\": \"http://localhost:5173/gym/payment-success\", \"amount_total\": 10000000, \"payment_link\": null, \"setup_intent\": null, \"subscription\": null, \"automatic_tax\": {\"status\": null, \"enabled\": false, \"provider\": null, \"liability\": null}, \"client_secret\": null, \"custom_fields\": [], \"shipping_cost\": null, \"total_details\": {\"amount_tax\": 0, \"amount_discount\": 0, \"amount_shipping\": 0}, \"customer_email\": null, \"origin_context\": null, \"payment_intent\": \"pi_3TCLUVJhH8az6DoF1gBolLa2\", \"payment_status\": \"paid\", \"recovered_from\": null, \"wallet_options\": null, \"amount_subtotal\": 10000000, \"adaptive_pricing\": {\"enabled\": true}, \"after_expiration\": null, \"customer_account\": null, \"customer_details\": {\"name\": \"еуые\", \"email\": \"test@example.com\", \"phone\": null, \"address\": {\"city\": null, \"line1\": null, \"line2\": null, \"state\": null, \"country\": \"KZ\", \"postal_code\": null}, \"tax_ids\": [], \"tax_exempt\": \"none\", \"business_name\": null, \"individual_name\": null}, \"invoice_creation\": {\"enabled\": false, \"invoice_data\": {\"footer\": null, \"issuer\": null, \"metadata\": [], \"description\": null, \"custom_fields\": null, \"account_tax_ids\": null, \"rendering_options\": null}}, \"shipping_options\": [], \"branding_settings\": {\"icon\": null, \"logo\": null, \"font_family\": \"default\", \"border_style\": \"rounded\", \"button_color\": \"#0074d4\", \"display_name\": \"New business sandbox\", \"background_color\": \"#ffffff\"}, \"customer_creation\": \"if_required\", \"consent_collection\": null, \"client_reference_id\": null, \"currency_conversion\": null, \"payment_method_types\": [\"card\"], \"allow_promotion_codes\": null, \"collected_information\": null, \"integration_identifier\": null, \"payment_method_options\": {\"card\": {\"request_three_d_secure\": \"automatic\"}}, \"phone_number_collection\": {\"enabled\": false}, \"payment_method_collection\": \"if_required\", \"billing_address_collection\": null, \"shipping_address_collection\": null, \"saved_payment_method_options\": null, \"payment_method_configuration_details\": null}','2026-03-18 14:54:14','2026-03-18 14:54:37'),(13,12,'cs_test_a1tdalp2TZGMiACkHHtVgXi8NMYTCOZiAlspXpwvrQTtkzLA3Kt43psx0W',NULL,4000.00,'cancelled',NULL,NULL,'2026-03-18 15:02:06','2026-03-18 15:07:54'),(14,14,'cs_test_a1xo9yqfjmMiSWoyEtiSDKOaucUsfr5kjOSKgOercUGYXf3LUd1ykFvExo',NULL,950000.00,'pending',NULL,NULL,'2026-03-18 15:45:22','2026-03-18 15:45:22'),(15,14,'cs_test_a1QLIQ96EapBJMiIUEwMQ296Ivj1bVGXXhjbqjRmXB0AlLZNivZAr8FWmP','pi_3TCMNAJhH8az6DoF1sBayLJN',950000.00,'succeeded','2026-03-18 15:51:06','{\"id\": \"cs_test_a1QLIQ96EapBJMiIUEwMQ296Ivj1bVGXXhjbqjRmXB0AlLZNivZAr8FWmP\", \"url\": null, \"mode\": \"payment\", \"locale\": null, \"object\": \"checkout.session\", \"status\": \"complete\", \"consent\": null, \"created\": 1773849039, \"invoice\": null, \"ui_mode\": \"hosted\", \"currency\": \"kzt\", \"customer\": null, \"livemode\": false, \"metadata\": {\"charge_id\": \"14\"}, \"discounts\": [], \"cancel_url\": \"http://localhost:5173/payment-cancel\", \"expires_at\": 1773935438, \"custom_text\": {\"submit\": null, \"after_submit\": null, \"shipping_address\": null, \"terms_of_service_acceptance\": null}, \"permissions\": null, \"submit_type\": null, \"success_url\": \"http://localhost:5173/payment-success\", \"amount_total\": 95000000, \"payment_link\": null, \"setup_intent\": null, \"subscription\": null, \"automatic_tax\": {\"status\": null, \"enabled\": false, \"provider\": null, \"liability\": null}, \"client_secret\": null, \"custom_fields\": [], \"shipping_cost\": null, \"total_details\": {\"amount_tax\": 0, \"amount_discount\": 0, \"amount_shipping\": 0}, \"customer_email\": null, \"origin_context\": null, \"payment_intent\": \"pi_3TCMNAJhH8az6DoF1sBayLJN\", \"payment_status\": \"paid\", \"recovered_from\": null, \"wallet_options\": null, \"amount_subtotal\": 95000000, \"adaptive_pricing\": {\"enabled\": true}, \"after_expiration\": null, \"customer_account\": null, \"customer_details\": {\"name\": \"втолыфивфлв\", \"email\": \"bdhjabj@eacmlp.com\", \"phone\": null, \"address\": {\"city\": null, \"line1\": null, \"line2\": null, \"state\": null, \"country\": \"KZ\", \"postal_code\": null}, \"tax_ids\": [], \"tax_exempt\": \"none\", \"business_name\": null, \"individual_name\": null}, \"invoice_creation\": {\"enabled\": false, \"invoice_data\": {\"footer\": null, \"issuer\": null, \"metadata\": [], \"description\": null, \"custom_fields\": null, \"account_tax_ids\": null, \"rendering_options\": null}}, \"shipping_options\": [], \"branding_settings\": {\"icon\": null, \"logo\": null, \"font_family\": \"default\", \"border_style\": \"rounded\", \"button_color\": \"#0074d4\", \"display_name\": \"New business sandbox\", \"background_color\": \"#ffffff\"}, \"customer_creation\": \"if_required\", \"consent_collection\": null, \"client_reference_id\": null, \"currency_conversion\": null, \"payment_method_types\": [\"card\"], \"allow_promotion_codes\": null, \"collected_information\": null, \"integration_identifier\": null, \"payment_method_options\": {\"card\": {\"request_three_d_secure\": \"automatic\"}}, \"phone_number_collection\": {\"enabled\": false}, \"payment_method_collection\": \"if_required\", \"billing_address_collection\": null, \"shipping_address_collection\": null, \"saved_payment_method_options\": null, \"payment_method_configuration_details\": null}','2026-03-18 15:50:39','2026-03-18 15:51:06');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penalties`
--

DROP TABLE IF EXISTS `penalties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penalties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `settlement_id` bigint unsigned NOT NULL,
  `rule_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `points` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penalties_settlement_id_foreign` (`settlement_id`),
  KEY `penalties_rule_id_foreign` (`rule_id`),
  KEY `penalties_created_by_foreign` (`created_by`),
  KEY `penalties_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `penalties_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `penalties_rule_id_foreign` FOREIGN KEY (`rule_id`) REFERENCES `penalty_rules` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `penalties_settlement_id_foreign` FOREIGN KEY (`settlement_id`) REFERENCES `settlements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penalties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penalties`
--

LOCK TABLES `penalties` WRITE;
/*!40000 ALTER TABLE `penalties` DISABLE KEYS */;
/*!40000 ALTER TABLE `penalties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penalty_evidences`
--

DROP TABLE IF EXISTS `penalty_evidences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penalty_evidences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penalty_id` bigint unsigned NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penalty_evidences_penalty_id_foreign` (`penalty_id`),
  CONSTRAINT `penalty_evidences_penalty_id_foreign` FOREIGN KEY (`penalty_id`) REFERENCES `penalties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penalty_evidences`
--

LOCK TABLES `penalty_evidences` WRITE;
/*!40000 ALTER TABLE `penalty_evidences` DISABLE KEYS */;
/*!40000 ALTER TABLE `penalty_evidences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penalty_redemptions`
--

DROP TABLE IF EXISTS `penalty_redemptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penalty_redemptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penalty_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penalty_redemptions_user_id_foreign` (`user_id`),
  KEY `penalty_redemptions_reviewed_by_foreign` (`reviewed_by`),
  KEY `penalty_redemptions_penalty_id_status_index` (`penalty_id`,`status`),
  CONSTRAINT `penalty_redemptions_penalty_id_foreign` FOREIGN KEY (`penalty_id`) REFERENCES `penalties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penalty_redemptions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `penalty_redemptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penalty_redemptions`
--

LOCK TABLES `penalty_redemptions` WRITE;
/*!40000 ALTER TABLE `penalty_redemptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `penalty_redemptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penalty_rules`
--

DROP TABLE IF EXISTS `penalty_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penalty_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_points` int NOT NULL,
  `redeemable` tinyint(1) NOT NULL DEFAULT '1',
  `creates_financial_charge` tinyint(1) NOT NULL DEFAULT '0',
  `financial_amount` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penalty_rules`
--

LOCK TABLES `penalty_rules` WRITE;
/*!40000 ALTER TABLE `penalty_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `penalty_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'Modules\\User\\Models\\User',1,'auth_token','7a7240c6bc49af01c0caf34e81fae471095d4339529b5c58b07fe72654465e8d','[\"*\"]',NULL,NULL,'2026-02-21 17:09:27','2026-02-21 17:09:27'),(2,'Modules\\User\\Models\\User',2,'auth_token','30e00c53c777d2a649b510abc91bf3b76fafd0b45fdce32419c292f233bdfbdc','[\"*\"]',NULL,NULL,'2026-02-21 17:27:40','2026-02-21 17:27:40'),(3,'Modules\\User\\Models\\User',3,'auth_token','f707162489b8002683c8e1e07f4e0e105632cc46c738b2c849a0afda23649c6b','[\"*\"]',NULL,NULL,'2026-02-21 17:37:15','2026-02-21 17:37:15'),(4,'Modules\\User\\Models\\User',3,'auth_token','3236209a73a46d618017c5dddf7e56504425fd932e586431d3e79e1e82a63ef3','[\"*\"]',NULL,NULL,'2026-02-21 17:41:09','2026-02-21 17:41:09'),(5,'Modules\\User\\Models\\User',3,'auth_token','fd8367ae7ebe493719f910b40fa4b2e7e2813db1b0327745820b63dff2ebac33','[\"*\"]',NULL,NULL,'2026-02-21 18:53:36','2026-02-21 18:53:36'),(6,'Modules\\User\\Models\\User',3,'auth_token','b0355e9de11668c3ebd0d8f7df26bc524e8d462d47281b78ad552f5d9e9c32c4','[\"*\"]','2026-02-23 17:10:10',NULL,'2026-02-21 19:36:22','2026-02-23 17:10:10'),(7,'Modules\\User\\Models\\User',2,'auth_token','3a9e3410ac1f3301f29474fc0b3663853603e42ace2084cc86113cef5229ab31','[\"*\"]',NULL,NULL,'2026-02-22 11:15:12','2026-02-22 11:15:12'),(8,'Modules\\User\\Models\\User',2,'auth_token','face365370610ca2ef97bef60e19e483f0fe585572f875a3220caa7333e58f9d','[\"*\"]','2026-02-22 12:50:53',NULL,'2026-02-22 11:34:19','2026-02-22 12:50:53'),(9,'Modules\\User\\Models\\User',4,'auth_token','5c99aa7aa84e3b619a1bf4ff84ce36426b20f00388694e0bc05eb850b1e05e1c','[\"*\"]',NULL,NULL,'2026-02-22 12:31:26','2026-02-22 12:31:26'),(10,'Modules\\User\\Models\\User',4,'auth_token','f2446ab09a2d576b2c81ad236098fcd43a6f1322fc856df15ea3935a81065e09','[\"*\"]','2026-02-22 12:34:32',NULL,'2026-02-22 12:33:58','2026-02-22 12:34:32'),(11,'Modules\\User\\Models\\User',4,'auth_token','e045a5cf5781a40d78e99f2ed4acf7128f4d0bf7e040dd7c18192ec048737c72','[\"*\"]','2026-02-22 12:52:14',NULL,'2026-02-22 12:51:31','2026-02-22 12:52:14'),(12,'Modules\\User\\Models\\User',2,'auth_token','11c7f99f6976ce1232c495f3c9623482ca568a1715cd40261ecebd210f32666b','[\"*\"]','2026-02-22 12:54:54',NULL,'2026-02-22 12:52:32','2026-02-22 12:54:54'),(13,'Modules\\User\\Models\\User',4,'auth_token','f73cb569a128db9894bd0afa7851d44354655f4594bb86826c7a2aad350c0c3b','[\"*\"]','2026-02-26 06:31:40',NULL,'2026-02-22 12:55:15','2026-02-26 06:31:40'),(14,'Modules\\User\\Models\\User',4,'auth_token','3b9a4497d006f0f2f64bc4a01467421a1bdec724f9d1e63700380f558874ec87','[\"*\"]','2026-02-25 16:16:22',NULL,'2026-02-22 12:58:00','2026-02-25 16:16:22'),(15,'Modules\\User\\Models\\User',3,'auth_token','597efc1e5f7d30c0427ce2f78bdceab662bae1e1d1d449a1f97eea5ef70b72b0','[\"*\"]',NULL,NULL,'2026-02-23 15:41:31','2026-02-23 15:41:31'),(16,'Modules\\User\\Models\\User',3,'auth_token','903e36d2ad42441e72cdeb5c14443fb2bfcccfd39990965be90c6b698092fd8a','[\"*\"]',NULL,NULL,'2026-02-23 15:58:27','2026-02-23 15:58:27'),(17,'Modules\\User\\Models\\User',3,'auth_token','c848494fda03c5db43b62be729dbc180d8ed3ca7ef94c7a0f1db7f2f57b00050','[\"*\"]',NULL,NULL,'2026-02-23 16:00:57','2026-02-23 16:00:57'),(18,'Modules\\User\\Models\\User',3,'auth_token','477e95ab0078b660844f6e9fd677dd3eb691120f9f6fc214335b645e048917e8','[\"*\"]',NULL,NULL,'2026-02-23 16:03:10','2026-02-23 16:03:10'),(19,'Modules\\User\\Models\\User',3,'auth_token','fddaa9d77539c6f3c3a018f21a0ed2e936789d4fc8c2e22fba6381fd35c3069d','[\"*\"]',NULL,NULL,'2026-02-23 16:11:34','2026-02-23 16:11:34'),(20,'Modules\\User\\Models\\User',3,'auth_token','fc975dc6e155197b2bc04fccd72b8d4f44ccfab54c96786a4df83a8dfbb11704','[\"*\"]',NULL,NULL,'2026-02-23 16:12:28','2026-02-23 16:12:28'),(21,'Modules\\User\\Models\\User',3,'auth_token','e2588997089cc3ebc2befc777f61cc8f13d7d3fa3b4f5430aa3a9acf1ecf97cc','[\"*\"]',NULL,NULL,'2026-02-23 16:18:03','2026-02-23 16:18:03'),(22,'Modules\\User\\Models\\User',3,'auth_token','9af963bc28cfadd156047906158059f04934c91a6d55b12599a832de13dc040e','[\"*\"]',NULL,NULL,'2026-02-23 16:19:24','2026-02-23 16:19:24'),(23,'Modules\\User\\Models\\User',3,'auth_token','fa0fc64bfb034fc67edf1567350e27d61ed194dce0d6ba106e9abf1fb8da53ca','[\"*\"]','2026-02-25 14:09:56',NULL,'2026-02-23 16:24:08','2026-02-25 14:09:56'),(24,'Modules\\User\\Models\\User',3,'auth_token','1689e2d8f8a630a56110d885c3a4ba56d373d9a09fbde9878f313b9eb25a4c5c','[\"*\"]','2026-03-10 15:25:32',NULL,'2026-02-25 14:56:01','2026-03-10 15:25:32'),(25,'Modules\\User\\Models\\User',3,'auth_token','b48c2da8f7de74e4e5da35b1e44b16e2ed8d21f631bb2174aeccdee1c1b67c89','[\"*\"]','2026-02-25 15:43:08',NULL,'2026-02-25 14:56:05','2026-02-25 15:43:08'),(26,'Modules\\User\\Models\\User',3,'auth_token','14e6f446ee77be2b01dd7b287e950d40f120d310fa9186d9aaba08f5e030d6df','[\"*\"]','2026-02-25 15:03:38',NULL,'2026-02-25 15:01:08','2026-02-25 15:03:38'),(27,'Modules\\User\\Models\\User',5,'auth_token','5a896b6df444b7176c5af77e86c01933d76839aef2062054fc24b5b4cea91e14','[\"*\"]',NULL,NULL,'2026-02-25 15:51:57','2026-02-25 15:51:57'),(28,'Modules\\User\\Models\\User',5,'auth_token','1a405ebb089c450909ac7d579f2c4a52f5283854607052c7e7d1d7bb2368fb7b','[\"*\"]',NULL,NULL,'2026-02-25 15:52:25','2026-02-25 15:52:25'),(29,'Modules\\User\\Models\\User',5,'auth_token','778e0cbccc63ac7fa7a424a42fafbc0f689d5f0c9ad68fbbac7390fbcdf5fa24','[\"*\"]','2026-02-25 15:54:27',NULL,'2026-02-25 15:52:54','2026-02-25 15:54:27'),(30,'Modules\\User\\Models\\User',6,'auth_token','d1ee0d5cfdf8094d9803c98bff858de2cc17642b76c4335388fe180b0f7d92fc','[\"*\"]',NULL,NULL,'2026-02-25 15:57:00','2026-02-25 15:57:00'),(32,'Modules\\User\\Models\\User',7,'auth_token','c8f003165009fcf23c8ad1e885a394b1521013a5f7ecc972b65cdb93ec17ceee','[\"*\"]','2026-03-17 18:06:43',NULL,'2026-02-25 16:04:27','2026-03-17 18:06:43'),(33,'Modules\\User\\Models\\User',4,'auth_token','227ef6ddb96b2914f9bcb304987568eeb498efc07904f737d18e69a2a8be9fd0','[\"*\"]','2026-02-25 16:31:04',NULL,'2026-02-25 16:19:17','2026-02-25 16:31:04'),(34,'Modules\\User\\Models\\User',2,'auth_token','d30961a3d2e3e93497387622c4244ab9ee09b4a121fc2e841f937f96e23ba72f','[\"*\"]','2026-02-25 16:33:10',NULL,'2026-02-25 16:32:39','2026-02-25 16:33:10'),(35,'Modules\\User\\Models\\User',7,'auth_token','262f537c17679400bae3afde8ca38720a3e76f2906a94bbf8d76a73048daf031','[\"*\"]','2026-02-25 16:36:20',NULL,'2026-02-25 16:35:26','2026-02-25 16:36:20'),(36,'Modules\\User\\Models\\User',4,'auth_token','2090cddb5ea4090883426a88234eef063bb586425d6118f45143670b316477ef','[\"*\"]','2026-02-25 17:36:06',NULL,'2026-02-25 17:21:03','2026-02-25 17:36:06'),(37,'Modules\\User\\Models\\User',7,'auth_token','2b91d1be114f07f9aaea40cfb7cc21e9a6fa4426047daa23310acf4800ed03cc','[\"*\"]','2026-02-25 17:45:10',NULL,'2026-02-25 17:39:45','2026-02-25 17:45:10'),(38,'Modules\\User\\Models\\User',4,'auth_token','98fd3130e728cca0f345741ab3066c7e0e11f4ff4bfaa933d8cb8d52be42df2f','[\"*\"]','2026-02-25 18:37:23',NULL,'2026-02-25 17:45:30','2026-02-25 18:37:23'),(39,'Modules\\User\\Models\\User',7,'auth_token','0119f9513d19b06baef0b48969e83955d24d6b28a47014a20e367b1e10ab5374','[\"*\"]','2026-02-25 18:48:50',NULL,'2026-02-25 18:37:42','2026-02-25 18:48:50'),(40,'Modules\\User\\Models\\User',4,'auth_token','6dd7ae9b8899a8aa7ced004957aac8ed98e3617e3f66a2cec311836754b0aee0','[\"*\"]','2026-02-25 19:06:00',NULL,'2026-02-25 18:49:12','2026-02-25 19:06:00'),(41,'Modules\\User\\Models\\User',7,'auth_token','07650750a06ccc5b1b8b0c73998a2fcc36693fed9ad75c414a205adc73f84266','[\"*\"]','2026-02-25 19:08:02',NULL,'2026-02-25 19:06:13','2026-02-25 19:08:02'),(42,'Modules\\User\\Models\\User',3,'auth_token','e027a498e84c60a7410345ea0f3bd4b8bd9d528db0fbc35a946243cda0f04a0e','[\"*\"]','2026-02-26 04:58:54',NULL,'2026-02-26 04:52:14','2026-02-26 04:58:54'),(43,'Modules\\User\\Models\\User',8,'auth_token','af3809468337ddee619f27b2a5525b18e4481e71115d56f01caf306deb72fa3e','[\"*\"]','2026-02-26 05:32:30',NULL,'2026-02-26 05:29:47','2026-02-26 05:32:30'),(44,'Modules\\User\\Models\\User',7,'auth_token','c4a8b8932e3046025588fb8c583bb833a5b88a3cbbbb59f448933e8a6e3ace3f','[\"*\"]','2026-02-26 05:33:37',NULL,'2026-02-26 05:32:43','2026-02-26 05:33:37'),(45,'Modules\\User\\Models\\User',8,'auth_token','fb14b674942807e7fbe071ff98490a0e6cc8c032c9530ac1a3203c1ab9f0b09a','[\"*\"]','2026-02-26 05:57:56',NULL,'2026-02-26 05:33:47','2026-02-26 05:57:56'),(46,'Modules\\User\\Models\\User',7,'auth_token','93ad3faad39cd4191b778c7934a63d05e2424177a78fb07c9078fa51c2a2f1dc','[\"*\"]','2026-02-26 06:32:11',NULL,'2026-02-26 06:31:58','2026-02-26 06:32:11'),(47,'Modules\\User\\Models\\User',4,'auth_token','90c53520429cadd21551700986223d8eb0a047c57e87c099d0c21a810d2516f0','[\"*\"]','2026-02-26 07:27:05',NULL,'2026-02-26 06:32:29','2026-02-26 07:27:05'),(48,'Modules\\User\\Models\\User',3,'auth_token','4de8cfc9c71096373a2e13cb1d9c7f895f12a23c2918d568fef0931c5fed0361','[\"*\"]','2026-03-10 15:27:45',NULL,'2026-03-10 15:26:17','2026-03-10 15:27:45'),(50,'Modules\\User\\Models\\User',3,'auth_token','8e0f3c3bcbde77325c9e215cd3a08b4c8b6acc83523dd76fcd3e0fa9631ac858','[\"*\"]','2026-03-11 13:22:24',NULL,'2026-03-10 15:32:13','2026-03-11 13:22:24'),(51,'Modules\\User\\Models\\User',3,'auth_token','f961e09b29041189bbd916ef8a4d3403d5fdcc98d559b77d77c360a4008a3474','[\"*\"]','2026-03-11 18:23:55',NULL,'2026-03-11 13:29:09','2026-03-11 18:23:55'),(52,'Modules\\User\\Models\\User',3,'auth_token','f87828075efb7cbfde5d944605b8177afd1a296f76e94d5e1515abae278bfe53','[\"*\"]','2026-03-11 17:26:02',NULL,'2026-03-11 17:17:34','2026-03-11 17:26:02'),(53,'Modules\\User\\Models\\User',3,'auth_token','04cf5005bdb7fddc9c80ad65e258449db3e3a1bf636695ce8cc1eb65323894c8','[\"*\"]','2026-03-14 20:00:50',NULL,'2026-03-11 17:26:20','2026-03-14 20:00:50'),(55,'Modules\\User\\Models\\User',9,'auth_token','cbacdacc91508ac528fbbf549200e1c071df2354f2d8508f8ebaaa22f7a06936','[\"*\"]','2026-03-12 17:05:13',NULL,'2026-03-12 15:35:34','2026-03-12 17:05:13'),(56,'Modules\\User\\Models\\User',7,'auth_token','1f9ae2b260b3f2426c30da41b3ca1a86aed95a46fecadd87052084c83a2fdc33','[\"*\"]','2026-03-18 15:44:05',NULL,'2026-03-12 15:36:53','2026-03-18 15:44:05'),(58,'Modules\\User\\Models\\User',3,'auth_token','e4809fe71763662adb50c5b61f1e822d72b44d91ca4ad99f593140f06cceef5c','[\"*\"]','2026-03-18 16:13:17',NULL,'2026-03-13 17:44:07','2026-03-18 16:13:17'),(59,'Modules\\User\\Models\\User',9,'auth_token','aae9c46214393f7cb6140d30c6fb9299024f19225d6bb2248f2ac5b544069ee9','[\"*\"]','2026-03-14 23:14:04',NULL,'2026-03-14 20:22:05','2026-03-14 23:14:04'),(63,'Modules\\User\\Models\\User',1,'auth_token','fc3f0f89d138241398b0f715fdc9206570d8d29c8c0797bea2d507a500553c8b','[\"*\"]','2026-03-16 13:33:39',NULL,'2026-03-16 12:08:06','2026-03-16 13:33:39'),(64,'Modules\\User\\Models\\User',1,'auth_token','182678c8e919b9c929ae40a0fc22d08c3f0389787f8dc7fe81901efc0958eb46','[\"*\"]','2026-03-16 14:04:23',NULL,'2026-03-16 13:37:02','2026-03-16 14:04:23'),(65,'Modules\\User\\Models\\User',10,'auth_token','35fe19c0d4bb4dd2004711549f06242cfd8597f887c3d6ad875af853180b277c','[\"*\"]',NULL,NULL,'2026-03-17 17:32:48','2026-03-17 17:32:48'),(67,'Modules\\User\\Models\\User',11,'auth_token','b43affe7d36c0fd6c865f669e5d09911a24e6e09812fe8b0affea6f2602c1b49','[\"*\"]',NULL,NULL,'2026-03-17 18:05:46','2026-03-17 18:05:46'),(74,'Modules\\User\\Models\\User',9,'auth_token','3076aa18e9407416219f40371d3cf5629886ee3c6421b14cfbe0c238f727c0e3','[\"*\"]','2026-03-18 17:20:34',NULL,'2026-03-18 16:29:35','2026-03-18 17:20:34'),(75,'Modules\\User\\Models\\User',15,'auth_token','af53e0e2d42f8c5868ed635b0f0d9e703018df5d710acdda43530069197a4457','[\"*\"]',NULL,NULL,'2026-03-18 17:22:20','2026-03-18 17:22:20'),(80,'Modules\\User\\Models\\User',15,'auth_token','67151873d295fec0516553dce3fd22a9c431533ecfe27f38c5361048615fe110','[\"*\"]','2026-03-18 18:16:14',NULL,'2026-03-18 17:41:56','2026-03-18 18:16:14');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_change_rooms`
--

DROP TABLE IF EXISTS `request_change_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `request_change_rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_change_rooms_student_id_foreign` (`student_id`),
  KEY `request_change_rooms_room_id_foreign` (`room_id`),
  CONSTRAINT `request_change_rooms_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `request_change_rooms_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `dorm_students` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_change_rooms`
--

LOCK TABLES `request_change_rooms` WRITE;
/*!40000 ALTER TABLE `request_change_rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_change_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_lives`
--

DROP TABLE IF EXISTS `request_lives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `request_lives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `preferred_room_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_lives_user_id_foreign` (`user_id`),
  KEY `request_lives_preferred_room_id_foreign` (`preferred_room_id`),
  CONSTRAINT `request_lives_preferred_room_id_foreign` FOREIGN KEY (`preferred_room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `request_lives_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_lives`
--

LOCK TABLES `request_lives` WRITE;
/*!40000 ALTER TABLE `request_lives` DISABLE KEYS */;
INSERT INTO `request_lives` VALUES (1,3,6,'pending','2026-02-24 17:13:47','2026-03-13 19:50:41'),(2,5,12,'rejected','2026-02-25 15:53:10','2026-02-25 16:36:05'),(3,6,8,'accepted','2026-02-25 15:57:37','2026-02-25 16:05:52'),(4,4,12,'rejected','2026-02-25 16:30:39','2026-02-25 17:23:03'),(5,4,23,'rejected','2026-02-25 17:23:26','2026-02-25 17:35:57'),(6,4,7,'rejected','2026-02-25 17:36:03','2026-02-25 17:40:12'),(7,4,71,'rejected','2026-02-25 17:46:16','2026-02-25 17:56:59'),(8,4,7,'rejected','2026-02-25 17:57:24','2026-02-25 18:36:01'),(9,4,13,'rejected','2026-02-25 18:36:37','2026-02-25 18:51:50'),(10,4,43,'rejected','2026-02-25 18:52:21','2026-02-25 19:06:45'),(11,8,12,'rejected','2026-02-26 05:30:11','2026-02-26 05:33:33'),(12,8,17,'accepted','2026-02-26 05:44:05','2026-02-26 05:45:32'),(13,4,7,'rejected','2026-02-26 06:31:28','2026-02-26 07:26:41'),(14,4,17,'pending','2026-02-26 07:27:01','2026-02-26 07:27:01'),(15,9,1,'accepted','2026-03-12 15:35:48','2026-03-12 15:43:10'),(16,1,4,'accepted','2026-03-16 12:08:25','2026-03-16 12:09:01'),(17,10,48,'accepted','2026-03-17 17:33:32','2026-03-17 17:35:20'),(18,11,48,'accepted','2026-03-17 18:06:14','2026-03-17 18:06:43'),(19,13,25,'accepted','2026-03-18 15:42:40','2026-03-18 15:44:05');
/*!40000 ALTER TABLE `request_lives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_types`
--

DROP TABLE IF EXISTS `room_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `room_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_types`
--

LOCK TABLES `room_types` WRITE;
/*!40000 ALTER TABLE `room_types` DISABLE KEYS */;
INSERT INTO `room_types` VALUES (1,'Business',950000.00,'2026-02-24 06:30:27','2026-03-12 15:31:36'),(2,'Comfort+',800000.00,'2026-02-24 06:30:42','2026-02-24 07:47:39'),(3,'Comfort',700000.00,'2026-02-24 06:31:00','2026-02-24 07:47:39'),(4,'Econom',500000.00,'2026-02-24 06:31:25','2026-02-24 07:47:39');
/*!40000 ALTER TABLE `room_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `floor_id` bigint unsigned NOT NULL,
  `room_type_id` bigint unsigned DEFAULT NULL,
  `room_number` int NOT NULL,
  `capacity` int NOT NULL,
  `live_cap` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rooms_floor_id_foreign` (`floor_id`),
  KEY `rooms_room_type_id_index` (`room_type_id`),
  CONSTRAINT `rooms_floor_id_foreign` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rooms_room_type_id_foreign` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,1,1,101,3,0,1,'2026-02-24 07:30:18','2026-02-24 07:47:39'),(2,1,2,102,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(3,1,3,103,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(4,1,4,104,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(5,1,1,105,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(6,2,1,201,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(7,2,2,202,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(8,2,3,203,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(9,2,4,204,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(10,2,1,205,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(11,3,1,301,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(12,3,2,302,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(13,3,3,303,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(14,3,4,304,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(15,3,1,305,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(16,4,1,401,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(17,4,2,402,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(18,4,3,403,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(19,4,4,404,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(20,4,1,405,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(21,5,1,501,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(22,5,2,502,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(23,5,3,503,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(24,5,4,504,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(25,5,1,505,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(36,9,1,101,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(37,9,2,102,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(38,9,3,103,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(39,9,4,104,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(40,9,1,105,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(41,10,1,201,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(42,10,2,202,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(43,10,3,203,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(44,10,4,204,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(45,10,1,205,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(46,11,1,301,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(47,11,2,302,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(48,11,3,303,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(49,11,4,304,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(50,11,1,305,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(51,12,1,401,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(52,12,2,402,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(53,12,3,403,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(54,12,4,404,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(55,12,1,405,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(56,13,1,501,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(57,13,2,502,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(58,13,3,503,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(59,13,4,504,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(60,13,1,505,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(66,15,1,101,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(67,15,2,102,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(68,15,3,103,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(69,15,4,104,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(70,15,1,105,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(71,16,1,201,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(72,16,2,202,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(73,16,3,203,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(74,16,4,204,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(75,16,1,205,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(76,17,1,301,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(77,17,2,302,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(78,17,3,303,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(79,17,4,304,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(80,17,1,305,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(81,18,1,401,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(82,18,2,402,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(83,18,3,403,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(84,18,4,404,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(85,18,1,405,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(86,19,1,501,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(87,19,2,502,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(88,19,3,503,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(89,19,4,504,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39'),(90,19,1,505,3,0,1,'2026-02-24 07:47:39','2026-02-24 07:47:39');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('0pJB18gdSU9wOnYUV0VT8pyqrn9pQyU4WNQ8zKGY',2,'100.64.0.11','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Safari/605.1.15','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZGNUbFpjN05MN1RKSUVsMXd5Mm44YnAxck12Nlltb3RwbURYekxhdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4iO3M6NToicm91dGUiO3M6MzA6ImZpbGFtZW50LmFkbWluLnBhZ2VzLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImM4YzEyNThjMDQ1NzZmNzYwMTJjODllZTI0MDE5MzIyNmMzODI4NTE2ZjA1MjgyMzBhMWI4ODFlNjkwNjJmZGEiO30=',1771832485),('5c8nzjEpCtFABAdw5sw1j4FE51FFREZmMmSDKVtc',NULL,'100.64.0.10','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNDhtM3FqUUlSUWM3SklkSWJVbjdscURWa01hQldYZDE5b1dCS1FyTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771825867),('8vIOPH38kTCKTijsLtWLihO6pPWMPB2lC2NovZWW',NULL,'100.64.0.5','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT05hbXJMZkYzQVJuSjBwZzFqR2lzYUpuNjNUVTVPUFlaUVJHS3dkRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827147),('BTIVGNRqmNAdgBkXXXQTNhjb2yUsBvXAFt0bmqpX',2,'100.64.0.11','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZ1VXRDRlWjZkRE1tNG5GaVZBa0VjUUp4UmVQWTd4M0IzTjJldVlYOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4iO3M6NToicm91dGUiO3M6MzA6ImZpbGFtZW50LmFkbWluLnBhZ2VzLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImM4YzEyNThjMDQ1NzZmNzYwMTJjODllZTI0MDE5MzIyNmMzODI4NTE2ZjA1MjgyMzBhMWI4ODFlNjkwNjJmZGEiO30=',1771832453),('BWgGzo0KO91bcuCLQmYvwajgzGVS66dC3C453mzZ',NULL,'100.64.0.10','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiREZGV1RQb1hVVzJISlZsZTducmk4TGhCTzEyR21WekFYclZuSFBNUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771825815),('DKHKOc8lJsLlcfBQLHGxHJVD8GplDPo1UJTpANZj',NULL,'100.64.0.3','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibFM4QkVMdFV0cG43OWpGbE1uZjhnWmVEWW1ZQTlUZ3ptaGF5aFZDZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827128),('EJedgKtuQTb8uaeB6PSASlcuhDPo1bFODQezGzZN',NULL,'100.64.0.2','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZzhyM2p5TFB4TW5PcGhYMDFpUk9zS0ZoZDRhZXl3QnhZN1ZxUWNKcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827235),('ERPd7GOWqMILzJKMcGYIk1QKYRAWyF661utiZtUG',NULL,'100.64.0.7','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNW5RRWxyem1SSXdvU1hrWWIyU0hqNTRNNHNob2xkZ3JTNml5YUxJVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771825842),('eX71cVfxdmEAG8mXvCjWgqmDxBDOgLe9ZRHWdaP0',NULL,'100.64.0.7','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Safari/605.1.15','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEg1aW9tcTV6SndEVFVXUzZSRUV1UFdQSGlDVjUyYUtRbWFScm83UyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827790),('i3N2xnjnK2Cq2gcUdT15vxTYvZQjtivdLNepYOvA',NULL,'100.64.0.7','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoicjNLZUtnZlFTbHdhR3Y0QVZ2U2Vkb1Ria0pqSmcwbTJ1TXpoM004VSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1771827266),('iFcWRKq2ClGUhjRqqRRoDF7YzygImTASFEubQtkZ',NULL,'100.64.0.2','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUIyTzNKSGlpNkZ4ZGtMcEZtY3VJZmNTWGxJRzBTYUFmbzdzd0xSaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771825876),('iZDxo26a0cDhjptKYPSC0X8FOMaLbPGn2MIHzLRa',NULL,'100.64.0.7','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2l6N2VMNEhwOE5TajZaR1NsSzI5cnp6Y21hVDR4OHlvYXpMQXVhbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827270),('J3ljY5z7BpAGKiBB7Q5uT7Fo31n9wCbnuydsHDXc',NULL,'100.64.0.6','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRWlVVVJscFFCQjZpMWd3UGh1Z0Vaa2NwMlN0UDF1OXIzSjh0ZFdxcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827134),('jn7HKnRxuuakPmdqgwZCojI5YBVWcjYiY1ig46Ah',NULL,'100.64.0.12','TelegramBot (like TwitterBot)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaGZNeEd1WHZUNTNQcFhjb2IxTERyNTZiN2UwUzZGV3RzcXNuc3JyUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771825805),('nP3Zlh3kW97eJbWyyc0Z44gUukHFUhqQhO77vVPz',NULL,'100.64.0.10','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiWGtXNlVuNlFrYzVUQnZlQ2Fad3F0UExxNzJXQlVtQm0yNUhNd2JOeiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1771827281),('o5hei8rQ8uPiKBweVAAkhGGLrraTzbMD6uLHwvoX',NULL,'100.64.0.3','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaU1yaUE1dTA3WjVXVEVkcUtuT09aak5BblAydjdnc21waFlIdEdVZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827124),('oaznE4v5vJ7xtTgSKq4nVNQfSTczodnSvjv7CM9q',NULL,'100.64.0.3','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Safari/605.1.15','YTozOntzOjY6Il90b2tlbiI7czo0MDoid0xkdjlGWjNmd2ZvOGg0NjdRREU5TzZqQmFnbXNrTWdzYUpNd2x5aCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771833021),('PDewtp18vUPT7xqhGWEKkiVAxCH3xX5WT4sIa3ii',NULL,'100.64.0.2','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVJyS2dXeWc3U1ZDeGhaRVdKRXlUZlIxY2hoTVpkb1A2Qm1BdjFNaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827244),('pHxpsmshQCqtpzQRd7lbbslsD2CON3TrjL1MlqTt',NULL,'100.64.0.4','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVGZYUnVlVVdPZDlmTWRQYjFhcWRlaXB1OVhnbk9iZnZzN3ZyZkpJZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771833958),('QP4ONfpnrRGSfNdBjfpYs9E6I64811L7kjJLybiy',NULL,'100.64.0.9','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUR4MzlVd2YxVTZTN3FFRHNVYzNVTkgwa0ZxSUlNVW9Cc0M4SW1LRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827242),('TXJWPluTudeX945OObOMtcIYATPtLQntyLZ7dkwI',NULL,'100.64.0.4','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiZWw5azdhV1NLT2loQzFRMk9QRXBrTDRqZDllT2IwbHhTNjJ5Ylp5SiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1771827253),('VYSBx64880GGqoNsByQJxoLGttWYt3cWWhkHjhRe',NULL,'100.64.0.2','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNkdDRWJQem5EWU42WGpiRXJPNzROMHJTV2pFbnNPeW11ZjNPNGpGNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771825438),('WgKYtgAxXhIIWnD4l8fW9TwClPpyLCxpeiOiCYYh',2,'100.64.0.6','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoicHFKcnJ1N1VHcXdnRzUyenhnTndHTDFaNHJabEFCejhoa2cwZjFGdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4iO3M6NToicm91dGUiO3M6MzA6ImZpbGFtZW50LmFkbWluLnBhZ2VzLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==',1771834157),('ZgOsIPUqWQ1RF0JDagfISAXHOC17VQYYXKBD6oN4',NULL,'100.64.0.2','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicURpT1ZVUlI0VXZjMGdZUnZQR0t4aFdHV0lWRHM5bGFCaHRuTzk3MSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827256),('zTBHepNedZzYBL4RGBH3pz2Faq41Wy2pqkm0DWhw',NULL,'100.64.0.4','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUldZTVc5b1FMcnZna0thbmRxRHA0aVhKd2RnZlU3cjdlV3FQWVprVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9kbXNiYWNrLXByb2R1Y3Rpb24udXAucmFpbHdheS5hcHAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1771827163);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settlements`
--

DROP TABLE IF EXISTS `settlements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settlements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `start_at` date NOT NULL,
  `end_at` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settlements_room_id_end_at_index` (`room_id`,`end_at`),
  KEY `settlements_user_id_end_at_index` (`user_id`,`end_at`),
  CONSTRAINT `settlements_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `settlements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settlements`
--

LOCK TABLES `settlements` WRITE;
/*!40000 ALTER TABLE `settlements` DISABLE KEYS */;
INSERT INTO `settlements` VALUES (1,6,8,'2026-02-25',NULL,'active','request_live',NULL,'2026-02-25 16:05:52','2026-02-25 16:05:52'),(2,3,6,'2026-02-25',NULL,'active','request_live',NULL,'2026-02-25 16:36:19','2026-02-25 16:36:19'),(3,8,17,'2026-02-26',NULL,'active','request_live',NULL,'2026-02-26 05:45:32','2026-02-26 05:45:32'),(4,4,7,'2026-02-26','2026-02-26','finished','request_live','personal','2026-02-26 06:32:07','2026-02-26 07:24:34'),(5,9,1,'2026-03-12',NULL,'active','request_live',NULL,'2026-03-12 15:43:10','2026-03-12 15:43:10'),(6,1,4,'2026-03-16',NULL,'active','request_live',NULL,'2026-03-16 12:09:01','2026-03-16 12:09:01'),(7,10,48,'2026-03-17',NULL,'active','request_live',NULL,'2026-03-17 17:35:20','2026-03-17 17:35:20'),(8,11,48,'2026-03-17',NULL,'active','request_live',NULL,'2026-03-17 18:06:43','2026-03-17 18:06:43'),(9,13,25,'2026-03-18',NULL,'active','request_live',NULL,'2026-03-18 15:44:05','2026-03-18 15:44:05');
/*!40000 ALTER TABLE `settlements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middlename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uni_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discipline_limit` int NOT NULL DEFAULT '10',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Азамат','Таубаев','Ерболатулы','22B030452','male',10,'azamatjds@example.com',NULL,'$2y$12$zQmLlK2RcZATMff.r5HBvemRy57Sqt2hNxZg95oEXZNDqK/DvHDsC','+77778889900',NULL,'2026-02-21 17:09:27','2026-02-21 17:09:27','student'),(2,'Admin','Adminov','Adminovich','ADM001','male',10,'admin@kbtu.kz',NULL,'$2y$12$3Ak5nCSETEAQGLfh7i.TweWXRujlTcqJuhIvaaQeV/iBINJnN/4bi','+7072477036','N4Gxiaf0xLLPnQ1OEyIo02OaTOxvwk4xnGTF26Z2JVBPHVHPtiwHFXb7QZU4','2026-02-21 17:27:40','2026-02-21 17:27:40','admin'),(3,'Аяжан','Таймас','Тауекелкызы','22B030447','female',10,'a_taimas@example.com',NULL,'$2y$12$40Ia.nbagOkTJfxLmfhweunCnwQzVAZS5lCjIDTFycYsvEPfNglpG','+77778889900',NULL,'2026-02-21 17:37:15','2026-03-10 15:26:30','student'),(4,'Temirlan','Abaizhanov','Samatovich','22B030503','male',10,'temirlan@example.com',NULL,'$2y$12$NYfxpyazCD3PoA65sbfXzunmlvL/H6OvAIF6AdCgEfkTCjs1RnezC','+77054897589',NULL,'2026-02-22 12:31:26','2026-02-25 16:19:02','student'),(5,'Нурасыл','Оразгали','Оразханулы','22B030448','male',10,'n_orazgaly@example.com',NULL,'$2y$12$Ul.VzV66PA0RQ1LQ5vPRO.V3iJwlikLakOa.R4RAWQyudrQoMVaAu','+77778889900',NULL,'2026-02-25 15:51:57','2026-02-25 15:51:57','student'),(6,'Нурхан','Тулепберген','Арыстанбекулы','22B030455','male',10,'n_tulepbergen@example.com',NULL,'$2y$12$RmadnhTD7l4q5vCYgavH..Cx742JqinvIS6jqN0cJVTr/MKYTL/Ni','+77778889900',NULL,'2026-02-25 15:57:00','2026-02-25 15:57:00','student'),(7,'Manager',NULL,NULL,NULL,'female',10,'manager@example.com',NULL,'$2y$12$ef7z4fF22F5ygzUj/gdWFOgnP40CBVmGrvPI/2CU/JIB82xJBR.h2',NULL,NULL,'2026-02-25 16:04:03','2026-03-12 15:36:48','manager'),(8,'Yernur','Shapkat',NULL,'22B030465','male',10,'shapkat@example.com',NULL,'$2y$12$/eMPloj/C3vyB26WGiRmnuOIrkDAhPSsvZ8AniKVQzfzLVtlOGGUe','+77076521836',NULL,'2026-02-26 05:15:20','2026-02-26 05:15:20','student'),(9,'Nurkhan','Tulepbergen','Arystanbekuly','22B030455','male',10,'n_tulepbergen@kbtu.kz',NULL,'$2y$12$.Qy5rZM6i4lq3qS5kjxMV.Dzt7.s.Y4q1no59eNmondWbpJAG2GLq','87072477036',NULL,'2026-03-12 15:34:18','2026-03-12 15:35:22','student'),(10,'Адина','Акылбек','Ерланкызы','22B030456','female',10,'a_akylbek@kbtu.kz',NULL,'$2y$12$LXLmOZrNybGVGtPzCzj/2.R2wzWVlYFWWe6Tzq1LJk2j.4NlXQn1u','+77778889900',NULL,'2026-03-17 17:32:48','2026-03-17 17:32:48','student'),(11,'Адина','Акылбек','Ерланкызы','22B030459','female',10,'student@kbtu.kz',NULL,'$2y$12$tQVn5wPd7H2z3uBhptKJd.0G649xU35yMcouSga3xmOvl03b/eer.','+77778889900',NULL,'2026-03-17 18:05:46','2026-03-17 18:05:46','student'),(13,'Test dorm student',NULL,NULL,NULL,'male',10,'test@kbtu.kz',NULL,'$2y$12$11YU5c6sAORIYjY1s55wzeQ6hoGjjk0ThnJfzo2CeLgCAGjcJaso2',NULL,NULL,'2026-03-18 13:43:35','2026-03-18 16:08:22','student'),(14,'test no dorm student',NULL,NULL,NULL,'female',10,'test2@kbtu.kz',NULL,'$2y$12$l2E41Kt9ln1AjAaRLtHx/Orz4tRh/S4OJTvUOcJYgazgNdgvJh94u',NULL,NULL,'2026-03-18 16:09:01','2026-03-18 16:09:01','student'),(15,'manager','manager','manager','22B030477','female',10,'manager@kbtu.kz',NULL,'$2y$12$ecTz3F2WAI5yXj5Vhpk2Mu3llbweOnW5Wa1Ufhti5sXfyAuBp.VyO','+77778889777',NULL,'2026-03-18 17:22:20','2026-03-18 17:22:20','manager');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-19 15:49:19
