-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- 호스트: db:3306
-- 생성 시간: 25-07-15 08:27
-- 서버 버전: 8.0.42
-- PHP 버전: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `nineonelabs`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `carts`
--

INSERT INTO `carts` (`id`, `customer_id`, `created_at`, `updated_at`) VALUES
(2, 2, '2025-07-09 06:59:18', '2025-07-09 06:59:18'),
(3, 3, '2025-07-09 07:55:09', '2025-07-09 07:55:09');

-- --------------------------------------------------------

--
-- 테이블 구조 `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `set_group_id` varchar(255) DEFAULT NULL COMMENT '동적 세트 그룹 번호',
  `set_group_sort` int UNSIGNED DEFAULT NULL COMMENT '세트 그룹 내 정렬 순서',
  `is_main_item` tinyint(1) NOT NULL DEFAULT '0' COMMENT '세트 내 메인 제품 여부',
  `selected` tinyint(1) NOT NULL DEFAULT '1' COMMENT '선택 여부',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `claims`
--

CREATE TABLE `claims` (
  `id` bigint UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `product_model` varchar(255) NOT NULL,
  `product_serial_number` varchar(255) NOT NULL,
  `product_description` text,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `delaer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'received',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `claims`
--

INSERT INTO `claims` (`id`, `product_name`, `product_code`, `product_model`, `product_serial_number`, `product_description`, `title`, `content`, `user_id`, `delaer_id`, `customer_name`, `customer_email`, `customer_phone`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Galilean', 'LP', 'ITL-1040P', 'asddassdasa', 'galilean loupe', 'sxczxczxcczxzxczxc', '<p>asd</p><p>asd</p><p>sd</p><p>asd</p><p>sda</p><p>asd</p><p>asd</p>', 7, NULL, '일루코_고객', 'illuco@illuco.com', '0102312312312', 'received', NULL, '2025-07-15 07:28:15', '2025-07-15 07:28:15'),
(2, '무선 헤드라이트', 'HL', 'IHL-2000', 'asddassd', '무선 헤드라이트. wireless headlight', 'sdasad', '<p>sdasddsa</p>', 7, NULL, '양상규', 'didtkdrb@naver.com', '01095312312', 'received', NULL, '2025-07-15 07:29:34', '2025-07-15 07:29:34');

-- --------------------------------------------------------

--
-- 테이블 구조 `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `dealer_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `description` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `dealer_id`, `name`, `country`, `phone`, `email`, `age`, `address`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, 7, NULL, '일루코_고객', 'KR', '0102312312312', 'illuco@illuco.com', NULL, '', 'tteas\r\nd\r\nasdsda\r\n', NULL, '2025-07-09 06:58:32', '2025-07-09 06:58:32'),
(3, 9, 9, '양상규', 'KR', '01095312312', 'didtkdrb@naver.com', NULL, '', '양상규 매니저 ', NULL, '2025-07-09 07:48:49', '2025-07-09 07:48:49');

-- --------------------------------------------------------

--
-- 테이블 구조 `dealers`
--

