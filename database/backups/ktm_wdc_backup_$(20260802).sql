-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: ktm_wdc
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `auctions`
--

DROP TABLE IF EXISTS `auctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auctions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_request_id` bigint unsigned NOT NULL,
  `scheduled_date` date NOT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auctions_warehouse_request_id_foreign` (`warehouse_request_id`),
  CONSTRAINT `auctions_warehouse_request_id_foreign` FOREIGN KEY (`warehouse_request_id`) REFERENCES `warehouse_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auctions`
--

LOCK TABLES `auctions` WRITE;
/*!40000 ALTER TABLE `auctions` DISABLE KEYS */;
/*!40000 ALTER TABLE `auctions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boxes`
--

DROP TABLE IF EXISTS `boxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boxes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entry_date` date NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipper_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `total_boxes` int NOT NULL,
  `box_number` int NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `stock_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `received_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `invoice_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packing_list_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_documents` json DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `boxes_qr_code_unique` (`qr_code`),
  UNIQUE KEY `boxes_barcode_unique` (`barcode`),
  KEY `boxes_warehouse_id_foreign` (`warehouse_id`),
  KEY `boxes_stock_id_foreign` (`stock_id`),
  KEY `boxes_batch_number_index` (`batch_number`),
  KEY `boxes_client_id_foreign` (`client_id`),
  CONSTRAINT `boxes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boxes_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `boxes_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boxes`
--

LOCK TABLES `boxes` WRITE;
/*!40000 ALTER TABLE `boxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `boxes` ENABLE KEYS */;
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `delivery_stops`
--

DROP TABLE IF EXISTS `delivery_stops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_stops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dispatch_order_id` bigint unsigned NOT NULL,
  `stop_order` int NOT NULL,
  `recipient_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `boxes_count` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `invoice_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_stops_dispatch_order_id_foreign` (`dispatch_order_id`),
  CONSTRAINT `delivery_stops_dispatch_order_id_foreign` FOREIGN KEY (`dispatch_order_id`) REFERENCES `dispatch_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_stops`
--

LOCK TABLES `delivery_stops` WRITE;
/*!40000 ALTER TABLE `delivery_stops` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_stops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dispatch_items`
--

DROP TABLE IF EXISTS `dispatch_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatch_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dispatch_order_id` bigint unsigned NOT NULL,
  `stock_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispatch_items_dispatch_order_id_foreign` (`dispatch_order_id`),
  KEY `dispatch_items_stock_id_foreign` (`stock_id`),
  CONSTRAINT `dispatch_items_dispatch_order_id_foreign` FOREIGN KEY (`dispatch_order_id`) REFERENCES `dispatch_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispatch_items_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispatch_items`
--

