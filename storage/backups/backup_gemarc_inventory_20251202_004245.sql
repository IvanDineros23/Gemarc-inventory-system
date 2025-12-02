-- MySQL/MariaDB Database Backup
-- Generated: 2025-12-02 00:42:45
-- Database: gemarc_inventory

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `deliveries`
--

DROP TABLE IF EXISTS `deliveries`;
CREATE TABLE `deliveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dr_number` varchar(255) DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `dr_date` datetime DEFAULT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `part_number` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_description` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 0,
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `intended_to` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(4) DEFAULT NULL COMMENT '1=approved,0=rejected,null=pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deliveries_product_id_foreign` (`product_id`),
  KEY `deliveries_approved_by_foreign` (`approved_by`),
  CONSTRAINT `deliveries_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `deliveries_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`id`, `dr_number`, `customer`, `dr_date`, `product_id`, `part_number`, `item_name`, `item_description`, `date`, `qty`, `unit_cost`, `unit`, `currency`, `remarks`, `intended_to`, `is_approved`, `created_at`, `updated_at`, `approved_by`, `approved_at`) VALUES ('1', 'DEL_1', 'IVAN DINEROS', '2025-11-28 14:09:14', '1', '445645645645', NULL, NULL, '2025-11-28 04:01:57', '1', NULL, NULL, NULL, 'Seeded sample delivery', NULL, NULL, '2025-11-28 04:01:57', '2025-11-28 04:01:57', NULL, NULL);


--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '0001_01_01_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '0001_01_01_000001_create_cache_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '0001_01_01_000002_create_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2025_11_19_020114_create_inventories_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2025_11_19_021135_update_inventories_table_full_columns', '3');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2025_11_19_000000_create_products_table', '4');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2025_11_19_000100_create_receivings_table', '5');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2025_11_25_143700_add_brand_to_products_table', '6');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2025_11_27_100000_add_is_consignment_to_products_table', '7');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('10', '2025_11_28_120500_create_deliveries_table', '8');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('11', '2025_11_28_130000_add_header_fields_to_deliveries_table', '9');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('12', '2025_11_28_140000_add_approval_fields_to_deliveries_table', '10');


--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES ('helpdesk@gemarcph.com', '$2y$12$if4RwZT6mqbxwbiBbIG3TO4FKbHnbZeM7oyZsEoLQrHzzTAS5w2YO', '2025-11-25 05:00:54');