CREATE TABLE `dealers` (
  `id` bigint UNSIGNED NOT NULL,
  `country` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `description` text,
  `memo` text,
  `use_default_memo` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `dealers`
--

INSERT INTO `dealers` (`id`, `country`, `code`, `address`, `description`, `memo`, `use_default_memo`, `deleted_at`, `created_at`, `updated_at`, `category_id`) VALUES
(9, 'FR', 'DF02', 'assdaasdsdasd', 'sd\r\nsd\r\n\r\nasd\r\nasd\r\n', NULL, 0, NULL, '2025-07-09 07:46:14', '2025-07-09 07:46:14', NULL),
(12, 'KR', 'JJ0231', 'sasd', 'sd\r\nsda\r\nsad\r\nasd\r\n', NULL, 0, NULL, '2025-07-10 06:22:29', '2025-07-10 06:22:29', NULL),
(13, 'PT', 'DAM', 'sddasdas', 'asdasdsad', NULL, 0, NULL, '2025-07-11 05:53:49', '2025-07-14 01:35:06', 4),
(15, 'KR', 'asdasdsa', 'dsasdasas', 'asasdads', NULL, 0, NULL, '2025-07-11 06:00:46', '2025-07-11 06:00:46', NULL),
(16, 'KR', 'sdsda', 'asd', 'adsasd', NULL, 0, NULL, '2025-07-11 06:01:06', '2025-07-11 06:01:06', 3);

-- --------------------------------------------------------

--
-- 테이블 구조 `file_attachments`
--

CREATE TABLE `file_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `file_attachable_type` varchar(255) NOT NULL,
  `file_attachable_id` bigint UNSIGNED NOT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `file_attachments`
--

INSERT INTO `file_attachments` (`id`, `file_attachable_type`, `file_attachable_id`, `original_name`, `name`, `mime_type`, `size`, `path`, `extension`, `upload_path`, `upload_url`, `file_key`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'producttemplate', 1, 'wired.jpg', 'd2061dd9561f852a7d24e31e3a09e44b.jpg', 'image/jpeg', 705413, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/d2061dd9561f852a7d24e31e3a09e44b.jpg', NULL, 'main_image', 0, '2025-07-03 11:37:22', '2025-07-03 11:37:22'),
(2, 'producttemplate', 2, 'wireless.jpg', '97d680b8337a9a2ce4d095128505d3ba.jpg', 'image/jpeg', 603879, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/97d680b8337a9a2ce4d095128505d3ba.jpg', NULL, 'main_image', 0, '2025-07-03 11:38:05', '2025-07-03 11:38:05'),
(3, 'producttemplate', 3, 'Frame 1.jpg', 'bbf663e9a6086a3495aefc8083ca3eda.jpg', 'image/jpeg', 235203, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/bbf663e9a6086a3495aefc8083ca3eda.jpg', NULL, 'main_image', 0, '2025-07-03 11:39:11', '2025-07-03 11:39:11'),
(4, 'producttemplate', 4, 'Frame 2.jpg', '11c4303ab70944024adbccc01e8568e1.jpg', 'image/jpeg', 210485, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/11c4303ab70944024adbccc01e8568e1.jpg', NULL, 'main_image', 0, '2025-07-03 11:39:55', '2025-07-03 11:39:55'),
(5, 'producttemplate', 5, 'image 511.jpg', 'fe5b3de13002c1649901e7ac675a6512.jpg', 'image/jpeg', 366310, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/fe5b3de13002c1649901e7ac675a6512.jpg', NULL, 'main_image', 0, '2025-07-03 23:44:48', '2025-07-03 23:44:48'),
(7, 'notice', 1, '683d3ef2e0582.jpeg', '38f981063a0e91b5db4e7e4176ece5c7.jpeg', 'image/jpeg', 247998, '/var/www/html/storage/uploads/public/notice/notice', 'jpeg', '/static/uploads/notice/notice/38f981063a0e91b5db4e7e4176ece5c7.jpeg', NULL, 'attach_1', 0, '2025-07-10 06:49:11', '2025-07-10 06:49:11'),
(18, 'notice', 12, '683d3ef2e0582.jpeg', '50e7043077c26d72b9b1cc910c752c80.jpeg', 'image/jpeg', 247998, '/var/www/html/storage/uploads/public/notice', 'jpeg', '/static/uploads/notice/50e7043077c26d72b9b1cc910c752c80.jpeg', NULL, 'attach_1', 0, '2025-07-10 07:00:09', '2025-07-10 07:00:09'),
(19, 'claim', 1, 'ines-alvarez-fdez-u6rZ2_bUgUE-unsplash.jpg', '412f7faffc3c2818fb7435403aa7a950.jpg', 'image/jpeg', 2886043, '/var/www/html/storage/uploads/public/claim', 'jpg', '/static/uploads/claim/412f7faffc3c2818fb7435403aa7a950.jpg', 'http://localhost:8080/static/uploads/claim/412f7faffc3c2818fb7435403aa7a950.jpg', 'attach_1', 0, '2025-07-15 07:28:15', '2025-07-15 07:28:15');

-- --------------------------------------------------------

--
-- 테이블 구조 `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `migrations`
--

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

-- --------------------------------------------------------

--
-- 테이블 구조 `notices`
--

CREATE TABLE `notices` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `visible_from` datetime DEFAULT NULL,
  `visible_to` datetime DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `notices`
--

INSERT INTO `notices` (`id`, `user_id`, `title`, `content`, `visible_from`, `visible_to`, `is_pinned`, `status`, `created_at`, `updated_at`) VALUES
(1, 7, 'ㄴㅇㄴㅇㅁㄴㅇㅁㄴㅇ', '<p><img style=\"width: 580px;\" src=\"/static/uploads/temp/f28365c1b25c52c2351761c5dea2204b.jpeg\"></p><p>ㄴㅁㅁㄴㅇㄴㄴㅇㅁㄴㅇㅁㄴㅇ</p>', '2025-07-10 12:00:00', '2025-07-10 12:00:00', 1, 'draft', '2025-07-10 06:46:11', '2025-07-10 06:46:11'),
(12, 7, 'testss', '<p>asdsdasdd</p><p>sd</p><p>aasd</p><p><br></p><p><br></p><p><br></p><p><img style=\"width: 580px;\" src=\"/static/uploads/temp/d3e3fe55322c426a5199270dc152b6c8.jpeg\"><br></p>', NULL, NULL, 0, 'draft', '2025-07-10 07:00:09', '2025-07-10 07:00:09');

-- --------------------------------------------------------

--
-- 테이블 구조 `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `data` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `dealer_id` bigint UNSIGNED DEFAULT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_id`, `dealer_id`, `orderer_name`, `orderer_email`, `orderer_phone`, `payment_date`, `delivery_date`, `shipping_date`, `canceled_at`, `order_no`, `order_status`, `total_amount`, `memo`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 7, 2, NULL, '관리자', 'test@test.com', '', NULL, NULL, NULL, NULL, 'OR-CST-20250709-00001', 'preparing', 1150.00, 'asdsdadssda', NULL, '2025-07-09 00:00:00', '2025-07-10 02:13:49'),
(2, 9, 3, 9, 'Defser', 'defser@defser.com', '010231221', NULL, NULL, NULL, '2025-07-09 18:08:01', 'OR-DF02-20250709-00001', 'completed', 2700.00, 'ㅁㄴㅇㄴㅇㅁㄴㅇㅇㄴㄴㅇㅁ', NULL, '2025-07-09 00:00:00', '2025-07-10 05:52:56'),
(3, 7, 3, NULL, '관리자', 'test@test.com', '', '2025-07-14', '2025-07-17', '2025-07-31', NULL, 'CST-2025-00001', 'preparing', 9260.00, 'ㅁㄴㅇㅇㄴㅁㄴㅇㅁㄴ\nㄴㅁㅇ\nㄴㅇㅁ\nㅁㄴㅇ\nㅁㄴㅇ\n', NULL, '2025-07-10 00:00:00', '2025-07-14 07:15:41');