LOCK TABLES `dispatch_items` WRITE;
/*!40000 ALTER TABLE `dispatch_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `dispatch_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dispatch_orders`
--

DROP TABLE IF EXISTS `dispatch_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatch_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dispatch_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `driver_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `driver_rate_id` bigint unsigned DEFAULT NULL,
  `warehouse_request_id` bigint unsigned DEFAULT NULL,
  `pickup_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pickup_latitude` decimal(10,8) DEFAULT NULL,
  `pickup_kataho_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_grid_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_longitude` decimal(11,8) DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci,
  `delivery_latitude` decimal(10,8) DEFAULT NULL,
  `delivery_kataho_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_grid_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_longitude` decimal(11,8) DEFAULT NULL,
  `distance_km` decimal(10,2) DEFAULT NULL,
  `base_price` decimal(12,2) DEFAULT NULL,
  `admin_margin` decimal(12,2) DEFAULT NULL,
  `driver_earning` decimal(12,2) DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bill_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `pan_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bill_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packing_list` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_confirmation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_documents` json DEFAULT NULL,
  `total_boxes` int NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `on_the_way_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `client_rating` int DEFAULT NULL,
  `client_feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pickup_contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accepted_by_client_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_distance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_date` date DEFAULT NULL,
  `tracking_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_due_date` date DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dispatch_orders_dispatch_number_unique` (`dispatch_number`),
  UNIQUE KEY `dispatch_orders_tracking_id_unique` (`tracking_id`),
  UNIQUE KEY `dispatch_orders_invoice_no_unique` (`invoice_no`),
  KEY `dispatch_orders_client_id_foreign` (`client_id`),
  KEY `dispatch_orders_driver_id_foreign` (`driver_id`),
  KEY `dispatch_orders_driver_rate_id_foreign` (`driver_rate_id`),
  KEY `dispatch_orders_warehouse_request_id_foreign` (`warehouse_request_id`),
  KEY `dispatch_orders_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `dispatch_orders_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispatch_orders_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispatch_orders_driver_rate_id_foreign` FOREIGN KEY (`driver_rate_id`) REFERENCES `driver_rates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispatch_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispatch_orders_warehouse_request_id_foreign` FOREIGN KEY (`warehouse_request_id`) REFERENCES `warehouse_requests` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispatch_orders`
--

LOCK TABLES `dispatch_orders` WRITE;
/*!40000 ALTER TABLE `dispatch_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `dispatch_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dispatches`
--

DROP TABLE IF EXISTS `dispatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `request_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `client_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_id` bigint unsigned DEFAULT NULL,
  `driver_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_id` bigint unsigned DEFAULT NULL,
  `pickup_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distance_km` decimal(10,2) DEFAULT NULL,
  `item_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `weight_kg` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `price` decimal(10,2) DEFAULT NULL,
  `driver_earning` decimal(10,2) DEFAULT NULL,
  `admin_commission` decimal(10,2) DEFAULT NULL,
  `proposed_price` decimal(10,2) DEFAULT NULL,
  `proposal_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `delivery_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `accepted_by_client_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dispatches_request_number_unique` (`request_number`),
  KEY `dispatches_request_number_index` (`request_number`),
  KEY `dispatches_client_id_index` (`client_id`),
  KEY `dispatches_driver_id_index` (`driver_id`),
  KEY `dispatches_status_index` (`status`),
  CONSTRAINT `dispatches_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispatches_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispatches`
--

LOCK TABLES `dispatches` WRITE;
/*!40000 ALTER TABLE `dispatches` DISABLE KEYS */;
/*!40000 ALTER TABLE `dispatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driver_rates`
--

DROP TABLE IF EXISTS `driver_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `driver_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_id` bigint unsigned NOT NULL,
  `flat_rate_0_5` decimal(10,2) NOT NULL DEFAULT '200.00',
  `flat_rate_6_10` decimal(10,2) NOT NULL DEFAULT '350.00',
  `flat_rate_11_20` decimal(10,2) NOT NULL DEFAULT '600.00',
  `rate_per_km_21_plus` decimal(10,2) NOT NULL DEFAULT '40.00',
  `base_fare` decimal(10,2) NOT NULL DEFAULT '0.00',
  `minimum_fare` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valid_until` timestamp NULL DEFAULT NULL,
  `rate_per_km` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rate_per_hour` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date` date DEFAULT NULL,
  `effective_from` timestamp NULL DEFAULT NULL,
  `effective_until` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `driver_rates_driver_id_foreign` (`driver_id`),
  KEY `driver_rates_user_id_foreign` (`user_id`),
  CONSTRAINT `driver_rates_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `driver_rates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_rates`
--

LOCK TABLES `driver_rates` WRITE;
/*!40000 ALTER TABLE `driver_rates` DISABLE KEYS */;
INSERT INTO `driver_rates` VALUES (1,4,NULL,4,200.00,350.00,600.00,40.00,0.00,0.00,'2027-01-01 10:44:26',0.00,0.00,'2026-07-01',NULL,NULL,1,NULL,'2026-07-01 10:44:26','2026-07-01 10:44:26'),(2,4,'Truck',4,200.00,350.00,600.00,40.00,100.00,200.00,'2027-01-01 11:37:37',40.00,500.00,'2026-07-01','2026-06-30 11:37:37','2027-01-01 11:37:37',1,NULL,'2026-07-01 11:37:37','2026-07-01 11:37:37'),(3,4,NULL,4,200.00,350.00,600.00,40.00,0.00,0.00,'2026-07-10 08:21:27',0.00,0.00,'2026-07-09',NULL,NULL,1,NULL,'2026-07-09 08:21:27','2026-07-09 08:21:27'),(4,5,NULL,5,250.00,400.00,650.00,45.00,0.00,0.00,'2026-07-10 08:21:27',0.00,0.00,'2026-07-09',NULL,NULL,1,NULL,'2026-07-09 08:21:27','2026-07-09 08:21:27'),(5,10,NULL,10,300.00,450.00,700.00,50.00,0.00,0.00,'2026-07-10 08:21:27',0.00,0.00,'2026-07-09',NULL,NULL,1,NULL,'2026-07-09 08:21:27','2026-07-09 08:21:27'),(6,11,NULL,11,200.00,350.00,600.00,40.00,0.00,0.00,'2026-07-10 08:21:27',0.00,0.00,'2026-07-09',NULL,NULL,1,NULL,'2026-07-09 08:21:27','2026-07-09 08:21:27');
/*!40000 ALTER TABLE `driver_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipment` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `equipment_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity_kg` decimal(10,2) DEFAULT NULL,
  `base_charge` decimal(10,2) DEFAULT NULL,
  `is_negotiable` tinyint(1) NOT NULL DEFAULT '1',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `year` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `weight` decimal(10,2) DEFAULT NULL,
  `engine_power` decimal(10,2) DEFAULT NULL,
  `bucket_capacity` decimal(10,2) DEFAULT NULL,
  `max_reach` decimal(10,2) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `weekly_rate` decimal(10,2) DEFAULT NULL,
  `monthly_rate` decimal(10,2) DEFAULT NULL,
  `security_deposit` decimal(10,2) DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `front_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `side_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `working_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `equipment_owner_id` bigint unsigned DEFAULT NULL,
  `availability_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  PRIMARY KEY (`id`),
  KEY `equipment_owner_id_foreign` (`owner_id`),
  KEY `equipment_equipment_owner_id_foreign` (`equipment_owner_id`),
  CONSTRAINT `equipment_equipment_owner_id_foreign` FOREIGN KEY (`equipment_owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `equipment_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipment`
--

LOCK TABLES `equipment` WRITE;
/*!40000 ALTER TABLE `equipment` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipment_jobs`
--

DROP TABLE IF EXISTS `equipment_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipment_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_request_id` bigint unsigned NOT NULL,
  `equipment_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','accepted','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `accepted_by_client_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agreed_rate` decimal(10,2) DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `partner_paid` tinyint(1) NOT NULL DEFAULT '0',
  `owner_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `job_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_location` text COLLATE utf8mb4_unicode_ci,
  `delivery_location` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `proposed_price` decimal(10,2) DEFAULT NULL,
  `accepted_by_owner_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `equipment_owner_id` bigint unsigned DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `equipment_jobs_warehouse_request_id_foreign` (`warehouse_request_id`),
  KEY `equipment_jobs_equipment_id_foreign` (`equipment_id`),
  KEY `equipment_jobs_owner_id_foreign` (`owner_id`),
  KEY `equipment_jobs_client_id_foreign` (`client_id`),
  KEY `equipment_jobs_equipment_owner_id_foreign` (`equipment_owner_id`),
  CONSTRAINT `equipment_jobs_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`),
  CONSTRAINT `equipment_jobs_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`),
  CONSTRAINT `equipment_jobs_equipment_owner_id_foreign` FOREIGN KEY (`equipment_owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `equipment_jobs_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `equipment_jobs_warehouse_request_id_foreign` FOREIGN KEY (`warehouse_request_id`) REFERENCES `warehouse_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipment_jobs`
--

LOCK TABLES `equipment_jobs` WRITE;
/*!40000 ALTER TABLE `equipment_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipment_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipment_requests`
--

DROP TABLE IF EXISTS `equipment_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipment_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `equipment_id` bigint unsigned DEFAULT NULL,
  `owner_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `equipment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipment_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `duration_days` int DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `proposed_price` decimal(10,2) DEFAULT NULL,
  `agreed_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected','assigned','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `equipment_requests_owner_id_foreign` (`owner_id`),
  KEY `equipment_requests_client_id_status_index` (`client_id`,`status`),
  KEY `equipment_requests_equipment_id_status_index` (`equipment_id`,`status`),
  KEY `equipment_requests_status_index` (`status`),
  KEY `equipment_requests_start_date_end_date_index` (`start_date`,`end_date`),
  CONSTRAINT `equipment_requests_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `equipment_requests_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE SET NULL,
  CONSTRAINT `equipment_requests_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipment_requests`
--

LOCK TABLES `equipment_requests` WRITE;
/*!40000 ALTER TABLE `equipment_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipment_requests` ENABLE KEYS */;
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
-- Table structure for table `insurances`
--

DROP TABLE IF EXISTS `insurances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `insurances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_request_id` bigint unsigned NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `premium` decimal(10,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `insurances_warehouse_request_id_foreign` (`warehouse_request_id`),
  CONSTRAINT `insurances_warehouse_request_id_foreign` FOREIGN KEY (`warehouse_request_id`) REFERENCES `warehouse_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `insurances`
--

