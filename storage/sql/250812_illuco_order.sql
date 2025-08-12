-- --------------------------------------------------------
-- 호스트:                          119.205.211.101
-- 서버 버전:                        8.0.31 - MySQL Community Server - GPL
-- 서버 OS:                        Linux
-- HeidiSQL 버전:                  12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- 테이블 db_illuco0423.carts 구조 내보내기
CREATE TABLE IF NOT EXISTS `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_carts_customer_id` (`customer_id`),
  CONSTRAINT `fk_carts_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.carts:~7 rows (대략적) 내보내기
INSERT INTO `carts` (`id`, `customer_id`, `created_at`, `updated_at`) VALUES
	(4, 6, '2025-08-06 02:02:16', '2025-08-06 02:02:16'),
	(5, 7, '2025-08-06 02:55:25', '2025-08-06 02:55:25'),
	(6, 8, '2025-08-06 03:28:40', '2025-08-06 03:28:40'),
	(7, 5, '2025-08-06 13:18:10', '2025-08-06 13:18:10'),
	(8, 10, '2025-08-06 14:39:24', '2025-08-06 14:39:24'),
	(9, 11, '2025-08-12 17:44:40', '2025-08-12 17:44:40'),
	(10, 12, '2025-08-12 17:47:02', '2025-08-12 17:47:02');

-- 테이블 db_illuco0423.cart_items 구조 내보내기
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `set_group_id` varchar(255) DEFAULT NULL COMMENT '동적 세트 그룹 번호',
  `set_group_sort` int unsigned DEFAULT NULL COMMENT '세트 그룹 내 정렬 순서',
  `is_main_item` tinyint(1) NOT NULL DEFAULT '0' COMMENT '세트 내 메인 제품 여부',
  `selected` tinyint(1) NOT NULL DEFAULT '1' COMMENT '선택 여부',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cart_items_cart_id` (`cart_id`),
  KEY `idx_cart_items_product_id` (`product_id`),
  KEY `idx_cart_items_cart_id_product_id` (`cart_id`,`product_id`),
  KEY `idx_cart_items_set_group_id` (`set_group_id`),
  KEY `idx_cart_items_set_group_id_set_group_sort` (`set_group_id`,`set_group_sort`),
  CONSTRAINT `fk_cart_items_cart_id` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.cart_items:~1 rows (대략적) 내보내기
INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `set_group_id`, `set_group_sort`, `is_main_item`, `selected`, `created_at`, `updated_at`) VALUES
	(58, 4, 58, 1, '3ca39c79-36fb-4a8e-a5bf-b7b49c2cbb6a', NULL, 1, 1, '2025-08-08 08:14:37', '2025-08-08 08:14:37');

-- 테이블 db_illuco0423.claims 구조 내보내기
CREATE TABLE IF NOT EXISTS `claims` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `product_model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `product_serial_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `product_description` text,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `dealer_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'received',
  `order_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_claims_user_id` (`user_id`),
  KEY `fk_claims_delaer_id` (`dealer_id`),
  CONSTRAINT `fk_claims_delaer_id` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_claims_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.claims:~19 rows (대략적) 내보내기
INSERT INTO `claims` (`id`, `product_name`, `product_code`, `product_model`, `product_serial_number`, `product_description`, `title`, `content`, `user_id`, `dealer_id`, `customer_name`, `customer_email`, `customer_phone`, `status`, `order_no`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 'Galilean', 'LP', 'ITL-1040P', 'asddassdasa', 'galilean loupe', 'sxczxczxcczxzxczxc', '<p>asd</p><p>asd</p><p>sd</p><p>asd</p><p>sda</p><p>asd</p><p>asd</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:04:45', '2025-07-15 07:28:15', '2025-07-17 06:04:45'),
	(2, '무선 헤드라이트', 'HL', 'IHL-2000', 'asddassd', '무선 헤드라이트. wireless headlight', 'sdasad', '<p>sdasddsa</p>', 7, NULL, '양상규', 'didtkdrb@naver.com', '01095312312', 'received', 'received', '2025-07-17 06:05:31', '2025-07-15 07:29:34', '2025-07-17 06:05:31'),
	(3, 'ErgoX', 'LP', 'ITL-1025G', '2123312231', 'ergox', 'ㅁㄴㅇㅇㄴㅁㄴㅇㅁㄴㅇ', '<p>ㄴㅇㅁㄴㅇㅁㄴㅇㄴㅇㅁ</p>', 7, NULL, '양상규', 'didtkdrb@naver.com', '01095312312', 'received', 'received', '2025-07-17 06:06:30', '2025-07-17 05:59:15', '2025-07-17 06:06:30'),
	(4, 'Galilean', 'LP', 'ITL-1040P', 'ㅁㄴㅇㅇ', 'galilean loupe', 'ㅁㄴㄴㅁㅇㄴㅁㅇㅁㄴㅇ', '<p>ㄴㅁㅇㄴㅇㅁㅁㅇ</p>', 7, NULL, '양상규', 'didtkdrb@naver.com', '01095312312', 'received', 'received', '2025-07-17 06:07:11', '2025-07-17 06:07:04', '2025-07-17 06:07:11'),
	(5, 'Galilean', 'LP', 'ITL-1040P', 'ㅁㄴㅇㄴㅇㅇㄴㄴㅇㅁ', 'galilean loupe', 'ㄴㅇㅁㅁㄴㅇ', '<p>ㄴㅇㅁㅁㄴㅇㅁㄴㅇ</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:09:42', '2025-07-17 06:09:34', '2025-07-17 06:09:42'),
	(6, 'ErgoX', 'LP', 'ITL-1025G', 'ㅁㄴㅁㄴㅇ', 'ergox', 'ㄴㅇㅁㅁㄴㅇㅁㄴㅇ', '<p>ㅁㄴㅇㅇㄴㄴㅇㅁㄴㅇㅁㅁㄴㅇ</p>', 7, NULL, '양상규', 'didtkdrb@naver.com', '01095312312', 'received', 'received', '2025-07-17 06:12:12', '2025-07-17 06:12:04', '2025-07-17 06:12:12'),
	(7, 'ErgoX', 'LP', 'ITL-1025G', 'ㄴㅁㅇㄴㅇㅁ', 'ergox', 'ㄴㅇㅁㄴㅁㅇㄴㅁㅇ', '<p>ㄴㅁㅇㄴㅇㄴㅇㅁ</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:13:51', '2025-07-17 06:13:44', '2025-07-17 06:13:51'),
	(8, 'Galilean', 'LP', 'ITL-1040P', 'ㅁㄴㅇㅇㅁㄴ', 'galilean loupe', 'ㄴㅇㄴㅇㅁ', '<p>ㄴㅇㅁㄴㅇㅁ</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:16:48', '2025-07-17 06:16:40', '2025-07-17 06:16:48'),
	(9, 'Galilean', 'LP', 'ITL-1040P', 'ㅁㄴㅇㅇㅁㄴㄴㅁㅇ', 'galilean loupe', 'ㄴㅁㅇㄴㅁㅇㅇ', '<p>ㄴㅇㅁㄴ</p>', 7, NULL, '양상규', 'didtkdrb@naver.com', '01095312312', 'received', 'received', '2025-07-17 06:17:36', '2025-07-17 06:17:31', '2025-07-17 06:17:36'),
	(10, 'Galilean', 'LP', 'ITL-1040P', 'ㅁㄴㅇ', 'galilean loupe', 'ㅁㄴㅇㅁㄴㅇㄴㅁㅇ', '<p>ㅁㄴㅇ</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:17:58', '2025-07-17 06:17:53', '2025-07-17 06:17:58'),
	(11, 'Galilean', 'LP', 'ITL-1040P', 'ㅁㄴㅇㄴㅇㅁ', 'galilean loupe', 'ㅁㄴㅇㅁㄴㅇㄴㅇ', '<p>ㅁㅁㅇㅇㅁㄴㄴㅇㅇㅁ</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:20:25', '2025-07-17 06:20:17', '2025-07-17 06:20:25'),
	(12, '무선 헤드라이트', 'HL', 'IHL-2000', 'ㅁㄴㅇㅇㄴ', '무선 헤드라이트. wireless headlight', 'ㄴㅇㄴㅇㄴㅁㅇ', '<p>ㅁㄴㅇㅁㄴㅇㅁㄴㅇㄴㅇㅁ</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:25:12', '2025-07-17 06:25:01', '2025-07-17 06:25:12'),
	(13, 'Galilean', 'LP', 'ITL-1040P', 'ㄴㅇㄴㅁㅇ', 'galilean loupe', 'ㅁㄴㅇㅁㄴㅇ', '<p>ㄴㅁㅇㄴㅇ</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:26:09', '2025-07-17 06:25:55', '2025-07-17 06:26:09'),
	(14, 'Galilean', 'LP', 'ITL-1040P', 'ㄴㅁㅇㅁㄴㅇㅁㄴㅇ', 'galilean loupe', 'ㅁㄴㅇㄴㅇㅁ', '<p>ㅁㄴㅇㄴㅇㅁㄴㅇ</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', 'received', '2025-07-17 06:27:23', '2025-07-17 06:27:08', '2025-07-17 06:27:23'),
	(15, 'Galilean', 'LP', 'ITL-1040P', 'asdda', NULL, 'assadasd', '<p>asads</p>', 9, 9, '양상규', 'didtkdrb@naver.com', '01095312312', 'completed', 'received', '2025-07-17 08:56:19', '2025-07-17 08:14:40', '2025-07-17 08:56:19'),
	(16, NULL, NULL, NULL, 'test', NULL, 'asdasdsda', '<p>asdasd</p>', 21, 21, NULL, NULL, NULL, 'received', 'adassda', NULL, '2025-08-06 03:14:23', '2025-08-06 03:14:23'),
	(17, 'ErgoX', 'LP', 'ITL-1025G', 'asdsdasd', NULL, 'sdadasasd', '<p>sadsdaasd</p>', 21, 21, '고객D-A', 'dacustomer@test.com', '010-1231-2312', 'completed', 'abc', NULL, '2025-08-06 03:15:07', '2025-08-06 14:46:47'),
	(22, NULL, NULL, NULL, 'asd', NULL, 'sdaasdasd', '<p>sdasadasd</p>', 23, NULL, NULL, NULL, NULL, 'received', '', '2025-08-07 07:43:40', '2025-08-07 07:43:36', '2025-08-07 07:43:40'),
	(25, NULL, NULL, NULL, 'das', NULL, 'asdasd', '<p>asdas</p>', 23, NULL, NULL, NULL, NULL, 'received', 'sad', NULL, '2025-08-07 07:48:00', '2025-08-07 07:48:00'),
	(26, NULL, NULL, NULL, 'ㄴㅇㅁㄴㅇㄴㅇㅁㄴㅁㅇ', NULL, 'ㄴㅇㅁㅁㄴㅇㅁㄴㅇ', '<p>ㄴㅇㅁㄴㅇㅁ</p>', 19, 19, NULL, NULL, NULL, 'received', 'ㄴㅇㅁㄴㄴㅁㅇ', NULL, '2025-08-07 08:42:43', '2025-08-07 08:42:43'),
	(27, NULL, NULL, NULL, 'ㅅㄷㄴ', NULL, 'testtest', '<p>asdsdasd</p>', 18, NULL, NULL, NULL, NULL, 'received', '', '2025-08-08 08:15:23', '2025-08-07 08:57:01', '2025-08-08 08:15:23');

-- 테이블 db_illuco0423.customers 구조 내보내기
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `dealer_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_customers_user_id` (`user_id`),
  KEY `fk_customers_dealer_id` (`dealer_id`),
  CONSTRAINT `fk_customers_dealer_id` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_customers_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.customers:~9 rows (대략적) 내보내기
INSERT INTO `customers` (`id`, `user_id`, `dealer_id`, `name`, `country`, `phone`, `email`, `age`, `address`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(4, 18, NULL, '고객A', 'KR', '01012345678', 'test@test.com', NULL, NULL, '', NULL, '2025-08-06 01:41:21', '2025-08-06 01:41:21'),
	(5, 18, NULL, '고객B', 'US', '123-1231-2312', 'testb@test.com', NULL, NULL, '', NULL, '2025-08-06 01:46:13', '2025-08-06 01:50:05'),
	(6, 21, 21, '고객D-A', 'KR', '010-1231-2312', 'dacustomer@test.com', 31, NULL, '', NULL, '2025-08-06 02:02:07', '2025-08-06 02:02:07'),
	(7, 21, 21, '고객D-B', 'DE', '001-2312-3123', 'customerdb@test.com', 45, NULL, '', NULL, '2025-08-06 02:55:13', '2025-08-06 02:55:13'),
	(8, 19, 19, '고객A-A', 'JP', '010-2312-3122', 'aaacustomer@test.com', NULL, NULL, '', NULL, '2025-08-06 03:28:05', '2025-08-06 03:28:05'),
	(9, 18, NULL, 'JAme 513', 'KR', '02-3123-1232', 'jam3sd12@gmail.com', 31, NULL, 'Description', '2025-08-06 14:07:03', '2025-08-06 14:06:22', '2025-08-06 14:07:03'),
	(10, 24, 24, 'F대리점 고객', 'JP', '010-1231-2312', 'fftest@test.com', 45, NULL, 'description', NULL, '2025-08-06 14:39:08', '2025-08-06 14:39:08'),
	(11, 18, NULL, 'Laura Lee', 'US', '123-5675-998777', 'laura.lee@illuco.com', 27, '', '', NULL, '2025-08-12 17:27:15', '2025-08-12 17:27:15'),
	(12, 25, 25, 'pil', 'US', '131-5151-5623', 'yij@kflsi.fdd', 34, '', '', NULL, '2025-08-12 17:44:24', '2025-08-12 17:44:24');

-- 테이블 db_illuco0423.dealers 구조 내보내기
CREATE TABLE IF NOT EXISTS `dealers` (
  `id` bigint unsigned NOT NULL,
  `country` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `description` text,
  `memo` text,
  `use_default_memo` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dealers_category_id_foreign` (`category_id`),
  CONSTRAINT `dealers_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dealers_id` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.dealers:~10 rows (대략적) 내보내기
