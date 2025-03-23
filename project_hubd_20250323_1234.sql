/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.7.2-MariaDB, for osx10.20 (arm64)
--
-- Host: mysql.danielhsu.dev    Database: project_hubd
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner` (
  `banner_id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `banner_title` varchar(50) DEFAULT NULL,
  `banner_img` varchar(255) DEFAULT NULL,
  `banner_description` varchar(100) DEFAULT NULL,
  `banner_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner`
--

LOCK TABLES `banner` WRITE;
/*!40000 ALTER TABLE `banner` DISABLE KEYS */;
INSERT INTO `banner` VALUES
(1,'★ DIY 銀黏土戒指 ★','banners/banner1.jpg\n','這裡是專屬你的手工飾品小天地！融合創意與心意，讓飾品不只是點綴，更是展現個性的魔法！','http://localhost/client-side/public/about_us','2025-03-17 18:26:13','2025-03-20 06:35:56'),
(2,'★ 週年慶優惠 ★','banners/banner2.jpg','HUBD 兩歲了（撒花）！為了慶祝這個特別的日子，3/20-4/20 期間，購物車結帳金額全面打九折，不要錯過！','http://localhost/client-side/public/categories_accessories','2025-03-17 18:26:13','2025-03-20 06:36:08'),
(3,'★ 春夏新裝到貨 ★','banners/banner3.jpg','黑灰白低調色大好き！換季時尚夏裝全面販售中，邀請大家一起穿出 2025 新氣象！非生日，也快樂！','http://localhost/client-side/public/categories_clothes','2025-03-17 18:26:13','2025-03-20 06:36:23');
/*!40000 ALTER TABLE `banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
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
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
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
-- Table structure for table `campaign_participants`
--

DROP TABLE IF EXISTS `campaign_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_participants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_participants_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `campaign_participants_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_participants`
--

LOCK TABLES `campaign_participants` WRITE;
/*!40000 ALTER TABLE `campaign_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('discount','buy_x_get_y','bundle','flash_sale','free_shipping') NOT NULL,
  `discount_method` enum('percentage','fixed') DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `buy_quantity` int(11) DEFAULT NULL,
  `free_quantity` int(11) DEFAULT NULL,
  `bundle_quantity` int(11) DEFAULT NULL,
  `bundle_discount` decimal(10,2) DEFAULT NULL,
  `flash_sale_start_time` timestamp NULL DEFAULT NULL,
  `flash_sale_end_time` timestamp NULL DEFAULT NULL,
  `flash_sale_discount` decimal(10,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `stock_limit` int(11) DEFAULT NULL,
  `per_user_limit` int(11) DEFAULT NULL,
  `applicable_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_products`)),
  `applicable_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_categories`)),
  `users` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`users`)),
  `description` text DEFAULT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `can_combine` tinyint(1) NOT NULL DEFAULT 0,
  `redemption_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaigns`
--

LOCK TABLES `campaigns` WRITE;
/*!40000 ALTER TABLE `campaigns` DISABLE KEYS */;
INSERT INTO `campaigns` VALUES
(1,'春季新品85折','discount','percentage',15.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-02-18','2025-04-18',NULL,NULL,NULL,'\"[1,2]\"',NULL,'春季新品上市，全館85折優惠','active',0,35,'2025-03-17 12:22:34','2025-03-17 12:22:34'),
(2,'文具專區買二送一','buy_x_get_y',NULL,NULL,2,1,NULL,NULL,NULL,NULL,NULL,'2025-03-17','2025-03-25',100,2,'\"[201,202,203,204,205]\"',NULL,NULL,'文具專區指定商品，買二送一','active',0,8,'2025-03-17 12:22:35','2025-03-17 12:22:35'),
(3,'電腦配件超值組合','bundle',NULL,NULL,NULL,NULL,3,20.00,NULL,NULL,NULL,'2025-03-18','2025-04-01',NULL,NULL,'\"[301,302,303,304,305]\"',NULL,NULL,'購買指定電腦配件三件以上，享8折優惠','active',0,3,'2025-03-17 12:22:35','2025-03-17 12:22:35'),
(4,'週末快閃5折','flash_sale',NULL,NULL,NULL,NULL,NULL,NULL,'2025-03-24 20:00:00','2025-03-27 07:59:59',50.00,'2025-03-25','2025-03-27',50,1,'\"[401,402,403]\"',NULL,NULL,'週末限時特賣，指定商品5折起','active',0,0,'2025-03-17 12:22:35','2025-03-17 12:22:35'),
(5,'新年免運優惠','free_shipping',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-12-18','2024-12-18',NULL,NULL,NULL,NULL,NULL,'新年期間，全館訂單免運費','disabled',1,120,'2025-03-17 12:22:35','2025-03-17 12:22:35'),
(6,'VIP會員專屬優惠','discount','percentage',25.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-18','2025-06-18',NULL,5,NULL,NULL,'\"[1,2,3,4,5]\"','VIP會員專屬，全館商品75折','active',0,0,'2025-03-17 12:22:35','2025-03-17 12:22:35'),
(7,'家電優惠週','discount','fixed',500.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-03-18','2025-04-01',NULL,NULL,NULL,'\"[5]\"',NULL,'家電類別商品滿3000折500','active',1,12,'2025-03-17 12:22:35','2025-03-17 12:22:35');
/*!40000 ALTER TABLE `campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `product_id` varchar(100) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `id` bigint(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `product_color` varchar(11) DEFAULT NULL,
  `product_size` varchar(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES
('ps001','女裝百褶拼接寬鬆上衣',19,4,'Black','M','2025-03-17 18:31:35','2025-03-22 10:55:05'),
('pj001','斜紋軟呢無領外套',19,3,'Black','L','2025-03-22 09:03:03','2025-03-22 09:22:01'),
('ps003',NULL,19,1,'Black','S','2025-03-22 01:22:12','2025-03-22 01:22:12'),
('ps002','不對稱異素材上衣',19,11,'White','L','2025-03-22 01:24:58','2025-03-22 10:55:10'),
('ps010','高領打褶無袖上衣',19,1,'Grey','M','2025-03-22 03:11:50','2025-03-22 03:11:50');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_flow_settings`
--

DROP TABLE IF EXISTS `cash_flow_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_flow_settings` (
  `name` varchar(50) NOT NULL,
  `Hash_Key` varchar(50) NOT NULL,
  `Hash_IV` varchar(50) NOT NULL,
  `merchant_ID` varchar(50) NOT NULL,
  `WEB_enable` tinyint(1) NOT NULL,
  `CVS_enable` tinyint(1) NOT NULL,
  `ATM_enable` tinyint(1) NOT NULL,
  `credit_enable` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_flow_settings`
--

LOCK TABLES `cash_flow_settings` WRITE;
/*!40000 ALTER TABLE `cash_flow_settings` DISABLE KEYS */;
INSERT INTO `cash_flow_settings` VALUES
('ECPAY','pwFHCqoQZGmho4w6','EkRm7iFT261dpevs','3002607',0,0,0,1,'2025-03-17 18:31:44','2025-03-17 18:31:44');
/*!40000 ALTER TABLE `cash_flow_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_usages`
--

DROP TABLE IF EXISTS `coupon_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_usages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_usages_coupon_id_foreign` (`coupon_id`),
  CONSTRAINT `coupon_usages_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_usages`
--

LOCK TABLES `coupon_usages` WRITE;
/*!40000 ALTER TABLE `coupon_usages` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon_usages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `discount_type` enum('percentage','fixed','shipping','buy_x_get_y') NOT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `min_purchase` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`products`)),
  `categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`categories`)),
  `users` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`users`)),
  `buy_quantity` int(11) DEFAULT NULL,
  `free_quantity` int(11) DEFAULT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `can_combine` tinyint(1) NOT NULL DEFAULT 0,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES
(1,'新會員首單9折','WELCOME10','percentage',10.00,100.00,'2025-02-18','2025-04-18',1,'新會員首次訂單可享9折優惠，最低消費金額$100','[{\"product_id\":\"pa001\",\"category_id\":201,\"product_name\":\"Navajo \\u7da0\\u677e\\u77f3\\u5341\\u5b57\\u661f\\u6212\",\"product_price\":5980,\"product_description\":\"925 silver | natural turquoise | metric circumference No.10\\uff08\\u516c\\u5236\\u570d10\\u865f\\uff09\",\"product_img\":\"products\\/accessories\\/pa001_00_01.jpg\",\"product_status\":null,\"created_at\":\"2025-03-10T10:44:53.000000Z\",\"updated_at\":\"2025-03-19T11:39:30.000000Z\",\"specifications\":[{\"product_id\":\"pa001\",\"product_size\":\"null\",\"product_color\":\"null\",\"product_stock\":100,\"created_at\":\"2025-03-18T02:34:06.000000Z\",\"updated_at\":\"2025-03-18T02:34:06.000000Z\",\"spec_id\":\"1\"}],\"images\":[],\"information\":[{\"product_id\":\"pa001\",\"title\":\"\\u6750\\u8cea\",\"content\":\"\\u7d14\\u928092.5% (925 silver)\\uff5c\\u5929\\u7136\\u7da0\\u677e\\u77f3(natural turquoise)\",\"created_at\":\"2025-03-18T02:28:13.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa001\",\"title\":\"\\u898f\\u683c\",\"content\":\"\\u516c\\u5236\\u570d10\\u865f (Metric circumference No.10 )\",\"created_at\":\"2025-03-18T02:28:13.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa001\",\"title\":\"\\u5176\\u4ed6\\u88dc\\u5145\",\"content\":\"\\u98fe\\u54c1\\u7686\\u624b\\u5de5\\u88fd\\u4f5c\\uff0c\\u8aa4\\u5dee\\u503c \\u00b10.5\\u516c\\u5206\\u7686\\u70ba\\u6b63\\u5e38\\u7bc4\\u570d\",\"created_at\":\"2025-03-18T02:28:13.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa001\",\"title\":\"\\u51fa\\u8ca8\\u8aaa\\u660e\",\"content\":\"\\u9810\\u8cfc\\u5546\\u54c1\\u51fa\\u8ca8\\u7d0421\\u5de5\\u4f5c\\u5929(\\u4e0d\\u542b\\u5047\\u65e5)\\uff0c\\u5efa\\u8b70\\u8207\\u73fe\\u8ca8\\u5546\\u54c1\\u5206\\u958b\\u4e0b\\u55ae\",\"created_at\":\"2025-03-18T02:28:13.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"}],\"display_images\":[],\"classifiction\":[{\"category_id\":201,\"parent_category\":\"\\u98fe\\u54c1\",\"child_category\":\"\\u7570\\u4e16\\u754c2000\",\"created_at\":\"2025-03-18T02:30:59.000000Z\",\"updated_at\":\"2025-03-19T09:15:10.000000Z\"}],\"id\":\"pa001\"},{\"product_id\":\"pa003\",\"category_id\":201,\"product_name\":\"Opal \\u86cb\\u767d\\u77f3\\u81d8\\u96d5\\u7d14\\u9280\\u6212\",\"product_price\":4350,\"product_description\":\"925 silver | natural opal | metric circumference No.10\\uff08\\u516c\\u5236\\u570d10\\u865f\\uff09\",\"product_img\":\"products\\/accessories\\/pa003_00_01.jpg\",\"product_status\":null,\"created_at\":\"2025-03-10T11:47:06.000000Z\",\"updated_at\":\"2025-03-19T11:39:30.000000Z\",\"specifications\":[{\"product_id\":\"pa003\",\"product_size\":\"null\",\"product_color\":\"null\",\"product_stock\":100,\"created_at\":\"2025-03-18T02:34:06.000000Z\",\"updated_at\":\"2025-03-18T02:34:06.000000Z\",\"spec_id\":\"3\"}],\"images\":[],\"information\":[{\"product_id\":\"pa003\",\"title\":\"\\u6750\\u8cea\",\"content\":\"\\u7d14\\u928092.5% (925 silver)\\uff5c\\u86cb\\u767d\\u77f3(natural opal)\",\"created_at\":\"2025-03-18T11:42:24.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa003\",\"title\":\"\\u898f\\u683c\",\"content\":\"\\u516c\\u5236\\u570d10\\u865f (Metric circumference No.10)\",\"created_at\":\"2025-03-18T11:42:24.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa003\",\"title\":\"\\u5176\\u4ed6\\u88dc\\u5145\",\"content\":\"\\u98fe\\u54c1\\u7686\\u624b\\u5de5\\u88fd\\u4f5c\\uff0c\\u8aa4\\u5dee\\u503c \\u00b10.5\\u516c\\u5206\\u7686\\u70ba\\u6b63\\u5e38\\u7bc4\\u570d\",\"created_at\":\"2025-03-18T11:42:24.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa003\",\"title\":\"\\u51fa\\u8ca8\\u8aaa\\u660e\",\"content\":\"\\u9810\\u8cfc\\u5546\\u54c1\\u51fa\\u8ca8\\u7d0421\\u5de5\\u4f5c\\u5929(\\u4e0d\\u542b\\u5047\\u65e5)\\uff0c\\u5efa\\u8b70\\u8207\\u73fe\\u8ca8\\u5546\\u54c1\\u5206\\u958b\\u4e0b\\u55ae\",\"created_at\":\"2025-03-18T11:42:24.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"}],\"display_images\":[],\"classifiction\":[{\"category_id\":201,\"parent_category\":\"\\u98fe\\u54c1\",\"child_category\":\"\\u7570\\u4e16\\u754c2000\",\"created_at\":\"2025-03-18T02:30:59.000000Z\",\"updated_at\":\"2025-03-19T09:15:10.000000Z\"}],\"id\":\"pa003\"},{\"product_id\":\"pa002\",\"category_id\":201,\"product_name\":\"Navajo \\u86cb\\u767d\\u77f3\\u92fc\\u5370\\u6212\",\"product_price\":4590,\"product_description\":\"925 silver | natural opal | metric circumference No.10\\uff08\\u516c\\u5236\\u570d10\\u865f\\uff09\",\"product_img\":\"products\\/accessories\\/pa002_00_01.jpg\",\"product_status\":null,\"created_at\":\"2025-03-10T11:42:49.000000Z\",\"updated_at\":\"2025-03-19T11:39:30.000000Z\",\"specifications\":[{\"product_id\":\"pa002\",\"product_size\":\"null\",\"product_color\":\"null\",\"product_stock\":100,\"created_at\":\"2025-03-18T02:34:06.000000Z\",\"updated_at\":\"2025-03-18T02:34:06.000000Z\",\"spec_id\":\"2\"}],\"images\":[],\"information\":[{\"product_id\":\"pa002\",\"title\":\"\\u6750\\u8cea\",\"content\":\"\\u7d14\\u928092.5% (925 silver)\\uff5c\\u86cb\\u767d\\u77f3(natural opal)\",\"created_at\":\"2025-03-18T02:28:13.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa002\",\"title\":\"\\u898f\\u683c\",\"content\":\"\\u516c\\u5236\\u570d10\\u865f (Metric circumference No.10 )\",\"created_at\":\"2025-03-18T02:28:13.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa002\",\"title\":\"\\u5176\\u4ed6\\u88dc\\u5145\",\"content\":\"\\u98fe\\u54c1\\u7686\\u624b\\u5de5\\u88fd\\u4f5c\\uff0c\\u8aa4\\u5dee\\u503c \\u00b10.5\\u516c\\u5206\\u7686\\u70ba\\u6b63\\u5e38\\u7bc4\\u570d\",\"created_at\":\"2025-03-18T02:28:13.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"},{\"product_id\":\"pa002\",\"title\":\"\\u51fa\\u8ca8\\u8aaa\\u660e\",\"content\":\"\\u9810\\u8cfc\\u5546\\u54c1\\u51fa\\u8ca8\\u7d0421\\u5de5\\u4f5c\\u5929(\\u4e0d\\u542b\\u5047\\u65e5)\\uff0c\\u5efa\\u8b70\\u8207\\u73fe\\u8ca8\\u5546\\u54c1\\u5206\\u958b\\u4e0b\\u55ae\",\"created_at\":\"2025-03-18T02:28:13.000000Z\",\"updated_at\":\"2025-03-18T11:50:28.000000Z\"}],\"display_images\":[],\"classifiction\":[{\"category_id\":201,\"parent_category\":\"\\u98fe\\u54c1\",\"child_category\":\"\\u7570\\u4e16\\u754c2000\",\"created_at\":\"2025-03-18T02:30:59.000000Z\",\"updated_at\":\"2025-03-19T09:15:10.000000Z\"}],\"id\":\"pa002\"}]','[{\"id\":\"\\u670d\\u98fe\",\"name\":\"\\u670d\\u98fe\",\"subCategories\":[{\"id\":101,\"name\":\"\\u77ed\\u8896\"},{\"id\":102,\"name\":\"\\u9577\\u8896\"},{\"id\":103,\"name\":\"\\u5916\\u5957\"}]},{\"id\":101,\"name\":\"\\u77ed\\u8896\"},{\"id\":102,\"name\":\"\\u9577\\u8896\"},{\"id\":103,\"name\":\"\\u5916\\u5957\"}]','[{\"id\":10,\"name\":\"\\u8a31\\u5927\\u7c73\",\"email\":\"pollylearnhsu@gmail.com\",\"phone\":null,\"birthday\":null,\"email_verified_at\":\"2025-03-11T07:25:57.000000Z\",\"created_at\":\"2025-03-11T07:25:57.000000Z\",\"updated_at\":\"2025-03-11T07:25:57.000000Z\"},{\"id\":19,\"name\":\"\\u8a31\\u5c11\\u5b87\",\"email\":\"nasa0824@gmail.com\",\"phone\":\"0910305411\",\"birthday\":\"2000-08-24\",\"email_verified_at\":\"2025-03-13T06:54:28.000000Z\",\"created_at\":\"2025-03-13T06:54:28.000000Z\",\"updated_at\":\"2025-03-22T06:17:19.000000Z\"},{\"id\":20,\"name\":\"\\u8a31\",\"email\":\"daniel@danielhsu.dev\",\"phone\":\"0912345678\",\"birthday\":null,\"email_verified_at\":\"2025-03-13T07:27:40.000000Z\",\"created_at\":\"2025-03-13T07:27:40.000000Z\",\"updated_at\":\"2025-03-15T03:54:04.000000Z\"}]',NULL,NULL,'active',0,0,'2025-03-17 12:22:34','2025-03-22 18:25:31'),
(2,'春季特賣8折','SPRING20','percentage',20.00,200.00,'2024-12-18','2024-12-18',NULL,'春季特賣活動，全場商品8折',NULL,'[{\"id\":101,\"name\":\"\\u77ed\\u8896\"}]','[]',NULL,NULL,'disabled',0,45,'2025-03-17 12:22:34','2025-03-22 18:13:36'),
(3,'滿$500折$50','SAVE50','fixed',50.00,500.00,'2024-12-18','2025-04-18',NULL,'消費滿$500，立即折抵$50',NULL,'\"[1,2,3]\"',NULL,NULL,NULL,'active',1,12,'2025-03-17 12:22:34','2025-03-17 12:22:34'),
(4,'全站免運費','FREESHIP','shipping',NULL,800.00,'2025-04-18','2025-04-18',NULL,'訂單滿$800，享免運費優惠',NULL,NULL,'[]',NULL,NULL,'active',1,8,'2025-03-17 12:22:34','2025-03-17 12:55:52'),
(5,'指定商品買一送一','BUY1GET1','buy_x_get_y',NULL,NULL,'2025-03-18','2025-03-25',1,'指定商品買一送一優惠','\"[101,102,103]\"',NULL,NULL,1,1,'active',0,3,'2025-03-17 12:22:34','2025-03-17 12:22:34'),
(6,'VIP會員85折','VIP15','percentage',15.00,300.00,'2025-03-18','2025-06-18',3,'VIP會員專屬85折優惠',NULL,NULL,'\"[1,2,3,4,5]\"',NULL,NULL,'active',0,0,'2025-03-17 12:22:34','2025-03-17 12:22:34'),
(7,'週年慶全館75折','ANNIV25','percentage',25.00,500.00,'2025-06-18','2025-07-02',NULL,'週年慶期間全館商品75折',NULL,NULL,NULL,NULL,NULL,'active',0,0,'2025-03-17 12:22:34','2025-03-17 12:22:34'),
(8,'生日券','BIRTH2503','fixed',100.00,NULL,NULL,NULL,NULL,NULL,'[]','[]','[]',NULL,NULL,'active',1,0,'2025-03-19 13:51:38','2025-03-19 13:51:38');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance`
--

DROP TABLE IF EXISTS `maintenance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenance` (
  `maintain_status` int(11) NOT NULL,
  `maintain_description` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`maintain_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance`
--

LOCK TABLES `maintenance` WRITE;
/*!40000 ALTER TABLE `maintenance` DISABLE KEYS */;
INSERT INTO `maintenance` VALUES
(1,'親愛的顧客，感謝您長期以來的支持！為了慶祝周年慶，我們將於近期暫時關閉網站進行升級與維護。敬請留意我們的開放時間，期待與您再度相見！','2025-03-10','2025-03-30');
/*!40000 ALTER TABLE `maintenance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2025_03_06_052803_create_personal_access_tokens_table',1),
(5,'2025_03_13_023216_create_members_table',2),
(6,'2025_03_13_055321_add_fields_to_users_table',2),
(7,'2025_03_17_181946_add_timestamps_to_banner_table',3),
(8,'2025_03_17_182219_add_timestamps_to_product_spec_table',4),
(9,'2025_03_20_032306_add_spec_id_to_product_spec',5),
(10,'2025_03_17_041536_create_coupons_table',6),
(11,'2025_03_17_041621_create_campaigns_table',6),
(12,'2025_03_18_042334_create_coupon_usages_table',6),
(13,'2025_03_18_042413_create_campaign_participants_table',6),
(14,'2025_03_22_100754_create_products_table',7),
(15,'2025_03_22_100300_create_coupon_product_table',8),
(16,'2025_03_22_100304_create_coupon_category_table',9),
(17,'2025_03_22_100309_create_coupon_user_table',10),
(18,'2025_03_22_100313_create_campaign_product_table',11),
(19,'2025_03_22_100317_create_campaign_category_table',12),
(20,'2025_03_22_100322_create_campaign_user_table',13),
(21,'2025_03_22_100326_update_coupons_and_campaigns_table',14),
(22,'2025_03_22_104648_update_coupon_and_campaign_category_table',15),
(23,'2025_03_22_104931_drop_and_recreate_coupon_product_table',16);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_detail` (
  `order_id` varchar(100) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_size` varchar(10) DEFAULT NULL,
  `product_color` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `product_price` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `order_detail_order_main_FK` (`order_id`),
  CONSTRAINT `order_detail_order_main_FK` FOREIGN KEY (`order_id`) REFERENCES `order_main` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_detail`
--

LOCK TABLES `order_detail` WRITE;
/*!40000 ALTER TABLE `order_detail` DISABLE KEYS */;
INSERT INTO `order_detail` VALUES
('601','女裝百褶拼接寬鬆上衣','S','Black',1,1640,'2025-03-16 22:02:52','2025-03-16 22:02:52'),
('707','女裝百褶拼接寬鬆上衣','S','Grey',24,1640,'2025-03-18 22:09:40','2025-03-18 22:09:40'),
('707','女裝不對稱異素材上衣','L','Black',9,1380,'2025-03-18 22:09:40','2025-03-18 22:09:40'),
('821','女裝百褶拼接寬鬆上衣','L','Black',30,1640,'2025-03-18 22:49:41','2025-03-18 22:49:41'),
('821','女裝不對稱異素材上衣','L','Grey',16,1380,'2025-03-18 22:49:41','2025-03-18 22:49:41'),
('676','女裝百褶拼接寬鬆上衣','L','Black',30,1640,'2025-03-19 22:11:01','2025-03-19 22:11:01'),
('676','女裝不對稱異素材上衣','L','Grey',16,1380,'2025-03-19 22:11:01','2025-03-19 22:11:01'),
('883','女裝百褶拼接寬鬆上衣','L','Black',30,1640,'2025-03-19 22:11:01','2025-03-19 22:11:01'),
('883','女裝不對稱異素材上衣','L','Grey',16,1380,'2025-03-19 22:11:01','2025-03-19 22:11:01'),
('570','女裝百褶拼接寬鬆上衣','M','Black',2,1640,'2025-03-22 01:36:22','2025-03-22 01:36:22'),
('570','斜紋軟呢無領外套','L','Black',3,9900,'2025-03-22 01:36:22','2025-03-22 01:36:22'),
('441','女裝百褶拼接寬鬆上衣','M','Black',2,1640,'2025-03-22 01:43:30','2025-03-22 01:43:30'),
('441','斜紋軟呢無領外套','L','Black',3,9900,'2025-03-22 01:43:30','2025-03-22 01:43:30'),
('851','女裝百褶拼接寬鬆上衣','M','Black',2,1640,'2025-03-22 01:43:53','2025-03-22 01:43:53'),
('851','斜紋軟呢無領外套','L','Black',3,9900,'2025-03-22 01:43:53','2025-03-22 01:43:53');
/*!40000 ALTER TABLE `order_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_main`
--

DROP TABLE IF EXISTS `order_main`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_main` (
  `order_id` varchar(100) NOT NULL,
  `trade_No` varchar(50) DEFAULT NULL,
  `trade_Date` timestamp NULL DEFAULT NULL,
  `id` bigint(20) DEFAULT NULL,
  `total_price_with_discount` int(11) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `trade_status` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_main`
--

LOCK TABLES `order_main` WRITE;
/*!40000 ALTER TABLE `order_main` DISABLE KEYS */;
INSERT INTO `order_main` VALUES
('441','65077856','2025-03-22 01:43:30',19,29582,'信用卡付款','交易成功','2025-03-22 01:43:30','2025-03-22 01:43:30'),
('570','78251044','2025-03-22 01:36:22',19,29582,'信用卡付款','交易成功','2025-03-22 01:36:22','2025-03-22 01:36:22'),
('601','33458611','2025-03-16 22:02:52',19,23732,'信用卡付款','交易成功','2025-03-16 22:02:52','2025-03-16 22:02:52'),
('619','31963400','2025-03-18 22:48:37',19,64052,'信用卡付款','交易成功','2025-03-18 22:48:37','2025-03-18 22:48:37'),
('645','12402759','2025-03-19 22:11:00',19,64052,'ATM轉帳','交易成功','2025-03-19 22:11:00','2025-03-19 22:11:00'),
('676','21205944','2025-03-19 22:11:01',19,64052,'ATM轉帳','交易成功','2025-03-19 22:11:01','2025-03-19 22:11:01'),
('707','24682708','2025-03-18 22:09:40',19,46502,'信用卡付款','交易成功','2025-03-18 22:09:40','2025-03-18 22:09:40'),
('821','98077253','2025-03-18 22:49:41',19,64052,'ATM轉帳','交易成功','2025-03-18 22:49:41','2025-03-18 22:49:41'),
('851','47938494','2025-03-22 01:43:53',19,29582,'信用卡付款','交易成功','2025-03-22 01:43:53','2025-03-22 01:43:53'),
('883','48861557','2025-03-19 22:11:01',19,64052,'ATM轉帳','交易成功','2025-03-19 22:11:01','2025-03-19 22:11:01');
/*!40000 ALTER TABLE `order_main` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES
('nasa0824@gmail.com','826533','2025-03-16 23:37:22');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
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
-- Table structure for table `product_classification`
--

DROP TABLE IF EXISTS `product_classification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_classification` (
  `category_id` int(11) NOT NULL,
  `parent_category` varchar(11) DEFAULT NULL,
  `child_category` varchar(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_classification`
--

LOCK TABLES `product_classification` WRITE;
/*!40000 ALTER TABLE `product_classification` DISABLE KEYS */;
INSERT INTO `product_classification` VALUES
(101,'服飾','短袖','2025-03-17 18:30:59','2025-03-17 18:30:59'),
(102,'服飾','長袖','2025-03-17 18:30:59','2025-03-17 18:30:59'),
(103,'服飾','外套','2025-03-17 18:30:59','2025-03-17 18:30:59'),
(201,'飾品','異世界2000','2025-03-17 18:30:59','2025-03-19 01:15:10'),
(202,'飾品','水晶晶系列','2025-03-19 01:15:04','2025-03-19 01:15:19');
/*!40000 ALTER TABLE `product_classification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_display_img`
--

DROP TABLE IF EXISTS `product_display_img`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_display_img` (
  `product_id` varchar(100) DEFAULT NULL,
  `product_img_URL` varchar(255) DEFAULT NULL,
  `product_display_order` int(11) DEFAULT NULL,
  `product_alt_text` varchar(100) DEFAULT NULL,
  KEY `fk_product_display_img` (`product_id`),
  CONSTRAINT `fk_product_display_img` FOREIGN KEY (`product_id`) REFERENCES `product_main` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_display_img`
--

LOCK TABLES `product_display_img` WRITE;
/*!40000 ALTER TABLE `product_display_img` DISABLE KEYS */;
INSERT INTO `product_display_img` VALUES
('pl009','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',NULL,NULL),
('pl009','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',NULL,NULL),
('ps016','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',NULL,NULL),
('ps016','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',NULL,NULL),
('ps017','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',NULL,NULL),
('ps017','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',NULL,NULL),
('ps018','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',NULL,NULL),
('ps018','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',NULL,NULL),
('ps019','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',NULL,NULL),
('ps019','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',NULL,NULL),
('ps020','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',1,'測試商品'),
('ps020','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',2,'測試商品'),
('ps021','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',1,'測試商品'),
('ps021','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',2,'測試商品'),
('ps022','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',1,'測試商品'),
('ps022','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',2,'測試商品'),
('ps024','products_display/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg',1,'測試商品'),
('ps024','products_display/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',2,'測試商品'),
('ps028','products_display/服飾/短袖/3.18/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',1,'3.18'),
('ps029','products_display/服飾/短袖/3.18/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',1,'3.18'),
('ps030','products_display/服飾/短袖/異世界2000/pexels-wearelivingart-8960867.jpg',1,'異世界2000'),
('pl010','products_display/服飾/長袖/異世界2000/pexels-wearelivingart-8960867.jpg',1,'異世界2000'),
('ps031','products_display/服飾/短袖/異世界2000/pexels-wearelivingart-8960867.jpg',1,'異世界2000'),
('pa011','products_display/飾品/異世界2000/異世界2000/pexels-wearelivingart-8960867.jpg',1,'異世界2000'),
('pa012','products_display/飾品/異世界2000/異世界2000/pexels-wearelivingart-8960867.jpg',1,'異世界2000'),
('ps032','products_display/服飾/短袖/3.19/pexels-wearelivingart-8960867.jpg',1,'3.19'),
('pa013','products_display/飾品/異世界2000/3.19測試/thumb-210617_01.png',1,'3.19測試'),
('pa013','products_display/飾品/異世界2000/3.19測試/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',2,'3.19測試'),
('pa014','products_display/飾品/水晶晶系列/3.20/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',1,'3.20'),
('pa014','products_display/飾品/水晶晶系列/3.20/thumb-210617_01.png',2,'3.20'),
('pa014','products_display/飾品/水晶晶系列/3.20/pexels-wearelivingart-8960867.jpg',3,'3.20'),
('ps032','products_display/服飾/短袖/3.19.20.2/thumb-210617_01.png',1,'3.19.20.2'),
('ps032','products_display/服飾/短袖/3.19.20.2.4/thumb-210617_01.png',1,'3.19.20.2.4'),
('ps033','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps034','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps035','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps036','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps037','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps038','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps039','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps040','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps041','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps042','products_display/服飾/短袖/3.20.final/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'3.20.final'),
('ps043','products_display/服飾/短袖/3.20.17.26/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',1,'3.20.17.26'),
('ps043','products_display/服飾/短袖/3.20.17.26/pexels-wearelivingart-8960867.jpg',2,'3.20.17.26'),
('ps043','products_display/服飾/短袖/3.20.17.26/thumb-210617_01.png',3,'3.20.17.26'),
('ps044','products_display/服飾/短袖/test/pexels-wearelivingart-8960867.jpg',1,'test'),
('ps044','products_display/服飾/短袖/test/thumb-210617_01.png',2,'test'),
('ps045','products_display/服飾/短袖/測試商品規格/thumb-210617_01.png',1,'測試商品規格'),
('ps045','products_display/服飾/短袖/測試商品規格/pexels-wearelivingart-8960867.jpg',2,'測試商品規格'),
('ps045','products_display/服飾/短袖/測試商品規格/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',3,'測試商品規格'),
('ps045','products_display/服飾/短袖/測試商品規格/224892b2e43c31d5666f73bc6116d0e5719293d2.png',4,'測試商品規格'),
('ps047','products_display/服飾/短袖/測試商品spec_id/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',1,'測試商品spec_id'),
('ps047','products_display/服飾/短袖/測試商品spec_id/thumb-210617_01.png',2,'測試商品spec_id'),
('ps048','products_display/服飾/短袖/測試商品規格spec_id/224892b2e43c31d5666f73bc6116d0e5719293d2.png',1,'測試商品規格spec_id'),
('ps048','products_display/服飾/短袖/測試商品規格spec_id/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png',2,'測試商品規格spec_id');
/*!40000 ALTER TABLE `product_display_img` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_img`
--

DROP TABLE IF EXISTS `product_img`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_img` (
  `product_id` varchar(100) DEFAULT NULL,
  `product_display_order` int(11) DEFAULT NULL,
  `product_alt_text` varchar(100) DEFAULT NULL,
  `product_img_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `fk_product_img` (`product_id`),
  CONSTRAINT `fk_product_img` FOREIGN KEY (`product_id`) REFERENCES `product_main` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_img`
--

LOCK TABLES `product_img` WRITE;
/*!40000 ALTER TABLE `product_img` DISABLE KEYS */;
INSERT INTO `product_img` VALUES
('ps001',1,'女裝百褶拼接寬鬆上衣','products/clothes/shorts/ps001_01_01.jpg\n','2025-03-17 18:30:34','2025-03-19 03:35:05'),
('ps001',2,'女裝百褶拼接寬鬆上衣','products/clothes/shorts/ps001_01_02.jpg','2025-03-17 18:30:34','2025-03-19 03:35:10'),
('ps001',3,'女裝百褶拼接寬鬆上衣','products/clothes/shorts/ps001_03_01.jpg','2025-03-17 18:30:34','2025-03-19 03:35:14'),
('ps001',4,'女裝百褶拼接寬鬆上衣','products/clothes/shorts/ps001_03_02.jpg','2025-03-17 18:30:34','2025-03-19 03:35:18'),
('ps002',1,'女裝不對稱異素材上衣','products/clothes/shorts/ps002_01_01.jpg','2025-03-17 18:30:34','2025-03-19 03:35:26'),
('ps002',2,'女裝不對稱異素材上衣','products/clothes/shorts/ps002_01_02.jpg','2025-03-17 18:30:34','2025-03-19 03:35:31'),
('ps002',3,'女裝不對稱異素材上衣','products/clothes/shorts/ps002_03_01.jpg','2025-03-17 18:30:34','2025-03-19 03:35:35'),
('ps002',4,'女裝不對稱異素材上衣','products/clothes/shorts/ps002_03_02.jpg','2025-03-17 18:30:34','2025-03-19 03:35:39'),
('pl009',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-17 18:30:34','2025-03-17 18:30:34'),
('pl009',NULL,NULL,'products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-17 18:30:34','2025-03-17 18:30:34'),
('pl009',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-17 18:30:34','2025-03-17 18:30:34'),
('ps003',1,'蕾絲滾邊短袖T恤','products/clothes/shorts/ps003_01_01.jpg','2025-03-18 03:13:28','2025-03-19 03:35:58'),
('ps003',2,'蕾絲滾邊短袖T恤','products/clothes/shorts/ps003_01_02.jpg','2025-03-18 03:13:28','2025-03-19 03:36:05'),
('ps003',3,'蕾絲滾邊短袖T恤','products/clothes/shorts/ps003_03_01.jpg','2025-03-18 03:13:28','2025-03-19 03:36:11'),
('ps003',4,'蕾絲滾邊短袖T恤','products/clothes/shorts/ps003_03_02.jpg','2025-03-18 03:13:28','2025-03-19 03:36:17'),
('ps004',1,'網紗層次荷葉袖上衣','products/clothes/shorts/ps004_01_01.jpg','2025-03-18 03:15:12','2025-03-19 03:36:24'),
('ps004',2,'網紗層次荷葉袖上衣','products/clothes/shorts/ps004_01_02.jpg','2025-03-18 03:15:12','2025-03-19 03:36:33'),
('ps004',3,'網紗層次荷葉袖上衣','products/clothes/shorts/ps004_03_01.jpg','2025-03-18 03:15:12','2025-03-19 03:36:40'),
('ps004',4,'網紗層次荷葉袖上衣','products/clothes/shorts/ps004_03_02.jpg','2025-03-18 03:15:12','2025-03-19 03:36:47'),
('ps005',1,'薄紗層次無袖背心','products/clothes/shorts/ps005_01_01.jpg','2025-03-18 03:15:12','2025-03-19 03:36:53'),
('ps005',2,'薄紗層次無袖背心','products/clothes/shorts/ps005_01_02.jpg','2025-03-18 03:15:12','2025-03-19 03:37:04'),
('ps005',3,'薄紗層次無袖背心','products/clothes/shorts/ps005_03_01.jpg','2025-03-18 03:15:12','2025-03-19 03:37:28'),
('ps005',4,'薄紗層次無袖背心','products/clothes/shorts/ps005_03_02.jpg','2025-03-18 03:15:12','2025-03-19 03:37:44'),
('ps006',1,'微高領拉克蘭袖T恤','products/clothes/shorts/ps006_01_01.jpg','2025-03-18 03:15:12','2025-03-19 03:37:52'),
('ps006',2,'微高領拉克蘭袖T恤','products/clothes/shorts/ps006_01_02.jpg','2025-03-18 03:15:12','2025-03-19 03:38:04'),
('ps006',3,'微高領拉克蘭袖T恤','products/clothes/shorts/ps006_03_01.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps006',4,'微高領拉克蘭袖T恤','products/clothes/shorts/ps006_03_02.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps007',1,'扭轉袖上衣','products/clothes/shorts/ps007_01_01.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps007',2,'扭轉袖上衣','products/clothes/shorts/ps007_01_02.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps007',3,'扭轉袖上衣','products/clothes/shorts/ps007_03_01.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps007',4,'扭轉袖上衣','products/clothes/shorts/ps007_03_02.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps008',1,'蕾絲拼接上衣','products/clothes/shorts/ps008_01_01.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps008',2,'蕾絲拼接上衣','products/clothes/shorts/ps008_01_02.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps008',3,'蕾絲拼接上衣','products/clothes/shorts/ps008_03_01.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('ps008',4,'蕾絲拼接上衣','products/clothes/shorts/ps008_03_02.jpg','2025-03-18 03:15:12','2025-03-19 03:38:43'),
('pl001',1,'圈布打褶圓領上衣','products/clothes/longs/pl001_01_01.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl001',2,'圈布打褶圓領上衣','products/clothes/longs/pl001_01_02.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl001',3,'圈布打褶圓領上衣','products/clothes/longs/pl001_03_01.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl001',4,'圈布打褶圓領上衣','products/clothes/longs/pl001_03_02.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl002',1,'半拉鍊高領長袖','products/clothes/longs/pl002_01_01.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl002',2,'半拉鍊高領長袖','products/clothes/longs/pl002_01_02.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl002',3,'半拉鍊高領長袖','products/clothes/longs/pl002_03_01.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl002',4,'半拉鍊高領長袖','products/clothes/longs/pl002_03_02.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl003',1,'打褶拼接圓領襯衫','products/clothes/longs/pl003_01_01.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl003',2,'打褶拼接圓領襯衫','products/clothes/longs/pl003_01_02.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl003',3,'打褶拼接圓領襯衫','products/clothes/longs/pl003_03_01.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl003',4,'打褶拼接圓領襯衫','products/clothes/longs/pl003_03_02.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl004',1,'正面蕾絲上衣','products/clothes/longs/pl004_01_01.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl004',2,'正面蕾絲上衣','products/clothes/longs/pl004_01_02.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl004',3,'正面蕾絲上衣','products/clothes/longs/pl004_03_01.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pl004',4,'正面蕾絲上衣','products/clothes/longs/pl004_03_02.jpg','2025-03-18 03:17:40','2025-03-19 03:38:43'),
('pj001',1,'斜紋軟呢無領外套','products/clothes/jackets/pj001_01_01.jpg','2025-03-18 03:19:29','2025-03-19 03:38:43'),
('pj001',2,'斜紋軟呢無領外套','products/clothes/jackets/pj001_01_02.jpg','2025-03-18 03:19:29','2025-03-19 03:38:43'),
('pj001',3,'斜紋軟呢無領外套','products/clothes/jackets/pj001_03_01.jpg','2025-03-18 03:19:29','2025-03-19 03:38:43'),
('pj001',4,'斜紋軟呢無領外套','products/clothes/jackets/pj001_03_02.jpg','2025-03-18 03:19:29','2025-03-19 03:38:43'),
('pj002',1,'羊羔絨外套','products/clothes/jackets/pj002_01_01.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj002',2,'羊羔絨外套','products/clothes/jackets/pj002_01_02.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj002',3,'羊羔絨外套','products/clothes/jackets/pj002_03_01.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj002',4,'羊羔絨外套','products/clothes/jackets/pj002_03_02.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj003',1,'粗花呢圓領外套','products/clothes/jackets/pj003_01_01.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj003',2,'粗花呢圓領外套','products/clothes/jackets/pj003_01_02.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj003',3,'粗花呢圓領外套','products/clothes/jackets/pj003_03_01.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj003',4,'粗花呢圓領外套','products/clothes/jackets/pj003_03_02.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj004',1,'腰部抽繩外套','products/clothes/jackets/pj004_01_01.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj004',2,'腰部抽繩外套','products/clothes/jackets/pj004_01_02.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj004',3,'腰部抽繩外套','products/clothes/jackets/pj004_03_01.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj004',4,'腰部抽繩外套','products/clothes/jackets/pj004_03_02.jpg','2025-03-18 03:19:30','2025-03-19 03:38:43'),
('pj005',1,'雙釦短版西裝外套','products/clothes/jackets/pj005_01_01.jpg','2025-03-18 03:19:52','2025-03-19 03:38:43'),
('pj005',2,'雙釦短版西裝外套','products/clothes/jackets/pj005_01_02.jpg','2025-03-18 03:19:52','2025-03-19 03:38:43'),
('pj005',3,'雙釦短版西裝外套','products/clothes/jackets/pj005_03_01.jpg','2025-03-18 03:19:52','2025-03-19 03:38:43'),
('pj005',4,'雙釦短版西裝外套','products/clothes/jackets/pj005_03_02.jpg','2025-03-18 03:19:52','2025-03-19 03:38:43'),
('pj006',1,'NRP羽絨外套','products/clothes/jackets/pj006_01_01.jpg','2025-03-18 03:19:52','2025-03-19 03:38:43'),
('pj006',2,'NRP羽絨外套','products/clothes/jackets/pj006_01_02.jpg','2025-03-18 03:19:52','2025-03-19 03:38:43'),
('pj006',3,'NRP羽絨外套','products/clothes/jackets/pj006_03_01.jpg','2025-03-18 03:19:52','2025-03-19 03:38:43'),
('pj006',4,'NRP羽絨外套','products/clothes/jackets/pj006_03_02.jpg','2025-03-18 03:19:52','2025-03-19 03:38:43'),
('ps009',1,'緹花無領上衣','products/clothes/shorts/ps009_01_01.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps009',2,'緹花無領上衣','products/clothes/shorts/ps009_01_02.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps009',3,'緹花無領上衣','products/clothes/shorts/ps009_03_01.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps009',4,'緹花無領上衣','products/clothes/shorts/ps009_03_02.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps010',1,'高領打褶無袖上衣','products/clothes/shorts/ps010_01_01.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps010',2,'高領打褶無袖上衣','products/clothes/shorts/ps010_01_02.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps010',3,'高領打褶無袖上衣','products/clothes/shorts/ps010_03_01.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps010',4,'高領打褶無袖上衣','products/clothes/shorts/ps010_03_02.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps011',1,'異素材寬鬆上衣','products/clothes/shorts/ps011_01_01.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps011',2,'異素材寬鬆上衣','products/clothes/shorts/ps011_01_02.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps011',3,'異素材寬鬆上衣','products/clothes/shorts/ps011_03_01.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps011',4,'異素材寬鬆上衣','products/clothes/shorts/ps011_03_02.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps012',1,'背部異素材上衣','products/clothes/shorts/ps012_01_01.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps012',2,'背部異素材上衣','products/clothes/shorts/ps012_01_02.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps012',3,'背部異素材上衣','products/clothes/shorts/ps012_03_01.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps012',4,'背部異素材上衣','products/clothes/shorts/ps012_03_02.jpg','2025-03-18 07:08:51','2025-03-19 03:38:43'),
('ps016',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:21:48','2025-03-18 08:21:48'),
('ps016',NULL,NULL,'products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-18 08:21:48','2025-03-18 08:21:48'),
('ps016',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:21:48','2025-03-18 08:21:48'),
('ps017',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:23:02','2025-03-18 08:23:02'),
('ps017',NULL,NULL,'products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-18 08:23:02','2025-03-18 08:23:02'),
('ps017',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:23:02','2025-03-18 08:23:02'),
('ps018',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:27:46','2025-03-18 08:27:46'),
('ps018',NULL,NULL,'products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-18 08:27:46','2025-03-18 08:27:46'),
('ps018',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:27:46','2025-03-18 08:27:46'),
('ps019',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:30:03','2025-03-18 08:30:03'),
('ps019',NULL,NULL,'products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-18 08:30:03','2025-03-18 08:30:03'),
('ps019',NULL,NULL,'products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:30:03','2025-03-18 08:30:03'),
('ps020',1,'測試商品','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:31:52','2025-03-18 08:31:52'),
('ps020',2,'測試商品','products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-18 08:31:52','2025-03-18 08:31:52'),
('ps020',3,'測試商品','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 08:31:52','2025-03-18 08:31:52'),
('ps021',1,'測試商品','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 09:34:11','2025-03-18 09:34:11'),
('ps021',2,'測試商品','products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-18 09:34:11','2025-03-18 09:34:11'),
('ps021',3,'測試商品','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 09:34:11','2025-03-18 09:34:11'),
('ps022',1,'測試商品','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 09:46:18','2025-03-18 09:46:18'),
('ps022',2,'測試商品','products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-18 09:46:18','2025-03-18 09:46:18'),
('ps022',3,'測試商品','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 09:46:18','2025-03-18 09:46:18'),
('ps024',1,'測試商品','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 10:53:03','2025-03-18 10:53:03'),
('ps024',2,'測試商品','products/服飾/短袖/測試商品/pexels-wearelivingart-8960867.jpg','2025-03-18 10:53:03','2025-03-18 10:53:03'),
('ps024',3,'測試商品','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 10:53:03','2025-03-18 10:53:03'),
('ps028',1,'3.18','products/服飾/短袖/3.18/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 13:02:28','2025-03-18 13:02:28'),
('ps029',1,'3.18','products/服飾/短袖/3.18/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-18 13:03:59','2025-03-18 13:03:59'),
('ps030',1,'異世界2000','products/服飾/短袖/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-19 01:25:42','2025-03-19 01:25:42'),
('pl010',1,'異世界2000','products/服飾/長袖/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-19 01:26:34','2025-03-19 01:26:34'),
('ps031',1,'異世界2000','products/服飾/短袖/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-19 01:40:39','2025-03-19 01:40:39'),
('pa011',1,'異世界2000','products/飾品/異世界2000/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-19 01:50:06','2025-03-19 01:50:06'),
('pa012',1,'異世界2000','products/飾品/異世界2000/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-19 01:52:50','2025-03-19 01:52:50'),
('ps032',1,'3.19','products/服飾/短袖/3.19/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-19 02:36:43','2025-03-19 02:36:43'),
('pa013',1,'3.19測試','products/飾品/異世界2000/3.19測試/224892b2e43c31d5666f73bc6116d0e5719293d2.png','2025-03-19 06:59:22','2025-03-19 06:59:22'),
('pa013',2,'3.19測試','products/飾品/異世界2000/3.19測試/pexels-wearelivingart-8960867.jpg','2025-03-19 06:59:22','2025-03-19 06:59:22'),
('pa014',1,'3.20','products/飾品/水晶晶系列/3.20/224892b2e43c31d5666f73bc6116d0e5719293d2.png','2025-03-20 01:49:20','2025-03-20 01:49:20'),
('ps033',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:33','2025-03-20 08:00:33'),
('ps033',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:33','2025-03-20 08:00:33'),
('ps034',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:34','2025-03-20 08:00:34'),
('ps034',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:34','2025-03-20 08:00:34'),
('ps035',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:36','2025-03-20 08:00:36'),
('ps035',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:36','2025-03-20 08:00:36'),
('ps036',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:37','2025-03-20 08:00:37'),
('ps036',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:37','2025-03-20 08:00:37'),
('ps037',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:38','2025-03-20 08:00:38'),
('ps037',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:38','2025-03-20 08:00:38'),
('ps038',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:39','2025-03-20 08:00:39'),
('ps038',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:39','2025-03-20 08:00:39'),
('ps039',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:40','2025-03-20 08:00:40'),
('ps039',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:41','2025-03-20 08:00:41'),
('ps040',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:42','2025-03-20 08:00:42'),
('ps040',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:42','2025-03-20 08:00:42'),
('ps041',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:43','2025-03-20 08:00:43'),
('ps041',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:43','2025-03-20 08:00:43'),
('ps042',1,'3.20.final','products/服飾/短袖/3.20.final/thumb-210617_01.png','2025-03-20 08:00:44','2025-03-20 08:00:44'),
('ps042',2,'3.20.final','products/服飾/短袖/3.20.final/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 08:00:44','2025-03-20 08:00:44'),
('ps043',1,'3.20.17.26','products/服飾/短袖/3.20.17.26/224892b2e43c31d5666f73bc6116d0e5719293d2.png','2025-03-20 09:27:38','2025-03-20 09:27:38'),
('ps043',2,'3.20.17.26','products/服飾/短袖/3.20.17.26/thumb-210617_01.png','2025-03-20 09:27:38','2025-03-20 09:27:38'),
('ps044',1,'test','products/服飾/短袖/test/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 09:30:39','2025-03-20 09:30:39'),
('ps044',2,'test','products/服飾/短袖/test/224892b2e43c31d5666f73bc6116d0e5719293d2.png','2025-03-20 09:30:39','2025-03-20 09:30:39'),
('ps045',1,'測試商品規格','products/服飾/短袖/測試商品規格/224892b2e43c31d5666f73bc6116d0e5719293d2.png','2025-03-20 10:28:51','2025-03-20 10:28:51'),
('ps045',2,'測試商品規格','products/服飾/短袖/測試商品規格/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 10:28:51','2025-03-20 10:28:51'),
('ps047',1,'測試商品spec_id','products/服飾/短袖/測試商品spec_id/224892b2e43c31d5666f73bc6116d0e5719293d2.png','2025-03-20 12:12:25','2025-03-20 12:12:25'),
('ps047',2,'測試商品spec_id','products/服飾/短袖/測試商品spec_id/pexels-wearelivingart-8960867.jpg','2025-03-20 12:12:25','2025-03-20 12:12:25'),
('ps048',1,'測試商品規格spec_id','products/服飾/短袖/測試商品規格spec_id/thumb-210617_01.png','2025-03-20 12:15:28','2025-03-20 12:15:28'),
('ps048',2,'測試商品規格spec_id','products/服飾/短袖/測試商品規格spec_id/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','2025-03-20 12:15:28','2025-03-20 12:15:28');
/*!40000 ALTER TABLE `product_img` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_information`
--

DROP TABLE IF EXISTS `product_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_information` (
  `product_id` varchar(100) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `fk_product_information` (`product_id`),
  CONSTRAINT `fk_product_information` FOREIGN KEY (`product_id`) REFERENCES `product_main` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_information`
--

LOCK TABLES `product_information` WRITE;
/*!40000 ALTER TABLE `product_information` DISABLE KEYS */;
INSERT INTO `product_information` VALUES
('pa001','材質','純銀92.5% (925 silver)｜天然綠松石(natural turquoise)','2025-03-17 18:28:13','2025-03-18 03:50:28'),
('pa001','規格','公制圍10號 (Metric circumference No.10 )','2025-03-17 18:28:13','2025-03-18 03:50:28'),
('pa001','其他補充','飾品皆手工製作，誤差值 ±0.5公分皆為正常範圍','2025-03-17 18:28:13','2025-03-18 03:50:28'),
('pa001','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-17 18:28:13','2025-03-18 03:50:28'),
('pa002','材質','純銀92.5% (925 silver)｜蛋白石(natural opal)','2025-03-17 18:28:13','2025-03-18 03:50:28'),
('pa002','規格','公制圍10號 (Metric circumference No.10 )','2025-03-17 18:28:13','2025-03-18 03:50:28'),
('pa002','其他補充','飾品皆手工製作，誤差值 ±0.5公分皆為正常範圍','2025-03-17 18:28:13','2025-03-18 03:50:28'),
('pa002','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-17 18:28:13','2025-03-18 03:50:28'),
('pl009','材質','棉質','2025-03-17 18:28:13','2025-03-17 18:28:13'),
('pl009','規格','L/XL','2025-03-17 18:28:13','2025-03-17 18:28:13'),
('pl009','出貨說明',NULL,'2025-03-17 18:28:13','2025-03-17 18:28:13'),
('pl009','其他補充','七天內發貨','2025-03-17 18:28:13','2025-03-17 18:28:13'),
('pa003','材質','純銀92.5% (925 silver)｜蛋白石(natural opal)','2025-03-18 03:42:24','2025-03-18 03:50:28'),
('pa003','規格','公制圍10號 (Metric circumference No.10)','2025-03-18 03:42:24','2025-03-18 03:50:28'),
('pa003','其他補充','飾品皆手工製作，誤差值 ±0.5公分皆為正常範圍','2025-03-18 03:42:24','2025-03-18 03:50:28'),
('pa003','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:42:24','2025-03-18 03:50:28'),
('pa004','材質','淡水珍珠｜日本玻璃珠｜日本米珠','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa004','規格','Free size','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa004','其他補充','飾品皆手工製作，誤差值 ±0.5公分皆為正常範圍','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa004','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa005','材質','淡水珍珠｜日本玻璃珠｜日本米珠','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa005','規格','Free size','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa005','其他補充','飾品皆手工製作，誤差值 ±0.5公分皆為正常範圍','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa005','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa006','材質','淡水珍珠｜日本玻璃珠｜日本米珠','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa006','規格','Free size','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa006','其他補充','飾品皆手工製作，誤差值 ±0.5公分皆為正常範圍','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa006','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa007','材質','淡水珍珠｜日本玻璃珠｜日本米珠','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa007','規格','Free size','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa007','其他補充','飾品皆手工製作，誤差值 ±0.5公分皆為正常範圍','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa007','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa008','材質','淡水珍珠｜日本玻璃珠｜日本米珠','2025-03-18 03:47:25','2025-03-18 03:50:28'),
('pa008','規格','Free size','2025-03-18 03:47:26','2025-03-18 03:50:28'),
('pa008','其他補充','飾品皆手工製作，誤差值 ±0.5公分皆為正常範圍','2025-03-18 03:47:26','2025-03-18 03:50:28'),
('pa008','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:47:26','2025-03-18 03:50:28'),
('ps001','材質','聚酯纖維｜棉','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps001','規格','S | M | L','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps001','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps001','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps002','材質','聚酯纖維｜棉','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps002','規格','S | M | L','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps002','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps002','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps003','材質','聚酯纖維｜棉','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps003','規格','S | M | L','2025-03-18 03:56:52','2025-03-18 03:56:52'),
('ps003','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps003','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps004','材質','聚酯纖維｜棉','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps004','規格','S | M | L','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps004','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps004','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps005','材質','聚酯纖維｜棉','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps005','規格','S | M | L','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps005','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps005','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps008','材質','聚酯纖維｜棉','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps008','規格','S | M | L','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps008','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps008','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps007','材質','聚酯纖維｜棉','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps007','規格','S | M | L','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps007','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps007','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps008','材質','聚酯纖維｜棉','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps008','規格','S | M | L','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps008','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('ps008','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:56:53','2025-03-18 03:56:53'),
('pl001','材質','聚酯纖維｜棉','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl001','規格','S | M | L','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl001','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl001','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl002','材質','聚酯纖維｜棉','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl002','規格','S | M | L','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl002','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl002','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl003','材質','聚酯纖維｜棉','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl003','規格','S | M | L','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl003','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl003','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl004','材質','聚酯纖維｜棉','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl004','規格','S | M | L','2025-03-18 03:57:17','2025-03-18 03:57:17'),
('pl004','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl004','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl005','材質','聚酯纖維｜棉','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl005','規格','S | M | L','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl005','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl005','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl008','材質','聚酯纖維｜棉','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl008','規格','S | M | L','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl008','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl008','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl007','材質','聚酯纖維｜棉','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl007','規格','S | M | L','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl007','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl007','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl008','材質','聚酯纖維｜棉','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl008','規格','S | M | L','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl008','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pl008','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:18','2025-03-18 03:57:18'),
('pj001','材質','聚酯纖維｜棉','2025-03-18 03:57:22','2025-03-18 03:57:22'),
('pj001','規格','S | M | L','2025-03-18 03:57:22','2025-03-18 03:57:22'),
('pj001','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:22','2025-03-18 03:57:22'),
('pj001','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:22','2025-03-18 03:57:22'),
('pj002','材質','聚酯纖維｜棉','2025-03-18 03:57:22','2025-03-18 03:57:22'),
('pj002','規格','S | M | L','2025-03-18 03:57:22','2025-03-18 03:57:22'),
('pj002','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj002','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj003','材質','聚酯纖維｜棉','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj003','規格','S | M | L','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj003','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj003','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj004','材質','聚酯纖維｜棉','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj004','規格','S | M | L','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj004','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj004','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj005','材質','聚酯纖維｜棉','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj005','規格','S | M | L','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj005','其他補充','商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('pj005','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 03:57:23','2025-03-18 03:57:23'),
('ps009','材質','聚酯纖維｜棉','2025-03-18 07:11:42','2025-03-18 07:11:42'),
('ps009','規格','S | M | L','2025-03-18 07:11:42','2025-03-18 07:11:42'),
('ps009','其他補充','緹花無商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。領上衣','2025-03-18 07:11:42','2025-03-18 07:11:42'),
('ps009','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 07:11:42','2025-03-18 07:11:42'),
('ps010','材質','聚酯纖維｜棉','2025-03-18 07:11:51','2025-03-18 07:11:51'),
('ps010','規格','S | M | L','2025-03-18 07:11:51','2025-03-18 07:11:51'),
('ps010','其他補充','緹花無商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。領上衣','2025-03-18 07:11:51','2025-03-18 07:11:51'),
('ps010','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 07:11:51','2025-03-18 07:11:51'),
('ps011','材質','聚酯纖維｜棉','2025-03-18 07:11:53','2025-03-18 07:11:53'),
('ps011','規格','S | M | L','2025-03-18 07:11:53','2025-03-18 07:11:53'),
('ps011','其他補充','緹花無商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。領上衣','2025-03-18 07:11:53','2025-03-18 07:11:53'),
('ps011','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 07:11:53','2025-03-18 07:11:53'),
('ps012','材質','聚酯纖維｜棉','2025-03-18 07:11:55','2025-03-18 07:11:55'),
('ps012','規格','S | M | L','2025-03-18 07:11:55','2025-03-18 07:11:55'),
('ps012','其他補充','緹花無商品色澤會依據環境光源或個人的手機電腦螢幕顯示而有些許不同，如實際商品有色差之情況敬請見諒。領上衣','2025-03-18 07:11:55','2025-03-18 07:11:55'),
('ps012','出貨說明','預購商品出貨約21工作天(不含假日)，建議與現貨商品分開下單','2025-03-18 07:11:55','2025-03-18 07:11:55'),
('ps016','材質','棉質','2025-03-18 08:21:47','2025-03-18 08:21:47'),
('ps016','規格','L/XL','2025-03-18 08:21:47','2025-03-18 08:21:47'),
('ps016','出貨說明',NULL,'2025-03-18 08:21:47','2025-03-18 08:21:47'),
('ps016','其他補充','七天內發貨','2025-03-18 08:21:47','2025-03-18 08:21:47'),
('ps017','材質','棉質','2025-03-18 08:23:02','2025-03-18 08:23:02'),
('ps017','規格','L/XL','2025-03-18 08:23:02','2025-03-18 08:23:02'),
('ps017','出貨說明',NULL,'2025-03-18 08:23:02','2025-03-18 08:23:02'),
('ps017','其他補充','七天內發貨','2025-03-18 08:23:02','2025-03-18 08:23:02'),
('ps018','材質','棉質','2025-03-18 08:27:46','2025-03-18 08:27:46'),
('ps018','規格','L/XL','2025-03-18 08:27:46','2025-03-18 08:27:46'),
('ps018','出貨說明',NULL,'2025-03-18 08:27:46','2025-03-18 08:27:46'),
('ps018','其他補充','七天內發貨','2025-03-18 08:27:46','2025-03-18 08:27:46'),
('ps019','材質','棉質','2025-03-18 08:30:03','2025-03-18 08:30:03'),
('ps019','規格','L/XL','2025-03-18 08:30:03','2025-03-18 08:30:03'),
('ps019','出貨說明',NULL,'2025-03-18 08:30:03','2025-03-18 08:30:03'),
('ps019','其他補充','七天內發貨','2025-03-18 08:30:03','2025-03-18 08:30:03'),
('ps020','材質','棉質','2025-03-18 08:31:52','2025-03-18 08:31:52'),
('ps020','規格','L/XL','2025-03-18 08:31:52','2025-03-18 08:31:52'),
('ps020','出貨說明',NULL,'2025-03-18 08:31:52','2025-03-18 08:31:52'),
('ps020','其他補充','七天內發貨','2025-03-18 08:31:52','2025-03-18 08:31:52'),
('ps021','材質','棉質','2025-03-18 09:34:11','2025-03-18 09:34:11'),
('ps021','規格','L/XL','2025-03-18 09:34:11','2025-03-18 09:34:11'),
('ps021','出貨說明',NULL,'2025-03-18 09:34:11','2025-03-18 09:34:11'),
('ps021','其他補充','七天內發貨','2025-03-18 09:34:11','2025-03-18 09:34:11'),
('ps022','材質','棉質','2025-03-18 09:46:17','2025-03-18 09:46:17'),
('ps022','規格','L/XL','2025-03-18 09:46:17','2025-03-18 09:46:17'),
('ps022','出貨說明',NULL,'2025-03-18 09:46:17','2025-03-18 09:46:17'),
('ps022','其他補充','七天內發貨','2025-03-18 09:46:17','2025-03-18 09:46:17'),
('ps024','材質','棉質','2025-03-18 10:53:03','2025-03-18 10:53:03'),
('ps024','規格','L/XL','2025-03-18 10:53:03','2025-03-18 10:53:03'),
('ps024','出貨說明',NULL,'2025-03-18 10:53:03','2025-03-18 10:53:03'),
('ps024','其他補充','七天內發貨','2025-03-18 10:53:03','2025-03-18 10:53:03'),
('ps025','材質','2','2025-03-18 04:36:41','2025-03-18 04:36:41'),
('ps025','規格','3','2025-03-18 04:36:41','2025-03-18 04:36:41'),
('ps025','出貨說明','4','2025-03-18 04:36:42','2025-03-18 04:36:42'),
('ps025','其他補充','5','2025-03-18 04:36:42','2025-03-18 04:36:42'),
('ps026','材質','2','2025-03-18 04:40:31','2025-03-18 04:40:31'),
('ps026','規格','3','2025-03-18 04:40:31','2025-03-18 04:40:31'),
('ps026','出貨說明','4','2025-03-18 04:40:31','2025-03-18 04:40:31'),
('ps026','其他補充','5','2025-03-18 04:40:31','2025-03-18 04:40:31'),
('ps027','材質','2','2025-03-18 04:46:03','2025-03-18 04:46:03'),
('ps027','規格','1','2025-03-18 04:46:03','2025-03-18 04:46:03'),
('ps027','出貨說明','4','2025-03-18 04:46:03','2025-03-18 04:46:03'),
('ps027','其他補充','5','2025-03-18 04:46:03','2025-03-18 04:46:03'),
('ps028','材質','2','2025-03-18 05:02:29','2025-03-18 05:02:29'),
('ps028','規格','1','2025-03-18 05:02:29','2025-03-18 05:02:29'),
('ps028','出貨說明','4','2025-03-18 05:02:29','2025-03-18 05:02:29'),
('ps028','其他補充','5','2025-03-18 05:02:29','2025-03-18 05:02:29'),
('ps029','材質','2','2025-03-18 05:04:01','2025-03-18 05:04:01'),
('ps029','規格','1','2025-03-18 05:04:01','2025-03-18 05:04:01'),
('ps029','出貨說明','4','2025-03-18 05:04:01','2025-03-18 05:04:01'),
('ps029','其他補充','5','2025-03-18 05:04:01','2025-03-18 05:04:01'),
('ps030','材質','22','2025-03-18 17:25:42','2025-03-18 17:25:42'),
('ps030','規格','33','2025-03-18 17:25:42','2025-03-18 17:25:42'),
('ps030','出貨說明','44','2025-03-18 17:25:42','2025-03-18 17:25:42'),
('ps030','其他補充','55','2025-03-18 17:25:42','2025-03-18 17:25:42'),
('pl010','材質','22','2025-03-18 17:26:34','2025-03-18 17:26:34'),
('pl010','規格','33','2025-03-18 17:26:34','2025-03-18 17:26:34'),
('pl010','出貨說明','44','2025-03-18 17:26:34','2025-03-18 17:26:34'),
('pl010','其他補充','55','2025-03-18 17:26:34','2025-03-18 17:26:34'),
('ps031','材質','22','2025-03-18 17:40:39','2025-03-18 17:40:39'),
('ps031','規格','33','2025-03-18 17:40:39','2025-03-18 17:40:39'),
('ps031','出貨說明','44','2025-03-18 17:40:39','2025-03-18 17:40:39'),
('ps031','其他補充','55','2025-03-18 17:40:39','2025-03-18 17:40:39'),
('pa011','材質','22','2025-03-18 17:50:06','2025-03-18 17:50:06'),
('pa011','規格','33','2025-03-18 17:50:06','2025-03-18 17:50:06'),
('pa011','出貨說明','44','2025-03-18 17:50:06','2025-03-18 17:50:06'),
('pa011','其他補充','55','2025-03-18 17:50:06','2025-03-18 17:50:06'),
('pa012','材質','22','2025-03-18 17:52:51','2025-03-18 17:52:51'),
('pa012','規格','33','2025-03-18 17:52:51','2025-03-18 17:52:51'),
('pa012','出貨說明','44','2025-03-18 17:52:51','2025-03-18 17:52:51'),
('pa012','其他補充','55','2025-03-18 17:52:51','2025-03-18 17:52:51'),
('ps032','材質','323443','2025-03-18 18:36:44','2025-03-19 21:42:39'),
('ps032','規格','233443','2025-03-18 18:36:44','2025-03-19 21:42:40'),
('ps032','出貨說明','313467','2025-03-18 18:36:44','2025-03-19 21:42:40'),
('ps032','其他補充','233445','2025-03-18 18:36:44','2025-03-19 21:42:40'),
('pa013','材質','21','2025-03-18 22:59:23','2025-03-18 22:59:23'),
('pa013','規格','22','2025-03-18 22:59:23','2025-03-18 22:59:23'),
('pa013','出貨說明','33','2025-03-18 22:59:23','2025-03-18 22:59:23'),
('pa013','其他補充','44','2025-03-18 22:59:23','2025-03-18 22:59:23'),
('ps033','材質','321','2025-03-20 00:00:33','2025-03-20 00:00:33'),
('ps033','規格','123','2025-03-20 00:00:33','2025-03-20 00:00:33'),
('ps033','出貨說明','321','2025-03-20 00:00:33','2025-03-20 00:00:33'),
('ps033','其他補充','123','2025-03-20 00:00:33','2025-03-20 00:00:33'),
('ps034','材質','321','2025-03-20 00:00:34','2025-03-20 00:00:34'),
('ps034','規格','123','2025-03-20 00:00:34','2025-03-20 00:00:34'),
('ps034','出貨說明','321','2025-03-20 00:00:35','2025-03-20 00:00:35'),
('ps034','其他補充','123','2025-03-20 00:00:35','2025-03-20 00:00:35'),
('ps035','材質','321','2025-03-20 00:00:36','2025-03-20 00:00:36'),
('ps035','規格','123','2025-03-20 00:00:36','2025-03-20 00:00:36'),
('ps035','出貨說明','321','2025-03-20 00:00:36','2025-03-20 00:00:36'),
('ps035','其他補充','123','2025-03-20 00:00:36','2025-03-20 00:00:36'),
('ps036','材質','321','2025-03-20 00:00:37','2025-03-20 00:00:37'),
('ps036','規格','123','2025-03-20 00:00:37','2025-03-20 00:00:37'),
('ps036','出貨說明','321','2025-03-20 00:00:37','2025-03-20 00:00:37'),
('ps036','其他補充','123','2025-03-20 00:00:37','2025-03-20 00:00:37'),
('ps037','材質','321','2025-03-20 00:00:38','2025-03-20 00:00:38'),
('ps037','規格','123','2025-03-20 00:00:38','2025-03-20 00:00:38'),
('ps037','出貨說明','321','2025-03-20 00:00:38','2025-03-20 00:00:38'),
('ps037','其他補充','123','2025-03-20 00:00:38','2025-03-20 00:00:38'),
('ps038','材質','321','2025-03-20 00:00:39','2025-03-20 00:00:39'),
('ps038','規格','123','2025-03-20 00:00:39','2025-03-20 00:00:39'),
('ps038','出貨說明','321','2025-03-20 00:00:39','2025-03-20 00:00:39'),
('ps038','其他補充','123','2025-03-20 00:00:40','2025-03-20 00:00:40'),
('ps039','材質','321','2025-03-20 00:00:41','2025-03-20 00:00:41'),
('ps039','規格','123','2025-03-20 00:00:41','2025-03-20 00:00:41'),
('ps039','出貨說明','321','2025-03-20 00:00:41','2025-03-20 00:00:41'),
('ps039','其他補充','123','2025-03-20 00:00:41','2025-03-20 00:00:41'),
('ps040','材質','321','2025-03-20 00:00:42','2025-03-20 00:00:42'),
('ps040','規格','123','2025-03-20 00:00:42','2025-03-20 00:00:42'),
('ps040','出貨說明','321','2025-03-20 00:00:42','2025-03-20 00:00:42'),
('ps040','其他補充','123','2025-03-20 00:00:42','2025-03-20 00:00:42'),
('ps041','材質','321','2025-03-20 00:00:43','2025-03-20 00:00:43'),
('ps041','規格','123','2025-03-20 00:00:43','2025-03-20 00:00:43'),
('ps041','出貨說明','321','2025-03-20 00:00:43','2025-03-20 00:00:43'),
('ps041','其他補充','123','2025-03-20 00:00:43','2025-03-20 00:00:43'),
('ps042','材質','321','2025-03-20 00:00:44','2025-03-20 00:00:44'),
('ps042','規格','123','2025-03-20 00:00:44','2025-03-20 00:00:44'),
('ps042','出貨說明','321','2025-03-20 00:00:44','2025-03-20 00:00:44'),
('ps042','其他補充','123','2025-03-20 00:00:44','2025-03-20 00:00:44'),
('ps043','材質','33','2025-03-20 01:27:38','2025-03-20 01:27:38'),
('ps043','規格','44','2025-03-20 01:27:38','2025-03-20 01:27:38'),
('ps043','出貨說明','55','2025-03-20 01:27:38','2025-03-20 01:27:38'),
('ps043','其他補充','66','2025-03-20 01:27:38','2025-03-20 01:27:38'),
('ps044','材質','32','2025-03-20 01:30:39','2025-03-20 01:30:39'),
('ps044','規格','12','2025-03-20 01:30:39','2025-03-20 01:30:39'),
('ps044','出貨說明','31','2025-03-20 01:30:40','2025-03-20 01:30:40'),
('ps044','其他補充','12','2025-03-20 01:30:40','2025-03-20 01:30:40'),
('ps046','材質',NULL,'2025-03-20 02:29:20','2025-03-20 02:29:20'),
('ps046','規格',NULL,'2025-03-20 02:29:20','2025-03-20 02:29:20'),
('ps046','出貨說明',NULL,'2025-03-20 02:29:20','2025-03-20 02:29:20'),
('ps046','其他補充',NULL,'2025-03-20 02:29:20','2025-03-20 02:29:20'),
('ps047','材質','213','2025-03-20 04:12:24','2025-03-20 04:12:24'),
('ps047','規格','123','2025-03-20 04:12:25','2025-03-20 04:12:25'),
('ps047','出貨說明','514','2025-03-20 04:12:25','2025-03-20 04:12:25'),
('ps047','其他補充','241','2025-03-20 04:12:25','2025-03-20 04:12:25'),
('pa014','材質','21','2025-03-20 05:23:53','2025-03-20 05:23:53'),
('pa014','規格','23','2025-03-20 05:23:53','2025-03-20 05:23:53'),
('pa014','出貨說明','34','2025-03-20 05:23:53','2025-03-20 05:23:53'),
('pa014','其他補充','44','2025-03-20 05:23:53','2025-03-20 05:23:53'),
('ps048','材質','123','2025-03-20 17:02:36','2025-03-20 17:02:36'),
('ps048','規格','452','2025-03-20 17:02:36','2025-03-20 17:02:36'),
('ps048','出貨說明','231','2025-03-20 17:02:36','2025-03-20 17:02:36'),
('ps048','其他補充','312','2025-03-20 17:02:36','2025-03-20 17:02:36'),
('ps045','材質','000','2025-03-20 17:36:14','2025-03-20 17:36:14'),
('ps045','規格','0000','2025-03-20 17:36:14','2025-03-20 17:36:14'),
('ps045','出貨說明','00000','2025-03-20 17:36:14','2025-03-20 17:36:14'),
('ps045','其他補充','000000','2025-03-20 17:36:14','2025-03-20 17:36:14');
/*!40000 ALTER TABLE `product_information` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_main`
--

DROP TABLE IF EXISTS `product_main`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_main` (
  `product_id` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_price` int(11) DEFAULT NULL,
  `product_description` varchar(255) DEFAULT NULL,
  `product_img` varchar(255) DEFAULT NULL,
  `product_status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`product_id`),
  KEY `fk_product_main` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_main`
--

LOCK TABLES `product_main` WRITE;
/*!40000 ALTER TABLE `product_main` DISABLE KEYS */;
INSERT INTO `product_main` VALUES
('pa001',201,'Navajo 綠松石十字星戒',5980,'925 silver | natural turquoise | metric circumference No.10（公制圍10號）','products/accessories/pa001_00_01.jpg',NULL,'2025-03-10 02:44:53','2025-03-19 03:39:30'),
('pa002',201,'Navajo 蛋白石鋼印戒',4590,'925 silver | natural opal | metric circumference No.10（公制圍10號）','products/accessories/pa002_00_01.jpg',NULL,'2025-03-10 03:42:49','2025-03-19 03:39:30'),
('pa003',201,'Opal 蛋白石臘雕純銀戒',4350,'925 silver | natural opal | metric circumference No.10（公制圍10號）','products/accessories/pa003_00_01.jpg',NULL,'2025-03-10 03:47:06','2025-03-19 03:39:30'),
('pa004',201,'水晶晶戒指（藍）',1590,'淡水珍珠｜日本玻璃珠｜日本米珠｜Free size','products/accessories/pa004_00_01.jpg',NULL,'2025-03-10 03:54:00','2025-03-19 03:39:30'),
('pa005',201,'水晶晶戒指（粉）',1490,'淡水珍珠｜日本玻璃珠｜日本米珠｜Free size','products/accessories/pa005_00_01.jpg',NULL,'2025-03-17 15:08:21','2025-03-19 03:39:30'),
('pa006',201,'水晶晶戒指（粉黃）',1490,'淡水珍珠｜日本玻璃珠｜日本米珠｜Free size','products/accessories/pa006_00_01.jpg',NULL,'2025-03-17 15:10:09','2025-03-19 03:39:30'),
('pa007',201,'水晶晶戒指（亮黃）',1490,'淡水珍珠｜日本玻璃珠｜日本米珠｜Free size','products/accessories/pa007_00_01.jpg',NULL,'2025-03-17 15:11:41','2025-03-19 03:39:30'),
('pa008',201,'水晶晶耳環 耳針式',1590,'淡水珍珠｜日本玻璃珠｜日本米珠｜Free size','products/accessories/pa008_00_01.jpg',NULL,'2025-03-17 15:12:55','2025-03-19 03:39:30'),
('pa009',201,'泡泡耳環 耳針式',1290,'淡水珍珠｜日本玻璃珠｜日本米珠｜Free size','products/accessories/pa009_00_01.jpg',NULL,'2025-03-17 15:14:00','2025-03-19 03:39:30'),
('pa010',201,'商品名稱',1000,'描述','圖片路徑','active','2025-03-19 01:29:33','2025-03-19 01:29:33'),
('pa011',201,'異世界2000',1980,'11','products/飾品/異世界2000/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-19 01:50:06','2025-03-19 01:50:06'),
('pa012',201,'異世界2000',1980,'11','products/飾品/異世界2000/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-19 01:52:50','2025-03-19 01:52:50'),
('pa013',201,'3.19測試',200,'ㄉ11','products/飾品/異世界2000/3.19測試/224892b2e43c31d5666f73bc6116d0e5719293d2.png','active','2025-03-19 06:59:22','2025-03-19 06:59:22'),
('pa014',202,'3.20',1326,'41','products/飾品/水晶晶系列/3.20/224892b2e43c31d5666f73bc6116d0e5719293d2.png','active','2025-03-20 01:49:20','2025-03-20 05:23:52'),
('pj001',103,'斜紋軟呢無領外套',9900,'單色基底，橫向織線製成花式紗線，袖口採用開岔設計，無論作為休閒裝扮或正式裝扮都非常適合','products/clothes/jackets/pj001_01_01.jpg',NULL,'2025-03-15 07:43:02','2025-03-19 03:39:30'),
('pj002',103,'羊羔絨外套',3540,'以大鈕扣與大衣領為特徵的防寒外套，表面為羊羔絨材質，排扣設計，適合春秋季天氣微涼時的穿搭，展現俐落性格','products/clothes/jackets/pj002_02_01.jpg',NULL,'2025-03-15 07:48:00','2025-03-19 03:39:30'),
('pj003',103,'粗花呢圓領外套',5760,'經典剪裁，向後推得上袖設計，讓整體看起來更加支幹練，適合當作休閒或正式場合的小外套','products/clothes/jackets/pj003_03_01.jpg',NULL,'2025-03-15 07:50:08','2025-03-19 03:39:30'),
('pj004',103,'腰部抽繩外套',3540,'採用兼具適度應討和柔韌性的聚酯纖維，帶有些許光澤，腰部設有可調節設計，本身較薄，適合搭配更多層次的穿搭，例如圍巾、針織衫或是背心','products/clothes/jackets/pj004_03_01.jpg',NULL,'2025-03-15 07:51:56','2025-03-19 03:39:30'),
('pj005',103,'雙釦短版西裝外套',5500,'聚酯纖維與螺紋材質，下襬加入了不同步材料增加層次，既是經典的西裝造型，也適合作為休閒服裝','products/clothes/jackets/pj005_01_01.jpg',NULL,'2025-03-15 07:54:20','2025-03-19 03:39:30'),
('pj006',103,'NRP 羽絨外套',10600,'採用羽絨補丁設計，相當蓬鬆柔軟，內側抽繩設計可形塑腰線，防風效果佳，適合購入偏大的尺寸，在秋冬季節穿著','products/clothes/jackets/pj006_03_01.jpg',NULL,'2025-03-15 07:59:08','2025-03-19 03:39:30'),
('pl001',102,'毛圈布 打褶 圓領上衣',2940,'百搭單品，內刷毛使用毛圈布製成，下擺為寬版羅紋的短版設計','products/clothes/longs/pl001_03_01.jpg',NULL,'2025-03-10 06:50:42','2025-03-19 04:59:56'),
('pl002',102,'半拉鍊 高領長袖',2900,'衣襬的毛邊設計是亮點，兩側繩帶可以調整尺寸，拉緊後下擺會收攏成波浪狀','products/clothes/longs/pl002_01_01.jpg',NULL,'2025-03-10 06:55:21','2025-03-19 04:58:32'),
('pl003',102,'打褶 拼接 圓領襯衫',3540,'採用立領樣式作為領口亮點，從稍微收緊的腰線延伸出百褶，與休閒下身衣著非常搭','products/clothes/longs/pl003_03_01.jpg',NULL,'2025-03-10 07:02:02','2025-03-19 03:39:30'),
('pl004',102,'正面蕾絲上衣',1800,'優雅蕾絲花紋加上藏釦設計，展現清爽俐落的風格','products/clothes/longs/pl004_01_01.jpg',NULL,'2025-03-10 07:16:31','2025-03-19 03:39:30'),
('pl005',102,'緹花流蘇V領開襟衫',3780,'以斜紋緹花編織，表面採流蘇設計剪裁，打造亮點，推薦與長大衣搭配穿著','products/clothes/longs/pl005_03_01.jpg',NULL,'2025-03-17 16:13:54','2025-03-19 03:39:30'),
('pl006',102,'緹花流蘇V領開襟衫',1040,'採用反摺高領設計的上衣款式，主打簡約風格，推薦搭配飾品穿著','products/clothes/longs/pl006_01_01.jpg',NULL,'2025-03-17 16:16:27','2025-03-19 03:39:30'),
('pl007',102,'圓點緹花襯衫',6990,'圓點花紋加上緹花剪裁設計，展現華麗與優雅氣質，非常適合作為典禮等活動場合的穿著','products/clothes/longs/pl007_03_01.jpg',NULL,'2025-03-17 16:20:59','2025-03-19 03:39:30'),
('pl008',102,'緹花流蘇高領上衣',3540,'秋冬經典人氣針織衫，以斜線緹花編織，搭配流蘇設計','products/clothes/longs/pl008_01_01.jpg',NULL,'2025-03-17 17:15:49','2025-03-19 03:39:30'),
('pl009',102,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-17 08:15:05','2025-03-17 17:51:42'),
('pl010',102,'異世界2000',1980,'11','products/服飾/長袖/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-19 01:26:34','2025-03-19 01:26:34'),
('ps001',101,'百褶拼接寬鬆上衣',1640,'袖口與衣襬為雪紡百褶，版型寬鬆，簡約中帶有特色，適合搭配丹寧褲或西裝褲','products/clothes/shorts/ps001_03_01.jpg',NULL,'2025-03-10 01:44:29','2025-03-19 05:00:20'),
('ps002',101,'不對稱異素材上衣',1380,'異素材拼接搭配同色材質，下擺加入不對稱設計，不同角度看起來不盡相同的造型讓人愛不釋手','products/clothes/shorts/ps002_01_01.jpg',NULL,'2025-03-10 01:44:29','2025-03-19 05:00:41'),
('ps003',101,'蕾絲滾邊短袖T恤',2280,'具有彈性的合身剪裁，蕾絲與滾邊設計，具有HUBD風格的單品','products/clothes/shorts/ps003_03_01.jpg',NULL,'2025-03-18 00:52:28','2025-03-19 03:39:30'),
('ps004',101,'網紗層次荷葉袖上衣',2080,'在休閒無袖上衣外加了一層句透視感的網紗，製造出堆疊層次的T恤，上層網紗縫入縱向荷葉邊','products/clothes/shorts/ps004_01_01.jpg',NULL,'2025-03-18 00:56:53','2025-03-19 03:39:30'),
('ps005',101,'薄紗層次無袖背心',1490,'飄逸的荷葉邊袖是整體的穿搭亮點！俐落的剪裁版型，非常適合搭配吊帶褲、工裝裙或吊帶裙穿著','products/clothes/shorts/ps005_03_01.jpg',NULL,'2025-03-18 00:59:26','2025-03-19 03:39:30'),
('ps006',101,'微高領拉克蘭袖T恤',1040,'小高領設計，主打簡約風格，適合搭配西裝褲或單寧褲','products/clothes/shorts/ps006_01_01.jpg',NULL,'2025-03-18 01:01:05','2025-03-19 03:39:30'),
('ps007',101,'扭轉袖上衣',1048,'稍微有挺度的天竺棉材質，扭轉袖為其亮點，簡約中帶有不凡，適合搭配西裝褲穿著或長裙','products/clothes/shorts/ps007_03_01.jpg',NULL,'2025-03-18 01:04:32','2025-03-19 03:39:30'),
('ps008',101,'蕾絲拼接上衣',2360,'衣身為天竺棉材質，袖口部分使用彈力網狀物，透膚感式中，推薦與簡約的單寧褲或中長裙做搭配','products/clothes/shorts/ps008_01_01.jpg',NULL,'2025-03-18 01:06:55','2025-03-19 03:39:30'),
('ps009',101,'緹花無領上衣',1340,'前短後長的剪裁，麻花編織紋路設計，適合與寬褲或長裙做搭配','products/clothes/shorts/ps009_03_01.jpg',NULL,'2025-03-18 06:56:30','2025-03-19 03:39:30'),
('ps010',101,'高領打褶無袖上衣',1340,'簡約時尚，肩部加入打褶的箱型剪裁上衣，微高領設計','products/clothes/shorts/ps010_01_01.jpg',NULL,'2025-03-18 07:00:45','2025-03-19 03:39:30'),
('ps011',101,'異素材寬鬆上衣',1280,'結合異材質拼貼的上衣，下襬有收緊抽繩，推薦搭配丹寧褲或是長裙','products/clothes/shorts/ps011_03_01.jpg',NULL,'2025-03-18 07:03:11','2025-03-19 03:39:30'),
('ps012',101,'背部異素材上衣',1280,'異材質拼接，下襬前短後長，展現簡約時尚的風格','products/clothes/shorts/ps012_01_01.jpg',NULL,'2025-03-18 07:05:26','2025-03-19 03:39:30'),
('ps013',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 08:18:55','2025-03-18 08:18:55'),
('ps014',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 08:19:58','2025-03-18 08:19:58'),
('ps015',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 08:20:19','2025-03-18 08:20:19'),
('ps016',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 08:21:47','2025-03-18 08:21:47'),
('ps017',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 08:23:02','2025-03-18 08:23:02'),
('ps018',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 08:27:46','2025-03-18 08:27:46'),
('ps019',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 08:30:03','2025-03-18 08:30:03'),
('ps020',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 08:31:52','2025-03-18 08:31:52'),
('ps021',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 09:34:10','2025-03-18 09:34:10'),
('ps022',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 09:46:17','2025-03-18 09:46:17'),
('ps023',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 10:52:44','2025-03-18 10:52:44'),
('ps024',101,'測試商品',1500,'這是一件短袖','products/服飾/短袖/測試商品/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 10:53:02','2025-03-18 10:53:02'),
('ps025',101,'123',1183,'1',NULL,'active','2025-03-18 12:36:40','2025-03-18 12:36:40'),
('ps026',101,'123',1183,'1',NULL,'active','2025-03-18 12:40:30','2025-03-18 12:40:30'),
('ps027',101,'3.18',1184,'3',NULL,'active','2025-03-18 12:46:02','2025-03-18 12:46:02'),
('ps028',101,'3.18',1184,'3','products/服飾/短袖/3.18/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 13:02:27','2025-03-18 13:02:27'),
('ps029',101,'3.18',1184,'3','products/服飾/短袖/3.18/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-18 13:03:59','2025-03-18 13:03:59'),
('ps030',101,'異世界2000',1980,'11','products/服飾/短袖/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-19 01:25:42','2025-03-19 01:25:42'),
('ps031',101,'異世界2000',1980,'11','products/服飾/短袖/異世界2000/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-19 01:40:39','2025-03-19 01:40:39'),
('ps032',101,'3.19.20.2.4',4004,'12334555','products/服飾/短袖/3.19/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-19 02:36:43','2025-03-19 20:04:55'),
('ps033',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:32','2025-03-20 08:00:32'),
('ps034',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:34','2025-03-20 08:00:34'),
('ps035',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:35','2025-03-20 08:00:35'),
('ps036',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:36','2025-03-20 08:00:36'),
('ps037',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:37','2025-03-20 08:00:37'),
('ps038',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:39','2025-03-20 08:00:39'),
('ps039',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:40','2025-03-20 08:00:40'),
('ps040',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:41','2025-03-20 08:00:41'),
('ps041',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:42','2025-03-20 08:00:42'),
('ps042',101,'3.20.final',1186,'123','products/服飾/短袖/3.20.final/thumb-210617_01.png','active','2025-03-20 08:00:43','2025-03-20 08:00:43'),
('ps043',101,'3.20.17.26',1200,'12','products/服飾/短袖/3.20.17.26/224892b2e43c31d5666f73bc6116d0e5719293d2.png','active','2025-03-20 09:27:37','2025-03-20 09:27:37'),
('ps044',101,'test',1200,'12','products/服飾/短袖/test/cf47f9fac4ed3037ff2a8ea83204e32aff8fb5f3.png','active','2025-03-20 09:30:39','2025-03-20 09:30:39'),
('ps045',101,'測試商品規格test123',9999,'31','products/服飾/短袖/測試商品規格/224892b2e43c31d5666f73bc6116d0e5719293d2.png','active','2025-03-20 10:28:50','2025-03-20 17:36:13'),
('ps046',101,'測試商品規格',1199969,NULL,NULL,'inactive','2025-03-20 10:29:24','2025-03-20 17:35:27'),
('ps047',101,'測試商品spec_id',11963,NULL,'products/服飾/短袖/測試商品spec_id/224892b2e43c31d5666f73bc6116d0e5719293d2.png','inactive','2025-03-20 12:12:24','2025-03-20 22:34:28'),
('ps048',101,'測試商品規格spec_id',120000,NULL,'products/服飾/短袖/測試商品規格spec_id/thumb-210617_01.png','active','2025-03-20 12:15:27','2025-03-20 17:17:16');
/*!40000 ALTER TABLE `product_main` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_spec`
--

DROP TABLE IF EXISTS `product_spec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_spec` (
  `product_id` varchar(100) NOT NULL,
  `product_size` varchar(20) DEFAULT NULL,
  `product_color` varchar(20) DEFAULT NULL,
  `product_stock` int(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `spec_id` varchar(100) DEFAULT NULL,
  KEY `fk_product_spec_product` (`product_id`),
  KEY `spec_id` (`spec_id`),
  CONSTRAINT `fk_product_spec_product` FOREIGN KEY (`product_id`) REFERENCES `product_main` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_spec`
--

LOCK TABLES `product_spec` WRITE;
/*!40000 ALTER TABLE `product_spec` DISABLE KEYS */;
INSERT INTO `product_spec` VALUES
('pa001','null','null',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','1'),
('pa002','null','null',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','2'),
('pa003','null','null',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','3'),
('pa004','null','null',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','4'),
('pl001','S','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','5'),
('pl001','S','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','6'),
('pl001','S','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','7'),
('pl001','M','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','8'),
('pl001','M','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','9'),
('pl001','M','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','10'),
('pl001','L','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','11'),
('pl001','L','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','12'),
('pl001','L','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','13'),
('pl002','S','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','14'),
('pl002','S','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','15'),
('pl002','S','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','16'),
('pl002','M','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','17'),
('pl002','M','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','18'),
('pl002','M','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','19'),
('pl002','L','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','20'),
('pl002','L','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','21'),
('pl002','L','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','22'),
('pl003','S','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','23'),
('pl003','S','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','24'),
('pl003','S','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','25'),
('pl003','M','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','26'),
('pl003','M','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','27'),
('pl003','M','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','28'),
('pl003','L','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','29'),
('pl003','L','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','30'),
('pl003','L','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','31'),
('pl004','S','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','32'),
('pl004','S','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','33'),
('pl004','S','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','34'),
('pl004','M','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','35'),
('pl004','M','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','36'),
('pl004','M','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','37'),
('pl004','L','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','38'),
('pl004','L','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','39'),
('pl004','L','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','40'),
('ps001','S','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','41'),
('ps001','S','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','42'),
('ps001','S','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','43'),
('ps001','M','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','44'),
('ps001','M','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','45'),
('ps001','M','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','46'),
('ps001','L','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','47'),
('ps001','L','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','48'),
('ps001','L','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','49'),
('ps002','S','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','50'),
('ps002','S','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','51'),
('ps002','S','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','52'),
('ps002','M','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','53'),
('ps002','M','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','54'),
('ps002','M','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','55'),
('ps002','L','Black',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','56'),
('ps002','L','Grey',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','57'),
('ps002','L','White',100,'2025-03-17 18:34:06','2025-03-17 18:34:06','58'),
('pj001','S','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','59'),
('pj001','M','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','60'),
('pj001','L','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','61'),
('pj001','S','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','62'),
('pj001','M','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','63'),
('pj001','L','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','64'),
('pj002','S','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','65'),
('pj002','M','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','66'),
('pj002','L','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','67'),
('pj002','S','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','68'),
('pj002','M','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','69'),
('pj002','L','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','70'),
('pj003','S','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','71'),
('pj003','M','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','72'),
('pj003','L','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','73'),
('pj003','S','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','74'),
('pj003','M','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','75'),
('pj003','L','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','76'),
('pj004','S','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','77'),
('pj004','M','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','78'),
('pj004','L','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','79'),
('pj004','S','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','80'),
('pj004','M','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','81'),
('pj004','L','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','82'),
('pj005','S','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','83'),
('pj005','M','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','84'),
('pj005','L','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','85'),
('pj005','S','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','86'),
('pj005','M','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','87'),
('pj005','L','Grey',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','88'),
('pj006','S','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','89'),
('pj006','M','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','90'),
('pj006','L','Black',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','91'),
('pj006','S','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','92'),
('pj006','M','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','93'),
('pj006','L','White',20,'2025-03-17 18:34:06','2025-03-17 18:34:06','94'),
('pl009','L','black',10,'2025-03-17 18:34:06','2025-03-17 18:34:06','95'),
('pl005','S','Black',20,NULL,NULL,'96'),
('pl005','M','Black',20,NULL,NULL,'97'),
('pl005','L','Black',20,NULL,NULL,'98'),
('pl005','S','Grey',20,NULL,NULL,'99'),
('pl005','M','Grey',20,NULL,NULL,'100'),
('pl005','L','Grey',20,NULL,NULL,'101'),
('pl005','S','White',20,NULL,NULL,'102'),
('pl005','M','White',20,NULL,NULL,'103'),
('pl005','L','White',20,NULL,NULL,'104'),
('pl006','S','Black',20,NULL,NULL,'105'),
('pl006','M','Black',20,NULL,NULL,'106'),
('pl006','L','Black',20,NULL,NULL,'107'),
('pl006','S','Grey',20,NULL,NULL,'108'),
('pl006','M','Grey',20,NULL,NULL,'109'),
('pl006','L','Grey',20,NULL,NULL,'110'),
('pl006','S','White',20,NULL,NULL,'111'),
('pl006','M','White',20,NULL,NULL,'112'),
('pl006','L','White',20,NULL,NULL,'113'),
('pl007','S','Black',20,NULL,NULL,'114'),
('pl007','M','Black',20,NULL,NULL,'115'),
('pl007','L','Black',20,NULL,NULL,'116'),
('pl007','S','Grey',20,NULL,NULL,'117'),
('pl007','M','Grey',20,NULL,NULL,'118'),
('pl007','L','Grey',20,NULL,NULL,'119'),
('pl007','S','White',20,NULL,NULL,'120'),
('pl007','M','White',20,NULL,NULL,'121'),
('pl007','L','White',20,NULL,NULL,'122'),
('pl008','S','Black',20,NULL,NULL,'123'),
('pl008','M','Black',20,NULL,NULL,'124'),
('pl008','L','Black',20,NULL,NULL,'125'),
('pl008','S','Grey',20,NULL,NULL,'126'),
('pl008','M','Grey',20,NULL,NULL,'127'),
('pl008','L','Grey',20,NULL,NULL,'128'),
('pl008','S','White',20,NULL,NULL,'129'),
('pl008','M','White',20,NULL,NULL,'130'),
('pl008','L','White',20,NULL,NULL,'131'),
('ps003','S','Black',20,NULL,NULL,'132'),
('ps003','M','Black',20,NULL,NULL,'133'),
('ps003','L','Black',20,NULL,NULL,'134'),
('ps003','S','Grey',20,NULL,NULL,'135'),
('ps003','M','Grey',20,NULL,NULL,'136'),
('ps003','L','Grey',20,NULL,NULL,'137'),
('ps003','S','White',20,NULL,NULL,'138'),
('ps003','M','White',20,NULL,NULL,'139'),
('ps003','L','White',20,NULL,NULL,'140'),
('ps004','S','Black',20,NULL,NULL,'141'),
('ps004','M','Black',20,NULL,NULL,'142'),
('ps004','L','Black',20,NULL,NULL,'143'),
('ps004','S','Grey',20,NULL,NULL,'144'),
('ps004','M','Grey',20,NULL,NULL,'145'),
('ps004','L','Grey',20,NULL,NULL,'146'),
('ps004','S','White',20,NULL,NULL,'147'),
('ps004','M','White',20,NULL,NULL,'148'),
('ps004','L','White',20,NULL,NULL,'149'),
('ps005','S','Black',20,NULL,NULL,'150'),
('ps005','M','Black',20,NULL,NULL,'151'),
('ps005','L','Black',20,NULL,NULL,'152'),
('ps005','S','Grey',20,NULL,NULL,'153'),
('ps005','M','Grey',20,NULL,NULL,'154'),
('ps005','L','Grey',20,NULL,NULL,'155'),
('ps005','S','White',20,NULL,NULL,'156'),
('ps005','M','White',20,NULL,NULL,'157'),
('ps005','L','White',20,NULL,NULL,'158'),
('ps006','S','Black',20,NULL,NULL,'159'),
('ps006','M','Black',20,NULL,NULL,'160'),
('ps006','L','Black',20,NULL,NULL,'161'),
('ps006','S','Grey',20,NULL,NULL,'162'),
('ps006','M','Grey',20,NULL,NULL,'163'),
('ps006','L','Grey',20,NULL,NULL,'164'),
('ps006','S','White',20,NULL,NULL,'165'),
('ps006','M','White',20,NULL,NULL,'166'),
('ps006','L','White',20,NULL,NULL,'167'),
('ps007','S','Black',20,NULL,NULL,'168'),
('ps007','M','Black',20,NULL,NULL,'169'),
('ps007','L','Black',20,NULL,NULL,'170'),
('ps007','S','Grey',20,NULL,NULL,'171'),
('ps007','M','Grey',20,NULL,NULL,'172'),
('ps007','L','Grey',20,NULL,NULL,'173'),
('ps007','S','White',20,NULL,NULL,'174'),
('ps007','M','White',20,NULL,NULL,'175'),
('ps007','L','White',20,NULL,NULL,'176'),
('ps008','S','Black',20,NULL,NULL,'177'),
('ps008','M','Black',20,NULL,NULL,'178'),
('ps008','L','Black',20,NULL,NULL,'179'),
('ps008','S','Grey',20,NULL,NULL,'180'),
('ps008','M','Grey',20,NULL,NULL,'181'),
('ps008','L','Grey',20,NULL,NULL,'182'),
('ps008','S','White',20,NULL,NULL,'183'),
('ps008','M','White',20,NULL,NULL,'184'),
('ps008','L','White',20,NULL,NULL,'185'),
('ps009','S','Black',20,NULL,NULL,'186'),
('ps009','M','Black',20,NULL,NULL,'187'),
('ps009','L','Black',20,NULL,NULL,'188'),
('ps009','S','Grey',20,NULL,NULL,'189'),
('ps009','M','Grey',20,NULL,NULL,'190'),
('ps009','L','Grey',20,NULL,NULL,'191'),
('ps009','S','White',20,NULL,NULL,'192'),
('ps009','M','White',20,NULL,NULL,'193'),
('ps009','L','White',20,NULL,NULL,'194'),
('ps010','S','Black',20,NULL,NULL,'195'),
('ps010','M','Black',20,NULL,NULL,'196'),
('ps010','L','Black',20,NULL,NULL,'197'),
('ps010','S','Grey',20,NULL,NULL,'198'),
('ps010','M','Grey',20,NULL,NULL,'199'),
('ps010','L','Grey',20,NULL,NULL,'200'),
('ps010','S','White',20,NULL,NULL,'201'),
('ps010','M','White',20,NULL,NULL,'202'),
('ps010','L','White',20,NULL,NULL,'203'),
('ps011','S','Black',20,NULL,NULL,'204'),
('ps011','M','Black',20,NULL,NULL,'205'),
('ps011','L','Black',20,NULL,NULL,'206'),
('ps011','S','Grey',20,NULL,NULL,'207'),
('ps011','M','Grey',20,NULL,NULL,'208'),
('ps011','L','Grey',20,NULL,NULL,'209'),
('ps011','S','White',20,NULL,NULL,'210'),
('ps011','M','White',20,NULL,NULL,'211'),
('ps011','L','White',20,NULL,NULL,'212'),
('ps012','S','Black',20,NULL,NULL,'213'),
('ps012','M','Black',20,NULL,NULL,'214'),
('ps012','L','Black',20,NULL,NULL,'215'),
('ps012','S','Grey',20,NULL,NULL,'216'),
('ps012','M','Grey',20,NULL,NULL,'217'),
('ps012','L','Grey',20,NULL,NULL,'218'),
('ps012','S','White',20,NULL,NULL,'219'),
('ps012','M','White',20,NULL,NULL,'220'),
('ps012','L','White',20,NULL,NULL,'221'),
('ps013','L','Black',10,NULL,NULL,'222'),
('ps014','L','Black',10,NULL,NULL,'223'),
('ps015','L','Back',10,NULL,NULL,'224'),
('ps016','L','Black',10,NULL,NULL,'225'),
('ps017','L','Black',10,NULL,NULL,'226'),
('ps018','L','Black',10,NULL,NULL,'227'),
('ps019','L','Black',10,NULL,NULL,'228'),
('ps020','L','Black',10,NULL,NULL,'229'),
('ps021','L','Black',10,NULL,NULL,'230'),
('ps022','L','Black',10,NULL,NULL,'231'),
('ps023','L','Black',10,NULL,NULL,'232'),
('ps024','L','Black',10,NULL,NULL,'233'),
('ps025',NULL,NULL,NULL,NULL,NULL,'234'),
('ps025',NULL,NULL,NULL,NULL,NULL,'235'),
('ps025',NULL,NULL,NULL,NULL,NULL,'236'),
('ps026',NULL,NULL,NULL,NULL,NULL,'237'),
('ps026',NULL,NULL,NULL,NULL,NULL,'238'),
('ps026',NULL,NULL,NULL,NULL,NULL,'239'),
('ps027',NULL,NULL,NULL,NULL,NULL,'240'),
('ps027',NULL,NULL,NULL,NULL,NULL,'241'),
('ps027',NULL,NULL,NULL,NULL,NULL,'242'),
('ps028','M','Black',200,NULL,NULL,'243'),
('ps028','L','Grey',30,NULL,NULL,'244'),
('ps028','S','Grey',40,NULL,NULL,'245'),
('ps029','M','Black',200,'2025-03-18 05:04:00','2025-03-18 05:04:00','246'),
('ps029','L','Grey',30,'2025-03-18 05:04:00','2025-03-18 05:04:00','247'),
('ps029','S','Grey',40,'2025-03-18 05:04:00','2025-03-18 05:04:00','248'),
('ps030',NULL,NULL,200,'2025-03-18 17:25:42','2025-03-18 17:25:42','249'),
('pl010',NULL,NULL,200,'2025-03-18 17:26:34','2025-03-18 17:26:34','250'),
('ps031',NULL,NULL,200,'2025-03-18 17:40:39','2025-03-18 17:40:39','251'),
('pa011',NULL,NULL,200,'2025-03-18 17:50:06','2025-03-18 17:50:06','252'),
('pa012',NULL,NULL,200,'2025-03-18 17:52:51','2025-03-18 17:52:51','253'),
('ps032','M','Black',80,'2025-03-18 18:36:44','2025-03-19 19:39:08','254'),
('ps032','M','Grey',30,'2025-03-18 18:36:44','2025-03-18 18:36:44','255'),
('pa013',NULL,NULL,200,'2025-03-18 22:59:23','2025-03-18 22:59:23','256'),
('ps032','L','Black',200,'2025-03-19 21:42:39','2025-03-19 21:42:39',NULL),
('ps033','M','Black',20,'2025-03-20 00:00:33','2025-03-20 00:00:33',NULL),
('ps033','S','Black',30,'2025-03-20 00:00:33','2025-03-20 00:00:33',NULL),
('ps034','M','Black',20,'2025-03-20 00:00:34','2025-03-20 00:00:34',NULL),
('ps034','S','Black',30,'2025-03-20 00:00:34','2025-03-20 00:00:34',NULL),
('ps035','M','Black',20,'2025-03-20 00:00:36','2025-03-20 00:00:36',NULL),
('ps035','S','Black',30,'2025-03-20 00:00:36','2025-03-20 00:00:36',NULL),
('ps036','M','Black',20,'2025-03-20 00:00:37','2025-03-20 00:00:37',NULL),
('ps036','S','Black',30,'2025-03-20 00:00:37','2025-03-20 00:00:37',NULL),
('ps037','M','Black',20,'2025-03-20 00:00:38','2025-03-20 00:00:38',NULL),
('ps037','S','Black',30,'2025-03-20 00:00:38','2025-03-20 00:00:38',NULL),
('ps038','M','Black',20,'2025-03-20 00:00:39','2025-03-20 00:00:39',NULL),
('ps038','S','Black',30,'2025-03-20 00:00:39','2025-03-20 00:00:39',NULL),
('ps039','M','Black',20,'2025-03-20 00:00:40','2025-03-20 00:00:40',NULL),
('ps039','S','Black',30,'2025-03-20 00:00:40','2025-03-20 00:00:40',NULL),
('ps040','M','Black',20,'2025-03-20 00:00:42','2025-03-20 00:00:42',NULL),
('ps040','S','Black',30,'2025-03-20 00:00:42','2025-03-20 00:00:42',NULL),
('ps041','M','Black',20,'2025-03-20 00:00:43','2025-03-20 00:00:43',NULL),
('ps041','S','Black',30,'2025-03-20 00:00:43','2025-03-20 00:00:43',NULL),
('ps042','M','Black',20,'2025-03-20 00:00:44','2025-03-20 00:00:44',NULL),
('ps042','S','Black',30,'2025-03-20 00:00:44','2025-03-20 00:00:44',NULL),
('ps043','L','Black',30,'2025-03-20 01:27:38','2025-03-20 01:27:38',NULL),
('ps043','M','Black',62,'2025-03-20 01:27:38','2025-03-20 01:27:38',NULL),
('ps044','L','Black',20,'2025-03-20 01:30:39','2025-03-20 01:30:39',NULL),
('ps044','M','Black',27,'2025-03-20 01:30:39','2025-03-20 01:30:39',NULL),
('ps047','S','White',30,'2025-03-20 04:12:24','2025-03-20 04:12:24',NULL),
('ps047','S','Gray',80,'2025-03-20 04:12:24','2025-03-20 04:12:24',NULL),
('ps047','M','Gray',110,'2025-03-20 04:12:24','2025-03-20 04:12:24',NULL),
('pa014',NULL,NULL,211,'2025-03-20 05:23:53','2025-03-20 05:23:53','SPEC202503201323525403'),
('ps048','L','White',30,'2025-03-20 17:02:36','2025-03-20 17:02:36','SPEC202503210102366478'),
('ps048','L','Gray',50,'2025-03-20 17:02:36','2025-03-20 17:02:36','SPEC202503210102368116'),
('ps048','M','White',45,'2025-03-20 17:02:36','2025-03-20 17:02:36','SPEC202503210102366009'),
('ps045','L','Black',2,'2025-03-20 17:36:13','2025-03-20 17:36:13','SPEC202503210136131504'),
('ps045','M','Black',3,'2025-03-20 17:36:14','2025-03-20 17:36:14','SPEC202503210136133269'),
('ps045','S','Black',4,'2025-03-20 17:36:14','2025-03-20 17:36:14','SPEC202503210136148701');
/*!40000 ALTER TABLE `product_spec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('3fdbAyefikxwpqvbvTcvROAiydDgEqLyxpRK9VXu',10,'::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYkxTejZMU05jUml2bnI3STR1Q09JdE9HTFByOE5GZW16NGM4RXdPSSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTA7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly9sb2NhbGhvc3QvY2xpZW50LXNpZGUvcHVibGljL2NhdGVnb3JpZXNfY2xvdGhlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1742698051),
('4whFeSYAeBLkdkM4liwRQZjZuZXGIHWhjD6zgRHz',10,'::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoicEVyUTRBMkJuemU5bW9zUFZyOWZwYjRtekJaVGVoTTFhaExyak9peSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTA7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9sb2NhbGhvc3QvY2xpZW50LXNpZGUvcHVibGljIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1742637435),
('rE1VCAmLNGQInVVmQrUjJfwVbT7yWYF3kUTGmpOm',10,'::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoieXJDbldsb2FEMDhwbXEzSW9ydG4zTXZtTUh1SXM2R282d21DTnZpbyI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTA7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9sb2NhbGhvc3QvY2xpZW50LXNpZGUvcHVibGljIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1742646745),
('s9DmGDUQuR136p7fDWLBZnvpqWynNe8FniIQY8uj',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicE1CR2xWNGdUb1FJYVhwaDNsSmt6NXVkdllsQnVTaWg5VXVVUDNjRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly9sb2NhbGhvc3QvY2xpZW50LXNpZGUvcHVibGljL3N5c3RlbS1tYWludGVuYW5jZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1742637524);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(10,'許大米','pollylearnhsu@gmail.com',NULL,NULL,'2025-03-10 23:25:57','$2y$12$oeM9TP7A6yOb9fq2pT/H7uOHS6WqAnhwqbVpOa.fTP7G6GN1Zfgqm','CusZtZMqMzJxgJ4LF2lIqel4zDcax3tCmmHmgsdmdjen5mK11B1qV6dkgm3Z','2025-03-10 23:25:57','2025-03-10 23:25:57'),
(19,'許少宇','nasa0824@gmail.com','0910305411','2000-08-24','2025-03-12 22:54:28','$2y$12$FgAcLzOBpV2keBfpARb3geTgvnIRyM0KaMl9g.P4TYc571KCCpdyu',NULL,'2025-03-12 22:54:28','2025-03-21 22:17:19'),
(20,'許','daniel@danielhsu.dev','0912345678',NULL,'2025-03-12 23:27:40','$2y$12$FgAcLzOBpV2keBfpARb3geTgvnIRyM0KaMl9g.P4TYc571KCCpdyu',NULL,'2025-03-12 23:27:40','2025-03-14 19:54:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlists` (
  `member_id` bigint(20) unsigned NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`member_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlists_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-03-23 12:35:03