LOCK TABLES `insurances` WRITE;
/*!40000 ALTER TABLE `insurances` DISABLE KEYS */;
/*!40000 ALTER TABLE `insurances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `warehouse_request_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('pending','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `pan_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_address` text COLLATE utf8mb4_unicode_ci,
  `items` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_warehouse_request_id_foreign` (`warehouse_request_id`),
  CONSTRAINT `invoices_warehouse_request_id_foreign` FOREIGN KEY (`warehouse_request_id`) REFERENCES `warehouse_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
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
  `attempts` smallint unsigned NOT NULL,
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
-- Table structure for table `kataho_locations`
--

DROP TABLE IF EXISTS `kataho_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kataho_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kataho_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grid_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plate_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(10,8) NOT NULL,
  `location_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'warehouse',
  `reference_id` bigint unsigned DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kataho_locations_kataho_code_unique` (`kataho_code`),
  KEY `kataho_locations_location_type_reference_id_index` (`location_type`,`reference_id`),
  KEY `kataho_locations_kataho_code_index` (`kataho_code`),
  KEY `kataho_locations_grid_id_index` (`grid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kataho_locations`
--

LOCK TABLES `kataho_locations` WRITE;
/*!40000 ALTER TABLE `kataho_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `kataho_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `margin_tiers`
--

DROP TABLE IF EXISTS `margin_tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `margin_tiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `min_amount` decimal(10,2) NOT NULL,
  `max_amount` decimal(10,2) DEFAULT NULL,
  `margin_percentage` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `margin_tiers`
--

LOCK TABLES `margin_tiers` WRITE;
/*!40000 ALTER TABLE `margin_tiers` DISABLE KEYS */;
/*!40000 ALTER TABLE `margin_tiers` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_15_000000_add_user_code_to_users_table',1),(5,'2024_01_15_000003_create_stocks_table',1),(6,'2024_01_15_000005_create_pickup_requests_table',1),(7,'2024_01_16_000003_add_status_to_users_table',1),(8,'2026_05_08_071650_create_warehouses_table',1),(9,'2026_05_08_072606_create_warehouse_photos_table',1),(10,'2026_05_10_064459_add_status_to_warehouses_table',1),(11,'2026_05_13_054809_add_is_admin_to_users_table',1),(12,'2026_05_13_071520_add_is_admin_column_to_users_table',1),(13,'2026_05_13_075210_create_warehouse_requests_table',1),(14,'2026_05_13_113000_create_vehicles_table',1),(15,'2026_05_13_113942_create_dispatch_orders_table',1),(16,'2026_05_13_114211_create_order_items_table',1),(17,'2026_05_13_121246_add_is_admin_to_users_table',1),(18,'2026_05_14_083119_add_contact_fields_to_dispatch_orders_table',1),(19,'2026_05_14_102601_add_driver_acceptance_fields_to_dispatch_orders_table',1),(20,'2026_05_14_102642_add_is_driver_to_users_table',1),(21,'2026_05_14_110710_add_phone_to_users_table',1),(22,'2026_05_15_061831_update_dispatch_orders_status_enum',1),(23,'2026_05_15_065006_add_location_fields_to_warehouses_table',1),(24,'2026_05_18_052443_add_space_management_fields_to_warehouses',1),(25,'2026_05_18_053558_add_preferred_warehouse_to_warehouse_requests',1),(26,'2026_05_18_055459_add_is_equipment_owner_to_users',1),(27,'2026_05_18_055529_create_equipment_table',1),(28,'2026_05_18_055645_create_equipment_jobs_table',1),(29,'2026_05_18_055707_add_equipment_fields_to_warehouse_requests',1),(30,'2026_05_18_064919_add_camera_stream_url_to_warehouses_table',1),(31,'2026_05_18_081202_add_is_equipment_owner_to_users',1),(32,'2026_05_18_095748_add_capacity_to_vehicles',1),(33,'2026_05_18_095824_add_total_quantity_to_dispatch_orders',1),(34,'2026_05_18_115427_add_type_to_warehouses_table',1),(35,'2026_05_18_115532_add_area_sq_m_to_warehouses',1),(36,'2026_05_19_080654_change_vehicle_type_to_string',1),(37,'2026_05_24_071833_add_missing_role_flags_to_users',1),(38,'2026_05_24_075040_add_role_flags_to_users',1),(39,'2026_05_25_064509_create_warehouse_documents_table',1),(40,'2026_05_25_064945_create_warehouse_request_warehouse_table',1),(41,'2026_05_25_071656_add_assigned_warehouse_id_to_warehouse_requests',1),(42,'2026_05_25_075049_add_qr_code_to_stocks',1),(43,'2026_05_25_152359_add_pricing_to_warehouses',1),(44,'2026_05_25_152429_create_price_negotiations_table',1),(45,'2026_05_25_152500_create_invoices_table',1),(46,'2026_05_25_152534_add_contract_notification_to_warehouse_requests',1),(47,'2026_05_25_153239_add_agreed_price_to_warehouse_requests',1),(48,'2026_05_25_153525_add_pricing_fields_to_warehouses',1),(49,'2026_05_25_153607_add_pricing_to_warehouse_requests',1),(50,'2026_05_25_153837_create_auctions_table',1),(51,'2026_05_25_161934_create_partner_job_offers_table',1),(52,'2026_05_26_072230_add_description_to_invoices_table',1),(53,'2026_05_26_081916_add_partner_paid_to_job_tables',1),(54,'2026_05_26_094657_create_margin_tiers_table',1),(55,'2026_05_26_095812_create_partner_proposals_table',1),(56,'2026_05_26_101658_add_accepted_by_client_status_to_dispatch_orders',1),(57,'2026_05_26_102146_add_accepted_by_client_status_to_equipment_jobs',1),(58,'2026_05_26_111241_add_contact_fields_to_warehouses',1),(59,'2026_05_27_081154_add_usable_capacity_to_warehouses',1),(60,'2026_05_28_062230_create_insurances_table',1),(61,'2026_05_28_075850_add_counter_offer_to_partner_proposals',1),(62,'2026_05_28_080509_add_driver_assignment_to_jobs',1),(63,'2026_05_29_061724_add_index_to_phone_on_users',1),(64,'2026_05_29_070102_add_profile_photo_to_users',1),(65,'2026_06_01_084901_create_personal_access_tokens_table',1),(66,'2026_06_01_090218_add_role_and_user_type_to_users_table',1),(67,'2026_06_02_000006_add_preferred_location_to_users_table',1),(68,'2026_06_02_000008_add_comprehensive_warehouse_fields',1),(69,'2026_06_02_000009_add_missing_columns_to_warehouse_requests',1),(70,'2026_06_02_000010_add_additional_area_fields_to_warehouses',1),(71,'2026_06_02_000011_add_missing_location_columns_to_warehouses',1),(72,'2026_06_02_000012_add_description_to_warehouses_table',1),(73,'2026_06_02_000013_add_all_missing_warehouse_columns',1),(74,'2026_06_02_063002_add_tracking_fields_to_dispatch_orders',1),(75,'2026_06_02_095135_add_document_fields_to_vehicles_table',1),(76,'2026_06_02_100450_add_amount_to_dispatch_orders_table',1),(77,'2026_06_02_102927_add_location_fields_to_warehouses_table',1),(78,'2026_06_03_000001_create_driver_rates_table',1),(79,'2026_06_03_000001_make_warehouse_dimensions_nullable',1),(80,'2026_06_03_000002_add_camera_stream_url_to_warehouses',1),(81,'2026_06_03_000003_add_missing_warehouse_columns_final',1),(82,'2026_06_03_000005_add_missing_columns_to_equipment_table',1),(83,'2026_06_03_000006_add_name_column_to_equipment_table',1),(84,'2026_06_03_000010_add_pricing_fields_to_dispatch_orders',1),(85,'2026_06_03_000011_add_registration_number_to_vehicles_table',1),(86,'2026_06_03_000012_add_driver_id_to_vehicles_table',1),(87,'2026_06_03_000013_add_plate_number_to_vehicles_table',1),(88,'2026_06_03_000014_add_all_missing_columns_to_vehicles',1),(89,'2026_06_03_000015_add_sku_to_stocks_table',1),(90,'2026_06_03_000016_create_boxes_table',1),(91,'2026_06_03_000017_add_document_fields_to_boxes_table',1),(92,'2026_06_04_000003_remove_unique_from_batch_number_in_boxes',1),(93,'2026_06_04_000004_add_client_id_to_boxes_table',1),(94,'2026_06_04_000005_add_missing_columns_to_boxes',1),(95,'2026_06_04_000010_create_delivery_stops_table',1),(96,'2026_06_04_075245_remove_unique_from_batch_number_in_boxes',1),(97,'2026_06_04_085046_add_document_columns_to_dispatch_orders',1),(98,'2026_06_04_091333_create_dispatch_items_table',1),(99,'2026_06_04_095445_add_tracking_fields_to_dispatch_orders',1),(100,'2026_06_04_100000_create_dispatch_system_tables',1),(101,'2026_06_04_130000_add_missing_columns_to_dispatch_orders_table',1),(102,'2026_06_04_134748_create_warehouse_tenants_table',1),(103,'2026_06_04_140000_add_invoice_document_to_delivery_stops',1),(104,'2026_06_04_154300_add_margin_fields_to_pickup_requests',1),(105,'2026_06_04_180000_create_user_contacts_table',1),(106,'2026_06_04_180001_create_notifications_table',1),(107,'2026_06_05_000001_add_missing_columns_to_dispatch_orders',1),(108,'2026_06_05_000002_create_delivery_stops_table_if_not_exists',1),(109,'2026_06_05_000002_create_dispatches_table',1),(110,'2026_06_05_000003_add_missing_columns_to_pickup_requests',1),(111,'2026_06_05_000010_create_vehicles_table',1),(112,'2026_06_05_000011_add_missing_columns_to_vehicles_table',1),(113,'2026_06_05_130339_add_warehouse_request_id_to_stocks_table',1),(114,'2026_06_05_999999_ensure_all_tables_exist',1),(115,'2026_06_12_000001_add_missing_invoice_fields',1),(116,'2026_06_12_085012_add_owner_id_to_equipment_jobs',1),(117,'2026_06_12_085204_fix_equipment_jobs_missing_columns',1),(118,'2026_06_12_085511_fix_all_equipment_jobs_columns',1),(119,'2026_06_12_090100_fix_all_equipment_related_columns',1),(120,'2026_06_12_091734_add_missing_financial_columns_to_all_tables',1),(121,'2026_06_12_101553_add_user_id_to_stocks_table',1),(122,'2026_06_12_102707_add_user_columns_to_invoices_table',1),(123,'2026_06_12_120728_add_missing_columns_to_users_table',1),(124,'2026_06_12_125652_fix_vehicles_table_add_user_id',1),(125,'2026_06_12_131053_fix_driver_rates_columns',1),(126,'2026_06_12_131659_add_user_id_to_equipment_table',1),(127,'2026_06_12_999998_fix_all_column_modifications',1),(128,'2026_06_12_999999_add_professional_fields',1),(129,'2026_06_26_160000_add_base_price_to_dispatch_orders',1),(130,'2026_07_01_145720_add_is_read_to_notifications_table',1),(131,'2026_07_01_145816_create_notifications_table',2),(132,'2026_07_01_150306_update_notifications_table_add_missing_columns',2),(133,'2026_07_01_161847_update_driver_rates_table_add_missing_columns',3),(134,'2026_07_01_162338_update_driver_rates_table_for_compatibility',4),(135,'2026_07_01_163240_create_proposals_table',5),(136,'2026_07_01_170325_add_user_id_to_warehouses_table',6),(137,'2026_07_01_171323_add_missing_columns_to_warehouses_table',7),(138,'2026_07_01_171727_create_equipment_requests_table',8),(139,'2026_07_01_172101_add_effective_dates_to_driver_rates_table',9),(140,'2024_07_01_000000_add_kataho_fields_to_warehouses_table',10),(141,'2024_07_01_000001_add_kataho_to_orders_table',11),(142,'2026_07_06_143528_add_kataho_fields_safely',11),(143,'2026_07_06_144632_add_missing_columns_to_pickup_requests',12),(144,'2026_07_07_141436_add_warehouse_id_to_dispatch_orders',13);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_logs`
--

DROP TABLE IF EXISTS `notification_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_logs_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_logs`
--

LOCK TABLES `notification_logs` WRITE;
/*!40000 ALTER TABLE `notification_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  KEY `notifications_created_at_index` (`created_at`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,'Welcome to KTM-WDC','Welcome to the Warehouse & Distribution Connect platform! We\'re excited to have you onboard.','success',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(2,1,'Complete Your Profile','Please complete your profile to get the best experience on KTM-WDC.','info',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(3,2,'Welcome to KTM-WDC','Welcome to the Warehouse & Distribution Connect platform! We\'re excited to have you onboard.','success',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(4,2,'Complete Your Profile','Please complete your profile to get the best experience on KTM-WDC.','info',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(5,3,'Welcome to KTM-WDC','Welcome to the Warehouse & Distribution Connect platform! We\'re excited to have you onboard.','success',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(6,3,'Complete Your Profile','Please complete your profile to get the best experience on KTM-WDC.','info',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(7,4,'Welcome to KTM-WDC','Welcome to the Warehouse & Distribution Connect platform! We\'re excited to have you onboard.','success',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(8,4,'Complete Your Profile','Please complete your profile to get the best experience on KTM-WDC.','info',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(9,5,'Welcome to KTM-WDC','Welcome to the Warehouse & Distribution Connect platform! We\'re excited to have you onboard.','success',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(10,5,'Complete Your Profile','Please complete your profile to get the best experience on KTM-WDC.','info',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(11,6,'Welcome to KTM-WDC','Welcome to the Warehouse & Distribution Connect platform! We\'re excited to have you onboard.','success',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(12,6,'Complete Your Profile','Please complete your profile to get the best experience on KTM-WDC.','info',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(13,7,'Welcome to KTM-WDC','Welcome to the Warehouse & Distribution Connect platform! We\'re excited to have you onboard.','success',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31'),(14,7,'Complete Your Profile','Please complete your profile to get the best experience on KTM-WDC.','info',0,NULL,'2026-07-01 10:28:31','2026-07-01 10:28:31');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partner_job_offers`
--

DROP TABLE IF EXISTS `partner_job_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partner_job_offers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_id` bigint unsigned NOT NULL,
  `partner_id` bigint unsigned NOT NULL,
  `proposed_price` decimal(10,2) NOT NULL,
  `admin_final_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_job_offers_job_type_job_id_index` (`job_type`,`job_id`),
  KEY `partner_job_offers_partner_id_foreign` (`partner_id`),
  CONSTRAINT `partner_job_offers_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partner_job_offers`
--

LOCK TABLES `partner_job_offers` WRITE;
/*!40000 ALTER TABLE `partner_job_offers` DISABLE KEYS */;
/*!40000 ALTER TABLE `partner_job_offers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partner_proposals`
--

DROP TABLE IF EXISTS `partner_proposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partner_proposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_id` bigint unsigned NOT NULL,
  `partner_id` bigint unsigned NOT NULL,
  `proposed_price` decimal(10,2) NOT NULL,
  `counter_offer` decimal(10,2) DEFAULT NULL,
  `admin_margin` decimal(10,2) NOT NULL,
  `status` enum('pending','accepted','rejected','negotiating') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `negotiation_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_proposals_job_type_job_id_index` (`job_type`,`job_id`),
  KEY `partner_proposals_partner_id_foreign` (`partner_id`),
  CONSTRAINT `partner_proposals_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partner_proposals`
--

LOCK TABLES `partner_proposals` WRITE;
/*!40000 ALTER TABLE `partner_proposals` DISABLE KEYS */;
/*!40000 ALTER TABLE `partner_proposals` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pickup_requests`
--

DROP TABLE IF EXISTS `pickup_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pickup_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `request_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `client_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_id` bigint unsigned DEFAULT NULL,
  `pickup_address` text COLLATE utf8mb4_unicode_ci,
  `destination_address` text COLLATE utf8mb4_unicode_ci,
  `pickup_latitude` decimal(10,8) DEFAULT NULL,
  `pickup_longitude` decimal(10,8) DEFAULT NULL,
  `driver_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kataho_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grid_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_kataho_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_grid_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` int NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pieces',
  `price` decimal(10,2) DEFAULT NULL,
  `agreed_price` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','assigned','picked_up','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `partner_paid` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_driver_id` bigint unsigned DEFAULT NULL,
  `admin_margin` decimal(10,2) DEFAULT NULL,
  `driver_earning` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `accepted_by_client_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `destination_latitude` decimal(10,8) DEFAULT NULL,
  `destination_longitude` decimal(10,8) DEFAULT NULL,
  `items_description` text COLLATE utf8mb4_unicode_ci,
  `weight` decimal(10,2) DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `scheduled_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pickup_requests_request_number_unique` (`request_number`),
  UNIQUE KEY `pickup_requests_tracking_id_unique` (`tracking_id`),
  UNIQUE KEY `pickup_requests_invoice_no_unique` (`invoice_no`),
  KEY `pickup_requests_client_id_foreign` (`client_id`),
  KEY `pickup_requests_driver_id_foreign` (`driver_id`),
  KEY `pickup_requests_request_number_index` (`request_number`),
  KEY `pickup_requests_client_code_index` (`client_code`),
  KEY `pickup_requests_driver_code_index` (`driver_code`),
  KEY `pickup_requests_status_index` (`status`),
  KEY `pickup_requests_assigned_driver_id_foreign` (`assigned_driver_id`),
  CONSTRAINT `pickup_requests_assigned_driver_id_foreign` FOREIGN KEY (`assigned_driver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `pickup_requests_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pickup_requests_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pickup_requests`
--

LOCK TABLES `pickup_requests` WRITE;
/*!40000 ALTER TABLE `pickup_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `pickup_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pickup_stops`
--

DROP TABLE IF EXISTS `pickup_stops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pickup_stops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pickup_request_id` bigint unsigned NOT NULL,
  `stop_order` int NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `boxes_count` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kataho_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kataho_grid_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pickup_stops_pickup_request_id_foreign` (`pickup_request_id`),
  CONSTRAINT `pickup_stops_pickup_request_id_foreign` FOREIGN KEY (`pickup_request_id`) REFERENCES `pickup_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pickup_stops`
--

LOCK TABLES `pickup_stops` WRITE;
/*!40000 ALTER TABLE `pickup_stops` DISABLE KEYS */;
/*!40000 ALTER TABLE `pickup_stops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_negotiations`
--

DROP TABLE IF EXISTS `price_negotiations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_negotiations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `negotiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `negotiable_id` bigint unsigned NOT NULL,
  `partner_id` bigint unsigned NOT NULL,
  `suggested_price` decimal(10,2) NOT NULL,
  `admin_margin_percent` decimal(5,2) DEFAULT NULL,
  `admin_margin_fixed` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `price_negotiations_negotiable_type_negotiable_id_index` (`negotiable_type`,`negotiable_id`),
  KEY `price_negotiations_partner_id_foreign` (`partner_id`),
  CONSTRAINT `price_negotiations_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_negotiations`
--

LOCK TABLES `price_negotiations` WRITE;
/*!40000 ALTER TABLE `price_negotiations` DISABLE KEYS */;
/*!40000 ALTER TABLE `price_negotiations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proposals`
--

DROP TABLE IF EXISTS `proposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_request_id` bigint unsigned DEFAULT NULL,
  `proposed_price` decimal(10,2) NOT NULL,
  `negotiated_price` decimal(10,2) DEFAULT NULL,
  `negotiation_message` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `valid_until` timestamp NULL DEFAULT NULL,
  `status` enum('pending','accepted','rejected','negotiating','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `accepted_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proposals_warehouse_request_id_foreign` (`warehouse_request_id`),
  KEY `proposals_client_id_status_index` (`client_id`,`status`),
  KEY `proposals_warehouse_id_status_index` (`warehouse_id`,`status`),
  KEY `proposals_status_index` (`status`),
  KEY `proposals_valid_until_index` (`valid_until`),
  CONSTRAINT `proposals_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proposals_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proposals_warehouse_request_id_foreign` FOREIGN KEY (`warehouse_request_id`) REFERENCES `warehouse_requests` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proposals`
--

LOCK TABLES `proposals` WRITE;
/*!40000 ALTER TABLE `proposals` DISABLE KEYS */;
/*!40000 ALTER TABLE `proposals` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `warehouse_request_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pieces',
  `number_of_boxes` int NOT NULL,
  `quantity_per_box` int NOT NULL,
  `total_quantity` int NOT NULL,
  `batch_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `warehouse_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grn_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quality_certificate_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_documents_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturing_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `received_date` date NOT NULL,
  `qr_code_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code_data` text COLLATE utf8mb4_unicode_ci,
  `status` enum('in_stock','partial','dispatched','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_stock',
  `remaining_quantity` int NOT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stocks_batch_id_unique` (`batch_id`),
  UNIQUE KEY `stocks_sku_unique` (`sku`),
  KEY `stocks_batch_id_index` (`batch_id`),
  KEY `stocks_sku_index` (`sku`),
  KEY `stocks_client_code_index` (`client_code`),
  KEY `stocks_warehouse_id_index` (`warehouse_id`),
  KEY `stocks_invoice_number_index` (`invoice_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receipt_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transactionable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transactionable_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_transaction_id_unique` (`transaction_id`),
  UNIQUE KEY `transactions_receipt_no_unique` (`receipt_no`),
  KEY `transactions_transactionable_type_transactionable_id_index` (`transactionable_type`,`transactionable_id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_contacts`
--

DROP TABLE IF EXISTS `user_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `receive_emails` tinyint(1) NOT NULL DEFAULT '1',
  `receive_sms` tinyint(1) NOT NULL DEFAULT '0',
  `receive_whatsapp` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_contacts_user_id_foreign` (`user_id`),
  CONSTRAINT `user_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_contacts`
--

LOCK TABLES `user_contacts` WRITE;
/*!40000 ALTER TABLE `user_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `avg_rating` decimal(3,1) NOT NULL DEFAULT '0.0',
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kataho_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kataho_grid_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kataho_address` text COLLATE utf8mb4_unicode_ci,
  `kataho_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_driver` tinyint(1) NOT NULL DEFAULT '0',
  `is_equipment_owner` tinyint(1) NOT NULL DEFAULT '0',
  `is_client` tinyint(1) NOT NULL DEFAULT '0',
  `is_property_owner` tinyint(1) NOT NULL DEFAULT '0',
  `preferred_latitude` decimal(10,8) DEFAULT NULL,
  `preferred_longitude` decimal(11,8) DEFAULT NULL,
  `preferred_location_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `search_radius` int NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_user_code_unique` (`user_code`),
  KEY `users_user_code_index` (`user_code`),
  KEY `users_phone_index` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'ADM4E6D6F','Admin User','admin@ktmwdc.com',NULL,'$2y$12$Yfw5wT5.fx1dhRLojMY14es7B4bT2/WLm4Ex5DFHSvuiocp5ks/M.','admin',1,0.0,'admin',NULL,NULL,NULL,NULL,0,'2026-07-01 10:27:22','2026-07-09 08:21:25','active','9800000000',NULL,NULL,1,0,0,0,0,NULL,NULL,NULL,10),(2,NULL,'Client User','client@ktmwdc.com',NULL,'$2y$12$diaIHRWv1ZlpyfKMm2xXneJW1N9GpmK5b0KaonQgI3gJgHvP0vpcy','client',1,0.0,'client',NULL,NULL,NULL,NULL,0,'2026-07-01 10:27:22','2026-07-01 10:27:22','active','9800000001',NULL,NULL,0,0,0,1,0,NULL,NULL,NULL,10),(3,NULL,'Test User','test@example.com',NULL,'$2y$12$/9LngaHw0JpzB2B3fpZ5o./3d5ECA.gqeSJqbF5.3sUj4Zn5TEDkm','client',1,0.0,'client',NULL,NULL,NULL,NULL,0,'2026-07-01 10:27:23','2026-07-01 10:27:23','active','9800000002',NULL,NULL,0,0,0,1,0,NULL,NULL,NULL,10),(4,NULL,'Driver User','driver@ktmwdc.com',NULL,'$2y$12$AaWgP9Ypvxh4Ckz4puaeO.6Dyhw2GkDUoD1VQoZgkYNqGflMbFm7u','driver',1,0.0,'driver',NULL,NULL,NULL,NULL,0,'2026-07-01 10:27:23','2026-07-01 10:27:23','active','9800000003',NULL,NULL,0,1,0,0,0,NULL,NULL,NULL,10),(5,'DRI5F3057','Madan Gurung','madan@driver.com',NULL,'$2y$12$vypXxsJJ.hCGb9IqA8pdAeV4qrP4afr6OwAOqjsXo9ptTKVcHSnJK','driver',1,0.0,'driver',NULL,NULL,NULL,NULL,0,'2026-07-01 10:27:24','2026-07-09 08:21:25','active','9800000010',NULL,NULL,0,1,0,0,0,NULL,NULL,NULL,10),(6,NULL,'Equipment Owner','equipment@equipment.com',NULL,'$2y$12$yVItBQCSJ2v/ZQRhj5HSJ.eRFhRN1YQftBme4Zvs7MT3PTv/dXpa6','equipment_owner',1,0.0,'equipment_owner',NULL,NULL,NULL,NULL,0,'2026-07-01 10:27:24','2026-07-01 10:27:24','active','9800000005',NULL,NULL,0,0,1,0,0,NULL,NULL,NULL,10),(7,NULL,'Warehouse Owner','warehouse@warehouse.com',NULL,'$2y$12$r5Y.j4OjVN1zMXu/7TsOtOITN4yl/ZDLW5zPSvRaLHp4o1MNp.8Ya','property_owner',1,0.0,'property_owner',NULL,NULL,NULL,NULL,0,'2026-07-01 10:27:24','2026-07-01 10:27:24','active','9800000006',NULL,NULL,0,0,0,0,1,NULL,NULL,NULL,10),(8,'CLI56228C','Kiran Thapa','kiran.netpack@gmail.com',NULL,'$2y$12$GwFg8G/lEpJ/ULqO6RUdu.8VPsealwQBf8kzGylohZeB046oRH9R6','client',1,0.0,NULL,NULL,NULL,NULL,NULL,0,'2026-07-09 08:21:25','2026-07-09 08:21:25','active','9800000001',NULL,NULL,0,0,0,1,0,NULL,NULL,NULL,10),(9,'CLI5AAAD1','Test Client','client@test.com',NULL,'$2y$12$37f5zxC2EvjOpzR4yoQvk.7quvRkNLryuz.JvqHPqv2wLcah.wU.C','client',1,0.0,NULL,NULL,NULL,NULL,NULL,0,'2026-07-09 08:21:25','2026-07-09 08:21:25','active','9800000002',NULL,NULL,0,0,0,1,0,NULL,NULL,NULL,10),(10,'DRI64514B','Sita Rai','sita@driver.com',NULL,'$2y$12$EIN.qR2QvOvNaK7JF8bMMu39fAMUcnSaWcT3sIZlIoF7Bnu2SLm3G','driver',1,0.0,NULL,NULL,NULL,NULL,NULL,0,'2026-07-09 08:21:26','2026-07-09 08:21:26','active','9800000011',NULL,NULL,0,1,0,0,0,NULL,NULL,NULL,10),(11,'DRI6898C1','Hari Shrestha','hari@driver.com',NULL,'$2y$12$9kNUr6XWkmkOV/mM0YZH0.yODKUfW0Z5nJpTcZPiLRFldzrOUWsUi','driver',1,0.0,NULL,NULL,NULL,NULL,NULL,0,'2026-07-09 08:21:26','2026-07-09 08:21:26','active','9800000012',NULL,NULL,0,1,0,0,0,NULL,NULL,NULL,10),(12,'PRO6CD482','Ram Sharma','ram@property.com',NULL,'$2y$12$F/eRYl90b8XBqJtE9uxTqukofb9xXemwkxlbuj14DLLzadLp1/E6i','property_owner',1,0.0,NULL,NULL,NULL,NULL,NULL,0,'2026-07-09 08:21:26','2026-07-09 08:21:26','active','9800000020',NULL,NULL,0,0,0,0,1,NULL,NULL,NULL,10),(13,'PRO71E799','Gita Adhikari','gita@property.com',NULL,'$2y$12$ngaA2hHO2GOtH1zbDckYseqpM2WxCGMwCStjUhkESlgaerNA1m.8q','property_owner',1,0.0,NULL,NULL,NULL,NULL,NULL,0,'2026-07-09 08:21:27','2026-07-09 08:21:27','active','9800000021',NULL,NULL,0,0,0,0,1,NULL,NULL,NULL,10),(14,'EQU761C31','Krishna Tamang','krishna@equipment.com',NULL,'$2y$12$K/sDzkNaB.VwRAsYoRL/G.iA4TL167nPR65ylFoD1v6X0GOVlc0na','equipment_owner',1,0.0,NULL,NULL,NULL,NULL,NULL,0,'2026-07-09 08:21:27','2026-07-09 08:21:27','active','9800000030',NULL,NULL,0,0,1,0,0,NULL,NULL,NULL,10),(15,'EQU7A5564','Maya Thapa','maya@equipment.com',NULL,'$2y$12$b//giu1OGph01j4vagmYfenuzpDozvSuYYbHQeXjiH83rtKHfTxDy','equipment_owner',1,0.0,NULL,NULL,NULL,NULL,NULL,0,'2026-07-09 08:21:27','2026-07-09 08:21:27','active','9800000031',NULL,NULL,0,0,1,0,0,NULL,NULL,NULL,10);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `driver_user_id` bigint unsigned DEFAULT NULL,
  `driver_id` bigint unsigned NOT NULL,
  `vehicle_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Standard',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'truck',
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plate_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `capacity_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `capacity_boxes` int DEFAULT NULL,
  `current_load` int NOT NULL DEFAULT '0',
  `vehicle_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fitness_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pollution_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pollution_valid_until` date DEFAULT NULL,
  `license_valid_until` date DEFAULT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_id` bigint unsigned DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` int DEFAULT NULL,
  `insurance_valid_until` date DEFAULT NULL,
  `fitness_valid_until` date DEFAULT NULL,
  `driver_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuel_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `insurance_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fitness_certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fitness_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pollution_certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pollution_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permit_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permit_valid_until` date DEFAULT NULL,
  `permit_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blue_book_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blue_book_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `front_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `back_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `left_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `right_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interior_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`),
  KEY `vehicles_driver_id_foreign` (`driver_id`),
  KEY `vehicles_driver_user_id_foreign` (`driver_user_id`),
  KEY `vehicles_owner_id_foreign` (`owner_id`),
  KEY `vehicles_verified_by_foreign` (`verified_by`),
  CONSTRAINT `vehicles_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicles_driver_user_id_foreign` FOREIGN KEY (`driver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicles_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicles_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,NULL,NULL,5,NULL,'Standard','truck','Tata Ace','BA 1 KA 1234',1.50,'tons','available','2026-07-09 08:21:27','2026-07-09 08:21:27',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,NULL,NULL,10,NULL,'Standard','van','Maruti Suzuki Eeco','BA 1 KA 5678',800.00,'kg','available','2026-07-09 08:21:27','2026-07-09 08:21:27',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,NULL,NULL,11,NULL,'Standard','truck','Hino 300','BA 1 KA 9012',5.00,'tons','available','2026-07-09 08:21:27','2026-07-09 08:21:27',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_documents`
--

DROP TABLE IF EXISTS `warehouse_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_documents_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `warehouse_documents_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_documents`
--

LOCK TABLES `warehouse_documents` WRITE;
/*!40000 ALTER TABLE `warehouse_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_photos`
--

DROP TABLE IF EXISTS `warehouse_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_photos_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `warehouse_photos_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_photos`
--

LOCK TABLES `warehouse_photos` WRITE;
/*!40000 ALTER TABLE `warehouse_photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_request_warehouse`
--

DROP TABLE IF EXISTS `warehouse_request_warehouse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_request_warehouse` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_request_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `allocated_space` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_request_warehouse_warehouse_request_id_foreign` (`warehouse_request_id`),
  KEY `warehouse_request_warehouse_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `warehouse_request_warehouse_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_request_warehouse_warehouse_request_id_foreign` FOREIGN KEY (`warehouse_request_id`) REFERENCES `warehouse_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_request_warehouse`
--

LOCK TABLES `warehouse_request_warehouse` WRITE;
/*!40000 ALTER TABLE `warehouse_request_warehouse` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse_request_warehouse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_requests`
--

DROP TABLE IF EXISTS `warehouse_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `required_space` decimal(10,2) NOT NULL,
  `duration_months` int NOT NULL,
  `invoice_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packing_list_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_warehouse_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','assigned','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `preferred_warehouse_id` bigint unsigned DEFAULT NULL,
  `last_notification_sent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extended_until` date DEFAULT NULL,
  `goods_auctioned_at` date DEFAULT NULL,
  `agreed_price` decimal(10,2) DEFAULT NULL,
  `security_deposit_paid` decimal(10,2) DEFAULT NULL,
  `agreed_price_per_unit` decimal(10,2) DEFAULT NULL,
  `security_deposit` decimal(10,2) DEFAULT NULL,
  `monthly_rent` decimal(10,2) DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `last_invoice_date` date DEFAULT NULL,
  `goods_auctioned` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `required_area` decimal(12,2) DEFAULT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci,
  `preferred_start_date` date DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_notes` text COLLATE utf8mb4_unicode_ci,
  `accepted_by_client_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_requests_client_id_foreign` (`client_id`),
  KEY `warehouse_requests_assigned_warehouse_id_foreign` (`assigned_warehouse_id`),
  KEY `warehouse_requests_preferred_warehouse_id_foreign` (`preferred_warehouse_id`),
  KEY `warehouse_requests_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `warehouse_requests_assigned_warehouse_id_foreign` FOREIGN KEY (`assigned_warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `warehouse_requests_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_requests_preferred_warehouse_id_foreign` FOREIGN KEY (`preferred_warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `warehouse_requests_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_requests`
--

LOCK TABLES `warehouse_requests` WRITE;
/*!40000 ALTER TABLE `warehouse_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_tenants`
--

DROP TABLE IF EXISTS `warehouse_tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `monthly_rent` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_tenants_warehouse_id_foreign` (`warehouse_id`),
  KEY `warehouse_tenants_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `warehouse_tenants_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_tenants_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_tenants`
--

LOCK TABLES `warehouse_tenants` WRITE;
/*!40000 ALTER TABLE `warehouse_tenants` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse_tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `owner_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `kataho_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kataho_grid_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kataho_plate_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kataho_address` text COLLATE utf8mb4_unicode_ci,
  `kataho_verified` tinyint(1) NOT NULL DEFAULT '0',
  `kataho_verified_at` timestamp NULL DEFAULT NULL,
  `area_sqft` decimal(10,2) DEFAULT NULL,
  `area_sqm` decimal(10,2) DEFAULT NULL,
  `length` double DEFAULT NULL,
  `width` double DEFAULT NULL,
  `height` double DEFAULT NULL,
  `total_capacity` decimal(10,2) DEFAULT NULL,
  `usable_capacity` decimal(10,2) DEFAULT NULL,
  `allocated_space` decimal(10,2) NOT NULL DEFAULT '0.00',
  `allow_shared` tinyint(1) NOT NULL DEFAULT '1',
  `has_cctv` tinyint(1) NOT NULL DEFAULT '0',
  `has_security_guard` tinyint(1) NOT NULL DEFAULT '0',
  `guard_count` int DEFAULT NULL,
  `has_labors` tinyint(1) NOT NULL DEFAULT '0',
  `is_motorable` tinyint(1) NOT NULL DEFAULT '0',
  `camera_stream_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance_from_city` decimal(8,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` enum('building','open_field') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'building',
  `price_per_unit` decimal(10,2) DEFAULT NULL,
  `price_unit_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `security_deposit_percentage` decimal(5,2) DEFAULT NULL,
  `security_deposit_percent` decimal(5,2) DEFAULT NULL,
  `security_deposit_fixed` decimal(10,2) DEFAULT NULL,
  `price_unit` enum('month','quarter','year') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'month',
  `warehouse_type` enum('building','plot_land','cold_storage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'building',
  `total_area_sqft` decimal(12,2) DEFAULT NULL,
  `total_area_sqm` decimal(12,2) DEFAULT NULL,
  `built_up_area` decimal(12,2) DEFAULT NULL,
  `open_area` decimal(12,2) DEFAULT NULL,
  `length_ft` decimal(10,2) DEFAULT NULL,
  `width_ft` decimal(10,2) DEFAULT NULL,
  `height_ft` decimal(10,2) DEFAULT NULL,
  `cctv_count` int NOT NULL DEFAULT '0',
  `security_guard_count` int NOT NULL DEFAULT '0',
  `fire_extinguisher_count` int NOT NULL DEFAULT '0',
  `has_fire_alarm` tinyint(1) NOT NULL DEFAULT '0',
  `has_sprinkler_system` tinyint(1) NOT NULL DEFAULT '0',
  `has_generator_backup` tinyint(1) NOT NULL DEFAULT '0',
  `has_loading_dock` tinyint(1) NOT NULL DEFAULT '0',
  `road_access_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_access_height` decimal(5,2) DEFAULT NULL,
  `nearest_highway_distance` decimal(8,2) DEFAULT NULL,
  `temperature_range_min` decimal(5,2) DEFAULT NULL,
  `temperature_range_max` decimal(5,2) DEFAULT NULL,
  `has_humidity_control` tinyint(1) NOT NULL DEFAULT '0',
  `has_backup_cooling` tinyint(1) NOT NULL DEFAULT '0',
  `nearest_bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nearest_fuel_station` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nearest_restaurant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ownership_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_clearance_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fire_safety_certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `building_approval_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `front_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interior_photo_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interior_photo_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interior_photo_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exterior_photo_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exterior_photo_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `security_room_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_features` text COLLATE utf8mb4_unicode_ci,
  `restrictions` text COLLATE utf8mb4_unicode_ci,
  `additional_notes` text COLLATE utf8mb4_unicode_ci,
  `built_up_area_sqm` decimal(12,2) DEFAULT NULL,
  `open_area_sqm` decimal(12,2) DEFAULT NULL,
  `usable_area_sqm` decimal(12,2) DEFAULT NULL,
  `location` text COLLATE utf8mb4_unicode_ci,
  `total_area` decimal(12,2) DEFAULT NULL,
  `security_deposit` decimal(12,2) DEFAULT NULL,
  `cctv_available` tinyint(1) NOT NULL DEFAULT '0',
  `security_guard` tinyint(1) NOT NULL DEFAULT '0',
  `fire_safety` tinyint(1) NOT NULL DEFAULT '0',
  `emergency_exit` tinyint(1) NOT NULL DEFAULT '0',
  `nearest_highway` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nearest_market` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operating_hours` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_restrictions` text COLLATE utf8mb4_unicode_ci,
  `documents_required` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `security_features` text COLLATE utf8mb4_unicode_ci,
  `nearest_police` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nearest_fire_station` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nearest_hospital` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usable_area` decimal(12,2) DEFAULT NULL,
  `cctv_urls` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `warehouses_owner_id_foreign` (`owner_id`),
  KEY `warehouses_user_id_foreign` (`user_id`),
  CONSTRAINT `warehouses_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES (1,NULL,7,'Test Warehouse','123 Test Street','Kathmandu','Bagmati','Nepal','44600',NULL,30.00,NULL,NULL,27.7172000,85.3240000,NULL,NULL,NULL,NULL,0,NULL,500.00,NULL,NULL,NULL,NULL,NULL,NULL,0.00,1,0,0,NULL,0,0,NULL,NULL,'approved','2026-07-01 11:29:06','2026-07-01 11:29:06','building',NULL,'fixed',NULL,NULL,NULL,'month','building',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'Test warehouse for proposals',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-02 13:21:10