INSERT INTO `dealers` (`id`, `country`, `code`, `address`, `description`, `memo`, `use_default_memo`, `deleted_at`, `created_at`, `updated_at`, `category_id`) VALUES
	(9, 'FR', 'DF02', 'assdaasdsdasd', 'sd\r\nsd\r\n\r\nasd\r\nasd\r\n', NULL, 0, NULL, '2025-07-09 07:46:14', '2025-07-17 07:20:02', 2),
	(12, 'KR', 'JJ0231', 'sasd', 'sd\r\nsda\r\nsad\r\nasd\r\n', NULL, 0, NULL, '2025-07-10 06:22:29', '2025-07-10 06:22:29', NULL),
	(13, 'PT', 'DAM', 'sddasdas', 'asdasdsad', NULL, 0, NULL, '2025-07-11 05:53:49', '2025-07-14 01:35:06', NULL),
	(15, 'KR', 'asdasdsa', 'dsasdasas', 'asasdads', NULL, 0, NULL, '2025-07-11 06:00:46', '2025-07-11 06:00:46', NULL),
	(16, 'KR', 'sdsda', 'asd', 'adsasd', NULL, 0, NULL, '2025-07-11 06:01:06', '2025-07-11 06:01:06', 3),
	(19, 'JP', 'DA01', '', '', NULL, 0, NULL, '2025-08-06 01:19:03', '2025-08-06 01:19:03', NULL),
	(20, 'DE', 'DA0B', 'test', 'test', NULL, 0, NULL, '2025-08-06 01:19:44', '2025-08-06 01:19:44', 2),
	(21, 'IT', 'DAC08', 'ccv', 'ss', NULL, 0, NULL, '2025-08-06 01:51:24', '2025-08-06 02:00:03', NULL),
	(24, 'ID', 'DAF03', 'addressa12', 'descriptionasdas', NULL, 0, NULL, '2025-08-06 14:08:20', '2025-08-06 14:49:54', NULL),
	(25, 'KR', 'KR-ILL', '', '', NULL, 0, NULL, '2025-08-12 17:13:36', '2025-08-12 17:13:54', NULL);

-- 테이블 db_illuco0423.file_attachments 구조 내보내기
CREATE TABLE IF NOT EXISTS `file_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `file_attachable_type` varchar(255) NOT NULL,
  `file_attachable_id` bigint unsigned NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` int DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `extension` varchar(20) NOT NULL,
  `upload_path` varchar(255) DEFAULT NULL,
  `upload_url` varchar(255) DEFAULT NULL,
  `file_key` varchar(255) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_file_attachments_file_attachable_type_file_attachable_id` (`file_attachable_type`,`file_attachable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.file_attachments:~17 rows (대략적) 내보내기