-- --------------------------------------------------------

--
-- 테이블 구조 `order_documents`
--

CREATE TABLE `order_documents` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `document_no` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `order_documents`
--

INSERT INTO `order_documents` (`id`, `order_id`, `user_id`, `document_no`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 'PI-CST-20250709-00001', 'proforma_invoice', NULL, '2025-07-09 06:59:26', '2025-07-09 06:59:26'),
(2, 1, 7, 'PR-CST-20250709-00001', 'product_request', NULL, '2025-07-09 06:59:26', '2025-07-09 06:59:26'),
(3, 1, 7, 'PL-CST-20250709-00001', 'packing_list', NULL, '2025-07-09 06:59:26', '2025-07-09 06:59:26'),
(4, 1, 7, 'CI-CST-20250709-00001', 'commercial_invoice', NULL, '2025-07-09 06:59:26', '2025-07-09 06:59:26'),
(5, 2, 9, 'PI-DF02-20250709-00001', 'proforma_invoice', NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(6, 2, 9, 'PR-DF02-20250709-00001', 'product_request', NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(7, 2, 9, 'PL-DF02-20250709-00001', 'packing_list', NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(8, 2, 9, 'CI-DF02-20250709-00001', 'commercial_invoice', NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(9, 3, 7, 'CST-2025-00001', 'proforma_invoice', NULL, '2025-07-10 23:50:44', '2025-07-10 23:50:44'),
(10, 3, 7, 'CST-2025-00002', 'product_request', NULL, '2025-07-10 23:50:44', '2025-07-10 23:50:44'),
(11, 3, 7, 'CST-2025-00003', 'packing_list', NULL, '2025-07-10 23:50:44', '2025-07-10 23:50:44'),
(12, 3, 7, 'CST-2025-00004', 'commercial_invoice', NULL, '2025-07-10 23:50:44', '2025-07-10 23:50:44');

-- --------------------------------------------------------

--
-- 테이블 구조 `order_document_commercial_invoices`
--

CREATE TABLE `order_document_commercial_invoices` (
  `id` bigint UNSIGNED NOT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `order_document_commercial_invoices`
--

INSERT INTO `order_document_commercial_invoices` (`id`, `bill_to_name`, `bill_to_address`, `bill_to_tel`, `bill_to_attn`, `bill_to_email`, `ship_to_name`, `ship_to_address`, `ship_to_tel`, `ship_to_attn`, `ship_to_email`, `ref_no`, `invoice_date`, `pi_no`, `po_no`, `carrier`, `estimated_delivery_date`, `payment_terms`, `price_terms`, `country_of_origin`, `currency`, `hs_code`, `dev`, `lst`, `ein`, `created_at`, `updated_at`) VALUES
(4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 06:59:26', '2025-07-09 06:59:26'),
(8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 23:50:44', '2025-07-10 23:50:44');

-- --------------------------------------------------------

--
-- 테이블 구조 `order_document_packing_lists`
--

CREATE TABLE `order_document_packing_lists` (
  `id` bigint UNSIGNED NOT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `order_document_packing_lists`
--

INSERT INTO `order_document_packing_lists` (`id`, `bill_to_name`, `bill_to_address`, `bill_to_tel`, `bill_to_attn`, `bill_to_email`, `ship_to_name`, `ship_to_address`, `ship_to_tel`, `ship_to_attn`, `ship_to_email`, `ref_no`, `packing_date`, `pi_no`, `po_no`, `carrier`, `estimated_delivery_date`, `payment_terms`, `price_terms`, `country_of_origin`, `packing_details`, `hs_code`, `total_cartons`, `total_quantity`, `total_weight`, `total_volume_cbm`, `created_at`, `updated_at`) VALUES
(3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 06:59:26', '2025-07-09 06:59:26'),
(7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 23:50:44', '2025-07-10 23:50:44');

-- --------------------------------------------------------

--
-- 테이블 구조 `order_document_product_requests`
--

CREATE TABLE `order_document_product_requests` (
  `id` bigint UNSIGNED NOT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `order_document_product_requests`
--

INSERT INTO `order_document_product_requests` (`id`, `country`, `customer_name`, `created_date`, `delivery_date`, `manager_name`, `document_no`, `box1_no`, `box1_weight`, `box1_size`, `box2_no`, `box2_weight`, `box2_size`, `box3_no`, `box3_weight`, `box3_size`, `box4_no`, `box4_weight`, `box4_size`, `box5_no`, `box5_weight`, `box5_size`, `note`, `created_at`, `updated_at`) VALUES
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 06:59:26', '2025-07-09 06:59:26'),
(6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 23:50:44', '2025-07-10 23:50:44');

-- --------------------------------------------------------

--
-- 테이블 구조 `order_document_proforma_invoices`
--

CREATE TABLE `order_document_proforma_invoices` (
  `id` bigint UNSIGNED NOT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `order_document_proforma_invoices`
--

INSERT INTO `order_document_proforma_invoices` (`id`, `document_no`, `purchase_order_no`, `invoice_no`, `invoice_date`, `buyer_name`, `buyer_address`, `buyer_tel`, `buyer_attn`, `buyer_email`, `country_of_origin`, `currency`, `salesperson_name`, `salesperson_tel`, `salesperson_email`, `estimated_date_of_delivery`, `price_terms`, `payment_terms`, `shipment_by`, `hs_code`, `freight_charge`, `bank_beneficiary`, `bank_name`, `bank_address`, `bank_swift_code`, `bank_account_no`, `note`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 06:59:26', '2025-07-09 06:59:26'),
(5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 23:50:44', '2025-07-10 23:50:44');

-- --------------------------------------------------------

--
-- 테이블 구조 `order_histories`
--

CREATE TABLE `order_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `dealer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '미수금 변동액',
  `settled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '이 히스토리가 처리 완료되었는지 여부',
  `memo` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `order_histories`
--

INSERT INTO `order_histories` (`id`, `order_id`, `dealer_id`, `customer_id`, `created_by`, `balance`, `settled`, `memo`, `created_at`, `updated_at`) VALUES
(5, 3, NULL, 3, 7, 1040.50, 0, 'ㄴㄴㅇㅇㅁㄴ\r\nㅁㄴㅇ\r\n', '2025-07-14 07:32:31', '2025-07-14 07:32:31');

-- --------------------------------------------------------

--
-- 테이블 구조 `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `set_group_id` varchar(255) DEFAULT NULL COMMENT '동적 세트 그룹 번호',
  `set_group_sort` int UNSIGNED DEFAULT NULL COMMENT '세트 그룹 내 정렬 순서',
  `is_main_item` tinyint(1) NOT NULL DEFAULT '0' COMMENT '세트 내 메인 제품 여부',
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `box_no` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `order_items`
--

INSERT INTO `order_items` (`id`, `set_group_id`, `set_group_sort`, `is_main_item`, `order_id`, `product_id`, `quantity`, `box_no`, `unit_price`, `total_price`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 1, 1, 1, 1, NULL, 1150.00, 1150.00, NULL, '2025-07-09 06:59:25', '2025-07-09 06:59:25'),
(2, NULL, NULL, 1, 2, 2, 2, NULL, 1350.00, 2700.00, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20'),
(3, NULL, NULL, 1, 3, 6, 2, NULL, 1150.00, 2300.00, NULL, '2025-07-10 23:50:43', '2025-07-10 23:50:43'),
(4, NULL, NULL, 1, 3, 5, 1, NULL, 1350.00, 1350.00, NULL, '2025-07-10 23:50:43', '2025-07-10 23:50:43'),
(5, 'd944d3f9-d37c-4f98-8bbc-dd6a86f44e32', NULL, 1, 3, 3, 2, NULL, 2745.00, 5490.00, NULL, '2025-07-10 23:50:43', '2025-07-10 23:50:43'),
(6, 'd944d3f9-d37c-4f98-8bbc-dd6a86f44e32', 0, 0, 3, 4, 4, NULL, 30.00, 120.00, NULL, '2025-07-10 23:50:43', '2025-07-10 23:50:43');

-- --------------------------------------------------------

--
-- 테이블 구조 `order_logs`
--

CREATE TABLE `order_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `previous_status` varchar(255) DEFAULT NULL COMMENT '변경 전 상태',
  `status` varchar(255) NOT NULL COMMENT '변경 후 상태',
  `message` text COMMENT '변경 사유 등 추가 메모',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `order_logs`
--

INSERT INTO `order_logs` (`id`, `order_id`, `user_id`, `previous_status`, `status`, `message`, `created_at`, `updated_at`) VALUES
(1, 3, 7, '', 'confirmed', NULL, '2025-07-14 07:05:38', '2025-07-14 07:05:38'),
(2, 3, 7, 'preparing', 'preparing', NULL, '2025-07-14 07:15:42', '2025-07-14 07:15:42'),
(3, 3, 7, 'preparing', 'confirmed', NULL, '2025-07-14 07:18:15', '2025-07-14 07:18:15');

-- --------------------------------------------------------

--
-- 테이블 구조 `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `resource` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `permissions`
--

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

-- --------------------------------------------------------

--
-- 테이블 구조 `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `template_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `products`
--

INSERT INTO `products` (`id`, `template_id`, `name`, `serial_number`, `type`, `code`, `model`, `price`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, '무선 헤드라이트', 'HLNNN25000001A', 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', NULL, '2025-07-09 06:59:18', '2025-07-10 02:13:50'),
(2, 4, 'Galilean', 'LPNNN25000001A', 'loupe', 'LP', 'ITL-1040P', 1350.00, 'galilean loupe', NULL, '2025-07-09 07:55:09', '2025-07-10 02:14:36'),
(3, 3, 'ErgoX', 'LPNNN25000003A', 'loupe', 'LP', 'ITL-1025G', 2745.00, 'ergox', NULL, '2025-07-10 23:48:31', '2025-07-14 07:15:42'),
(4, 5, '처방렌즈', 'PLNNN25000001A', NULL, 'PL', 'PR-LENS-30', 30.00, '처방렌즈', NULL, '2025-07-10 23:48:31', '2025-07-14 07:15:42'),
(5, 4, 'Galilean', 'LPNNN25000002A', 'loupe', 'LP', 'ITL-1040P', 1350.00, 'galilean loupe', NULL, '2025-07-10 23:48:50', '2025-07-14 07:15:42'),
(6, 2, '무선 헤드라이트', 'HLNNN25000002A', 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', NULL, '2025-07-10 23:49:23', '2025-07-14 07:15:42');

-- --------------------------------------------------------

--
-- 테이블 구조 `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` text,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `product_categories`
--

INSERT INTO `product_categories` (`id`, `parent_id`, `slug`, `label`, `description`, `is_visible`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, NULL, 'headlight', 'Headlight', NULL, 1, 1, '2025-07-03 11:34:38', '2025-07-03 11:34:38'),
(2, NULL, 'loupe', 'Loupe', NULL, 1, 2, '2025-07-03 11:34:44', '2025-07-03 11:34:44'),
(3, NULL, 'dermatoscope', 'Dermatoscope', NULL, 1, 3, '2025-07-03 11:34:52', '2025-07-03 11:34:52'),
(4, NULL, 'accessory', 'Accessory', NULL, 1, 4, '2025-07-03 11:34:57', '2025-07-03 11:34:57');

-- --------------------------------------------------------

--
-- 테이블 구조 `product_headlights`
--

CREATE TABLE `product_headlights` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(40) DEFAULT NULL,
  `wireless_color` varchar(255) DEFAULT NULL,
  `engraving_text` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `product_headlights`
--

INSERT INTO `product_headlights` (`id`, `type`, `wireless_color`, `engraving_text`, `created_at`, `updated_at`) VALUES
(1, NULL, 'gray', 'asdsdasddas', '2025-07-09 06:59:18', '2025-07-09 06:59:18'),
(6, NULL, 'gold', NULL, '2025-07-10 23:49:24', '2025-07-10 23:49:24');

-- --------------------------------------------------------

--
-- 테이블 구조 `product_loupes`
--

CREATE TABLE `product_loupes` (
  `id` bigint UNSIGNED NOT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `product_loupes`
--

INSERT INTO `product_loupes` (`id`, `type`, `engraving_text`, `frame_type`, `working_distance`, `od_sph`, `os_sph`, `od_cyl`, `os_cyl`, `od_axis`, `os_axis`, `od_add`, `os_add`, `pd_right`, `pd_left`, `pd_total`, `vertex_distance`, `add_option`, `created_at`, `updated_at`) VALUES
(2, 'ready-made', NULL, 'frame4', 47.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:09', '2025-07-09 07:55:09'),
(3, 'custom-made', NULL, 'frame1', 47.0, 1.00, 0.00, 2.00, 1.00, 20, 120, 2.00, 1.00, 27.0, 28.0, 55.0, 24.0, 'ignore', '2025-07-10 23:48:31', '2025-07-10 23:48:31'),
(5, 'ready-made', NULL, 'frame4', 46.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 23:48:50', '2025-07-10 23:48:50');

-- --------------------------------------------------------

--
-- 테이블 구조 `product_templates`
--

CREATE TABLE `product_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sort_order` int NOT NULL DEFAULT '0',
  `description` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `product_templates`
--

INSERT INTO `product_templates` (`id`, `category_id`, `name`, `code`, `model`, `price`, `sort_order`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, '유선 헤드라이트', 'HL', 'IHL-1000', 1050.00, 0, 'wired headlight.', NULL, '2025-07-03 11:37:22', '2025-07-03 11:37:22'),
(2, 1, '무선 헤드라이트', 'HL', 'IHL-2000', 1150.00, 0, '무선 헤드라이트. wireless headlight', NULL, '2025-07-03 11:38:05', '2025-07-03 11:38:05'),
(3, 2, 'ErgoX', 'LP', 'ITL-1025G', 2745.00, 0, 'ergox', NULL, '2025-07-03 11:39:11', '2025-07-03 11:40:05'),
(4, 2, 'Galilean', 'LP', 'ITL-1040P', 1350.00, 0, 'galilean loupe', NULL, '2025-07-03 11:39:55', '2025-07-03 11:39:55'),
(5, 4, '처방렌즈', 'PL', 'PR-LENS-30', 30.00, 0, '', NULL, '2025-07-03 23:44:48', '2025-07-03 23:44:55');

-- --------------------------------------------------------

--
-- 테이블 구조 `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `roles`
--

INSERT INTO `roles` (`id`, `label`, `name`, `description`, `created_at`, `updated_at`) VALUES
(5, '관리자', 'admin', '최고 관리자', '2025-07-09 06:51:43', '2025-07-10 02:37:58'),
(6, '대리점', 'dealer', '대리점 권한입니다.', '2025-07-09 07:43:58', '2025-07-10 02:37:56'),
(7, '직원', 'employee', '직원 권한입니다.', '2025-07-09 07:44:26', '2025-07-10 02:37:43'),
(8, '영업담당자', 'sales', '영업담당자 권한입니다.', '2025-07-09 07:45:46', '2025-07-10 02:37:29');

-- --------------------------------------------------------

--
-- 테이블 구조 `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `role_permissions`
--

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
(41, 6, 9, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(42, 6, 10, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(43, 6, 11, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(44, 6, 12, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(45, 6, 13, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(46, 6, 14, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(47, 6, 15, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(48, 6, 16, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(49, 6, 17, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(50, 6, 18, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(51, 6, 19, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(52, 6, 20, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(53, 6, 21, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(54, 6, 22, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(55, 6, 23, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(56, 6, 24, '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
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
(129, 8, 40, '2025-07-09 07:45:46', '2025-07-09 07:45:46');

-- --------------------------------------------------------

--
-- 테이블 구조 `role_users`
--

CREATE TABLE `role_users` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `role_users`
--

INSERT INTO `role_users` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(2, 7, 5, '2025-07-09 06:56:48', '2025-07-09 06:56:48'),
(3, 9, 6, '2025-07-09 07:46:14', '2025-07-09 07:46:14'),
(4, 10, 7, '2025-07-10 02:30:42', '2025-07-10 02:30:42'),
(8, 11, 8, '2025-07-10 04:14:39', '2025-07-10 04:14:39'),
(9, 12, 6, '2025-07-10 06:22:29', '2025-07-10 06:22:29'),
(10, 13, 6, '2025-07-11 05:53:49', '2025-07-11 05:53:49'),
(12, 15, 6, '2025-07-11 06:00:46', '2025-07-11 06:00:46'),
(13, 16, 6, '2025-07-11 06:01:06', '2025-07-11 06:01:06');

-- --------------------------------------------------------

--
-- 테이블 구조 `serial_numbers`
--

CREATE TABLE `serial_numbers` (
  `id` bigint UNSIGNED NOT NULL,
  `serial_no` varchar(50) NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `model_prefix` varchar(20) NOT NULL,
  `special_prefix` varchar(20) NOT NULL DEFAULT 'NNN',
  `year` char(2) NOT NULL,
  `sequence` int UNSIGNED NOT NULL,
  `revision` char(2) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text,
  `last_activity` timestamp NULL DEFAULT NULL,
  `payload` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `sessions`
--

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
(132, 'fffce7d51014b78e86876e37be1f981ac747d208', 7, '172.23.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 17:01:34', 'a:2:{s:11:\"_csrf_token\";s:64:\"8bc7a388cc4bc4fbaff2eaf6449a39d992def1f3934fa0ca35c9cf2c6f59f72e\";s:7:\"user_id\";i:7;}', '2025-07-14 05:23:58', '2025-07-14 17:01:34'),
(133, '6796168db8f31f32f776e2cdd548ba3447e7ef21', NULL, '172.23.0.1', NULL, '2025-07-14 15:13:19', 'a:0:{}', '2025-07-14 06:13:19', '2025-07-14 15:13:19'),
(141, '00d4edc4199bc15c8fd66233ffa65d0594c6e496', 7, '172.23.0.1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2025-07-15 17:26:00', 'a:2:{s:11:\"_csrf_token\";s:64:\"84443a7444398cfa8190cc3ed6fe254b9af9942ce1e46e4b24c2805244e140a9\";s:7:\"user_id\";i:7;}', '2025-07-15 01:03:14', '2025-07-15 17:26:00'),
(145, '3b7429ac7f09520c293dc07eec1f4822ec304b4c', NULL, '172.23.0.1', NULL, '2025-07-15 10:50:24', 'a:0:{}', '2025-07-15 01:50:24', '2025-07-15 10:50:24'),
(152, '77b57bbef5ba548eccf624c6844a4f198a336c73', NULL, '172.23.0.1', 'Mozilla/5.0 zgrab/0.x', '2025-07-15 17:23:29', 'a:0:{}', '2025-07-15 08:23:29', '2025-07-15 17:23:29'),
(153, '98db0911f86859cc674f049d26949581004caf14', NULL, '172.23.0.1', 'Mozilla/5.0 zgrab/0.x', '2025-07-15 17:23:30', 'a:1:{s:11:\"_csrf_token\";s:64:\"87f9136a0976ece94bc7b5d857ee656ba81067546ce797da28fec67d0083ac96\";}', '2025-07-15 08:23:30', '2025-07-15 17:23:30');

-- --------------------------------------------------------

--
-- 테이블 구조 `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `users`
--

INSERT INTO `users` (`id`, `name`, `type`, `email`, `phone`, `password`, `gender`, `email_verified_at`, `is_active`, `birth`, `deleted_at`, `created_at`, `updated_at`) VALUES
(7, '관리자', 'admin', 'test@test.com', NULL, '$2y$10$zQGPYeE4yaq7ZtRv.KrdQ.uGV/kHnHcDd4gOq/sZ4cGV3NIskxN9u', 'U', NULL, 1, NULL, NULL, '2025-07-09 06:56:48', '2025-07-09 06:56:48'),
(9, 'Defser', 'dealer', 'defser@defser.com', '010231221', '$2y$10$bGFJpwMNwh3FicwLMTQZc.X5iMLBXAvZSmfrP6F10nxQV5iormSp2', 'U', NULL, 1, NULL, NULL, '2025-07-09 07:46:14', '2025-07-09 07:46:14'),
(10, '장준', 'employee', 'dldhfl607@naver.com', '01047152909', '$2y$10$aHdaBG317th7rKbH46EZSO5NF7AUPo.iUaYwXMVn6jKo8Pq8jgkte', 'U', NULL, 1, NULL, NULL, '2025-07-10 02:30:42', '2025-07-10 05:11:21'),
(11, '나인원랩스', 'employee', 'nineonelabs@gmail.com', '029519154', '$2y$10$MzddN9PEQ9fPubJ.OOOXuuskoloWA2B33gJpHCvcP7YXrECe61pfK', 'U', NULL, 1, NULL, NULL, '2025-07-10 02:42:45', '2025-07-10 02:42:45'),
(12, '장준대리점', 'dealer', 'jjn87.dev@gmail.com', '0102312312312', '$2y$10$E6B6R8Qwq2Y.zDqiWcKJqucwtkrFVmxgRdJHtkk0ey4PjRKl193Sa', 'U', NULL, 1, NULL, NULL, '2025-07-10 06:22:29', '2025-07-10 06:22:29'),
(13, 'tomato    ', 'dealer', 'tomato@tomato.com', '0102012312231', '$2y$10$GA2ZglnH2tVk71tQTNNnUOL0HWc3XyPXpyD.W6f2leBKdbIjGKJmC', 'U', NULL, 1, NULL, NULL, '2025-07-11 05:53:49', '2025-07-14 01:35:06'),
(15, '홍길순', 'dealer', 'hone@home.com', '12031200123', '$2y$10$w4AEJqhV944hPJlTPQT/kuTW52YoOZIH6QoI1888iSj3fe7ROdzPG', 'U', NULL, 1, NULL, '2025-07-11 06:00:51', '2025-07-11 06:00:46', '2025-07-11 06:00:51'),
(16, 'asdsda', 'dealer', 'asdasd@asdasd.com', '12331291230123', '$2y$10$YYFl2pigvS4KigVqHsBBE.n7k0HGkb.s9Fq6ers9Dk/TWa1FqXLYm', 'U', NULL, 1, NULL, '2025-07-11 06:01:12', '2025-07-11 06:01:06', '2025-07-11 06:01:12');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carts_customer_id` (`customer_id`);

--
-- 테이블의 인덱스 `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cart_items_cart_id` (`cart_id`),
  ADD KEY `idx_cart_items_product_id` (`product_id`),
  ADD KEY `idx_cart_items_cart_id_product_id` (`cart_id`,`product_id`),
  ADD KEY `idx_cart_items_set_group_id` (`set_group_id`),
  ADD KEY `idx_cart_items_set_group_id_set_group_sort` (`set_group_id`,`set_group_sort`);

--
-- 테이블의 인덱스 `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_claims_user_id` (`user_id`),
  ADD KEY `fk_claims_delaer_id` (`delaer_id`);

--
-- 테이블의 인덱스 `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customers_user_id` (`user_id`),
  ADD KEY `fk_customers_dealer_id` (`dealer_id`);

--
-- 테이블의 인덱스 `dealers`
--
ALTER TABLE `dealers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dealers_category_id_foreign` (`category_id`);

--
-- 테이블의 인덱스 `file_attachments`
--
ALTER TABLE `file_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_file_attachments_file_attachable_type_file_attachable_id` (`file_attachable_type`,`file_attachable_id`);

--
-- 테이블의 인덱스 `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notices_user_id` (`user_id`);

--
-- 테이블의 인덱스 `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_user_id` (`user_id`);

--
-- 테이블의 인덱스 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user_id` (`user_id`),
  ADD KEY `fk_orders_customer_id` (`customer_id`),
  ADD KEY `fk_orders_dealer_id` (`dealer_id`);

--
-- 테이블의 인덱스 `order_documents`
--
ALTER TABLE `order_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_documents_order_id` (`order_id`),
  ADD KEY `fk_order_documents_user_id` (`user_id`);

--
-- 테이블의 인덱스 `order_document_commercial_invoices`
--
ALTER TABLE `order_document_commercial_invoices`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `order_document_packing_lists`
--
ALTER TABLE `order_document_packing_lists`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `order_document_product_requests`
--
ALTER TABLE `order_document_product_requests`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `order_document_proforma_invoices`
--
ALTER TABLE `order_document_proforma_invoices`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `order_histories`
--
ALTER TABLE `order_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_histories_order_id` (`order_id`),
  ADD KEY `fk_order_histories_dealer_id` (`dealer_id`),
  ADD KEY `fk_order_histories_customer_id` (`customer_id`),
  ADD KEY `fk_order_histories_created_by` (`created_by`);

--
-- 테이블의 인덱스 `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order_id` (`order_id`),
  ADD KEY `idx_order_items_product_id` (`product_id`),
  ADD KEY `idx_order_items_order_id_product_id` (`order_id`,`product_id`),
  ADD KEY `idx_order_items_set_group_id` (`set_group_id`),
  ADD KEY `idx_order_items_set_group_id_set_group_sort` (`set_group_id`,`set_group_sort`);

--
-- 테이블의 인덱스 `order_logs`
--
ALTER TABLE `order_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_logs_order_id` (`order_id`),
  ADD KEY `fk_order_logs_user_id` (`user_id`);

--
-- 테이블의 인덱스 `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_password_reset_tokens_user_id` (`user_id`);

--
-- 테이블의 인덱스 `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_template_id` (`template_id`);

--
-- 테이블의 인덱스 `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_categories_parent_id` (`parent_id`);

--
-- 테이블의 인덱스 `product_headlights`
--
ALTER TABLE `product_headlights`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `product_loupes`
--
ALTER TABLE `product_loupes`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `product_templates`
--
ALTER TABLE `product_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_templates_category_id` (`category_id`);

--
-- 테이블의 인덱스 `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_remember_tokens_user_id` (`user_id`);

--
-- 테이블의 인덱스 `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_role_permissions_role_id` (`role_id`),
  ADD KEY `fk_role_permissions_permission_id` (`permission_id`);

--
-- 테이블의 인덱스 `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_role_users_user_id_role_id` (`user_id`,`role_id`),
  ADD KEY `fk_role_users_role_id` (`role_id`);

--
-- 테이블의 인덱스 `serial_numbers`
--
ALTER TABLE `serial_numbers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_serial_numbers_model_prefix_year` (`model_prefix`,`year`),
  ADD KEY `fk_serial_numbers_product_id` (`product_id`);

--
-- 테이블의 인덱스 `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sessions_user_id` (`user_id`);

--
-- 테이블의 인덱스 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 테이블의 AUTO_INCREMENT `claims`
--
ALTER TABLE `claims`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 테이블의 AUTO_INCREMENT `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `file_attachments`
--
ALTER TABLE `file_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- 테이블의 AUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 테이블의 AUTO_INCREMENT `notices`
--
ALTER TABLE `notices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 테이블의 AUTO_INCREMENT `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `order_documents`
--
ALTER TABLE `order_documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 테이블의 AUTO_INCREMENT `order_document_commercial_invoices`
--
ALTER TABLE `order_document_commercial_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 테이블의 AUTO_INCREMENT `order_document_packing_lists`
--
ALTER TABLE `order_document_packing_lists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 테이블의 AUTO_INCREMENT `order_document_product_requests`
--
ALTER TABLE `order_document_product_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 테이블의 AUTO_INCREMENT `order_histories`
--
ALTER TABLE `order_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 테이블의 AUTO_INCREMENT `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 테이블의 AUTO_INCREMENT `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- 테이블의 AUTO_INCREMENT `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 테이블의 AUTO_INCREMENT `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 테이블의 AUTO_INCREMENT `product_templates`
--
ALTER TABLE `product_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 테이블의 AUTO_INCREMENT `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 테이블의 AUTO_INCREMENT `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- 테이블의 AUTO_INCREMENT `role_users`
--
ALTER TABLE `role_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 테이블의 AUTO_INCREMENT `serial_numbers`
--
ALTER TABLE `serial_numbers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- 테이블의 AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- 덤프된 테이블의 제약사항
--

--
-- 테이블의 제약사항 `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_carts_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart_id` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `fk_claims_delaer_id` FOREIGN KEY (`delaer_id`) REFERENCES `dealers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_claims_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customers_dealer_id` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_customers_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- 테이블의 제약사항 `dealers`
--
ALTER TABLE `dealers`
  ADD CONSTRAINT `dealers_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_dealers_id` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `fk_notices_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_dealer_id` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- 테이블의 제약사항 `order_documents`
--
ALTER TABLE `order_documents`
  ADD CONSTRAINT `fk_order_documents_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_documents_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- 테이블의 제약사항 `order_document_commercial_invoices`
--
ALTER TABLE `order_document_commercial_invoices`
  ADD CONSTRAINT `fk_order_document_commercial_invoices_id` FOREIGN KEY (`id`) REFERENCES `order_documents` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `order_document_packing_lists`
--
ALTER TABLE `order_document_packing_lists`
  ADD CONSTRAINT `fk_order_document_packing_lists_id` FOREIGN KEY (`id`) REFERENCES `order_documents` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `order_document_product_requests`
--
ALTER TABLE `order_document_product_requests`
  ADD CONSTRAINT `fk_order_document_product_requests_id` FOREIGN KEY (`id`) REFERENCES `order_documents` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `order_document_proforma_invoices`
--
ALTER TABLE `order_document_proforma_invoices`
  ADD CONSTRAINT `fk_order_document_proforma_invoices_id` FOREIGN KEY (`id`) REFERENCES `order_documents` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `order_histories`
--
ALTER TABLE `order_histories`
  ADD CONSTRAINT `fk_order_histories_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_histories_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_histories_dealer_id` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_histories_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `order_logs`
--
ALTER TABLE `order_logs`
  ADD CONSTRAINT `fk_order_logs_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_logs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- 테이블의 제약사항 `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `fk_password_reset_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_template_id` FOREIGN KEY (`template_id`) REFERENCES `product_templates` (`id`) ON DELETE SET NULL;

--
-- 테이블의 제약사항 `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `fk_product_categories_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `product_headlights`
--
ALTER TABLE `product_headlights`
  ADD CONSTRAINT `fk_product_headlights_id` FOREIGN KEY (`id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `product_loupes`
--
ALTER TABLE `product_loupes`
  ADD CONSTRAINT `fk_product_loupes_id` FOREIGN KEY (`id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `product_templates`
--
ALTER TABLE `product_templates`
  ADD CONSTRAINT `fk_product_templates_category_id` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

--
-- 테이블의 제약사항 `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `fk_remember_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_role_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `role_users`
--
ALTER TABLE `role_users`
  ADD CONSTRAINT `fk_role_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_role_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `serial_numbers`
--
ALTER TABLE `serial_numbers`
  ADD CONSTRAINT `fk_serial_numbers_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
