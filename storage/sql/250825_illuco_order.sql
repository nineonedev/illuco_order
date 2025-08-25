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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.carts:~7 rows (대략적) 내보내기
INSERT INTO `carts` (`id`, `customer_id`, `created_at`, `updated_at`) VALUES
	(4, 6, '2025-08-06 02:02:16', '2025-08-06 02:02:16'),
	(5, 7, '2025-08-06 02:55:25', '2025-08-06 02:55:25'),
	(6, 8, '2025-08-06 03:28:40', '2025-08-06 03:28:40'),
	(7, 5, '2025-08-06 13:18:10', '2025-08-06 13:18:10'),
	(8, 10, '2025-08-06 14:39:24', '2025-08-06 14:39:24'),
	(9, 11, '2025-08-12 17:44:40', '2025-08-12 17:44:40'),
	(10, 12, '2025-08-12 17:47:02', '2025-08-12 17:47:02'),
	(11, 13, '2025-08-13 08:42:56', '2025-08-13 08:42:56');

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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.cart_items:~2 rows (대략적) 내보내기
INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `set_group_id`, `set_group_sort`, `is_main_item`, `selected`, `created_at`, `updated_at`) VALUES
	(74, 11, 74, 1, NULL, NULL, 1, 1, '2025-08-19 17:10:17', '2025-08-19 17:10:17'),
	(75, 11, 75, 3, NULL, NULL, 1, 1, '2025-08-19 17:10:30', '2025-08-19 17:10:30');

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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.claims:~21 rows (대략적) 내보내기
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
	(26, NULL, NULL, NULL, 'ㄴㅇㅁㄴㅇㄴㅇㅁㄴㅁㅇ', NULL, 'ㄴㅇㅁㅁㄴㅇㅁㄴㅇ', '<p>ㄴㅇㅁㄴㅇㅁ</p>', 19, 19, NULL, NULL, NULL, 'completed', 'ㄴㅇㅁㄴㄴㅁㅇ', NULL, '2025-08-07 08:42:43', '2025-08-19 18:20:02'),
	(27, NULL, NULL, NULL, 'ㅅㄷㄴ', NULL, 'testtest', '<p>asdsdasd</p>', 18, NULL, NULL, NULL, NULL, 'received', '', '2025-08-08 08:15:23', '2025-08-07 08:57:01', '2025-08-08 08:15:23'),
	(28, 'Ergo X', 'IAL-1040', 'IAL-1040', 'test2321', NULL, 'test', '<p>sdasd</p>', 18, NULL, '고객A-A', 'aaacustomer@test.com', '010-2312-3122', 'processing', '', NULL, '2025-08-12 20:27:29', '2025-08-19 18:19:50'),
	(29, 'Cleaning Kit', 'CLEANING_KIT_DERMATO', 'Cleaning-Kit-D', 'asndfkl', NULL, 'asd', '<p>as</p>', 25, 25, 'pil', 'yij@kflsi.fdd', '131-5151-5623', 'received', '', NULL, '2025-08-20 12:01:22', '2025-08-20 12:01:22');

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
	(12, 25, 25, 'pil', 'US', '131-5151-5623', 'yij@kflsi.fdd', 34, '', '', NULL, '2025-08-12 17:44:24', '2025-08-12 17:44:24'),
	(13, 18, NULL, 'victor', 'RU', '010-3374-3675', 'victor33@gmail.com', 33, '11 arbat st. moscow', '', NULL, '2025-08-13 08:37:28', '2025-08-13 08:37:43');

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
	(25, 'KR', 'KR-ILL', '', '', NULL, 0, NULL, '2025-08-12 17:13:36', '2025-08-12 17:13:54', NULL),
	(27, 'KR', 'KR-INFO', '경기도 군포시 고산로 166 SK벤티움 102동 304호', '테스트', NULL, 0, NULL, '2025-08-16 13:42:24', '2025-08-16 13:42:24', 3);

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
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.file_attachments:~86 rows (대략적) 내보내기
INSERT INTO `file_attachments` (`id`, `file_attachable_type`, `file_attachable_id`, `original_name`, `name`, `mime_type`, `size`, `path`, `extension`, `upload_path`, `upload_url`, `file_key`, `sort_order`, `created_at`, `updated_at`) VALUES
	(1, 'producttemplate', 1, 'wired.jpg', 'd2061dd9561f852a7d24e31e3a09e44b.jpg', 'image/jpeg', 705413, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/d2061dd9561f852a7d24e31e3a09e44b.jpg', NULL, 'main_image', 0, '2025-07-03 11:37:22', '2025-08-12 11:44:34'),
	(2, 'producttemplate', 2, 'wireless.jpg', '97d680b8337a9a2ce4d095128505d3ba.jpg', 'image/jpeg', 603879, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/97d680b8337a9a2ce4d095128505d3ba.jpg', NULL, 'main_image', 0, '2025-07-03 11:38:05', '2025-08-12 11:44:34'),
	(3, 'producttemplate', 3, 'Frame 1.jpg', 'bbf663e9a6086a3495aefc8083ca3eda.jpg', 'image/jpeg', 235203, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/bbf663e9a6086a3495aefc8083ca3eda.jpg', NULL, 'main_image', 0, '2025-07-03 11:39:11', '2025-08-12 11:44:34'),
	(4, 'producttemplate', 4, 'Frame 2.jpg', '11c4303ab70944024adbccc01e8568e1.jpg', 'image/jpeg', 210485, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/11c4303ab70944024adbccc01e8568e1.jpg', NULL, 'main_image', 0, '2025-07-03 11:39:55', '2025-08-12 11:44:34'),
	(5, 'producttemplate', 5, 'image 511.jpg', 'fe5b3de13002c1649901e7ac675a6512.jpg', 'image/jpeg', 366310, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/fe5b3de13002c1649901e7ac675a6512.jpg', NULL, 'main_image', 0, '2025-07-03 23:44:48', '2025-08-12 11:44:34'),
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
	(31, 'producttemplate', 8, 'gal_01.jpg', 'aeb03ca4615b5be0f9485a6f5a94af64.jpg', 'image/jpeg', 1361855, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/aeb03ca4615b5be0f9485a6f5a94af64.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/producttemplate/aeb03ca4615b5be0f9485a6f5a94af64.jpg', 'main_image', 0, '2025-08-07 10:18:54', '2025-08-07 10:18:54'),
	(32, 'producttemplate', 14, 'galilean_gal_0.jpg', '7fcda94ce431859acd73b31d7f374a0b.jpg', 'image/jpeg', 1329270, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/7fcda94ce431859acd73b31d7f374a0b.jpg', 'http://localhost:8080/static/uploads/producttemplate/7fcda94ce431859acd73b31d7f374a0b.jpg', 'main_image', 0, '2025-08-12 09:09:41', '2025-08-12 11:44:34'),
	(33, 'producttemplate', 15, '__Shot_From_The_Sky__Army_Show_1945_Oak_Ridge_(24971013612).jpg', 'e800094fedccf666c8d5cb7b68fa7882.jpg', 'image/jpeg', 4947, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/e800094fedccf666c8d5cb7b68fa7882.jpg', 'http://localhost:8080/static/uploads/producttemplate/e800094fedccf666c8d5cb7b68fa7882.jpg', 'main_image', 0, '2025-08-12 09:16:46', '2025-08-12 11:44:34'),
	(34, 'producttemplate', 24, 'Flip-Up.png', '8cb1112dcc04c44e9af6cdeb7247b044.png', 'image/png', 1025175, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/8cb1112dcc04c44e9af6cdeb7247b044.png', 'http://localhost:8080/static/uploads/producttemplate/8cb1112dcc04c44e9af6cdeb7247b044.png', 'main_image', 0, '2025-08-12 10:38:43', '2025-08-12 11:44:34'),
	(35, 'producttemplate', 23, 'Prismatic.png', '16e0413242a20dd0101241b0b92ea647.png', 'image/png', 968038, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/16e0413242a20dd0101241b0b92ea647.png', 'http://localhost:8080/static/uploads/producttemplate/16e0413242a20dd0101241b0b92ea647.png', 'main_image', 0, '2025-08-12 10:39:04', '2025-08-12 11:44:34'),
	(36, 'producttemplate', 22, 'Prismatic.png', '822d1702ad0cee3302bb65d1a0c3f0d9.png', 'image/png', 968038, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/822d1702ad0cee3302bb65d1a0c3f0d9.png', 'http://localhost:8080/static/uploads/producttemplate/822d1702ad0cee3302bb65d1a0c3f0d9.png', 'main_image', 0, '2025-08-12 10:39:15', '2025-08-12 11:44:34'),
	(37, 'producttemplate', 20, 'Prismatic.png', 'de7f3c324a2875806f117b576f8b5ea8.png', 'image/png', 968038, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/de7f3c324a2875806f117b576f8b5ea8.png', 'http://localhost:8080/static/uploads/producttemplate/de7f3c324a2875806f117b576f8b5ea8.png', 'main_image', 0, '2025-08-12 10:39:28', '2025-08-12 11:44:34'),
	(38, 'producttemplate', 21, 'Prismatic.png', 'b60338ab4b504b30efe1e193b619a3e1.png', 'image/png', 968038, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/b60338ab4b504b30efe1e193b619a3e1.png', 'http://localhost:8080/static/uploads/producttemplate/b60338ab4b504b30efe1e193b619a3e1.png', 'main_image', 0, '2025-08-12 10:39:34', '2025-08-12 11:44:34'),
	(39, 'producttemplate', 16, 'Ergo X.png', '1b9272b4d2d237cfffd9f9e04447b386.png', 'image/png', 785410, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/1b9272b4d2d237cfffd9f9e04447b386.png', 'http://localhost:8080/static/uploads/producttemplate/1b9272b4d2d237cfffd9f9e04447b386.png', 'main_image', 0, '2025-08-12 10:39:48', '2025-08-12 11:44:34'),
	(40, 'producttemplate', 17, 'Galilean.png', '0d5e5cccb723afcc8f250691a6e3fd8f.png', 'image/png', 911452, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/0d5e5cccb723afcc8f250691a6e3fd8f.png', 'http://localhost:8080/static/uploads/producttemplate/0d5e5cccb723afcc8f250691a6e3fd8f.png', 'main_image', 0, '2025-08-12 10:39:57', '2025-08-12 11:44:34'),
	(41, 'producttemplate', 18, 'Galilean.png', 'd1c6a16787e2ae9d9ae0196002fb9cfd.png', 'image/png', 911452, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/d1c6a16787e2ae9d9ae0196002fb9cfd.png', 'http://localhost:8080/static/uploads/producttemplate/d1c6a16787e2ae9d9ae0196002fb9cfd.png', 'main_image', 0, '2025-08-12 10:40:05', '2025-08-12 11:44:34'),
	(42, 'producttemplate', 19, 'Galilean.png', '0e4770b5ec6cb58248928973a9574fb5.png', 'image/png', 911452, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/0e4770b5ec6cb58248928973a9574fb5.png', 'http://localhost:8080/static/uploads/producttemplate/0e4770b5ec6cb58248928973a9574fb5.png', 'main_image', 0, '2025-08-12 10:40:11', '2025-08-12 11:44:34'),
	(43, 'producttemplate', 25, 'Goggle - Rimless.png', 'dc66e0addb528c01e84b882f228a1f0f.png', 'image/png', 908142, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/dc66e0addb528c01e84b882f228a1f0f.png', 'http://localhost:8080/static/uploads/producttemplate/dc66e0addb528c01e84b882f228a1f0f.png', 'main_image', 0, '2025-08-12 10:41:06', '2025-08-12 11:44:34'),
	(44, 'producttemplate', 26, 'Goggle - Rimmed.png', '77d225094e616276192476181cb7ebe9.png', 'image/png', 910446, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/77d225094e616276192476181cb7ebe9.png', 'http://localhost:8080/static/uploads/producttemplate/77d225094e616276192476181cb7ebe9.png', 'main_image', 0, '2025-08-12 10:41:21', '2025-08-12 11:44:34'),
	(45, 'producttemplate', 78, 'Frame 4 orange.png', '4697c5383b66fd5973dfc51d9c2ee68a.png', 'image/png', 738440, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/4697c5383b66fd5973dfc51d9c2ee68a.png', 'http://localhost:8080/static/uploads/producttemplate/4697c5383b66fd5973dfc51d9c2ee68a.png', 'main_image', 0, '2025-08-12 10:51:01', '2025-08-12 11:44:34'),
	(46, 'producttemplate', 77, 'Frame 4 black.png', '52643750e923cf1a848eb693ec1ce6d1.png', 'image/png', 777549, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/52643750e923cf1a848eb693ec1ce6d1.png', 'http://localhost:8080/static/uploads/producttemplate/52643750e923cf1a848eb693ec1ce6d1.png', 'main_image', 0, '2025-08-12 10:51:06', '2025-08-12 11:44:34'),
	(47, 'producttemplate', 76, 'Frame 4 black.png', '59a193aa5bcad2e3189d8168c4d15ea8.png', 'image/png', 777549, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/59a193aa5bcad2e3189d8168c4d15ea8.png', 'http://localhost:8080/static/uploads/producttemplate/59a193aa5bcad2e3189d8168c4d15ea8.png', 'main_image', 0, '2025-08-12 10:51:21', '2025-08-12 11:44:34'),
	(48, 'producttemplate', 74, 'Frame 2 blue.png', 'd149ed6f9195801191038340aba955dd.png', 'image/png', 855285, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/d149ed6f9195801191038340aba955dd.png', 'http://localhost:8080/static/uploads/producttemplate/d149ed6f9195801191038340aba955dd.png', 'main_image', 0, '2025-08-12 10:51:36', '2025-08-12 11:44:34'),
	(49, 'producttemplate', 73, 'Frame 2 Silver.png', '7e116b947d3ae1f20960eea74dc5a63c.png', 'image/png', 866117, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/7e116b947d3ae1f20960eea74dc5a63c.png', 'http://localhost:8080/static/uploads/producttemplate/7e116b947d3ae1f20960eea74dc5a63c.png', 'main_image', 0, '2025-08-12 10:51:42', '2025-08-12 11:44:34'),
	(50, 'producttemplate', 75, 'Frame 2 pink.png', 'a9ca99b0d8f0f2afb11e423819fb8c01.png', 'image/png', 840624, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/a9ca99b0d8f0f2afb11e423819fb8c01.png', 'http://localhost:8080/static/uploads/producttemplate/a9ca99b0d8f0f2afb11e423819fb8c01.png', 'main_image', 0, '2025-08-12 10:51:56', '2025-08-12 11:44:34'),
	(51, 'producttemplate', 72, 'Frame 1 Blue.png', 'd97209aded8263948db23616c8eb3dc6.png', 'image/png', 570184, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/d97209aded8263948db23616c8eb3dc6.png', 'http://localhost:8080/static/uploads/producttemplate/d97209aded8263948db23616c8eb3dc6.png', 'main_image', 0, '2025-08-12 10:52:17', '2025-08-12 11:44:34'),
	(52, 'producttemplate', 71, 'Frame 1 Red.png', 'bd79f9a8aee365e78f1ca47475de6b68.png', 'image/png', 578695, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/bd79f9a8aee365e78f1ca47475de6b68.png', 'http://localhost:8080/static/uploads/producttemplate/bd79f9a8aee365e78f1ca47475de6b68.png', 'main_image', 0, '2025-08-12 10:52:24', '2025-08-12 11:44:34'),
	(53, 'producttemplate', 70, 'Frame 1 Green.png', '1be13cad8347f95c66ae1b91d9c62321.png', 'image/png', 899376, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/1be13cad8347f95c66ae1b91d9c62321.png', 'http://localhost:8080/static/uploads/producttemplate/1be13cad8347f95c66ae1b91d9c62321.png', 'main_image', 0, '2025-08-12 10:52:30', '2025-08-12 11:44:34'),
	(54, 'producttemplate', 69, 'Frame 1 Coffee.png', '5085f5619f49a7de4c7357de8286f43a.png', 'image/png', 973710, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/5085f5619f49a7de4c7357de8286f43a.png', 'http://localhost:8080/static/uploads/producttemplate/5085f5619f49a7de4c7357de8286f43a.png', 'main_image', 0, '2025-08-12 10:52:36', '2025-08-12 11:44:34'),
	(55, 'producttemplate', 68, 'Frame 1 Black.png', 'dd2b3a9814f979c8aeb2d36d95383bd8.png', 'image/png', 900377, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/dd2b3a9814f979c8aeb2d36d95383bd8.png', 'http://localhost:8080/static/uploads/producttemplate/dd2b3a9814f979c8aeb2d36d95383bd8.png', 'main_image', 0, '2025-08-12 10:52:40', '2025-08-12 11:44:34'),
	(57, 'producttemplate', 52, 'IDS-1000.png', 'bb038437c90d3b4fde48eb3f61c73cb3.png', 'image/png', 1187928, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/bb038437c90d3b4fde48eb3f61c73cb3.png', 'http://localhost:8080/static/uploads/producttemplate/bb038437c90d3b4fde48eb3f61c73cb3.png', 'main_image', 0, '2025-08-12 10:53:56', '2025-08-12 11:44:34'),
	(58, 'producttemplate', 53, 'IDS-3100.png', '1b6c928fe5ecc9c3e8c9cb191b60108d.png', 'image/png', 1207812, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/1b6c928fe5ecc9c3e8c9cb191b60108d.png', 'http://localhost:8080/static/uploads/producttemplate/1b6c928fe5ecc9c3e8c9cb191b60108d.png', 'main_image', 0, '2025-08-12 10:54:25', '2025-08-12 11:44:34'),
	(59, 'producttemplate', 79, 'IDS-1100.png', '99d180fe66e058860c82e9ccb32eba4e.png', 'image/png', 1503132, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/99d180fe66e058860c82e9ccb32eba4e.png', 'http://localhost:8080/static/uploads/producttemplate/99d180fe66e058860c82e9ccb32eba4e.png', 'main_image', 0, '2025-08-12 10:56:17', '2025-08-12 11:44:34'),
	(60, 'producttemplate', 80, 'IDS-1000 Plus.png', 'c48fb7273fd666d243d48b9007294d1c.png', 'image/png', 1119961, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/c48fb7273fd666d243d48b9007294d1c.png', 'http://localhost:8080/static/uploads/producttemplate/c48fb7273fd666d243d48b9007294d1c.png', 'main_image', 0, '2025-08-12 10:57:26', '2025-08-12 11:44:34'),
	(61, 'producttemplate', 31, 'Wireless Headlight.png', 'b15d17d9fa71d93cac5bff58adb1ebb0.png', 'image/png', 1296698, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/b15d17d9fa71d93cac5bff58adb1ebb0.png', 'http://localhost:8080/static/uploads/producttemplate/b15d17d9fa71d93cac5bff58adb1ebb0.png', 'main_image', 0, '2025-08-12 10:58:35', '2025-08-12 11:44:34'),
	(62, 'producttemplate', 30, 'Wired Headlight.png', '41e5eda6e8155485ac2eabd4f765d60c.png', 'image/png', 981138, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/41e5eda6e8155485ac2eabd4f765d60c.png', 'http://localhost:8080/static/uploads/producttemplate/41e5eda6e8155485ac2eabd4f765d60c.png', 'main_image', 0, '2025-08-12 10:58:38', '2025-08-12 11:44:34'),
	(63, 'producttemplate', 32, 'Additional Battery for Wired Headlight.png', '69b7b32ec821e11b6fedfc7b3e41ed2a.png', 'image/png', 802220, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/69b7b32ec821e11b6fedfc7b3e41ed2a.png', 'http://localhost:8080/static/uploads/producttemplate/69b7b32ec821e11b6fedfc7b3e41ed2a.png', 'main_image', 0, '2025-08-12 11:00:21', '2025-08-12 11:44:34'),
	(64, 'producttemplate', 33, 'Additional Battery for Wireless Headlight.png', '63c359cc4679594921ab4562eac4fd7a.png', 'image/png', 775104, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/63c359cc4679594921ab4562eac4fd7a.png', 'http://localhost:8080/static/uploads/producttemplate/63c359cc4679594921ab4562eac4fd7a.png', 'main_image', 0, '2025-08-12 11:00:34', '2025-08-12 11:44:34'),
	(65, 'producttemplate', 34, 'Headband.png', 'df015c20df87472105253d8b3ab702d3.png', 'image/png', 667393, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/df015c20df87472105253d8b3ab702d3.png', 'http://localhost:8080/static/uploads/producttemplate/df015c20df87472105253d8b3ab702d3.png', 'main_image', 0, '2025-08-12 11:00:39', '2025-08-12 11:44:34'),
	(66, 'producttemplate', 35, 'Frame for Headlights.png', '4dbda33d7b827bb38bf5ff0e46903929.png', 'image/png', 759727, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/4dbda33d7b827bb38bf5ff0e46903929.png', 'http://localhost:8080/static/uploads/producttemplate/4dbda33d7b827bb38bf5ff0e46903929.png', 'main_image', 0, '2025-08-12 11:00:47', '2025-08-12 11:44:34'),
	(67, 'producttemplate', 36, 'Universal Adapter.png', 'f91464a4ea6204e874683c90d6c8e0b6.png', 'image/png', 1259056, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/f91464a4ea6204e874683c90d6c8e0b6.png', 'http://localhost:8080/static/uploads/producttemplate/f91464a4ea6204e874683c90d6c8e0b6.png', 'main_image', 0, '2025-08-12 11:00:56', '2025-08-12 11:44:34'),
	(68, 'producttemplate', 37, 'Clamp Adapter.png', 'eae0d88b582cb420f1d4eb8aac762cc2.png', 'image/png', 1382545, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/eae0d88b582cb420f1d4eb8aac762cc2.png', 'http://localhost:8080/static/uploads/producttemplate/eae0d88b582cb420f1d4eb8aac762cc2.png', 'main_image', 0, '2025-08-12 11:01:08', '2025-08-12 11:44:34'),
	(69, 'producttemplate', 38, 'Belt Clip for Wired Headlight.png', '3acc042d67e49064ddb2623f96a468a2.png', 'image/png', 1127951, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/3acc042d67e49064ddb2623f96a468a2.png', 'http://localhost:8080/static/uploads/producttemplate/3acc042d67e49064ddb2623f96a468a2.png', 'main_image', 0, '2025-08-12 11:01:16', '2025-08-12 11:44:34'),
	(70, 'producttemplate', 39, 'Extension Cable.png', 'ff34d8f189114e20e0b18512b1fc4bbc.png', 'image/png', 804931, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/ff34d8f189114e20e0b18512b1fc4bbc.png', 'http://localhost:8080/static/uploads/producttemplate/ff34d8f189114e20e0b18512b1fc4bbc.png', 'main_image', 0, '2025-08-12 11:01:25', '2025-08-12 11:44:34'),
	(71, 'producttemplate', 40, 'Headstrap for Metal Frame.png', 'f849b1a1dd22856902d0293c4a16cabf.png', 'image/png', 1190415, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/f849b1a1dd22856902d0293c4a16cabf.png', 'http://localhost:8080/static/uploads/producttemplate/f849b1a1dd22856902d0293c4a16cabf.png', 'main_image', 0, '2025-08-12 11:01:34', '2025-08-12 11:44:34'),
	(72, 'producttemplate', 41, 'Headstrap for Goggle.png', 'd4e95a3789597b41066ce2b22ae210c7.png', 'image/png', 1195526, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/d4e95a3789597b41066ce2b22ae210c7.png', 'http://localhost:8080/static/uploads/producttemplate/d4e95a3789597b41066ce2b22ae210c7.png', 'main_image', 0, '2025-08-12 11:01:39', '2025-08-12 11:44:34'),
	(73, 'producttemplate', 42, 'Side Shield for Metal Frame.png', '25cb41efcb8674fc5d861412b1b25da1.png', 'image/png', 1199916, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/25cb41efcb8674fc5d861412b1b25da1.png', 'http://localhost:8080/static/uploads/producttemplate/25cb41efcb8674fc5d861412b1b25da1.png', 'main_image', 0, '2025-08-12 11:01:47', '2025-08-12 11:44:34'),
	(74, 'producttemplate', 43, 'Side Shield for Goggle.png', '7b7c0be35b0038b131763814d0339cd7.png', 'image/png', 900523, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/7b7c0be35b0038b131763814d0339cd7.png', 'http://localhost:8080/static/uploads/producttemplate/7b7c0be35b0038b131763814d0339cd7.png', 'main_image', 0, '2025-08-12 11:01:51', '2025-08-12 11:44:34'),
	(75, 'producttemplate', 45, 'Screwdriver.png', '929a176a18b925af539e91a8412aa091.png', 'image/png', 1230687, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/929a176a18b925af539e91a8412aa091.png', 'http://localhost:8080/static/uploads/producttemplate/929a176a18b925af539e91a8412aa091.png', 'main_image', 0, '2025-08-12 11:02:01', '2025-08-12 11:44:34'),
	(76, 'producttemplate', 46, 'Nose Pad.png', '51559c8ad18d8196854c35053d67de0d.png', 'image/png', 1252098, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/51559c8ad18d8196854c35053d67de0d.png', 'http://localhost:8080/static/uploads/producttemplate/51559c8ad18d8196854c35053d67de0d.png', 'main_image', 0, '2025-08-12 11:02:08', '2025-08-12 11:44:34'),
	(77, 'producttemplate', 47, 'U Shaped Nose Pad.png', '982a267db3b55371be78ec3278e02bf3.png', 'image/png', 1271580, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/982a267db3b55371be78ec3278e02bf3.png', 'http://localhost:8080/static/uploads/producttemplate/982a267db3b55371be78ec3278e02bf3.png', 'main_image', 0, '2025-08-12 11:02:15', '2025-08-12 11:44:34'),
	(78, 'producttemplate', 48, 'UV Filter.png', '34c4bd10461f15c460e6d2f5734d68d6.png', 'image/png', 1385578, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/34c4bd10461f15c460e6d2f5734d68d6.png', 'http://localhost:8080/static/uploads/producttemplate/34c4bd10461f15c460e6d2f5734d68d6.png', 'main_image', 0, '2025-08-12 11:02:27', '2025-08-12 11:44:34'),
	(79, 'producttemplate', 44, 'Cleaning Kit.png', '83bec73342b7320f796d6b096bf74ed3.png', 'image/png', 1431770, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/83bec73342b7320f796d6b096bf74ed3.png', 'http://localhost:8080/static/uploads/producttemplate/83bec73342b7320f796d6b096bf74ed3.png', 'main_image', 0, '2025-08-12 11:02:41', '2025-08-12 11:44:34'),
	(80, 'producttemplate', 54, 'Compact Universal Clamp.png', '42b0493224e846647adc479b0b3bfeb3.png', 'image/png', 1193545, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/42b0493224e846647adc479b0b3bfeb3.png', 'http://localhost:8080/static/uploads/producttemplate/42b0493224e846647adc479b0b3bfeb3.png', 'main_image', 0, '2025-08-12 11:04:10', '2025-08-12 11:44:34'),
	(81, 'producttemplate', 55, '8mm Small Contact Plate.png', '045c0635bc203a94af0299136671fbf0.png', 'image/png', 489084, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/045c0635bc203a94af0299136671fbf0.png', 'http://localhost:8080/static/uploads/producttemplate/045c0635bc203a94af0299136671fbf0.png', 'main_image', 0, '2025-08-12 11:04:16', '2025-08-12 11:44:34'),
	(82, 'producttemplate', 56, 'Belt Clip Leather Pouch.png', 'f5b2d5eb3d78d091238e5afecc6ed7b5.png', 'image/png', 1233749, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/f5b2d5eb3d78d091238e5afecc6ed7b5.png', 'http://localhost:8080/static/uploads/producttemplate/f5b2d5eb3d78d091238e5afecc6ed7b5.png', 'main_image', 0, '2025-08-12 11:04:21', '2025-08-12 11:44:34'),
	(83, 'producttemplate', 57, 'Sleeve with Lanyard (1100).png', '99e12a8836625e8605ad11e6afada78a.png', 'image/png', 1277840, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/99e12a8836625e8605ad11e6afada78a.png', 'http://localhost:8080/static/uploads/producttemplate/99e12a8836625e8605ad11e6afada78a.png', 'main_image', 0, '2025-08-12 11:04:30', '2025-08-12 11:44:34'),
	(84, 'producttemplate', 58, 'Sleeve with Lanyard (1000).png', 'a1f521fdad4ac03827a6356e8f627834.png', 'image/png', 1223687, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/a1f521fdad4ac03827a6356e8f627834.png', 'http://localhost:8080/static/uploads/producttemplate/a1f521fdad4ac03827a6356e8f627834.png', 'main_image', 0, '2025-08-12 11:04:34', '2025-08-12 11:44:34'),
	(85, 'producttemplate', 59, 'Replacement Battery (1100).png', '58b42ef909409f319c5691431216dd2c.png', 'image/png', 1339545, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/58b42ef909409f319c5691431216dd2c.png', 'http://localhost:8080/static/uploads/producttemplate/58b42ef909409f319c5691431216dd2c.png', 'main_image', 0, '2025-08-12 11:04:40', '2025-08-12 11:44:34'),
	(86, 'producttemplate', 60, 'Replacement Battery (1000).png', '9bda651f965d7c9eb932bab3f4dd8d3c.png', 'image/png', 1339360, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/9bda651f965d7c9eb932bab3f4dd8d3c.png', 'http://localhost:8080/static/uploads/producttemplate/9bda651f965d7c9eb932bab3f4dd8d3c.png', 'main_image', 0, '2025-08-12 11:04:45', '2025-08-12 11:44:34'),
	(87, 'producttemplate', 61, 'Replacement Battery (3100).png', 'ae3af8cad16f9773506879e0a0869497.png', 'image/png', 1243942, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/ae3af8cad16f9773506879e0a0869497.png', 'http://localhost:8080/static/uploads/producttemplate/ae3af8cad16f9773506879e0a0869497.png', 'main_image', 0, '2025-08-12 11:04:48', '2025-08-12 11:44:34'),
	(88, 'producttemplate', 62, 'Mirrorless Camera Adapter.png', '0fa0055083e1f38d46725b0a19beb4fa.png', 'image/png', 1162747, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/0fa0055083e1f38d46725b0a19beb4fa.png', 'http://localhost:8080/static/uploads/producttemplate/0fa0055083e1f38d46725b0a19beb4fa.png', 'main_image', 0, '2025-08-12 11:04:55', '2025-08-12 11:44:34'),
	(89, 'producttemplate', 63, 'Protective Glass (1100).png', 'b29656bbbc29fd4d837cab739ee0a47b.png', 'image/png', 701998, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/b29656bbbc29fd4d837cab739ee0a47b.png', 'http://localhost:8080/static/uploads/producttemplate/b29656bbbc29fd4d837cab739ee0a47b.png', 'main_image', 0, '2025-08-12 11:05:01', '2025-08-12 11:44:34'),
	(90, 'producttemplate', 64, 'Protective Glass (1000).png', '57ffc3110b30ac6e153f97df76f6fa7f.png', 'image/png', 495071, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/57ffc3110b30ac6e153f97df76f6fa7f.png', 'http://localhost:8080/static/uploads/producttemplate/57ffc3110b30ac6e153f97df76f6fa7f.png', 'main_image', 0, '2025-08-12 11:05:06', '2025-08-12 11:44:34'),
	(91, 'producttemplate', 65, 'USB Cable.png', '34d4839dca6f13070865aa4d3e80e05e.png', 'image/png', 1641286, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/34d4839dca6f13070865aa4d3e80e05e.png', 'http://localhost:8080/static/uploads/producttemplate/34d4839dca6f13070865aa4d3e80e05e.png', 'main_image', 0, '2025-08-12 11:05:13', '2025-08-12 11:44:34'),
	(92, 'producttemplate', 66, 'Magnet USB charging Cable for 1100C.png', 'bacb9a4db6f654f1835a1e8e24560545.png', 'image/png', 533472, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/bacb9a4db6f654f1835a1e8e24560545.png', 'http://localhost:8080/static/uploads/producttemplate/bacb9a4db6f654f1835a1e8e24560545.png', 'main_image', 0, '2025-08-12 11:05:28', '2025-08-12 11:44:34'),
	(93, 'producttemplate', 67, 'Cleaning Kit.png', 'a94c8f84ff3b004c60f4f672ec1122e2.png', 'image/png', 1392678, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/a94c8f84ff3b004c60f4f672ec1122e2.png', 'http://localhost:8080/static/uploads/producttemplate/a94c8f84ff3b004c60f4f672ec1122e2.png', 'main_image', 0, '2025-08-12 11:05:44', '2025-08-12 11:44:34'),
	(94, 'producttemplate', 50, 'IDM M.png', '88e50d9834feb7b4d5d9061f74dc32a5.png', 'image/png', 924837, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/88e50d9834feb7b4d5d9061f74dc32a5.png', 'http://localhost:8080/static/uploads/producttemplate/88e50d9834feb7b4d5d9061f74dc32a5.png', 'main_image', 0, '2025-08-12 11:12:57', '2025-08-12 11:44:34'),
	(95, 'producttemplate', 49, 'Photo Mirror.png', '8943a0e208a7a5e76c49bd1190711733.png', 'image/png', 726768, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'png', '/static/uploads/producttemplate/8943a0e208a7a5e76c49bd1190711733.png', 'http://localhost:8080/static/uploads/producttemplate/8943a0e208a7a5e76c49bd1190711733.png', 'main_image', 0, '2025-08-12 11:13:00', '2025-08-12 11:44:34'),
	(96, 'producttemplate', 81, 'lens_05.jpg', 'e69f76f5f3da4a5367df6ebbb3826226.jpg', 'image/jpeg', 337980, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/e69f76f5f3da4a5367df6ebbb3826226.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/producttemplate/e69f76f5f3da4a5367df6ebbb3826226.jpg', 'main_image', 0, '2025-08-12 20:24:16', '2025-08-12 20:24:16'),
	(97, 'claim', 28, 'galilean_gal_0.jpg', '9b18e1980d27f20348950df0f1bcfc45.jpg', 'image/jpeg', 1329270, '/home/illu0423order/www/storage/uploads/public/claim', 'jpg', '/static/uploads/claim/9b18e1980d27f20348950df0f1bcfc45.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/claim/9b18e1980d27f20348950df0f1bcfc45.jpg', 'attach_1', 0, '2025-08-12 20:27:29', '2025-08-12 20:27:29'),
	(101, 'producttemplate', 54, 'IDS-1100C.png', '69638d4807a5235e7d03e1bb59da0b3a.png', 'image/png', 1173465, '/home/illu0423order/www/storage/uploads/public/producttemplate/producttemplate', 'png', '/static/uploads/producttemplate/producttemplate/69638d4807a5235e7d03e1bb59da0b3a.png', 'http://illuco-order.nineonelabs.co.kr/static/uploads/producttemplate/producttemplate/69638d4807a5235e7d03e1bb59da0b3a.png', 'main_image', 0, '2025-08-12 20:45:03', '2025-08-12 11:49:27'),
	(105, 'producttemplate', 51, 'IDS-1100C.png', '6b8eb64f12eee54df1bb8680a3f5539f.png', 'image/png', 1173465, '/home/illu0423order/www/storage/uploads/public/producttemplate/producttemplate', 'png', '/static/uploads/producttemplate/producttemplate/6b8eb64f12eee54df1bb8680a3f5539f.png', 'http://illuco-order.nineonelabs.co.kr/static/uploads/producttemplate/producttemplate/6b8eb64f12eee54df1bb8680a3f5539f.png', 'main_image', 0, '2025-08-12 20:54:27', '2025-08-12 20:54:27'),
	(106, 'producttemplate', 82, 'shutterstock_79604194.jpg', '35ea77fc29e050272a6d133d64c54bf2.jpg', 'image/jpeg', 2301364, '/home/illu0423order/www/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/35ea77fc29e050272a6d133d64c54bf2.jpg', 'http://illuco-order.nineonelabs.co.kr/static/uploads/producttemplate/35ea77fc29e050272a6d133d64c54bf2.jpg', 'main_image', 0, '2025-08-16 13:52:06', '2025-08-16 13:52:06');

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.notices:~4 rows (대략적) 내보내기
INSERT INTO `notices` (`id`, `user_id`, `title`, `content`, `visible_from`, `visible_to`, `is_pinned`, `status`, `created_at`, `updated_at`) VALUES
	(17, 18, '공지사항 테스트', '<p>테스트 테스트</p>', NULL, NULL, 0, 'draft', '2025-08-06 01:53:13', '2025-08-06 01:53:13'),
	(18, 18, '공지사항 테스트2', '<p>공지사항 테스트입니다.</p><p><br></p><p><img src="/static/uploads/temp/c03fcdfe21e4707b7f56e5fb41c980d4.jpg" style="width: 572px;"></p><p><br></p><p><br></p><p>테스트</p><p><br></p><p><br></p><p><br></p>', NULL, NULL, 0, 'draft', '2025-08-06 12:56:28', '2025-08-06 12:56:28'),
	(19, 18, '공지사항 테스트3', '<p>공지사항 테스트</p>', NULL, NULL, 1, 'draft', '2025-08-06 13:00:20', '2025-08-16 14:01:55'),
	(20, 18, '공지사항 테스트 44', '<p>공지사항 테스트 4&nbsp;</p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;">공지사항 테스트 4&nbsp;</span></p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;">공지사항 테스트 4&nbsp;</span><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;"></span></p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;"><br></span></p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;"><br></span></p><p><span style="font-family: Inter, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400;"><br></span></p>', NULL, NULL, 0, 'draft', '2025-08-06 14:31:59', '2025-08-06 14:31:59'),
	(21, 18, '공지사항', '<p>IDS-1100 가격 인상</p><p>기존 $375 에서 $400으로 변경</p>', NULL, NULL, 0, 'draft', '2025-08-20 11:58:29', '2025-08-20 11:58:29');

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.orders:~2 rows (대략적) 내보내기
INSERT INTO `orders` (`id`, `user_id`, `customer_id`, `dealer_id`, `orderer_name`, `orderer_email`, `orderer_phone`, `payment_date`, `delivery_date`, `shipping_date`, `canceled_at`, `order_no`, `order_status`, `total_amount`, `memo`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(29, 18, 8, NULL, 'ILLUCO Korea', 'illucokorea@gmail.com', '010-1231-2312', '2025-08-13', '2025-08-04', '2025-08-02', NULL, 'CST-2025-00001', 'preparing', 5770.00, 'test', NULL, '2025-08-12 20:25:51', '2025-08-12 20:26:18'),
	(30, 18, 13, NULL, 'ILLUCO Korea', 'illucokorea@gmail.com', '010-1231-2312', NULL, NULL, NULL, NULL, 'CST-2025-00002', 'new', 2805.00, '', NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(31, 18, 13, NULL, 'ILLUCO Korea', 'illucokorea@gmail.com', '010-1231-2312', NULL, NULL, NULL, NULL, 'CST-2025-00003', 'preparing', 1610.00, '请查看形式发票（PI），并请告知是否一切无误。\n\n为了赶上您所指定的日期，我们将开始准备发货。', NULL, '2025-08-13 15:59:05', '2025-08-20 11:50:08'),
	(32, 25, 12, 25, 'Lucy  ', 'yjl@illuco.co.kr', '010-9219-8739', NULL, NULL, NULL, NULL, 'KR-ILL-2025-00001', 'new', 1350.00, '', NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21');

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
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_documents:~15 rows (대략적) 내보내기
INSERT INTO `order_documents` (`id`, `order_id`, `user_id`, `document_no`, `type`, `status`, `created_at`, `updated_at`) VALUES
	(81, 29, 18, 'CST-2025-00001', 'proforma_invoice', NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(82, 29, 18, 'CST-2025-00002', 'product_request', NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(83, 29, 18, 'CST-2025-00003', 'packing_list', NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(84, 29, 18, 'CST-2025-00004', 'commercial_invoice', NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(85, 30, 18, 'CST-2025-00005', 'proforma_invoice', NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(86, 30, 18, 'CST-2025-00006', 'product_request', NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(87, 30, 18, 'CST-2025-00007', 'packing_list', NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(88, 30, 18, 'CST-2025-00008', 'commercial_invoice', NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(89, 31, 18, 'CST-2025-00009', 'proforma_invoice', NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(90, 31, 18, 'CST-2025-00010', 'product_request', NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(91, 31, 18, 'CST-2025-00011', 'packing_list', NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(92, 31, 18, 'CST-2025-00012', 'commercial_invoice', NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(93, 32, 25, 'KR-ILL-2025-00001', 'proforma_invoice', NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21'),
	(94, 32, 25, 'KR-ILL-2025-00002', 'product_request', NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21'),
	(95, 32, 25, 'KR-ILL-2025-00003', 'packing_list', NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21'),
	(96, 32, 25, 'KR-ILL-2025-00004', 'commercial_invoice', NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21');

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
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_document_commercial_invoices:~2 rows (대략적) 내보내기
INSERT INTO `order_document_commercial_invoices` (`id`, `bill_to_name`, `bill_to_address`, `bill_to_tel`, `bill_to_attn`, `bill_to_email`, `ship_to_name`, `ship_to_address`, `ship_to_tel`, `ship_to_attn`, `ship_to_email`, `ref_no`, `invoice_date`, `pi_no`, `po_no`, `carrier`, `estimated_delivery_date`, `payment_terms`, `price_terms`, `country_of_origin`, `currency`, `hs_code`, `dev`, `lst`, `ein`, `created_at`, `updated_at`) VALUES
	(84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21');

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
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_document_packing_lists:~2 rows (대략적) 내보내기
INSERT INTO `order_document_packing_lists` (`id`, `bill_to_name`, `bill_to_address`, `bill_to_tel`, `bill_to_attn`, `bill_to_email`, `ship_to_name`, `ship_to_address`, `ship_to_tel`, `ship_to_attn`, `ship_to_email`, `ref_no`, `packing_date`, `pi_no`, `po_no`, `carrier`, `estimated_delivery_date`, `payment_terms`, `price_terms`, `country_of_origin`, `packing_details`, `hs_code`, `total_cartons`, `total_quantity`, `total_weight`, `total_volume_cbm`, `created_at`, `updated_at`) VALUES
	(83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(95, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21');

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
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_document_product_requests:~2 rows (대략적) 내보내기
INSERT INTO `order_document_product_requests` (`id`, `country`, `customer_name`, `created_date`, `delivery_date`, `manager_name`, `document_no`, `box1_no`, `box1_weight`, `box1_size`, `box2_no`, `box2_weight`, `box2_size`, `box3_no`, `box3_weight`, `box3_size`, `box4_no`, `box4_weight`, `box4_size`, `box5_no`, `box5_weight`, `box5_size`, `note`, `created_at`, `updated_at`, `item1_no`, `item2_no`, `item3_no`, `item4_no`, `item5_no`, `item6_no`, `item7_no`, `item8_no`, `item9_no`, `item10_no`, `item11_no`, `item12_no`, `item13_no`, `item14_no`, `item15_no`, `item16_no`) VALUES
	(82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(86, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(90, '러시아', 'victor', '2025-08-19', NULL, 'ILLUCO Korea', NULL, '1', '20', '40*40*40', '3', '20', '', '', '', '', '', '', '', '', '', '', '메롱\r\n', '2025-08-13 15:59:05', '2025-08-19 18:02:26', '1', '3', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
	(94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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

-- 테이블 데이터 db_illuco0423.order_document_proforma_invoices:~2 rows (대략적) 내보내기
INSERT INTO `order_document_proforma_invoices` (`id`, `document_no`, `purchase_order_no`, `invoice_no`, `invoice_date`, `buyer_name`, `buyer_address`, `buyer_tel`, `buyer_attn`, `buyer_email`, `country_of_origin`, `currency`, `salesperson_name`, `salesperson_tel`, `salesperson_email`, `estimated_date_of_delivery`, `price_terms`, `payment_terms`, `shipment_by`, `hs_code`, `freight_charge`, `bank_beneficiary`, `bank_name`, `bank_address`, `bank_swift_code`, `bank_account_no`, `note`, `created_at`, `updated_at`) VALUES
	(81, 'CST-2025-00001', '', NULL, '2025-08-19', '고객A-A', '', '010-2312-3122', '', 'aaacustomer@test.com', NULL, NULL, 'ILLUCO Korea', '010-1231-2312', 'illucokorea@gmail.com', '', '', '', '', '', 0.00, '', '', '', '', '', '', '2025-08-12 20:25:51', '2025-08-19 17:33:22'),
	(85, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21');

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_histories:~3 rows (대략적) 내보내기
INSERT INTO `order_histories` (`id`, `order_id`, `dealer_id`, `customer_id`, `created_by`, `balance`, `settled`, `memo`, `created_at`, `updated_at`) VALUES
	(8, 31, NULL, 13, 18, 0.00, 0, 'DKLFJDLJFLDSJFLDJLFK', '2025-08-16 15:04:19', '2025-08-16 15:04:19'),
	(9, 31, NULL, 13, 18, 0.00, 0, '메롱', '2025-08-19 17:46:48', '2025-08-19 17:46:48'),
	(10, 31, NULL, 13, 18, 11220.00, 1, '1', '2025-08-19 17:47:37', '2025-08-19 17:47:37');

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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_items:~8 rows (대략적) 내보내기
INSERT INTO `order_items` (`id`, `set_group_id`, `set_group_sort`, `is_main_item`, `order_id`, `product_id`, `quantity`, `box_no`, `unit_price`, `total_price`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(43, NULL, NULL, 1, 29, 67, 2, NULL, 950.00, 1900.00, NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(44, NULL, NULL, 1, 29, 66, 1, NULL, 1050.00, 1050.00, NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(45, '07fe6f88-14d0-47c6-a366-d279df3286f5', NULL, 1, 29, 64, 2, NULL, 1350.00, 2700.00, NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(46, '07fe6f88-14d0-47c6-a366-d279df3286f5', 0, 0, 29, 65, 4, NULL, 30.00, 120.00, NULL, '2025-08-12 20:25:51', '2025-08-12 20:25:51'),
	(47, '5e937fdc-d7b2-4a86-8b13-0e91db94de9d', NULL, 1, 30, 68, 1, NULL, 2745.00, 2745.00, NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(48, '5e937fdc-d7b2-4a86-8b13-0e91db94de9d', 0, 0, 30, 69, 2, NULL, 30.00, 60.00, NULL, '2025-08-13 08:45:09', '2025-08-13 08:45:09'),
	(49, NULL, NULL, 1, 31, 71, 1, NULL, 260.00, 260.00, NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(50, NULL, NULL, 1, 31, 70, 1, NULL, 1350.00, 1350.00, NULL, '2025-08-13 15:59:05', '2025-08-13 15:59:05'),
	(51, NULL, NULL, 1, 32, 76, 1, NULL, 1350.00, 1350.00, NULL, '2025-08-20 12:02:21', '2025-08-20 12:02:21');

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.order_logs:~0 rows (대략적) 내보내기
INSERT INTO `order_logs` (`id`, `order_id`, `user_id`, `previous_status`, `status`, `message`, `created_at`, `updated_at`) VALUES
	(27, 29, 18, 'new', 'preparing', NULL, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(28, 31, 18, 'new', 'preparing', NULL, '2025-08-20 11:50:08', '2025-08-20 11:50:08');

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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.products:~9 rows (대략적) 내보내기
INSERT INTO `products` (`id`, `template_id`, `name`, `serial_number`, `type`, `code`, `model`, `price`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(64, 17, 'Galilean Loupes', NULL, 'loupe', 'ITL-1025G', 'ITL-1025G', 1350.00, 'TTL 갈릴레안 루페 2.5x 계열', NULL, '2025-08-12 20:25:22', '2025-08-12 20:25:22'),
	(65, 81, '처방렌즈', NULL, NULL, 'PR-LEN-30', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-08-12 20:25:22', '2025-08-12 20:25:22'),
	(66, 30, 'IHL-1000 - wired', NULL, '', 'IHL-1000', 'IHL-1000 - wired', 1050.00, '유선 헤드라이트, UV 필터 포함', NULL, '2025-08-12 20:25:31', '2025-08-12 20:25:31'),
	(67, 79, 'IDS-1100', NULL, '', 'DS', 'IDS-1100', 950.00, 'IDS-1100', NULL, '2025-08-12 20:25:47', '2025-08-12 20:25:47'),
	(68, 16, 'Ergo X', NULL, 'loupe', 'IAL-1040', 'IAL-1040', 2745.00, '인체공학 각도형 TTL 루페(Ergo X), 장시간 착용에 최적화', NULL, '2025-08-13 08:42:56', '2025-08-13 08:42:56'),
	(69, 81, '처방렌즈', NULL, NULL, 'PR-LEN-30', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-08-13 08:42:56', '2025-08-13 08:42:56'),
	(70, 19, 'Galilean Loupes', NULL, 'loupe', 'ITL-1035G', 'ITL-1035G', 1350.00, 'TTL 갈릴레안 루페 3.5x 계열', NULL, '2025-08-13 13:47:29', '2025-08-13 13:47:29'),
	(71, 62, 'Mirrorless Camera Adapter', NULL, '', 'MIRRORLESS_ADAPTER', 'Mirrorless Camera Adapter', 260.00, '미러리스 카메라 어댑터', NULL, '2025-08-13 13:47:38', '2025-08-13 13:47:38'),
	(74, 30, 'IHL-1000 - wired', NULL, '', 'IHL-1000', 'IHL-1000 - wired', 1050.00, '유선 헤드라이트, UV 필터 포함', NULL, '2025-08-19 17:10:17', '2025-08-19 17:10:17'),
	(75, 31, 'IHL-2000 - wireless', NULL, '', 'IHL-2000', 'IHL-2000 - wireless', 1150.00, '무선 헤드라이트, 교체식 배터리', NULL, '2025-08-19 17:10:30', '2025-08-19 17:10:30'),
	(76, 17, 'Galilean Loupes', NULL, 'loupe', 'ITL-1025G', 'ITL-1025G', 1350.00, 'TTL 갈릴레안 루페 2.5x 계열', NULL, '2025-08-20 12:02:18', '2025-08-20 12:02:18');

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.product_categories:~7 rows (대략적) 내보내기
INSERT INTO `product_categories` (`id`, `parent_id`, `slug`, `label`, `description`, `is_visible`, `sort_order`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'headlight', 'Headlights', NULL, 1, 1, '2025-07-03 11:34:38', '2025-08-12 11:06:47'),
	(2, NULL, 'loupe', 'Loupes', NULL, 1, 0, '2025-07-03 11:34:44', '2025-08-12 11:06:47'),
	(3, NULL, 'dermatoscope', 'Dermatoscope', NULL, 1, 3, '2025-07-03 11:34:52', '2025-08-12 11:07:00'),
	(11, NULL, NULL, 'Dental Mirror', NULL, 1, 2, '2025-08-06 14:09:47', '2025-08-12 11:06:47'),
	(12, NULL, NULL, 'Frame', NULL, 1, 4, '2025-08-12 10:06:09', '2025-08-12 11:07:15'),
	(14, NULL, NULL, 'Loupe & Headlight Accessory', NULL, 1, 5, '2025-08-12 10:06:09', '2025-08-12 11:07:22'),
	(15, NULL, NULL, 'Dermatoscope Accessory', NULL, 1, 6, '2025-08-12 10:06:09', '2025-08-12 11:07:27'),
	(16, NULL, NULL, 'Camera', NULL, 1, 7, '2025-08-16 13:49:34', '2025-08-16 13:49:34');

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

-- 테이블 데이터 db_illuco0423.product_headlights:~0 rows (대략적) 내보내기

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

-- 테이블 데이터 db_illuco0423.product_loupes:~4 rows (대략적) 내보내기
INSERT INTO `product_loupes` (`id`, `type`, `engraving_text`, `frame_type`, `working_distance`, `od_sph`, `os_sph`, `od_cyl`, `os_cyl`, `od_axis`, `os_axis`, `od_add`, `os_add`, `pd_right`, `pd_left`, `pd_total`, `vertex_distance`, `add_option`, `created_at`, `updated_at`) VALUES
	(64, 'custom-made', NULL, 'frame2', 49.0, 1.00, 0.00, 0.00, 1.00, 20, 30, 1.00, 2.00, 27.0, 29.0, 56.0, 24.0, 'include', '2025-08-12 20:25:22', '2025-08-12 20:25:22'),
	(68, 'custom-made', NULL, 'frame4', 65.0, 0.25, 0.25, 0.25, 0.50, 100, 30, 0.00, 0.00, 32.0, 33.0, 65.0, 20.0, 'ignore', '2025-08-13 08:42:56', '2025-08-13 08:42:56'),
	(70, 'ready-made', NULL, 'frame1', 40.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-13 13:47:29', '2025-08-13 13:47:29'),
	(76, 'custom-made', '12323123', 'frame1', 45.0, 0.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 0.00, 30.0, 30.0, 60.0, 17.0, 'ignore', '2025-08-20 12:02:18', '2025-08-20 12:02:18');

-- 테이블 db_illuco0423.product_serials 구조 내보내기
CREATE TABLE IF NOT EXISTS `product_serials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '고유 ID',
  `product_id` bigint unsigned NOT NULL COMMENT '제품 ID (products 테이블 참조)',
  `serial_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '시리얼 번호 (고유값)',
  `status` enum('in_stock','reserved','sold','returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'in_stock' COMMENT '상태',
  `order_item_id` bigint unsigned DEFAULT NULL COMMENT '주문항목 ID (order_items 참조)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `fk_product_serials_product_id` (`product_id`),
  KEY `fk_product_serials_order_item_id` (`order_item_id`),
  CONSTRAINT `fk_product_serials_order_item_id` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_product_serials_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='제품 시리얼 개별 관리';

-- 테이블 데이터 db_illuco0423.product_serials:~9 rows (대략적) 내보내기
INSERT INTO `product_serials` (`id`, `product_id`, `serial_number`, `status`, `order_item_id`, `created_at`, `updated_at`) VALUES
	(25, 67, 'DSNNN25000001A', 'sold', 43, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(26, 67, 'DSNNN25000002A', 'sold', 43, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(27, 66, 'IHL-1000NNN25000001A', 'sold', 44, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(28, 64, 'ITL-1025GNNN25000001A', 'sold', 45, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(29, 64, 'ITL-1025GNNN25000002A', 'sold', 45, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(30, 65, 'PR-LEN-30NNN25000001A', 'sold', 46, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(31, 65, 'PR-LEN-30NNN25000002A', 'sold', 46, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(32, 65, 'PR-LEN-30NNN25000003A', 'sold', 46, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(33, 65, 'PR-LEN-30NNN25000004A', 'sold', 46, '2025-08-12 20:26:18', '2025-08-12 20:26:18'),
	(36, 71, 'MIRRORLESS_ADAPTERNNN25000001A', 'sold', 49, '2025-08-20 11:50:08', '2025-08-20 11:50:08'),
	(37, 70, 'ITL-1035GNNN25000001A', 'sold', 50, '2025-08-20 11:50:08', '2025-08-20 11:50:08');

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
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.product_templates:~62 rows (대략적) 내보내기
INSERT INTO `product_templates` (`id`, `category_id`, `name`, `code`, `model`, `price`, `sort_order`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(16, 2, 'Ergo X', 'IAL-1040', 'IAL-1040', 2745.00, 1, '인체공학 각도형 TTL 루페(Ergo X), 장시간 착용에 최적화', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(17, 2, 'Galilean Loupes', 'ITL-1025G', 'ITL-1025G', 1350.00, 2, 'TTL 갈릴레안 루페 2.5x 계열', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(18, 2, 'Galilean Loupes', 'ITL-1030G', 'ITL-1030G', 1350.00, 3, 'TTL 갈릴레안 루페 3.0x 계열', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(19, 2, 'Galilean Loupes', 'ITL-1035G', 'ITL-1035G', 1350.00, 4, 'TTL 갈릴레안 루페 3.5x 계열', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(20, 2, 'Prismatic', 'ITL-1040P', 'ITL-1040P', 1350.00, 5, 'TTL 프리즘 루페 4.0x 계열', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(21, 2, 'Prismatic', 'ITL-1045P', 'ITL-1045P', 1350.00, 6, 'TTL 프리즘 루페 4.5x 계열', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(22, 2, 'Prismatic', 'ITL-1055P', 'ITL-1055P', 1350.00, 7, 'TTL 프리즘 루페 5.5x 계열', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(23, 2, 'Prismatic', 'ITL-1065P', 'ITL-1065P', 1350.00, 8, 'TTL 프리즘 루페 6.5x 계열', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(24, 2, 'Flip-Up Loupes', 'IFL-1030G', 'IFL-1030G', 605.00, 9, '플립업 갈릴레안 루페', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(25, 12, 'Goggle - Rimless', 'GOGGLE_RIMLESS', 'Goggle - Rimless', 0.00, 10, '루페/헤드라이트용 고글 프레임(림리스)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(26, 12, 'Goggle - Rimmed', 'GOGGLE_RIMMED', 'Goggle - Rimmed', 0.00, 11, '루페/헤드라이트용 고글 프레임(림 있음)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(30, 1, 'IHL-1000 - wired', 'IHL-1000', 'IHL-1000 - wired', 1050.00, 15, '유선 헤드라이트, UV 필터 포함', NULL, '2025-08-12 10:06:09', '2025-08-12 10:58:54'),
	(31, 1, 'IHL-2000 - wireless', 'IHL-2000', 'IHL-2000 - wireless', 1150.00, 16, '무선 헤드라이트, 교체식 배터리', NULL, '2025-08-12 10:06:09', '2025-08-12 10:58:35'),
	(32, 14, 'Additional Battery for Wired Headlight', 'BATTERY_WIRED_HEADLIGHT', 'Battery for IHL-1000', 365.00, 17, '유선 헤드라이트 전용 예비 배터리', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(33, 14, 'Additional Battery for Wireless Headlight', 'BATTERY_WIRELESS_HEADLIGHT', 'Battery for IHL-2000', 150.00, 18, '무선 헤드라이트 추가 배터리', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(34, 14, 'Headband', 'HEADBAND', 'Headband', 210.00, 19, '헤드라이트 전용 헤드밴드', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(35, 14, 'Frame for Headlights', 'FRAME_FOR_HEADLIGHTS', 'Frame for Headlight', 180.00, 20, '헤드라이트 장착용 프레임(헤드라이트 미포함)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(36, 14, 'Universal Adapter', 'UNIVERSAL_ADAPTER', 'Universal Adapter', 45.00, 21, '헤드라이트 범용 어댑터', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(37, 14, 'Clamp Adapter', 'CLAMP_ADAPTER', 'Clamp Type Adapter', 50.00, 22, '헤드라이트 클램프형 어댑터', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(38, 14, 'Belt Clip for Wired Headlight', 'BELT_CLIP_WIRED', 'Belt Clip', 25.00, 23, '유선 헤드라이트 배터리 클립', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(39, 14, 'Extension Cable', 'EXTENSION_CABLE', 'Extension Cable', 0.00, 24, '연장 케이블(가격 미공개)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(40, 14, 'Headstrap for Metal Frame', 'HEADSTRAP_METAL', 'Headstrap for Metal Frame', 0.00, 25, '메탈 프레임용 헤드스트랩', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(41, 14, 'Headstrap for Goggle', 'HEADSTRAP_GOGGLE', 'Headstrap for Goggle', 0.00, 26, '고글용 헤드스트랩', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(42, 14, 'Side Shield for Metal Frame', 'SIDE_SHIELD_METAL', 'Side Shield for Metal Frame', 13.99, 27, '메탈 프레임용 사이드 실드(유사품 참고가)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(43, 14, 'Side Shield for Goggle', 'SIDE_SHIELD_GOGGLE', 'Side Shield for Goggle', 13.99, 28, '고글용 사이드 실드(유사품 참고가)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(44, 14, 'Cleaning Kit', 'CLEANING_KIT_HEADLIGHT', 'H-Cleaning-Kit ', 0.00, 29, '클리닝 키트(가격 미공개)', NULL, '2025-08-12 10:06:09', '2025-08-12 11:02:41'),
	(45, 14, 'Screwdriver', 'SCREWDRIVER', 'Screwdriver', 0.00, 30, '소형 드라이버(가격 미공개)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(46, 14, 'Nose Pad', 'NOSE_PAD', 'Nose Pad', 0.00, 31, '코패드(가격 미공개)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(47, 14, 'U Shaped Nose Pad', 'U_SHAPED_NOSE_PAD', 'U-Shaped Nose Pad', 0.00, 32, 'U자형 코패드(가격 미공개)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(48, 14, 'UV Filter', 'UV_FILTER', 'UV Filter', 0.00, 33, 'UV 필터(별도 판매가 미확인, 본체 동봉)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(49, 11, 'DENTAL MIRROR', 'IDM-P', 'IDM-P', 155.00, 34, '치과 포토 미러(포토 미러 계열)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(50, 11, 'DENTAL MIRROR', 'IDM-M', 'IDM-M', 85.00, 35, '치과 구강 미러(핸들 포함, 5팩 기준)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(51, 3, 'IDS-1100C', 'DS', 'IDS-1100C', 950.00, 36, '더마토스코프 1100, 10배/25mm 렌즈', NULL, '2025-08-12 10:06:09', '2025-08-12 10:53:38'),
	(52, 3, 'IDS-1000', 'DS', 'IDS-1000', 650.00, 37, '더마토스코프 1000(단종/가격 미공개)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:55:19'),
	(53, 3, 'IDS 3100', 'DS', 'IDS-3100', 850.00, 38, '우드램프(365/395/405nm)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:54:32'),
	(54, 15, 'Compact Universal Clamp', 'COMPACT_UNIVERSAL_CLAMP', 'Universal Phone Clamp', 65.00, 39, '스마트폰 고정 클램프(IDS-1100/1000+ 호환)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(55, 15, '8mm Small Contact Plate', 'CONTACT_PLATE_8MM', '8mm Small Contact Plate', 0.00, 40, '8mm 소형 콘택트 플레이트(가격 미확인)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(56, 15, 'Belt Clip Leather Pouch', 'LEATHER_POUCH', 'Leather Pouch w/ Belt Clip', 30.00, 41, '레더 파우치(벨트클립 포함)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(57, 15, 'Sleeve with Lanyard (1100)', 'SLEEVE_1100', 'Sleeve with Lanyard (1100)', 26.00, 42, '실리콘 슬리브+랜야드(IDS-1100)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(58, 15, 'Sleeve with Lanyard (1000)', 'SLEEVE_1000', 'Sleeve with Lanyard (1000)', 26.00, 43, '실리콘 슬리브+랜야드(IDS-1000)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(59, 15, 'Replacement Battery (1100)', 'REPL_BATT_1100', 'Replacement Battery (1100)', 75.00, 44, 'IDS-1100/1100C 교체 배터리(해외가 참고)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(60, 15, 'Replacement Battery (1000)', 'REPL_BATT_1000', 'Replacement Battery (1000)', 40.00, 45, 'IDS-1000 교체 배터리(해외가 참고)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(61, 15, 'Replacement Battery (3100)', 'REPL_BATT_3100', 'Replacement Battery (3100)', 160.00, 46, 'IDS-3100 교체 배터리(해외가 참고)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(62, 15, 'Mirrorless Camera Adapter', 'MIRRORLESS_ADAPTER', 'Mirrorless Camera Adapter', 260.00, 47, '미러리스 카메라 어댑터', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(63, 15, 'Protective Glass (1100)', 'PROTECTIVE_GLASS_1100', 'Protective Glass (1100)', 150.00, 48, 'IDS-1100/1100C 보호유리(호환 렌즈)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(64, 15, 'Protective Glass (1000)', 'PROTECTIVE_GLASS_1000', 'Protective Glass (1000)', 0.00, 49, 'IDS-1000 보호유리(가격 미확인)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(65, 15, 'USB Cable', 'USB_CABLE', 'USB Cable', 0.00, 50, 'USB 충전 케이블(가격 미공개)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(66, 15, 'Magnet USB charging Cable for 1100C', 'MAGNET_USB_CABLE_1100C', 'Magnet USB Cable (1100C)', 0.00, 51, '마그넷 USB 충전 케이블(1100C)', NULL, '2025-08-12 10:06:09', '2025-08-12 10:06:09'),
	(67, 15, 'Cleaning Kit', 'CLEANING_KIT_DERMATO', 'Cleaning-Kit-D', 0.00, 52, '클리닝 키트(가격 미공개)', NULL, '2025-08-12 10:06:09', '2025-08-12 11:05:44'),
	(68, 12, 'Frame 1 - Black', 'FRAME1_BLACK', 'Frame 1 (Black)', 0.00, 1, 'Frame 1 - 블랙 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(69, 12, 'Frame 1 - Coffee', 'FRAME1_COFFEE', 'Frame 1 (Coffee)', 0.00, 2, 'Frame 1 - 커피 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(70, 12, 'Frame 1 - Green', 'FRAME1_GREEN', 'Frame 1 (Green)', 0.00, 3, 'Frame 1 - 그린 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(71, 12, 'Frame 1 - Red', 'FRAME1_RED', 'Frame 1 (Red)', 0.00, 4, 'Frame 1 - 레드 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(72, 12, 'Frame 1 - Blue', 'FRAME1_BLUE', 'Frame 1 (Blue)', 0.00, 5, 'Frame 1 - 블루 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(73, 12, 'Frame 2 - Silver', 'FRAME2_SILVER', 'Frame 2 (Silver)', 0.00, 6, 'Frame 2 - 실버 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(74, 12, 'Frame 2 - Blue', 'FRAME2_BLUE', 'Frame 2 (Blue)', 0.00, 7, 'Frame 2 - 블루 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(76, 12, 'Frame 4 - Brown', 'FRAME4_BROWN', 'Frame 4 (Brown)', 0.00, 9, 'Frame 4 - 브라운 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(77, 12, 'Frame 4 - Black', 'FRAME4_BLACK', 'Frame 4 (Black)', 0.00, 10, 'Frame 4 - 블랙 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(78, 12, 'Frame 4 - Orange', 'FRAME4_ORANGE', 'Frame 4 (Orange)', 0.00, 11, 'Frame 4 - 오렌지 색상', NULL, '2025-08-12 10:50:22', '2025-08-12 10:50:22'),
	(79, 3, 'IDS-1100', 'DS', 'IDS-1100', 950.00, 36, 'IDS-1100', NULL, '2025-08-12 10:56:17', '2025-08-12 10:56:35'),
	(80, 3, 'IDS-1000 Plus', 'DS', 'IDS-1000-PLUS', 1000.00, 37, 'IDS-1000 Plus', NULL, '2025-08-12 10:57:26', '2025-08-12 10:57:26'),
	(81, 14, '처방렌즈', 'PR-LEN-30', 'PR-LENS-30', 30.00, 0, '', NULL, '2025-08-12 20:24:16', '2025-08-12 20:24:16'),
	(82, 16, '9100 Camera', '9100cam', 'Dermaview', 1000.00, 0, 'Camera for 9100', NULL, '2025-08-16 13:52:06', '2025-08-16 13:52:06');

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.role_users:~17 rows (대략적) 내보내기
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
	(23, 25, 6, '2025-08-12 17:13:36', '2025-08-12 17:13:36'),
	(24, 26, 7, '2025-08-16 13:38:52', '2025-08-16 13:38:52'),
	(25, 27, 6, '2025-08-16 13:42:24', '2025-08-16 13:42:24'),
	(26, 28, 7, '2025-08-20 11:57:12', '2025-08-20 11:57:12');

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
) ENGINE=InnoDB AUTO_INCREMENT=405 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.sessions:~29 rows (대략적) 내보내기
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
	(369, '4df39459ef9e1c67b41d2608c024d9ee47908657', NULL, '172.23.0.1', NULL, '2025-08-12 18:39:42', 'a:0:{}', '2025-08-12 09:39:42', '2025-08-12 18:39:42'),
	(393, '3d1410f488f1268aec09397d034e638623387680', 18, '122.38.251.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-16 06:12:34', 'a:2:{s:11:"_csrf_token";s:64:"bec98a50b0c2c72a25f0a4c77782f8720db82ce122698443b06c90df3257ceaa";s:7:"user_id";i:18;}', '2025-08-16 14:51:53', '2025-08-16 15:12:34'),
	(395, 'b7e30a68037b0c99bfa248bea970f87514aa495e', 18, '122.38.251.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-19 09:20:03', 'a:2:{s:11:"_csrf_token";s:64:"c83957e48e9730384361dd61a27ed188372a13e9464b999a78096c779680c13c";s:7:"user_id";i:18;}', '2025-08-19 16:40:05', '2025-08-19 18:20:03'),
	(397, '7822bfff5dbf59091c9bb170a450191581ddb24d', 18, '122.38.251.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 01:00:28', 'a:2:{s:11:"_csrf_token";s:64:"d334c12cdd93ad06c93a35f7d4ff42c8c6b27ff4aafe686fb60262d8c37ef8a0";s:7:"user_id";i:18;}', '2025-08-20 09:43:37', '2025-08-20 10:00:28'),
	(403, '4100b4f9345adaf87c76cf06a51013f019e2ad30', 18, '122.38.251.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0', '2025-08-20 03:03:31', 'a:2:{s:11:"_csrf_token";s:64:"b9a37a44ab514c02937fed2075d12e1dbd075abf063d65ef2cc7bf9d108b2ebe";s:7:"user_id";i:18;}', '2025-08-20 12:02:51', '2025-08-20 12:03:31'),
	(404, '3c8bf751a5e1af83e2f7e409785b6f1d0719c8a7', NULL, '223.118.50.68', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/28.0 Chrome/130.0.0.0 Mobile Safari/537.36', '2025-08-23 06:16:53', 'a:1:{s:11:"_csrf_token";s:64:"2bb39cb7f76c238b7b7cb1caf0be3a23f39481aeb068d928fe4c41ef8d2a1912";}', '2025-08-23 15:16:52', '2025-08-23 15:16:53');

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 테이블 데이터 db_illuco0423.users:~17 rows (대략적) 내보내기
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
	(25, 'Lucy  ', 'dealer', 'yjl@illuco.co.kr', '010-9219-8739', '$2y$10$ofo5z51qZZLgV1Sw9RYBeuOwKxwzgtYhhbxx./cEW1HxsTk3BL7YO', 'U', NULL, 0, NULL, NULL, '2025-08-12 17:13:36', '2025-08-12 17:17:53'),
	(26, 'JY', 'employee', 'jyc@illuco.co.kr', '010-8351-1180', '$2y$10$1XICoHfZVthzrEBhBNgYOeTcGrk43L8DR0LpP7nziwE37WsEjHXXu', 'U', NULL, 1, NULL, NULL, '2025-08-16 13:38:52', '2025-08-16 13:38:52'),
	(27, 'JYC', 'dealer', 'info@illuco.co.kr', '031-4288-825', '$2y$10$OsLGvyqko6Dvo0y/GVDf2.NCWgvCb.0/2tjSI8fz8tbnd0u9lZOhK', 'U', NULL, 1, NULL, NULL, '2025-08-16 13:42:24', '2025-08-16 13:42:24'),
	(28, 'Angela', 'employee', 'kms@illuco.co.kr', '010-8272-6232', '$2y$10$ijH5hJWj8Yb6eza8FGQPXuFdL9WePR4LQFNsqCyyCE.I/4g6aeeUS', 'U', NULL, 1, NULL, NULL, '2025-08-20 11:57:12', '2025-08-20 11:57:12');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