--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `part_number` varchar(255) DEFAULT NULL,
  `inventory_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `fo_number` varchar(255) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `qty_received` int(11) DEFAULT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `beginning_inventory` int(11) DEFAULT NULL,
  `ending_inventory` int(11) DEFAULT NULL,
  `total` decimal(14,2) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_consignment` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `part_number`, `inventory_id`, `name`, `description`, `supplier`, `fo_number`, `date_received`, `qty_received`, `unit_price`, `beginning_inventory`, `ending_inventory`, `total`, `image_path`, `is_consignment`, `created_at`, `updated_at`, `brand`) VALUES ('1', 'B088N', 'B088N', 'VISCOMETER BATH', 'VISCOMETER BATH 0,02°C STABILITY, ASTM D2170, EN12596
', 'MATEST', 'FO-GEI-23-1254', '2024-04-29', '2', '443375.00', '1', '1', '886750.00', NULL, '0', '2025-11-19 03:42:39', '2025-11-19 03:42:39', 'MATEST');
INSERT INTO `products` (`id`, `part_number`, `inventory_id`, `name`, `description`, `supplier`, `fo_number`, `date_received`, `qty_received`, `unit_price`, `beginning_inventory`, `ending_inventory`, `total`, `image_path`, `is_consignment`, `created_at`, `updated_at`, `brand`) VALUES ('2', 'B088-70', NULL, 'ZEITFUCHS CROSS‐ARM VISCOM.', 'MODEL: B088-70 ZEITFUCHS CROSS‐ARM VISCOM.  0.6  TO  3', 'MATEST', 'FO-GEI-23-1254', '2024-04-29', '2', '34631.00', NULL, '2', '69262.00', NULL, '0', '2025-11-19 03:51:58', '2025-11-20 01:53:12', 'MATEST');
INSERT INTO `products` (`id`, `part_number`, `inventory_id`, `name`, `description`, `supplier`, `fo_number`, `date_received`, `qty_received`, `unit_price`, `beginning_inventory`, `ending_inventory`, `total`, `image_path`, `is_consignment`, `created_at`, `updated_at`, `brand`) VALUES ('7', 'LDO-7250S', 'LT-LDO-7250S-001', 'AGING OVEN', 'AGING OVEN UP TO 250C -LABTECH', 'LABTECH', 'FO-GEI-17-534', NULL, '1', '299310.40', NULL, '1', '299310.40', NULL, '0', '2025-11-19 08:09:12', '2025-11-19 08:09:38', 'LABTECH');
INSERT INTO `products` (`id`, `part_number`, `inventory_id`, `name`, `description`, `supplier`, `fo_number`, `date_received`, `qty_received`, `unit_price`, `beginning_inventory`, `ending_inventory`, `total`, `image_path`, `is_consignment`, `created_at`, `updated_at`, `brand`) VALUES ('8', 'DHG-9420A', 'NJ-OVEN-DHG-001', 'DRYING OVEN', 'LABORATORY OVEN 420L', 'NANJING', 'FO-GEI-22-1147', NULL, NULL, '148963.92', NULL, '3', '446891.76', NULL, '1', '2025-11-27 02:13:43', '2025-11-27 02:22:15', 'NANJING');
INSERT INTO `products` (`id`, `part_number`, `inventory_id`, `name`, `description`, `supplier`, `fo_number`, `date_received`, `qty_received`, `unit_price`, `beginning_inventory`, `ending_inventory`, `total`, `image_path`, `is_consignment`, `created_at`, `updated_at`, `brand`) VALUES ('9', 'SWELL PLATE', 'LF-PLATE-SWEL-001', 'SWELL PLATE', 'SWELL PLATE', 'LOCAL FABRICATION', NULL, NULL, NULL, '2200.00', NULL, '7', '15400.00', NULL, '1', '2025-11-27 02:23:47', '2025-11-27 02:23:47', 'LOCAL FABRICATION');


--
-- Table structure for table `receivings`
--

DROP TABLE IF EXISTS `receivings`;
CREATE TABLE `receivings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `fo_number` varchar(255) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `qty_received` int(11) DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `beginning_inventory` int(11) DEFAULT NULL,
  `ending_inventory` int(11) DEFAULT NULL,
  `details_file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receivings_product_id_foreign` (`product_id`),
  CONSTRAINT `receivings_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `receivings`
--

INSERT INTO `receivings` (`id`, `product_id`, `fo_number`, `date_received`, `qty_received`, `unit_price`, `beginning_inventory`, `ending_inventory`, `details_file_path`, `created_at`, `updated_at`) VALUES ('1', '2', NULL, NULL, '1', '25000.00', NULL, NULL, NULL, '2025-11-20 01:52:01', '2025-11-20 01:53:12');


--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('sMgodGxRDzeTVc0sC163SeCIxQHif4jTODaBTPdS', '16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYW83dDJ2Z2RRZTVxaHJZMmhFcVRKV1V2U1hpcmE3T2p0YXhBeUpZNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9maWxlIjtzOjU6InJvdXRlIjtzOjEyOiJwcm9maWxlLmVkaXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNjt9', '1764636159');


--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES ('14', 'Daniel Purchasing', 'helpdesk@gemarcph.com', '2025-11-25 13:08:28', '$2y$12$Ymiw.845SUmLODKicswsC.p9YNKXFk3JTG1SB08Lryv9S6KJRICmS', 'smfJf2ZMuiei4ORjaokpRnnIKANFrN6YekH00oDZPmgBf6dDo0xGw7S5bmRB', '2025-11-25 05:07:14', '2025-12-01 05:19:00', 'user');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES ('16', 'ivan dineros', 'jamesivandineros@gmail.com', NULL, '$2y$12$jLdV/xOdmsYNi5ZFr.kQhuqo9r1VE.4.jOqDbDIt7NPbGE4uxgamG', 'X8dt7ucHvD0D7nevwXOBKK4i2aDE0FnTkGLfMXacCqm0eGIDBNLHJSWMOoHy', '2025-12-01 05:25:26', '2025-12-01 05:25:26', 'user');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