INSERT INTO `file_attachments` (`id`, `file_attachable_type`, `file_attachable_id`, `original_name`, `name`, `mime_type`, `size`, `path`, `extension`, `upload_path`, `upload_url`, `file_key`, `sort_order`, `created_at`, `updated_at`) VALUES
	(1, 'producttemplate', 1, 'wired.jpg', 'd2061dd9561f852a7d24e31e3a09e44b.jpg', 'image/jpeg', 705413, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/d2061dd9561f852a7d24e31e3a09e44b.jpg', NULL, 'main_image', 0, '2025-07-03 11:37:22', '2025-07-03 11:37:22'),
	(2, 'producttemplate', 2, 'wireless.jpg', '97d680b8337a9a2ce4d095128505d3ba.jpg', 'image/jpeg', 603879, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/97d680b8337a9a2ce4d095128505d3ba.jpg', NULL, 'main_image', 0, '2025-07-03 11:38:05', '2025-07-03 11:38:05'),
	(3, 'producttemplate', 3, 'Frame 1.jpg', 'bbf663e9a6086a3495aefc8083ca3eda.jpg', 'image/jpeg', 235203, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/bbf663e9a6086a3495aefc8083ca3eda.jpg', NULL, 'main_image', 0, '2025-07-03 11:39:11', '2025-07-03 11:39:11'),
	(4, 'producttemplate', 4, 'Frame 2.jpg', '11c4303ab70944024adbccc01e8568e1.jpg', 'image/jpeg', 210485, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/11c4303ab70944024adbccc01e8568e1.jpg', NULL, 'main_image', 0, '2025-07-03 11:39:55', '2025-07-03 11:39:55'),
	(5, 'producttemplate', 5, 'image 511.jpg', 'fe5b3de13002c1649901e7ac675a6512.jpg', 'image/jpeg', 366310, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/fe5b3de13002c1649901e7ac675a6512.jpg', NULL, 'main_image', 0, '2025-07-03 23:44:48', '2025-07-03 23:44:48'),
	(7, 'notice', 1, '683d3ef2e0582.jpeg', '38f981063a0e91b5db4e7e4176ece5c7.jpeg', 'image/jpeg', 247998, '/var/www/html/storage/uploads/public/notice/notice', 'jpeg', '/static/uploads/notice/notice/38f981063a0e91b5db4e7e4176ece5c7.jpeg', NULL, 'attach_1', 0, '2025-07-10 06:49:11', '2025-07-10 06:49:11'),
	(18, 'notice', 12, '683d3ef2e0582.jpeg', '50e7043077c26d72b9b1cc910c752c80.jpeg', 'image/jpeg', 247998, '/var/www/html/storage/uploads/public/notice', 'jpeg', '/static/uploads/notice/50e7043077c26d72b9b1cc910c752c80.jpeg', NULL, 'attach_1', 0, '2025-07-10 07:00:09', '2025-07-10 07:00:09'),
	(19, 'claim', 1, 'ines-alvarez-fdez-u6rZ2_bUgUE-unsplash.jpg', '412f7faffc3c2818fb7435403aa7a950.jpg', 'image/jpeg', 2886043, '/var/www/html/storage/uploads/public/claim', 'jpg', '/static/uploads/claim/412f7faffc3c2818fb7435403aa7a950.jpg', 'http://localhost:8080/static/uploads/claim/412f7faffc3c2818fb7435403aa7a950.jpg', 'attach_1', 0, '2025-07-15 07:28:15', '2025-07-15 07:28:15'),
	(21, 'notice', 17, 'gal_01.jpg', 'bcbe2ad20d78a8af92ca34349b9d3158.jpg', 'image/jpeg', 1361855, '/var/www/html/storage/uploads/public/notice', 'jpg', '/static/uploads/notice/bcbe2ad20d78a8af92ca34349b9d3158.jpg', 'http://localhost:8080/static/uploads/notice/bcbe2ad20d78a8af92ca34349b9d3158.jpg', 'attach_1', 0, '2025-08-06 01:53:13', '2025-08-06 01:53:13'),
	(22, 'claim', 16, '13732772_640_360_60fps.mp4', '2719fed797587713521ac8d239acea8e.mp4', 'video/mp4', 2587581, '/var/www/html/storage/uploads/public/claim', 'mp4', '/static/uploads/claim/2719fed797587713521ac8d239acea8e.mp4', 'http://localhost:8080/static/uploads/claim/2719fed797587713521ac8d239acea8e.mp4', 'attach_1', 0, '2025-08-06 03:14:23', '2025-08-06 03:14:23'),
	(23, 'claim', 16, 'a-chosen-soul-rUmr0kPf7Eo-unsplash.jpg', '64cd8180d4f21835080416a705ce4d93.jpg', 'image/jpeg', 156034, '/var/www/html/storage/uploads/public/claim', 'jpg', '/static/uploads/claim/64cd8180d4f21835080416a705ce4d93.jpg', 'http://localhost:8080/static/uploads/claim/64cd8180d4f21835080416a705ce4d93.jpg', 'attach_2', 0, '2025-08-06 03:14:23', '2025-08-06 03:14:23'),
	(24, 'notice', 18, 'gal_01.jpg', '3d2d7207f4ee803d951b4b06161acbca.jpg', 'image/jpeg', 1361855, '/home/illu0423order/www/storage/uploads/public/notice', 'jpg', '/static/uploads/notice/3d2d7207f4ee803d951b4b06161acbca.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/notice/3d2d7207f4ee803d951b4b06161acbca.jpg', 'attach_1', 0, '2025-08-06 12:56:28', '2025-08-06 12:56:28'),
	(25, 'producttemplate', 6, 'gal_01.jpg', 'd2a622c13cfc7eb0dcd2ce95699d6a82.jpg', 'image/jpeg', 1361855, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/d2a622c13cfc7eb0dcd2ce95699d6a82.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/producttemplate/d2a622c13cfc7eb0dcd2ce95699d6a82.jpg', 'main_image', 0, '2025-08-06 13:45:54', '2025-08-06 13:45:54'),
	(26, 'producttemplate', 7, 'gal_01.jpg', 'e0ea4185dce31168381f685e973ab340.jpg', 'image/jpeg', 1361855, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/e0ea4185dce31168381f685e973ab340.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/producttemplate/e0ea4185dce31168381f685e973ab340.jpg', 'main_image', 0, '2025-08-06 14:11:47', '2025-08-06 14:11:47'),
	(27, 'notice', 20, 'gal_01.jpg', 'eb0be84807aa6ea7673ab7ddab53399e.jpg', 'image/jpeg', 1361855, '/home/illu0423order/www/storage/uploads/public/notice', 'jpg', '/static/uploads/notice/eb0be84807aa6ea7673ab7ddab53399e.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/notice/eb0be84807aa6ea7673ab7ddab53399e.jpg', 'attach_1', 0, '2025-08-06 14:31:59', '2025-08-06 14:31:59'),
	(28, 'claim', 25, 'gal_01.jpg', 'b9b4f7847f18dda2e97a555ade3e789b.jpg', 'image/jpeg', 1361855, '/home/illu0423order/www/storage/uploads/public/claim', 'jpg', '/static/uploads/claim/b9b4f7847f18dda2e97a555ade3e789b.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/claim/b9b4f7847f18dda2e97a555ade3e789b.jpg', 'attach_1', 0, '2025-08-07 07:48:00', '2025-08-07 07:48:00'),
	(29, 'claim', 26, '13732772_640_360_60fps.mp4', '8649a0a4e23aee9d7ba87641402d97e9.mp4', 'video/mp4', 2587581, '/home/illu0423order/www/storage/uploads/public/claim', 'mp4', '/static/uploads/claim/8649a0a4e23aee9d7ba87641402d97e9.mp4', 'http://illuco-order.nineonelabs.co.kr/static/uploads/claim/8649a0a4e23aee9d7ba87641402d97e9.mp4', 'attach_1', 0, '2025-08-07 08:42:43', '2025-08-07 08:42:43'),
	(30, 'claim', 26, 'a-chosen-soul-rUmr0kPf7Eo-unsplash.jpg', '9c1d827f8967adc3a70e7e59129c6aac.jpg', 'image/jpeg', 156034, '/home/illu0423order/www/storage/uploads/public/claim', 'jpg', '/static/uploads/claim/9c1d827f8967adc3a70e7e59129c6aac.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/claim/9c1d827f8967adc3a70e7e59129c6aac.jpg', 'attach_2', 0, '2025-08-07 08:42:43', '2025-08-07 08:42:43'),
	(31, 'producttemplate', 8, 'gal_01.jpg', 'aeb03ca4615b5be0f9485a6f5a94af64.jpg', 'image/jpeg', 1361855, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/aeb03ca4615b5be0f9485a6f5a94af64.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/producttemplate/aeb03ca4615b5be0f9485a6f5a94af64.jpg', 'main_image', 0, '2025-08-07 10:18:54', '2025-08-07 10:18:54');

-- 테이블 db_illuco0423.migrations 구조 내보내기
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.migrations:~31 rows (대략적) 내보내기
INSERT INTO `migrations` (`id`, `name`, `batch`, `created_at`, `updated_at`) VALUES
	(1, '20250608_040545_create_users_table', 1, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(2, '20250608_040617_create_file_attachments_table', 2, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(3, '20250612_040546_create_sessions_table', 3, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(4, '20250612_231703_create_dealers_table', 4, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(5, '20250612_231710_create_customers_table', 5, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(6, '20250612_232603_create_notices_table', 6, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(7, '20250612_234333_create_product_categories_table', 7, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(8, '20250612_234420_create_product_templates_table', 8, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(9, '20250613_002038_create_products_table', 9, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(10, '20250613_002039_create_product_headlights_table', 10, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(11, '20250613_002039_create_product_loupes_table', 11, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(12, '20250613_002932_create_claims_table', 12, '2025-07-09 06:40:50', '2025-07-09 06:40:50'),
	(13, '20250613_011346_create_rules_table', 13, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(14, '20250613_011407_create_permissions_table', 14, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(15, '20250613_011535_create_role_permissions_table', 15, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(16, '20250613_011603_create_role_users_table', 16, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(17, '20250613_012126_create_notifications_table', 17, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(18, '20250613_012251_create_password_reset_tokens_table', 18, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(19, '20250613_012251_create_remember_tokens_table', 19, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(20, '20250613_014848_create_carts_table', 20, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(21, '20250613_015036_create_cart_items_table', 21, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(22, '20250613_015109_create_orders_table', 22, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(23, '20250613_015313_create_order_items_table', 23, '2025-07-09 06:40:51', '2025-07-09 06:40:51'),
	(24, '20250617_080212_create_order_documents_table', 24, '2025-07-09 06:40:52', '2025-07-09 06:40:52'),
	(25, '20250617_080213_create_order_document_commercial_invoices_table', 25, '2025-07-09 06:40:52', '2025-07-09 06:40:52'),
	(26, '20250617_080213_create_order_document_packling_lists_table', 26, '2025-07-09 06:40:52', '2025-07-09 06:40:52'),
	(27, '20250617_080213_create_order_document_product_requests_table', 27, '2025-07-09 06:40:52', '2025-07-09 06:40:52'),
	(28, '20250617_080213_create_order_document_proforma_invoices_table', 28, '2025-07-09 06:40:52', '2025-07-09 06:40:52'),
	(29, '20250629_053434_create_serial_numbers_table', 29, '2025-07-09 06:40:52', '2025-07-09 06:40:52'),
	(30, '20250629_062026_create_order_histories_table', 30, '2025-07-09 06:40:52', '2025-07-09 06:40:52'),
	(31, '20250629_062026_create_order_logs_table', 31, '2025-07-11 08:36:10', '2025-07-11 08:36:10');

-- 테이블 db_illuco0423.notices 구조 내보내기
CREATE TABLE IF NOT EXISTS `notices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `visible_from` datetime DEFAULT NULL,
  `visible_to` datetime DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notices_user_id` (`user_id`),
  CONSTRAINT `fk_notices_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.notices:~3 rows (대략적) 내보내기
INSERT INTO `notices` (`id`, `user_id`, `title`, `content`, `visible_from`, `visible_to`, `is_pinned`, `status`, `created_at`, `updated_at`) VALUES
	(17, 18, '공지사항 테스트', '<p>테스트 테스트</p>', NULL, NULL, 0, 'draft', '2025-08-06 01:53:13', '2025-08-06 01:53:13'),
	(18, 18, '공지사항 테스트2', '<p>공지사항 테스트입니다.</p><p><br></p><p><img src="/static/uploads/temp/c03fcdfe21e4707b7f56e5fb41c980d4.jpg" style="width: 572px;"></p><p><br></p><p><br></p><p>테스트</p><p><br></p><p><br></p><p><br></p>', NULL, NULL, 0, 'draft', '2025-08-06 12:56:28', '2025-08-06 12:56:28'),
	(19, 22, '공지사항 테스트3', '<p>공지사항 테스트</p>', NULL, NULL, 1, 'draft', '2025-08-06 13:00:20', '2025-08-06 13:00:20'),
	(20, 18, '공지사항 테스트 44', '<p>공지사항 테스트 4&nbsp;</p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;">공지사항 테스트 4&nbsp;</span></p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;">공지사항 테스트 4&nbsp;</span><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;"></span></p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;"><br></span></p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;"><br></span></p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;"><br></span></p>', NULL, NULL, 0, 'draft', '2025-08-06 14:31:59', '2025-08-06 14:31:59');

-- 테이블 db_illuco0423.notifications 구조 내보내기
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(100) NOT NULL,
  `data` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notifications_user_id` (`user_id`),
  CONSTRAINT `fk_notifications_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.notifications:~0 rows (대략적) 내보내기

-- 테이블 db_illuco0423.orders 구조 내보내기
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `dealer_id` bigint unsigned DEFAULT NULL,
  `orderer_name` varchar(255) NOT NULL,
  `orderer_email` varchar(255) DEFAULT NULL,
  `orderer_phone` varchar(255) DEFAULT NULL,
  `payment_date` date DEFAULT NULL COMMENT '발주일',
  `delivery_date` date DEFAULT NULL COMMENT '납기일',
  `shipping_date` date DEFAULT NULL COMMENT '출하일',
  `canceled_at` datetime DEFAULT NULL COMMENT '주문취소일',
  `order_no` varchar(50) NOT NULL,
  `order_status` varchar(255) NOT NULL DEFAULT 'new',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `memo` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_orders_user_id` (`user_id`),
  KEY `fk_orders_customer_id` (`customer_id`),
  KEY `fk_orders_dealer_id` (`dealer_id`),
  CONSTRAINT `fk_orders_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_dealer_id` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.orders:~10 rows (대략적) 내보내기
INSERT INTO `orders` (`id`, `user_id`, `customer_id`, `dealer_id`, `orderer_name`, `orderer_email`, `orderer_phone`, `payment_date`, `delivery_date`, `shipping_date`, `canceled_at`, `order_no`, `order_status`, `total_amount`, `memo`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(6, 21, 6, 21, '대리점 CA ', 'dealer-c@gmail.com', '010-2312-3124', NULL, NULL, NULL, '2025-08-06 11:45:20', 'DAC08-2025-00001', 'canceled', 7850.00, '오더 주문완료!', '2025-08-07 15:37:16', '2025-08-06 02:05:19', '2025-08-07 15:37:16'),
	(7, 21, 7, 21, '대리점 CA ', 'dealer-c@gmail.com', '010-2312-3124', NULL, NULL, NULL, NULL, 'DAC08-2025-00002', 'new', 2300.00, 'test', '2025-08-07 15:37:16', '2025-08-06 02:55:32', '2025-08-07 15:37:16'),
	(16, 19, 8, 19, 'A대리점', 'dealer-a@gmail.com', '01012312312', NULL, NULL, NULL, NULL, 'DA01-2025-00001', 'new', 2700.00, 'test', '2025-08-07 15:37:16', '2025-08-06 03:34:00', '2025-08-07 15:37:16'),
	(20, 19, 8, 19, 'A대리점', 'dealer-a@gmail.com', '01012312312', '2025-08-06', '2025-08-08', '2025-08-14', NULL, 'DA01-2025-00002', 'preparing', 2700.00, 'ㅅㄷㄴㅅ', '2025-08-07 15:37:16', '2025-08-06 03:37:40', '2025-08-07 15:37:16'),
	(21, 22, 5, NULL, '나인원랩스', 'nineonelabs@gmail.com', '029549153', NULL, NULL, NULL, NULL, 'CST-2025-00001', 'new', 1410.00, 'test', '2025-08-07 15:37:16', '2025-08-06 13:18:17', '2025-08-07 15:37:16'),
	(22, 18, 5, NULL, '일루코 본사', 'illucokorea@gmail.com', '010-1231-2312', '2025-08-01', '2025-08-07', '2025-08-08', NULL, 'CST-2025-00002', 'preparing', 4823.00, '일루코 본사에서 주문완료', '2025-08-07 15:37:16', '2025-08-06 14:18:31', '2025-08-07 15:37:16'),
	(23, 24, 10, 24, '대리점 FF ', 'dealer-f@gmail.com', '010-3123-1221', NULL, NULL, NULL, NULL, 'DAF03-2025-00001', 'preparing', 3198.00, 'test', NULL, '2025-08-06 14:40:42', '2025-08-07 15:16:12'),
	(24, 24, 10, 24, '대리점 FF ', 'dealer-f@gmail.com', '010-3123-1221', NULL, NULL, NULL, '2025-08-06 14:43:58', 'DAF03-2025-00002', 'canceled', 3198.00, 'test', '2025-08-07 15:37:20', '2025-08-06 14:43:36', '2025-08-07 15:37:20'),
	(25, 18, 10, NULL, '일루코 본사', 'illucokorea@gmail.com', '010-1231-2312', NULL, NULL, NULL, NULL, 'CST-2025-00001', 'new', 2078.00, '잘만들어주세요. 사시입니다.', NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(26, 18, 6, NULL, '일루코 본사', 'illucokorea@gmail.com', '010-1231-2312', '2025-08-07', '2025-08-08', '2025-08-08', NULL, 'CST-2025-00002', 'completed', 2745.00, '', NULL, '2025-08-07 19:58:33', '2025-08-08 08:19:12'),
	(27, 18, 11, NULL, 'ILLUCO Korea', 'illucokorea@gmail.com', '010-1231-2312', NULL, NULL, NULL, NULL, 'CST-2025-00003', 'confirmed', 1410.00, '케이스에만 각인. 프레임은 각인 x', NULL, '2025-08-12 17:45:19', '2025-08-12 17:46:57'),
	(28, 25, 12, 25, 'Lucy  ', 'yjl@illuco.co.kr', '010-9219-8739', NULL, NULL, NULL, NULL, 'KR-ILL-2025-00001', 'confirmed', 2560.00, '', NULL, '2025-08-12 17:47:25', '2025-08-12 17:59:37');

-- 테이블 db_illuco0423.order_documents 구조 내보내기
CREATE TABLE IF NOT EXISTS `order_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `document_no` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_order_documents_order_id` (`order_id`),
  KEY `fk_order_documents_user_id` (`user_id`),
  CONSTRAINT `fk_order_documents_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_documents_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_documents:~48 rows (대략적) 내보내기
INSERT INTO `order_documents` (`id`, `order_id`, `user_id`, `document_no`, `type`, `status`, `created_at`, `updated_at`) VALUES
	(21, 6, 21, 'DAC08-2025-00001', 'proforma_invoice', NULL, '2025-08-06 02:05:19', '2025-08-06 02:05:19'),
	(22, 6, 21, 'DAC08-2025-00002', 'product_request', NULL, '2025-08-06 02:05:19', '2025-08-06 02:05:19'),
	(23, 6, 21, 'DAC08-2025-00003', 'packing_list', NULL, '2025-08-06 02:05:19', '2025-08-06 02:05:19'),
	(24, 6, 21, 'DAC08-2025-00004', 'commercial_invoice', NULL, '2025-08-06 02:05:19', '2025-08-06 02:05:19'),
	(25, 7, 21, 'DAC08-2025-00005', 'proforma_invoice', NULL, '2025-08-06 02:55:33', '2025-08-06 02:55:33'),
	(26, 7, 21, 'DAC08-2025-00006', 'product_request', NULL, '2025-08-06 02:55:33', '2025-08-06 02:55:33'),
	(27, 7, 21, 'DAC08-2025-00007', 'packing_list', NULL, '2025-08-06 02:55:33', '2025-08-06 02:55:33'),
	(28, 7, 21, 'DAC08-2025-00008', 'commercial_invoice', NULL, '2025-08-06 02:55:33', '2025-08-06 02:55:33'),
	(29, 16, 19, 'DA01-2025-00001', 'proforma_invoice', NULL, '2025-08-06 03:34:00', '2025-08-06 03:34:00'),
	(30, 16, 19, 'DA01-2025-00002', 'product_request', NULL, '2025-08-06 03:34:00', '2025-08-06 03:34:00'),
	(31, 16, 19, 'DA01-2025-00003', 'packing_list', NULL, '2025-08-06 03:34:00', '2025-08-06 03:34:00'),
	(32, 16, 19, 'DA01-2025-00004', 'commercial_invoice', NULL, '2025-08-06 03:34:00', '2025-08-06 03:34:00'),
	(45, 20, 19, 'DA01-2025-00005', 'proforma_invoice', NULL, '2025-08-06 03:37:40', '2025-08-06 03:37:40'),
	(46, 20, 19, 'DA01-2025-00006', 'product_request', NULL, '2025-08-06 03:37:40', '2025-08-06 03:37:40'),
	(47, 20, 19, 'DA01-2025-00007', 'packing_list', NULL, '2025-08-06 03:37:40', '2025-08-06 03:37:40'),
	(48, 20, 19, 'DA01-2025-00008', 'commercial_invoice', NULL, '2025-08-06 03:37:40', '2025-08-06 03:37:40'),
	(49, 21, 22, 'CST-2025-00001', 'proforma_invoice', NULL, '2025-08-06 13:18:17', '2025-08-06 13:18:17'),
	(50, 21, 22, 'CST-2025-00002', 'product_request', NULL, '2025-08-06 13:18:17', '2025-08-06 13:18:17'),
	(51, 21, 22, 'CST-2025-00003', 'packing_list', NULL, '2025-08-06 13:18:17', '2025-08-06 13:18:17'),
	(52, 21, 22, 'CST-2025-00004', 'commercial_invoice', NULL, '2025-08-06 13:18:17', '2025-08-06 13:18:17'),
	(53, 22, 18, 'CST-2025-00005', 'proforma_invoice', NULL, '2025-08-06 14:18:31', '2025-08-06 14:18:31'),
	(54, 22, 18, 'CST-2025-00006', 'product_request', NULL, '2025-08-06 14:18:31', '2025-08-06 14:18:31'),
	(55, 22, 18, 'CST-2025-00007', 'packing_list', NULL, '2025-08-06 14:18:31', '2025-08-06 14:18:31'),
	(56, 22, 18, 'CST-2025-00008', 'commercial_invoice', NULL, '2025-08-06 14:18:31', '2025-08-06 14:18:31'),
	(57, 23, 24, 'DAF03-2025-00001', 'proforma_invoice', NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(58, 23, 24, 'DAF03-2025-00002', 'product_request', NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(59, 23, 24, 'DAF03-2025-00003', 'packing_list', NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(60, 23, 24, 'DAF03-2025-00004', 'commercial_invoice', NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(61, 24, 24, 'DAF03-2025-00005', 'proforma_invoice', NULL, '2025-08-06 14:43:36', '2025-08-06 14:43:36'),
	(62, 24, 24, 'DAF03-2025-00006', 'product_request', NULL, '2025-08-06 14:43:36', '2025-08-06 14:43:36'),
	(63, 24, 24, 'DAF03-2025-00007', 'packing_list', NULL, '2025-08-06 14:43:36', '2025-08-06 14:43:36'),
	(64, 24, 24, 'DAF03-2025-00008', 'commercial_invoice', NULL, '2025-08-06 14:43:36', '2025-08-06 14:43:36'),
	(65, 25, 18, 'CST-2025-00009', 'proforma_invoice', NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(66, 25, 18, 'CST-2025-00010', 'product_request', NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(67, 25, 18, 'CST-2025-00011', 'packing_list', NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(68, 25, 18, 'CST-2025-00012', 'commercial_invoice', NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(69, 26, 18, 'CST-2025-00013', 'proforma_invoice', NULL, '2025-08-07 19:58:33', '2025-08-07 19:58:33'),
	(70, 26, 18, 'CST-2025-00014', 'product_request', NULL, '2025-08-07 19:58:33', '2025-08-07 19:58:33'),
	(71, 26, 18, 'CST-2025-00015', 'packing_list', NULL, '2025-08-07 19:58:33', '2025-08-07 19:58:33'),
	(72, 26, 18, 'CST-2025-00016', 'commercial_invoice', NULL, '2025-08-07 19:58:33', '2025-08-07 19:58:33'),
	(73, 27, 18, 'CST-2025-00017', 'proforma_invoice', NULL, '2025-08-12 17:45:20', '2025-08-12 17:45:20'),
	(74, 27, 18, 'CST-2025-00018', 'product_request', NULL, '2025-08-12 17:45:20', '2025-08-12 17:45:20'),
	(75, 27, 18, 'CST-2025-00019', 'packing_list', NULL, '2025-08-12 17:45:20', '2025-08-12 17:45:20'),
	(76, 27, 18, 'CST-2025-00020', 'commercial_invoice', NULL, '2025-08-12 17:45:20', '2025-08-12 17:45:20'),
	(77, 28, 25, 'KR-ILL-2025-00001', 'proforma_invoice', NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25'),
	(78, 28, 25, 'KR-ILL-2025-00002', 'product_request', NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25'),
	(79, 28, 25, 'KR-ILL-2025-00003', 'packing_list', NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25'),
	(80, 28, 25, 'KR-ILL-2025-00004', 'commercial_invoice', NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25');

-- 테이블 db_illuco0423.order_document_commercial_invoices 구조 내보내기
CREATE TABLE IF NOT EXISTS `order_document_commercial_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bill_to_name` varchar(255) DEFAULT NULL,
  `bill_to_address` varchar(255) DEFAULT NULL,
  `bill_to_tel` varchar(255) DEFAULT NULL,
  `bill_to_attn` varchar(255) DEFAULT NULL,
  `bill_to_email` varchar(255) DEFAULT NULL,
  `ship_to_name` varchar(255) DEFAULT NULL,
  `ship_to_address` varchar(255) DEFAULT NULL,
  `ship_to_tel` varchar(255) DEFAULT NULL,
  `ship_to_attn` varchar(255) DEFAULT NULL,
  `ship_to_email` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `pi_no` varchar(255) DEFAULT NULL,
  `po_no` varchar(255) DEFAULT NULL,
  `carrier` varchar(255) DEFAULT NULL,
  `estimated_delivery_date` varchar(255) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `price_terms` varchar(255) DEFAULT NULL,
  `country_of_origin` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `hs_code` varchar(255) DEFAULT NULL,
  `dev` varchar(255) DEFAULT NULL,
  `lst` varchar(255) DEFAULT NULL,
  `ein` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_order_document_commercial_invoices_id` FOREIGN KEY (`id`) REFERENCES `order_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_document_commercial_invoices:~10 rows (대략적) 내보내기
INSERT INTO `order_document_commercial_invoices` (`id`, `bill_to_name`, `bill_to_address`, `bill_to_tel`, `bill_to_attn`, `bill_to_email`, `ship_to_name`, `ship_to_address`, `ship_to_tel`, `ship_to_attn`, `ship_to_email`, `ref_no`, `invoice_date`, `pi_no`, `po_no`, `carrier`, `estimated_delivery_date`, `payment_terms`, `price_terms`, `country_of_origin`, `currency`, `hs_code`, `dev`, `lst`, `ein`, `created_at`, `updated_at`) VALUES
	(24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 02:05:19', '2025-08-06 02:05:19'),
	(28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 02:55:33', '2025-08-06 02:55:33'),
	(32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:34:00', '2025-08-06 03:34:00'),
	(48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:37:40', '2025-08-06 03:37:40'),
	(52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 13:18:17', '2025-08-06 13:18:17'),
	(56, 'asddas', 'adsda', '010231231231', 'dasasdds', 'test@test.com', 'dasd', 'adad', '0102312312312', 'adsasd', 'test@test.com', 'asddas', '2025-08-06', '', '', 'sad', 'sasda', 'a', 'sdas', 'd', 'dasds', 'tesad', 'sadas', 'asd', '12312sd', '2025-08-06 14:18:31', '2025-08-06 14:30:56'),
	(60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:43:36', '2025-08-06 14:43:36'),
	(68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 19:58:33', '2025-08-07 19:58:33'),
	(76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 17:45:20', '2025-08-12 17:45:20'),
	(80, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25');

-- 테이블 db_illuco0423.order_document_packing_lists 구조 내보내기
CREATE TABLE IF NOT EXISTS `order_document_packing_lists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bill_to_name` varchar(255) DEFAULT NULL,
  `bill_to_address` varchar(255) DEFAULT NULL,
  `bill_to_tel` varchar(255) DEFAULT NULL,
  `bill_to_attn` varchar(255) DEFAULT NULL,
  `bill_to_email` varchar(255) DEFAULT NULL,
  `ship_to_name` varchar(255) DEFAULT NULL,
  `ship_to_address` varchar(255) DEFAULT NULL,
  `ship_to_tel` varchar(255) DEFAULT NULL,
  `ship_to_attn` varchar(255) DEFAULT NULL,
  `ship_to_email` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `packing_date` date DEFAULT NULL,
  `pi_no` varchar(255) DEFAULT NULL,
  `po_no` varchar(255) DEFAULT NULL,
  `carrier` varchar(255) DEFAULT NULL,
  `estimated_delivery_date` varchar(255) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `price_terms` varchar(255) DEFAULT NULL,
  `country_of_origin` varchar(255) DEFAULT NULL,
  `packing_details` text,
  `hs_code` varchar(255) DEFAULT NULL,
  `total_cartons` int DEFAULT NULL,
  `total_quantity` int DEFAULT NULL,
  `total_weight` decimal(10,2) DEFAULT NULL,
  `total_volume_cbm` decimal(10,5) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_order_document_packing_lists_id` FOREIGN KEY (`id`) REFERENCES `order_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_document_packing_lists:~10 rows (대략적) 내보내기
INSERT INTO `order_document_packing_lists` (`id`, `bill_to_name`, `bill_to_address`, `bill_to_tel`, `bill_to_attn`, `bill_to_email`, `ship_to_name`, `ship_to_address`, `ship_to_tel`, `ship_to_attn`, `ship_to_email`, `ref_no`, `packing_date`, `pi_no`, `po_no`, `carrier`, `estimated_delivery_date`, `payment_terms`, `price_terms`, `country_of_origin`, `packing_details`, `hs_code`, `total_cartons`, `total_quantity`, `total_weight`, `total_volume_cbm`, `created_at`, `updated_at`) VALUES
	(23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 02:05:19', '2025-08-06 02:05:19'),
	(27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 02:55:33', '2025-08-06 02:55:33'),
	(31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:34:00', '2025-08-06 03:34:00'),
	(47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:37:40', '2025-08-06 03:37:40'),
	(51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 13:18:17', '2025-08-06 13:18:17'),
	(55, 'asdasd', 'sad', '01023123122', 'tsad', 'asdas@asdas.com', 'asdas', 'asd', '01023123122', 'sadasd', 'test@test.com', 'asdas', '2025-08-06', 'ACada', 'asSDas', 'asdad', 'asdas', 'asdasd', 'sda', 'dsasd', 'test', 'asdaasd', NULL, NULL, NULL, NULL, '2025-08-06 14:18:31', '2025-08-06 14:29:56'),
	(59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:43:36', '2025-08-06 14:43:36'),
	(67, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(71, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 19:58:33', '2025-08-07 19:58:33'),
	(75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 17:45:20', '2025-08-12 17:45:20'),
	(79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25');

-- 테이블 db_illuco0423.order_document_product_requests 구조 내보내기
CREATE TABLE IF NOT EXISTS `order_document_product_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `manager_name` varchar(255) DEFAULT NULL,
  `document_no` varchar(255) DEFAULT NULL,
  `box1_no` varchar(255) DEFAULT NULL,
  `box1_weight` varchar(255) DEFAULT NULL,
  `box1_size` varchar(255) DEFAULT NULL,
  `box2_no` varchar(255) DEFAULT NULL,
  `box2_weight` varchar(255) DEFAULT NULL,
  `box2_size` varchar(255) DEFAULT NULL,
  `box3_no` varchar(255) DEFAULT NULL,
  `box3_weight` varchar(255) DEFAULT NULL,
  `box3_size` varchar(255) DEFAULT NULL,
  `box4_no` varchar(255) DEFAULT NULL,
  `box4_weight` varchar(255) DEFAULT NULL,
  `box4_size` varchar(255) DEFAULT NULL,
  `box5_no` varchar(255) DEFAULT NULL,
  `box5_weight` varchar(255) DEFAULT NULL,
  `box5_size` varchar(255) DEFAULT NULL,
  `note` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `item1_no` varchar(255) DEFAULT NULL,
  `item2_no` varchar(255) DEFAULT NULL,
  `item3_no` varchar(255) DEFAULT NULL,
  `item4_no` varchar(255) DEFAULT NULL,
  `item5_no` varchar(255) DEFAULT NULL,
  `item6_no` varchar(255) DEFAULT NULL,
  `item7_no` varchar(255) DEFAULT NULL,
  `item8_no` varchar(255) DEFAULT NULL,
  `item9_no` varchar(255) DEFAULT NULL,
  `item10_no` varchar(255) DEFAULT NULL,
  `item11_no` varchar(255) DEFAULT NULL,
  `item12_no` varchar(255) DEFAULT NULL,
  `item13_no` varchar(255) DEFAULT NULL,
  `item14_no` varchar(255) DEFAULT NULL,
  `item15_no` varchar(255) DEFAULT NULL,
  `item16_no` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_order_document_product_requests_id` FOREIGN KEY (`id`) REFERENCES `order_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_document_product_requests:~10 rows (대략적) 내보내기
INSERT INTO `order_document_product_requests` (`id`, `country`, `customer_name`, `created_date`, `delivery_date`, `manager_name`, `document_no`, `box1_no`, `box1_weight`, `box1_size`, `box2_no`, `box2_weight`, `box2_size`, `box3_no`, `box3_weight`, `box3_size`, `box4_no`, `box4_weight`, `box4_size`, `box5_no`, `box5_weight`, `box5_size`, `note`, `created_at`, `updated_at`, `item1_no`, `item2_no`, `item3_no`, `item4_no`, `item5_no`, `item6_no`, `item7_no`, `item8_no`, `item9_no`, `item10_no`, `item11_no`, `item12_no`, `item13_no`, `item14_no`, `item15_no`, `item16_no`) VALUES
	(22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 02:05:19', '2025-08-06 02:05:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 02:55:33', '2025-08-06 02:55:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:34:00', '2025-08-06 03:34:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:37:40', '2025-08-06 03:37:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 13:18:17', '2025-08-06 13:18:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(54, 'Korea', 'Jan gjun', '2025-08-06', '2025-08-07', NULL, NULL, '1', '402', '102 x 123', '', '', '', '', '', '', '', '', '', '', '', '', 'test', '2025-08-06 14:18:31', '2025-08-06 14:28:37', '1', '1', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
	(58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:43:36', '2025-08-06 14:43:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(70, '대한민국', '고객D-A', '2025-08-07', '2025-09-01', '일루코 본사', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2025-08-07 19:58:33', '2025-08-07 19:59:46', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
	(74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 17:45:20', '2025-08-12 17:45:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(78, '미국', 'pil', '2025-08-12', NULL, 'Lucy  ', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2025-08-12 17:47:25', '2025-08-12 17:51:36', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- 테이블 db_illuco0423.order_document_proforma_invoices 구조 내보내기
CREATE TABLE IF NOT EXISTS `order_document_proforma_invoices` (
  `id` bigint unsigned NOT NULL,
  `document_no` varchar(255) DEFAULT NULL,
  `purchase_order_no` varchar(255) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `buyer_name` varchar(255) DEFAULT NULL,
  `buyer_address` varchar(255) DEFAULT NULL,
  `buyer_tel` varchar(255) DEFAULT NULL,
  `buyer_attn` varchar(255) DEFAULT NULL,
  `buyer_email` varchar(255) DEFAULT NULL,
  `country_of_origin` varchar(255) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `salesperson_name` varchar(255) DEFAULT NULL,
  `salesperson_tel` varchar(255) DEFAULT NULL,
  `salesperson_email` varchar(255) DEFAULT NULL,
  `estimated_date_of_delivery` varchar(255) DEFAULT NULL,
  `price_terms` varchar(255) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `shipment_by` varchar(255) DEFAULT NULL,
  `hs_code` varchar(255) DEFAULT NULL,
  `freight_charge` decimal(12,2) DEFAULT NULL,
  `bank_beneficiary` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_address` varchar(255) DEFAULT NULL,
  `bank_swift_code` varchar(255) DEFAULT NULL,
  `bank_account_no` varchar(255) DEFAULT NULL,
  `note` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_order_document_proforma_invoices_id` FOREIGN KEY (`id`) REFERENCES `order_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_document_proforma_invoices:~10 rows (대략적) 내보내기
INSERT INTO `order_document_proforma_invoices` (`id`, `document_no`, `purchase_order_no`, `invoice_no`, `invoice_date`, `buyer_name`, `buyer_address`, `buyer_tel`, `buyer_attn`, `buyer_email`, `country_of_origin`, `currency`, `salesperson_name`, `salesperson_tel`, `salesperson_email`, `estimated_date_of_delivery`, `price_terms`, `payment_terms`, `shipment_by`, `hs_code`, `freight_charge`, `bank_beneficiary`, `bank_name`, `bank_address`, `bank_swift_code`, `bank_account_no`, `note`, `created_at`, `updated_at`) VALUES
	(21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 02:05:19', '2025-08-06 02:05:19'),
	(25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 02:55:33', '2025-08-06 02:55:33'),
	(29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:34:00', '2025-08-06 03:34:00'),
	(45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:37:40', '2025-08-06 03:37:40'),
	(49, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 13:18:17', '2025-08-06 13:18:17'),
	(53, 'AS2Csa', '1231', NULL, '2025-08-06', 'KBI Company', 'addeess', '01023122132', 'dasdas', 'test@test.com', NULL, NULL, 'ja n gjun', '01012312123', 'test@test.com', '1sdasd2sdsa', '12312dasd', 'tesdas', 'asdsadas', 'Czxcasda2dasas', 0.00, 'sadasd', 'test123', 'addes', 'sadsda', 'sdsdasad', 'dssd', '2025-08-06 14:18:31', '2025-08-06 14:26:30'),
	(57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:43:36', '2025-08-06 14:43:36'),
	(65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 19:58:33', '2025-08-07 19:58:33'),
	(73, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 17:45:20', '2025-08-12 17:45:20'),
	(77, 'KR-ILL-2025-00001', '', NULL, '2025-08-12', 'pil', '102-304 Gosan-ro 166', '131-5151-5623', '', 'yij@kflsi.fdd', NULL, NULL, 'Lucy  ', '010-9219-8739', 'yjl@illuco.co.kr', '7/30', 'DAP', '', '', '', 0.00, '', '', '', '', '', '', '2025-08-12 17:47:25', '2025-08-12 17:51:58');

-- 테이블 db_illuco0423.order_histories 구조 내보내기
CREATE TABLE IF NOT EXISTS `order_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `dealer_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '미수금 변동액',
  `settled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '이 히스토리가 처리 완료되었는지 여부',
  `memo` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_order_histories_order_id` (`order_id`),
  KEY `fk_order_histories_dealer_id` (`dealer_id`),
  KEY `fk_order_histories_customer_id` (`customer_id`),
  KEY `fk_order_histories_created_by` (`created_by`),
  CONSTRAINT `fk_order_histories_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_order_histories_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_order_histories_dealer_id` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_order_histories_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_histories:~1 rows (대략적) 내보내기
INSERT INTO `order_histories` (`id`, `order_id`, `dealer_id`, `customer_id`, `created_by`, `balance`, `settled`, `memo`, `created_at`, `updated_at`) VALUES
	(6, 22, NULL, 5, 18, 0.00, 0, '오더 주문 A필드에 문제있음.', '2025-08-06 14:23:25', '2025-08-06 14:23:25'),
	(7, 28, 25, 12, 18, 0.00, 0, 'qw', '2025-08-12 17:49:37', '2025-08-12 17:49:37');

-- 테이블 db_illuco0423.order_items 구조 내보내기
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `set_group_id` varchar(255) DEFAULT NULL COMMENT '동적 세트 그룹 번호',
  `set_group_sort` int unsigned DEFAULT NULL COMMENT '세트 그룹 내 정렬 순서',
  `is_main_item` tinyint(1) NOT NULL DEFAULT '0' COMMENT '세트 내 메인 제품 여부',
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `box_no` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id` (`order_id`),
  KEY `idx_order_items_product_id` (`product_id`),
  KEY `idx_order_items_order_id_product_id` (`order_id`,`product_id`),
  KEY `idx_order_items_set_group_id` (`set_group_id`),
  KEY `idx_order_items_set_group_id_set_group_sort` (`set_group_id`,`set_group_sort`),
  CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_items:~11 rows (대략적) 내보내기
INSERT INTO `order_items` (`id`, `set_group_id`, `set_group_sort`, `is_main_item`, `order_id`, `product_id`, `quantity`, `box_no`, `unit_price`, `total_price`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(29, 'c56f52e7-d671-4590-be2d-fc701c305ac1', NULL, 1, 23, 50, 1, NULL, 2018.00, 2018.00, NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(30, 'c56f52e7-d671-4590-be2d-fc701c305ac1', 0, 0, 23, 51, 1, NULL, 30.00, 30.00, NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(31, NULL, NULL, 1, 23, 49, 1, NULL, 1150.00, 1150.00, NULL, '2025-08-06 14:40:42', '2025-08-06 14:40:42'),
	(35, '02a40e65-8f08-4d5a-b1bf-177559581316', NULL, 1, 25, 55, 1, NULL, 2018.00, 2018.00, NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(36, '02a40e65-8f08-4d5a-b1bf-177559581316', 0, 0, 25, 56, 2, NULL, 30.00, 60.00, NULL, '2025-08-07 18:42:39', '2025-08-07 18:42:39'),
	(37, NULL, NULL, 1, 26, 57, 1, NULL, 2745.00, 2745.00, NULL, '2025-08-07 19:58:33', '2025-08-07 19:58:33'),
	(38, '961e2bdf-aff1-427b-abab-b03548e80d1a', NULL, 1, 27, 59, 1, NULL, 1350.00, 1350.00, NULL, '2025-08-12 17:45:19', '2025-08-12 17:45:19'),
	(39, '961e2bdf-aff1-427b-abab-b03548e80d1a', 0, 0, 27, 60, 2, NULL, 30.00, 60.00, NULL, '2025-08-12 17:45:19', '2025-08-12 17:45:19'),
	(40, NULL, NULL, 1, 28, 63, 1, NULL, 1150.00, 1150.00, NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25'),
	(41, '0f4ebfd9-3c2e-4bfa-aa1a-8e7b1ce4ca90', NULL, 1, 28, 61, 1, NULL, 1350.00, 1350.00, NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25'),
	(42, '0f4ebfd9-3c2e-4bfa-aa1a-8e7b1ce4ca90', 0, 0, 28, 62, 2, NULL, 30.00, 60.00, NULL, '2025-08-12 17:47:25', '2025-08-12 17:47:25');

-- 테이블 db_illuco0423.order_logs 구조 내보내기
CREATE TABLE IF NOT EXISTS `order_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `previous_status` varchar(255) DEFAULT NULL COMMENT '변경 전 상태',
  `status` varchar(255) NOT NULL COMMENT '변경 후 상태',
  `message` text COMMENT '변경 사유 등 추가 메모',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_order_logs_order_id` (`order_id`),
  KEY `fk_order_logs_user_id` (`user_id`),
  CONSTRAINT `fk_order_logs_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_logs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_logs:~22 rows (대략적) 내보내기
INSERT INTO `order_logs` (`id`, `order_id`, `user_id`, `previous_status`, `status`, `message`, `created_at`, `updated_at`) VALUES
	(5, 6, 21, 'new', 'canceled', NULL, '2025-08-06 02:15:42', '2025-08-06 02:15:42'),
	(6, 6, 21, 'new', 'canceled', NULL, '2025-08-06 02:16:55', '2025-08-06 02:16:55'),
	(7, 6, 21, 'new', 'canceled', NULL, '2025-08-06 02:18:45', '2025-08-06 02:18:45'),
	(8, 6, 21, 'new', 'canceled', NULL, '2025-08-06 02:44:15', '2025-08-06 02:44:15'),
	(9, 6, 21, 'new', 'canceled', NULL, '2025-08-06 02:44:45', '2025-08-06 02:44:45'),
	(10, 6, 21, 'new', 'canceled', NULL, '2025-08-06 02:45:20', '2025-08-06 02:45:20'),
	(11, 20, 22, 'new', 'preparing', NULL, '2025-08-06 03:40:58', '2025-08-06 03:40:58'),
	(12, 20, 22, 'new', 'preparing', NULL, '2025-08-06 03:44:27', '2025-08-06 03:44:27'),
	(13, 22, 18, 'new', 'preparing', NULL, '2025-08-06 14:22:53', '2025-08-06 14:22:53'),
	(14, 23, 18, 'new', 'preparing', NULL, '2025-08-06 14:42:13', '2025-08-06 14:42:13'),
	(15, 24, 24, 'new', 'canceled', NULL, '2025-08-06 14:43:58', '2025-08-06 14:43:58'),
	(16, 23, 18, 'preparing', 'preparing', NULL, '2025-08-07 15:15:14', '2025-08-07 15:15:14'),
	(17, 23, 18, 'preparing', 'shipped', NULL, '2025-08-07 15:16:08', '2025-08-07 15:16:08'),
	(18, 23, 18, 'shipped', 'preparing', NULL, '2025-08-07 15:16:12', '2025-08-07 15:16:12'),
	(19, 26, 18, 'new', 'shipped', NULL, '2025-08-08 08:17:42', '2025-08-08 08:17:42'),
	(20, 26, 18, 'shipped', 'rejected', NULL, '2025-08-08 08:18:50', '2025-08-08 08:18:50'),
	(21, 26, 18, 'rejected', 'completed', NULL, '2025-08-08 08:19:12', '2025-08-08 08:19:12'),
	(22, 27, 18, 'new', 'confirmed', NULL, '2025-08-12 17:46:57', '2025-08-12 17:46:57'),
	(23, 28, 18, 'new', 'confirmed', NULL, '2025-08-12 17:51:00', '2025-08-12 17:51:00'),
	(24, 28, 18, 'confirmed', 'completed', NULL, '2025-08-12 17:57:44', '2025-08-12 17:57:44'),
	(25, 28, 18, 'completed', 'shipped', NULL, '2025-08-12 17:58:07', '2025-08-12 17:58:07'),
	(26, 28, 18, 'shipped', 'confirmed', NULL, '2025-08-12 17:59:37', '2025-08-12 17:59:37');

-- 테이블 db_illuco0423.password_reset_tokens 구조 내보내기
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_password_reset_tokens_user_id` (`user_id`),
  CONSTRAINT `fk_password_reset_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.password_reset_tokens:~0 rows (대략적) 내보내기

-- 테이블 db_illuco0423.permissions 구조 내보내기
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.permissions:~40 rows (대략적) 내보내기
INSERT INTO `permissions` (`id`, `resource`, `action`, `created_at`, `updated_at`) VALUES
	(1, 'employee', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(2, 'employee', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(3, 'employee', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(4, 'employee', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(5, 'dealer', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(6, 'dealer', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(7, 'dealer', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(8, 'dealer', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(9, 'claim', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(10, 'claim', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(11, 'claim', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(12, 'claim', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(13, 'notice', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(14, 'notice', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(15, 'notice', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(16, 'notice', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(17, 'customer', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(18, 'customer', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(19, 'customer', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(20, 'customer', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(21, 'product', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(22, 'product', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(23, 'product', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(24, 'product', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(25, 'order', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(26, 'order', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(27, 'order', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(28, 'order', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(29, 'cart', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(30, 'cart', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(31, 'cart', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(32, 'cart', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(33, 'role', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(34, 'role', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(35, 'role', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(36, 'role', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(37, 'category', 'create', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(38, 'category', 'read', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(39, 'category', 'update', '2025-07-09 06:51:36', '2025-07-09 06:51:36'),
	(40, 'category', 'delete', '2025-07-09 06:51:36', '2025-07-09 06:51:36');

-- 테이블 db_illuco0423.products 구조 내보내기
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `template_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_products_template_id` (`template_id`),
  CONSTRAINT `fk_products_template_id` FOREIGN KEY (`template_id`) REFERENCES `product_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.products:~36 rows (대략적) 내보내기
INSERT INTO `products` (`id`, `template_id`, `name`, `serial_number`, `type`, `code`, `model`, `price`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 2, '무선 헤드라이트', 'HLNNN25000001A', 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', '2025-07-17 02:13:47', '2025-07-09 06:59:18', '2025-07-17 02:13:47'),
	(2, 4, 'Galilean', 'LPNNN25000001A', 'loupe', 'LP', 'ITL-1040P', 1350.00, 'galilean loupe', '2025-07-17 02:13:37', '2025-07-09 07:55:09', '2025-07-17 02:13:37'),
	(3, 3, 'ErgoX', 'LPNNN25000003A', 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', '2025-07-17 02:13:47', '2025-07-10 23:48:31', '2025-07-17 02:13:47'),
	(4, 5, '처방렌즈', 'PLNNN25000001A', NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', '2025-07-17 02:13:47', '2025-07-10 23:48:31', '2025-07-17 02:13:47'),
	(5, 4, 'Galilean', 'LPNNN25000002A', 'loupe', 'LP', 'ITL-1040P', 1350.00, 'galilean loupe', '2025-07-17 02:13:47', '2025-07-10 23:48:50', '2025-07-17 02:13:47'),
	(6, 2, '무선 헤드라이트', 'HLNNN25000002A', 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', '2025-07-17 02:13:47', '2025-07-10 23:49:23', '2025-07-17 02:13:47'),
	(7, 3, 'ErgoX', 'LPNNN25000005A', 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-07-17 07:30:29', '2025-07-21 14:03:19'),
	(8, 3, 'ErgoX', 'LPNNN25000004A', 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-07-17 07:30:51', '2025-07-21 14:03:19'),
	(9, 3, 'ErgoX', 'LPNNN25000003A', 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-07-17 07:31:23', '2025-07-21 14:03:19'),
	(10, 5, '처방렌즈', 'PLNNN25000001A', NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-07-17 07:31:23', '2025-07-21 14:03:19'),
	(11, 3, 'ErgoX', 'LPNNN25000002A', 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-07-17 07:45:58', '2025-07-21 14:03:19'),
	(12, 3, 'ErgoX', 'LPNNN25000001A', 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-07-17 07:46:29', '2025-07-21 14:03:19'),
	(13, 3, 'ErgoX', NULL, 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-07-21 13:57:44', '2025-07-21 13:57:44'),
	(14, 5, '처방렌즈', NULL, NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-07-21 13:57:44', '2025-07-21 13:57:44'),
	(15, 3, 'ErgoX', NULL, 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', '2025-08-07 15:37:16', '2025-08-06 02:02:17', '2025-08-07 15:37:16'),
	(16, 5, '처방렌즈', NULL, NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', '2025-08-07 15:37:16', '2025-08-06 02:02:17', '2025-08-07 15:37:16'),
	(17, 2, '무선 헤드라이트', NULL, 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', '2025-08-07 15:37:16', '2025-08-06 02:02:41', '2025-08-07 15:37:16'),
	(43, 4, 'Galilean', 'LPNNN25000006A', 'loupe', 'LP', 'ITL-1040P', 1350.00, 'galilean loupe', '2025-08-07 15:37:16', '2025-08-06 03:34:29', '2025-08-07 15:37:16'),
	(46, 7, 'Flip-up', 'LPNNN25000008A', 'loupe', 'LP', 'ITL-1035G', 2018.00, 'flip-up loupe desription', '2025-08-07 15:37:16', '2025-08-06 14:15:43', '2025-08-07 15:37:16'),
	(47, 3, 'ErgoX', 'LPNNN25000007A', 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', '2025-08-07 15:37:16', '2025-08-06 14:18:09', '2025-08-07 15:37:16'),
	(48, 5, '처방렌즈', 'PLNNN25000002A', NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', '2025-08-07 15:37:16', '2025-08-06 14:18:09', '2025-08-07 15:37:16'),
	(49, 2, '무선 헤드라이트', 'HLNNN25000001A', 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', NULL, '2025-08-06 14:39:24', '2025-08-06 14:42:13'),
	(50, 7, 'Flip-up', 'LPNNN25000009A', 'loupe', 'LP', 'ITL-1035G', 2018.00, 'flip-up loupe desription', NULL, '2025-08-06 14:40:18', '2025-08-06 14:42:13'),
	(51, 5, '처방렌즈', 'PLNNN25000003A', NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-08-06 14:40:18', '2025-08-06 14:42:13'),
	(52, 7, 'Flip-up', NULL, 'loupe', 'LP', 'ITL-1035G', 2018.00, 'flip-up loupe desription', '2025-08-07 15:37:20', '2025-08-06 14:43:15', '2025-08-07 15:37:20'),
	(53, 5, '처방렌즈', NULL, NULL, 'PL', 'PR-LENS-30', 30.00, '', '2025-08-07 15:37:20', '2025-08-06 14:43:15', '2025-08-07 15:37:20'),
	(54, 2, '무선 헤드라이트', NULL, 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', '2025-08-07 15:37:20', '2025-08-06 14:43:15', '2025-08-07 15:37:20'),
	(55, 7, 'Flip-up', NULL, 'loupe', 'LP', 'ITL-1035G', 2018.00, 'flip-up loupe desription', NULL, '2025-08-07 18:41:08', '2025-08-07 18:41:08'),
	(56, 5, '처방렌즈', NULL, NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-08-07 18:41:08', '2025-08-07 18:41:08'),
	(57, 3, 'ErgoX', NULL, 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-08-07 19:58:26', '2025-08-07 19:58:26'),
	(58, 3, 'ErgoX', NULL, 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-08-08 08:14:37', '2025-08-08 08:14:37'),
	(59, 4, 'Galilean', NULL, 'loupe', 'LP', 'ITL-1040P', 1350.00, 'galilean loupe', NULL, '2025-08-12 17:44:40', '2025-08-12 17:44:40'),
	(60, 5, '처방렌즈', NULL, NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-08-12 17:44:40', '2025-08-12 17:44:40'),
	(61, 4, 'Galilean', NULL, 'loupe', 'LP', 'ITL-1040P', 1350.00, 'galilean loupe', NULL, '2025-08-12 17:47:02', '2025-08-12 17:47:02'),
	(62, 5, '처방렌즈', NULL, NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-08-12 17:47:02', '2025-08-12 17:47:02'),
	(63, 2, '무선 헤드라이트', NULL, 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', NULL, '2025-08-12 17:47:18', '2025-08-12 17:47:18');

-- 테이블 db_illuco0423.product_categories 구조 내보내기
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `description` text,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_product_categories_parent_id` (`parent_id`),
  CONSTRAINT `fk_product_categories_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.product_categories:~4 rows (대략적) 내보내기
INSERT INTO `product_categories` (`id`, `parent_id`, `slug`, `label`, `description`, `is_visible`, `sort_order`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'headlight', 'Headlights', NULL, 1, 2, '2025-07-03 11:34:38', '2025-08-12 17:30:27'),
	(2, NULL, 'loupe', 'Loupes', NULL, 1, 1, '2025-07-03 11:34:44', '2025-08-12 17:30:24'),
	(3, NULL, 'dermatoscope', 'Dermatoscope', NULL, 1, 0, '2025-07-03 11:34:52', '2025-08-12 17:30:36'),
	(11, NULL, NULL, 'Dental Mirror', NULL, 1, 3, '2025-08-06 14:09:47', '2025-08-12 17:29:39');

-- 테이블 db_illuco0423.product_headlights 구조 내보내기
CREATE TABLE IF NOT EXISTS `product_headlights` (
  `id` bigint unsigned NOT NULL,
  `type` varchar(40) DEFAULT NULL,
  `wireless_color` varchar(255) DEFAULT NULL,
  `engraving_text` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_product_headlights_id` FOREIGN KEY (`id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.product_headlights:~7 rows (대략적) 내보내기
INSERT INTO `product_headlights` (`id`, `type`, `wireless_color`, `engraving_text`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'gray', 'asdsdasddas', '2025-07-09 06:59:18', '2025-07-09 06:59:18'),
	(6, NULL, 'gold', NULL, '2025-07-10 23:49:24', '2025-07-10 23:49:24'),
	(17, NULL, 'gray', NULL, '2025-08-06 02:02:41', '2025-08-06 02:02:41'),
	(49, NULL, 'pink', '장준', '2025-08-06 14:39:24', '2025-08-06 14:39:24'),
	(54, NULL, 'pink', '장준', '2025-08-06 14:43:15', '2025-08-06 14:43:15'),
	(63, NULL, 'gray', 'PIL', '2025-08-12 17:47:18', '2025-08-12 17:47:18');

-- 테이블 db_illuco0423.product_loupes 구조 내보내기
CREATE TABLE IF NOT EXISTS `product_loupes` (
  `id` bigint unsigned NOT NULL,
  `type` enum('ready-made','custom-made') NOT NULL COMMENT '제품 타입',
  `engraving_text` varchar(255) DEFAULT NULL,
  `frame_type` varchar(255) DEFAULT NULL,
  `working_distance` decimal(5,1) DEFAULT NULL COMMENT '작업거리(WD), 단위 cm',
  `od_sph` decimal(4,2) DEFAULT NULL,
  `os_sph` decimal(4,2) DEFAULT NULL,
  `od_cyl` decimal(4,2) DEFAULT NULL,
  `os_cyl` decimal(4,2) DEFAULT NULL,
  `od_axis` int DEFAULT NULL,
  `os_axis` int DEFAULT NULL,
  `od_add` decimal(4,2) DEFAULT NULL,
  `os_add` decimal(4,2) DEFAULT NULL,
  `pd_right` decimal(4,1) DEFAULT NULL,
  `pd_left` decimal(4,1) DEFAULT NULL,
  `pd_total` decimal(4,1) DEFAULT NULL,
  `vertex_distance` decimal(4,1) DEFAULT NULL COMMENT '버텍스 거리 (VD, 단위: mm)',
  `add_option` enum('ignore','include','zero_diopter') DEFAULT NULL COMMENT 'ADD 옵션',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_product_loupes_id` FOREIGN KEY (`id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.product_loupes:~19 rows (대략적) 내보내기
INSERT INTO `product_loupes` (`id`, `type`, `engraving_text`, `frame_type`, `working_distance`, `od_sph`, `os_sph`, `od_cyl`, `os_cyl`, `od_axis`, `os_axis`, `od_add`, `os_add`, `pd_right`, `pd_left`, `pd_total`, `vertex_distance`, `add_option`, `created_at`, `updated_at`) VALUES
	(2, 'ready-made', NULL, 'frame4', 47.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:09', '2025-07-09 07:55:09'),
	(3, 'custom-made', NULL, 'frame1', 47.0, 1.00, 0.00, 2.00, 1.00, 20, 120, 2.00, 1.00, 27.0, 28.0, 55.0, 24.0, 'ignore', '2025-07-10 23:48:31', '2025-07-10 23:48:31'),
	(5, 'ready-made', NULL, 'frame4', 46.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 23:48:50', '2025-07-10 23:48:50'),
	(7, 'ready-made', NULL, 'frame2', 47.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-17 07:30:29', '2025-07-17 07:30:29'),
	(8, 'ready-made', NULL, 'frame2', 47.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-17 07:30:51', '2025-07-17 07:30:51'),
	(9, 'custom-made', NULL, 'frame2', 47.0, 1.00, 2.00, -1.00, 3.00, 120, 20, 2.00, 1.00, 27.0, 28.0, 55.0, 24.0, 'include', '2025-07-17 07:31:23', '2025-07-17 07:31:23'),
	(11, 'custom-made', 'ㄴㅁㅇㅁㄴㅇㄴㅇㄴㅁ', 'frame2', 46.0, 0.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 0.00, 27.0, 28.0, 55.0, 24.0, 'include', '2025-07-17 07:45:58', '2025-07-17 07:45:58'),
	(12, 'custom-made', NULL, 'frame2', 46.0, 0.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 0.00, 27.0, 28.0, 55.0, 24.0, 'ignore', '2025-07-17 07:46:29', '2025-07-17 07:46:29'),
	(13, 'custom-made', NULL, 'frame2', 48.0, 1.00, 1.00, 0.00, -1.00, 20, 180, 3.00, 2.00, 28.0, 27.0, 55.0, 24.0, 'ignore', '2025-07-21 13:57:44', '2025-07-21 13:57:44'),
	(15, 'custom-made', NULL, 'frame1', 47.0, 1.00, 1.00, 0.00, -1.00, 35, 120, 4.00, 2.00, 28.0, 29.0, 57.0, 24.0, 'ignore', '2025-08-06 02:02:17', '2025-08-06 02:02:17'),
	(43, 'ready-made', NULL, 'frame4', 46.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 03:34:29', '2025-08-06 03:34:29'),
	(46, 'ready-made', NULL, 'frame2', 40.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 14:15:43', '2025-08-06 14:15:43'),
	(47, 'custom-made', NULL, 'frame2', 40.0, 1.00, 0.00, 2.00, 2.00, 20, 15, 1.00, 0.00, 28.0, 29.0, 57.0, 24.0, 'include', '2025-08-06 14:18:09', '2025-08-06 14:18:09'),
	(50, 'custom-made', '홍길동', 'frame2', 40.0, 1.00, -1.00, 0.00, 1.00, 20, 0, 2.00, 4.00, 30.0, 28.0, 58.0, 24.0, 'include', '2025-08-06 14:40:18', '2025-08-06 14:40:18'),
	(52, 'custom-made', '홍길동', 'frame2', 40.0, 1.00, -1.00, 0.00, 1.00, 20, 0, 2.00, 4.00, 30.0, 28.0, 58.0, 24.0, 'include', '2025-08-06 14:43:15', '2025-08-06 14:43:15'),
	(55, 'custom-made', 'Dr.Hong (오스카)', 'frame2', 50.0, -2.50, 2.50, -2.50, 0.00, 180, 0, 2.00, 0.00, 31.0, 30.0, 61.0, 10.0, 'include', '2025-08-07 18:41:08', '2025-08-07 18:41:08'),
	(57, 'ready-made', '제리', 'frame1', 50.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 19:58:26', '2025-08-07 19:58:26'),
	(58, 'ready-made', '제리', 'frame1', 50.0, 0.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 0.00, 0.0, 0.0, 0.0, 0.0, NULL, '2025-08-08 08:14:37', '2025-08-08 08:14:37'),
	(59, 'custom-made', 'Dr. Lee', 'frame4', 45.0, -1.25, -0.25, 0.00, 0.00, 0, 0, 0.00, 0.00, 27.0, 27.0, 54.0, 12.0, 'include', '2025-08-12 17:44:40', '2025-08-12 17:44:40'),
	(61, 'custom-made', 'PIL', 'frame4', 47.0, 0.50, 0.75, 0.75, 0.75, 150, 150, 0.75, 0.75, 32.0, 32.0, 64.0, 23.0, 'include', '2025-08-12 17:47:02', '2025-08-12 17:47:02');

-- 테이블 db_illuco0423.product_serials 구조 내보내기
CREATE TABLE IF NOT EXISTS `product_serials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '고유 ID',
  `product_id` bigint unsigned NOT NULL COMMENT '제품 ID (products 테이블 참조)',
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '시리얼 번호 (고유값)',
  `status` enum('in_stock','reserved','sold','returned') COLLATE utf8mb4_unicode_ci DEFAULT 'in_stock' COMMENT '상태',
  `order_item_id` bigint unsigned DEFAULT NULL COMMENT '주문항목 ID (order_items 참조)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `fk_product_serials_product_id` (`product_id`),
  KEY `fk_product_serials_order_item_id` (`order_item_id`),
  CONSTRAINT `fk_product_serials_order_item_id` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_product_serials_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='제품 시리얼 개별 관리';

-- 테이블 데이터 db_illuco0423.product_serials:~2 rows (대략적) 내보내기
INSERT INTO `product_serials` (`id`, `product_id`, `serial_number`, `status`, `order_item_id`, `created_at`, `updated_at`) VALUES
	(1, 50, 'LPNNN25000001A', 'sold', 29, '2025-08-07 15:15:14', '2025-08-07 15:15:14'),
	(2, 51, 'PLNNN25000001A', 'sold', 30, '2025-08-07 15:15:14', '2025-08-07 15:15:14'),
	(3, 49, 'HLNNN25000001A', 'sold', 31, '2025-08-07 15:15:14', '2025-08-07 15:15:14');

-- 테이블 db_illuco0423.product_templates 구조 내보내기
CREATE TABLE IF NOT EXISTS `product_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sort_order` int NOT NULL DEFAULT '0',
  `description` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_product_templates_category_id` (`category_id`),
  CONSTRAINT `fk_product_templates_category_id` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.product_templates:~7 rows (대략적) 내보내기
INSERT INTO `product_templates` (`id`, `category_id`, `name`, `code`, `model`, `price`, `sort_order`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 1, '유선 헤드라이트', 'HL', 'IHL-1000', 1050.00, 0, 'wired headlight.', NULL, '2025-07-03 11:37:22', '2025-07-03 11:37:22'),
	(2, 1, '무선 헤드라이트', 'HL', 'IHL-2000', 1150.00, 0, '무선 헤드라이트. wireless headlight', NULL, '2025-07-03 11:38:05', '2025-07-03 11:38:05'),
	(3, 2, 'ErgoX', 'LP', 'ITL-1025G', 2745.00, 0, 'ergox', NULL, '2025-07-03 11:39:11', '2025-07-03 11:40:05'),
	(4, 2, 'Galilean', 'LP', 'ITL-1040P', 1350.00, 0, 'galilean loupe', NULL, '2025-07-03 11:39:55', '2025-07-03 11:39:55'),
	(5, NULL, '처방렌즈', 'PL', 'PR-LENS-30', 30.00, 0, '', NULL, '2025-07-03 23:44:48', '2025-07-03 23:44:55'),
	(7, 2, 'Flip-up', 'LP', 'ITL-1035G', 380.00, 6, 'flip-up loupe desription', NULL, '2025-08-06 14:11:47', '2025-08-12 17:24:08');

-- 테이블 db_illuco0423.remember_tokens 구조 내보내기
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_remember_tokens_user_id` (`user_id`),
  CONSTRAINT `fk_remember_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.remember_tokens:~0 rows (대략적) 내보내기

-- 테이블 db_illuco0423.roles 구조 내보내기
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.roles:~4 rows (대략적) 내보내기
INSERT INTO `roles` (`id`, `label`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(5, '관리자', 'admin', '최고 관리자', '2025-07-09 06:51:43', '2025-07-10 02:37:58'),
	(6, '대리점', 'dealer', '대리점 권한입니다.', '2025-07-09 07:43:58', '2025-07-10 02:37:56'),
	(7, '직원', 'employee', '직원 권한입니다.', '2025-07-09 07:44:26', '2025-07-10 02:37:43'),
	(8, '영업담당자', 'sales', '영업담당자 권한입니다.', '2025-07-09 07:45:46', '2025-07-10 02:37:29');

-- 테이블 db_illuco0423.role_permissions 구조 내보내기
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_role_permissions_role_id` (`role_id`),
  KEY `fk_role_permissions_permission_id` (`permission_id`),
  CONSTRAINT `fk_role_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.role_permissions:~121 rows (대략적) 내보내기
INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
	(1, 5, 1, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(2, 5, 2, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(3, 5, 3, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(4, 5, 4, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(5, 5, 5, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(6, 5, 6, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(7, 5, 7, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(8, 5, 8, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(9, 5, 9, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(10, 5, 10, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(11, 5, 11, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(12, 5, 12, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(13, 5, 13, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(14, 5, 14, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(15, 5, 15, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(16, 5, 16, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(17, 5, 17, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(18, 5, 18, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(19, 5, 19, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(20, 5, 20, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(21, 5, 21, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(22, 5, 22, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(23, 5, 23, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(24, 5, 24, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(25, 5, 25, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(26, 5, 26, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(27, 5, 27, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(28, 5, 28, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(29, 5, 29, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(30, 5, 30, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(31, 5, 31, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(32, 5, 32, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(33, 5, 33, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(34, 5, 34, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(35, 5, 35, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(36, 5, 36, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(37, 5, 37, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(38, 5, 38, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(39, 5, 39, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(40, 5, 40, '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
	(42, 6, 10, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(46, 6, 14, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(49, 6, 17, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(50, 6, 18, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(51, 6, 19, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(52, 6, 20, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(57, 6, 26, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(58, 6, 29, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(59, 6, 30, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(60, 6, 31, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(61, 6, 32, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
	(62, 7, 5, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(63, 7, 6, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(64, 7, 7, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(65, 7, 8, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(66, 7, 9, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(67, 7, 10, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(68, 7, 11, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(69, 7, 12, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(70, 7, 13, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(71, 7, 14, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(72, 7, 15, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(73, 7, 16, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(74, 7, 17, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(75, 7, 18, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(76, 7, 19, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(77, 7, 20, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(78, 7, 21, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(79, 7, 22, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(80, 7, 23, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(81, 7, 24, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(82, 7, 25, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(83, 7, 26, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(84, 7, 27, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(85, 7, 28, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(86, 7, 29, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(87, 7, 30, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(88, 7, 31, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(89, 7, 32, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(90, 7, 37, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(91, 7, 38, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(92, 7, 39, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(93, 7, 40, '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
	(94, 8, 1, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(95, 8, 2, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(96, 8, 3, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(97, 8, 4, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(98, 8, 5, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(99, 8, 6, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(100, 8, 7, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(101, 8, 8, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(102, 8, 9, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(103, 8, 10, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(104, 8, 11, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(105, 8, 12, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(106, 8, 13, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(107, 8, 14, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(108, 8, 15, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(109, 8, 16, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(110, 8, 17, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(111, 8, 18, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(112, 8, 19, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(113, 8, 20, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(114, 8, 21, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(115, 8, 22, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(116, 8, 23, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(117, 8, 24, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(118, 8, 25, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(119, 8, 26, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(120, 8, 27, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(121, 8, 28, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(122, 8, 29, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(123, 8, 30, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(124, 8, 31, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(125, 8, 32, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(126, 8, 37, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(127, 8, 38, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(128, 8, 39, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(129, 8, 40, '2025-07-09 07:45:46', '2025-07-09 07:45:46'),
	(130, 6, 25, '2025-07-17 07:09:05', '2025-07-17 07:09:05'),
	(131, 6, 9, '2025-07-17 08:06:52', '2025-07-17 08:06:52');

-- 테이블 db_illuco0423.role_users 구조 내보내기
CREATE TABLE IF NOT EXISTS `role_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_role_users_user_id_role_id` (`user_id`,`role_id`),
  KEY `fk_role_users_role_id` (`role_id`),
  CONSTRAINT `fk_role_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.role_users:~15 rows (대략적) 내보내기
INSERT INTO `role_users` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
	(2, 7, 5, '2025-07-09 06:56:48', '2025-07-09 06:56:48'),
	(3, 9, 6, '2025-07-09 07:46:14', '2025-07-09 07:46:14'),
	(4, 10, 7, '2025-07-10 02:30:42', '2025-07-10 02:30:42'),
	(8, 11, 8, '2025-07-10 04:14:39', '2025-07-10 04:14:39'),
	(9, 12, 6, '2025-07-10 06:22:29', '2025-07-10 06:22:29'),
	(10, 13, 6, '2025-07-11 05:53:49', '2025-07-11 05:53:49'),
	(12, 15, 6, '2025-07-11 06:00:46', '2025-07-11 06:00:46'),
	(13, 16, 6, '2025-07-11 06:01:06', '2025-07-11 06:01:06'),
	(14, 17, 7, '2025-08-06 01:05:29', '2025-08-06 01:05:29'),
	(16, 19, 6, '2025-08-06 01:19:03', '2025-08-06 01:19:03'),
	(17, 20, 6, '2025-08-06 01:19:44', '2025-08-06 01:19:44'),
	(18, 21, 6, '2025-08-06 01:51:24', '2025-08-06 01:51:24'),
	(19, 18, 8, '2025-08-06 03:22:35', '2025-08-06 03:22:35'),
	(20, 22, 7, '2025-08-06 03:25:50', '2025-08-06 03:25:50'),
	(21, 23, 7, '2025-08-06 14:04:31', '2025-08-06 14:04:31'),
	(22, 24, 6, '2025-08-06 14:08:20', '2025-08-06 14:08:20'),
	(23, 25, 6, '2025-08-12 17:13:36', '2025-08-12 17:13:36');

-- 테이블 db_illuco0423.serial_numbers 구조 내보내기
CREATE TABLE IF NOT EXISTS `serial_numbers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `serial_no` varchar(50) NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `model_prefix` varchar(20) NOT NULL,
  `special_prefix` varchar(20) NOT NULL DEFAULT 'NNN',
  `year` char(2) NOT NULL,
  `sequence` int unsigned NOT NULL,
  `revision` char(2) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_serial_numbers_model_prefix_year` (`model_prefix`,`year`),
  KEY `fk_serial_numbers_product_id` (`product_id`),
  CONSTRAINT `fk_serial_numbers_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.serial_numbers:~0 rows (대략적) 내보내기

-- 테이블 db_illuco0423.sessions 구조 내보내기
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text,
  `last_activity` timestamp NULL DEFAULT NULL,
  `payload` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sessions_user_id` (`user_id`),
  CONSTRAINT `fk_sessions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.sessions:~28 rows (대략적) 내보내기
INSERT INTO `sessions` (`id`, `session_id`, `user_id`, `ip_address`, `user_agent`, `last_activity`, `payload`, `created_at`, `updated_at`) VALUES
	(13, 'b70aac41f6522965e1762155c7cc331ea6f13455', NULL, '172.23.0.1', NULL, '2025-07-09 16:43:10', 'a:0:{}', '2025-07-09 07:43:10', '2025-07-09 16:43:10'),
	(30, '1ed43804cf7ec34d6f08502478b1c05e14446d7b', NULL, '172.23.0.1', NULL, '2025-07-10 10:23:35', 'a:0:{}', '2025-07-10 01:23:35', '2025-07-10 10:23:35'),
	(35, 'eaa93f5149e25d8d5eca9dde4cdd082557f24189', NULL, '172.23.0.1', NULL, '2025-07-10 10:39:01', 'a:0:{}', '2025-07-10 01:39:01', '2025-07-10 10:39:01'),
	(64, '9eaf8603b51226df51c3ff6ad53451b6aee37816', NULL, '172.23.0.1', NULL, '2025-07-10 15:24:16', 'a:0:{}', '2025-07-10 06:24:16', '2025-07-10 15:24:16'),
	(79, '4c437989aa118fd3b3e139edf3c4e4213e599fe8', NULL, '172.23.0.1', NULL, '2025-07-11 08:11:03', 'a:0:{}', '2025-07-10 23:11:03', '2025-07-11 08:11:03'),
	(80, '96cdb57800dc76735f248345253fa758005641b0', NULL, '172.23.0.1', NULL, '2025-07-11 08:11:09', 'a:0:{}', '2025-07-10 23:11:09', '2025-07-11 08:11:09'),
	(82, '3fcf023cda89f6bc0dd54f28265cbe404b1b83ab', NULL, '172.23.0.1', NULL, '2025-07-11 08:58:22', 'a:0:{}', '2025-07-10 23:58:22', '2025-07-11 08:58:22'),
	(101, 'c20cbf3c2611294c80f8528de3db331190794aca', NULL, '172.23.0.1', NULL, '2025-07-11 15:32:08', 'a:0:{}', '2025-07-11 06:32:08', '2025-07-11 15:32:08'),
	(104, '394c0087496b9ea7c6f4fe77df1a6324b56706d7', NULL, '172.23.0.1', NULL, '2025-07-11 16:09:22', 'a:0:{}', '2025-07-11 07:09:22', '2025-07-11 16:09:22'),
	(108, 'db4ba1a1dc218d985ad05ba4ac0bfd6abca10880', NULL, '172.23.0.1', NULL, '2025-07-11 17:14:40', 'a:0:{}', '2025-07-11 08:14:40', '2025-07-11 17:14:40'),
	(110, '66215f7856a9344bf5b802f46b3ce088387ad48a', NULL, '172.23.0.1', NULL, '2025-07-11 17:51:00', 'a:0:{}', '2025-07-11 08:51:00', '2025-07-11 17:51:00'),
	(118, '2bacfa4bfac6e7d308d7969bf4e52632591e8007', NULL, '172.23.0.1', NULL, '2025-07-14 09:31:24', 'a:0:{}', '2025-07-14 00:31:24', '2025-07-14 09:31:24'),
	(119, 'b92e40ff45e6cee93b6aceba327d9e05286686e9', NULL, '172.23.0.1', NULL, '2025-07-14 09:35:00', 'a:0:{}', '2025-07-14 00:35:00', '2025-07-14 09:35:00'),
	(133, '6796168db8f31f32f776e2cdd548ba3447e7ef21', NULL, '172.23.0.1', NULL, '2025-07-14 15:13:19', 'a:0:{}', '2025-07-14 06:13:19', '2025-07-14 15:13:19'),
	(145, '3b7429ac7f09520c293dc07eec1f4822ec304b4c', NULL, '172.23.0.1', NULL, '2025-07-15 10:50:24', 'a:0:{}', '2025-07-15 01:50:24', '2025-07-15 10:50:24'),
	(162, 'd31616de7f745c203f720b6773d259dd393267cb', NULL, '172.23.0.1', NULL, '2025-07-16 10:57:53', 'a:0:{}', '2025-07-16 01:57:53', '2025-07-16 10:57:53'),
	(164, 'c54659aaf0fae9892653920023150b6be26bbdd4', NULL, '172.23.0.1', NULL, '2025-07-16 11:29:33', 'a:0:{}', '2025-07-16 02:29:33', '2025-07-16 11:29:33'),
	(165, '201c2a33e5d144124d62a6d639c645f5defa7278', NULL, '172.23.0.1', NULL, '2025-07-16 12:05:11', 'a:0:{}', '2025-07-16 03:05:11', '2025-07-16 12:05:11'),
	(166, '54415caaded3746024b17bf3369a6181885da475', NULL, '172.23.0.1', NULL, '2025-07-16 12:44:25', 'a:0:{}', '2025-07-16 03:44:25', '2025-07-16 12:44:25'),
	(167, 'b5ac9d427823ceb7ffc58d55b4fe1ff44c623e49', NULL, '172.23.0.1', NULL, '2025-07-16 13:14:05', 'a:0:{}', '2025-07-16 04:14:05', '2025-07-16 13:14:05'),
	(173, 'ab3ad336fd11773352bc14c28c3ded86f2244486', NULL, '172.23.0.1', NULL, '2025-07-16 15:10:19', 'a:0:{}', '2025-07-16 06:10:19', '2025-07-16 15:10:19'),
	(203, 'd44374065454ec13c1fff4eacf8bab52200cb08b', NULL, '172.23.0.1', NULL, '2025-07-17 15:34:48', 'a:0:{}', '2025-07-17 06:34:48', '2025-07-17 15:34:48'),
	(242, '11c5b27028501c0dac15e8c3456723d89d3db31e', NULL, '172.23.0.1', NULL, '2025-07-17 20:24:23', 'a:0:{}', '2025-07-17 11:24:23', '2025-07-17 20:24:23'),
	(258, 'ef5c4b6c35d7243a8d9d0c754fd8bdd4d465a340', NULL, '172.23.0.1', NULL, '2025-08-06 10:47:42', 'a:0:{}', '2025-08-06 01:47:42', '2025-08-06 10:47:42'),
	(267, '7d56432268b175c1421ce9392030ecce958e0927', NULL, '172.23.0.1', NULL, '2025-08-06 11:04:40', 'a:0:{}', '2025-08-06 02:04:40', '2025-08-06 11:04:40'),
	(358, '5b1af5cbd4bb4077d751da5da52ec6bb3dd9c63e', 18, '122.38.251.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 08:52:07', 'a:2:{s:11:"_csrf_token";s:64:"408dd1b55f6e56b4ecae77671b959034632f28f8a5a159a07a889c53e2849f6e";s:7:"user_id";i:18;}', '2025-08-12 17:27:33', '2025-08-12 17:52:07'),
	(360, '77c98d104b0e503d7ea5e0385e71cf5a4c741656', 18, '220.72.94.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 08:59:52', 'a:2:{s:11:"_csrf_token";s:64:"2e74a125e9d774447e37c035a66f238e4dcf3980327f0c42e7b31402b6bbd8cb";s:7:"user_id";i:18;}', '2025-08-12 17:52:57', '2025-08-12 17:59:52'),
	(365, '1a026eb57fec607e95fd96bc5534e06f207e147d', NULL, '122.38.251.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0', '2025-08-12 09:01:20', 'a:1:{s:11:"_csrf_token";s:64:"787e2a5bd7bcd3492529f4fff41979b47167ddf3199a8305543d6efa61ee4748";}', '2025-08-12 18:01:20', '2025-08-12 18:01:20');

-- 테이블 db_illuco0423.users 구조 내보내기
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'admin, employee, dealer',
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `gender` char(1) NOT NULL DEFAULT 'U',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '계정 사용 여부',
  `birth` date DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.users:~15 rows (대략적) 내보내기
INSERT INTO `users` (`id`, `name`, `type`, `email`, `phone`, `password`, `gender`, `email_verified_at`, `is_active`, `birth`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(7, '관리자', 'admin', 'test@test.com', NULL, '$2y$10$zQGPYeE4yaq7ZtRv.KrdQ.uGV/kHnHcDd4gOq/sZ4cGV3NIskxN9u', 'U', NULL, 1, NULL, NULL, '2025-07-09 06:56:48', '2025-07-09 06:56:48'),
	(9, 'Defser ', 'dealer', 'defser@defser.com', '010231221', '$2y$10$bGFJpwMNwh3FicwLMTQZc.X5iMLBXAvZSmfrP6F10nxQV5iormSp2', 'U', NULL, 1, NULL, '2025-08-06 01:07:06', '2025-07-09 07:46:14', '2025-08-06 01:07:06'),
	(10, '장준', 'employee', 'dldhfl607@naver.com', '01047152909', '$2y$10$aHdaBG317th7rKbH46EZSO5NF7AUPo.iUaYwXMVn6jKo8Pq8jgkte', 'U', NULL, 1, NULL, '2025-08-06 01:05:15', '2025-07-10 02:30:42', '2025-08-06 01:05:15'),
	(11, '나인원랩스', 'employee', 'nineonelabs@gmail.com', '029519154', '$2y$10$MzddN9PEQ9fPubJ.OOOXuuskoloWA2B33gJpHCvcP7YXrECe61pfK', 'U', NULL, 1, NULL, '2025-08-06 01:06:34', '2025-07-10 02:42:45', '2025-08-06 01:06:34'),
	(12, '장준대리점', 'dealer', 'jjn87.dev@gmail.com', '0102312312312', '$2y$10$E6B6R8Qwq2Y.zDqiWcKJqucwtkrFVmxgRdJHtkk0ey4PjRKl193Sa', 'U', NULL, 1, NULL, '2025-08-06 01:07:06', '2025-07-10 06:22:29', '2025-08-06 01:07:06'),
	(13, 'tomato    ', 'dealer', 'tomato@tomato.com', '0102012312231', '$2y$10$GA2ZglnH2tVk71tQTNNnUOL0HWc3XyPXpyD.W6f2leBKdbIjGKJmC', 'U', NULL, 1, NULL, '2025-08-06 01:07:06', '2025-07-11 05:53:49', '2025-08-06 01:07:06'),
	(15, '홍길순', 'dealer', 'hone@home.com', '12031200123', '$2y$10$w4AEJqhV944hPJlTPQT/kuTW52YoOZIH6QoI1888iSj3fe7ROdzPG', 'U', NULL, 1, NULL, '2025-07-11 06:00:51', '2025-07-11 06:00:46', '2025-07-11 06:00:51'),
	(16, 'asdsda', 'dealer', 'asdasd@asdasd.com', '12331291230123', '$2y$10$YYFl2pigvS4KigVqHsBBE.n7k0HGkb.s9Fq6ers9Dk/TWa1FqXLYm', 'U', NULL, 1, NULL, '2025-07-11 06:01:12', '2025-07-11 06:01:06', '2025-07-11 06:01:12'),
	(17, '장준2', 'employee', 'dldhfl607@naver.com', '010231231', '$2y$10$bY5VODL0Ih/E2CVQbDjWQOSpQsi0Hjldo/2S.Mn3R/U.7uekZY4dS', 'U', NULL, 1, NULL, '2025-08-06 01:06:34', '2025-08-06 01:05:29', '2025-08-06 01:06:34'),
	(18, 'ILLUCO Korea', 'employee', 'illucokorea@gmail.com', '010-1231-2312', '$2y$10$/U3iPuUW5SuG9w5DP/U6qOlFb2wIXCM0bAYKrCWSH.EP60IcV1gTC', 'U', NULL, 0, NULL, NULL, '2025-08-06 01:06:55', '2025-08-12 17:31:22'),
	(19, 'A대리점', 'dealer', 'dealer-a@gmail.com', '01012312312', '$2y$10$/9iD1UPCNw7hV0BFVjlVO.jBEVDv2BiRcfcz2IzuZHgVsFIJIzkKi', 'U', NULL, 1, NULL, NULL, '2025-08-06 01:19:03', '2025-08-06 01:19:03'),
	(20, '대리점B', 'dealer', 'dealer-b@gmail.com', '01012312312', '$2y$10$lly43fbtesAXLD/NwhzosegJl03YAWRbc1Y7i124zvtqxXcosjh1m', 'U', NULL, 1, NULL, NULL, '2025-08-06 01:19:44', '2025-08-06 01:19:44'),
	(21, '대리점 CA ', 'dealer', 'dealer-c@gmail.com', '010-2312-3124', '$2y$10$xnUqYTbwfTZbU.jd7dmBp.UZU1oSWXaORzR221cL0NRBOQtzxiI.i', 'U', NULL, 0, NULL, NULL, '2025-08-06 01:51:24', '2025-08-06 02:00:03'),
	(22, '나인원랩스', 'employee', 'nineonelabs@gmail.com', '029549153', '$2y$10$IoJx20rKd0NvXspdQ9LCbeCFZumJPRMJvqkIYPKd9IuhqAsQWgHla', 'U', NULL, 0, NULL, NULL, '2025-08-06 03:25:50', '2025-08-06 03:26:54'),
	(23, '장민', 'employee', '3mirabo2909@gmail.com', '010-8712-6123', '$2y$10$p/XeHWild/boiMRFzAUEQu3HfDPgUVv/HmiwrAuxp5RMvckpjeEsq', 'U', NULL, 0, NULL, NULL, '2025-08-06 14:04:31', '2025-08-06 14:04:59'),
	(24, '대리점 FF  ', 'dealer', 'dealer-f@gmail.com', '010-3123-1221', '$2y$10$KBAgT.9VRLcuEndh.LDtg.xYyI5uOJ8LbL21m0u38UhMoAtHL3PXK', 'U', NULL, 0, NULL, NULL, '2025-08-06 14:08:20', '2025-08-06 14:49:54'),
	(25, 'Lucy  ', 'dealer', 'yjl@illuco.co.kr', '010-9219-8739', '$2y$10$ofo5z51qZZLgV1Sw9RYBeuOwKxwzgtYhhbxx./cEW1HxsTk3BL7YO', 'U', NULL, 0, NULL, NULL, '2025-08-12 17:13:36', '2025-08-12 17:17:53');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
