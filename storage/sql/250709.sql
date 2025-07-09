-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- 호스트: db:3306
-- 생성 시간: 25-07-09 09:16
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
  `description` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `dealer_id`, `name`, `country`, `phone`, `email`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, 7, NULL, '일루코_고객', 'KR', '0102312312312', 'illuco@illuco.com', 'tteas\r\nd\r\nasdsda\r\n', NULL, '2025-07-09 06:58:32', '2025-07-09 06:58:32'),
(3, 9, 9, '양상규', 'KR', '01095312312', 'didtkdrb@naver.com', '양상규 매니저 ', NULL, '2025-07-09 07:48:49', '2025-07-09 07:48:49');

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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `dealers`
--

INSERT INTO `dealers` (`id`, `country`, `code`, `address`, `description`, `memo`, `use_default_memo`, `deleted_at`, `created_at`, `updated_at`) VALUES
(9, 'FR', 'DF02', 'assdaasdsdasd', 'sd\r\nsd\r\n\r\nasd\r\nasd\r\n', NULL, 0, NULL, '2025-07-09 07:46:14', '2025-07-09 07:46:14');

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
  `file_key` varchar(255) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `file_attachments`
--

INSERT INTO `file_attachments` (`id`, `file_attachable_type`, `file_attachable_id`, `original_name`, `name`, `mime_type`, `size`, `path`, `extension`, `upload_path`, `file_key`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'producttemplate', 1, 'wired.jpg', 'd2061dd9561f852a7d24e31e3a09e44b.jpg', 'image/jpeg', 705413, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/d2061dd9561f852a7d24e31e3a09e44b.jpg', 'main_image', 0, '2025-07-03 11:37:22', '2025-07-03 11:37:22'),
(2, 'producttemplate', 2, 'wireless.jpg', '97d680b8337a9a2ce4d095128505d3ba.jpg', 'image/jpeg', 603879, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/97d680b8337a9a2ce4d095128505d3ba.jpg', 'main_image', 0, '2025-07-03 11:38:05', '2025-07-03 11:38:05'),
(3, 'producttemplate', 3, 'Frame 1.jpg', 'bbf663e9a6086a3495aefc8083ca3eda.jpg', 'image/jpeg', 235203, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/bbf663e9a6086a3495aefc8083ca3eda.jpg', 'main_image', 0, '2025-07-03 11:39:11', '2025-07-03 11:39:11'),
(4, 'producttemplate', 4, 'Frame 2.jpg', '11c4303ab70944024adbccc01e8568e1.jpg', 'image/jpeg', 210485, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/11c4303ab70944024adbccc01e8568e1.jpg', 'main_image', 0, '2025-07-03 11:39:55', '2025-07-03 11:39:55'),
(5, 'producttemplate', 5, 'image 511.jpg', 'fe5b3de13002c1649901e7ac675a6512.jpg', 'image/jpeg', 366310, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/fe5b3de13002c1649901e7ac675a6512.jpg', 'main_image', 0, '2025-07-03 23:44:48', '2025-07-03 23:44:48');

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
(30, '20250629_062026_create_order_histories_table', 30, '2025-07-09 06:40:52', '2025-07-09 06:40:52');

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
(1, 7, 2, NULL, '관리자', 'test@test.com', '', NULL, NULL, NULL, NULL, 'OR-CST-20250709-00001', 'confirmed', 1150.00, 'asdsdadssda', NULL, '2025-07-09 00:00:00', '2025-07-09 07:34:59'),
(2, 9, 3, 9, 'Defser', 'defser@defser.com', '010231221', NULL, NULL, NULL, '2025-07-09 18:08:01', 'OR-DF02-20250709-00001', 'canceled', 2700.00, 'ㅁㄴㅇㄴㅇㅁㄴㅇㅇㄴㄴㅇㅁ', NULL, '2025-07-09 00:00:00', '2025-07-09 09:08:01');

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
(8, 2, 9, 'CI-DF02-20250709-00001', 'commercial_invoice', NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20');

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
(8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20');

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
(7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20');

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
(6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20');

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
(5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20');

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
(2, NULL, NULL, 1, 2, 2, 2, NULL, 1350.00, 2700.00, NULL, '2025-07-09 07:55:20', '2025-07-09 07:55:20');

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
(1, 2, '무선 헤드라이트', NULL, 'headlight', 'HL', 'IHL-2000', 1150.00, '무선 헤드라이트. wireless headlight', NULL, '2025-07-09 06:59:18', '2025-07-09 06:59:18'),
(2, 4, 'Galilean', NULL, 'loupe', 'LP', 'ITL-1040P', 1350.00, 'galilean loupe', NULL, '2025-07-09 07:55:09', '2025-07-09 07:55:09');

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
  `wireless_color` varchar(255) DEFAULT NULL,
  `engraving_text` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `product_headlights`
--

INSERT INTO `product_headlights` (`id`, `wireless_color`, `engraving_text`, `created_at`, `updated_at`) VALUES
(1, 'gray', 'asdsdasddas', '2025-07-09 06:59:18', '2025-07-09 06:59:18');

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
(2, 'ready-made', NULL, 'frame4', 47.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 07:55:09', '2025-07-09 07:55:09');

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
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(5, 'admin', '최고 관리자', '2025-07-09 06:51:43', '2025-07-09 06:51:43'),
(6, 'dealer', '대리점 권한입니다.', '2025-07-09 07:43:58', '2025-07-09 07:43:58'),
(7, 'employee', '직원 권한입니다.', '2025-07-09 07:44:26', '2025-07-09 07:44:26'),
(8, 'sales', '영업담당자 권한입니다.', '2025-07-09 07:45:46', '2025-07-09 07:45:46');

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
(3, 9, 6, '2025-07-09 07:46:14', '2025-07-09 07:46:14');

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
(2, 'eb1b7aa08543a257aa90d982321e17448e33b206', NULL, '172.23.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', '2025-07-09 15:48:08', 'a:0:{}', '2025-07-09 06:48:08', '2025-07-09 15:48:08'),
(8, 'c5d0e3c24ad8c321b8c9118f845c4c0151d326c5', NULL, '172.23.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', '2025-07-09 16:19:33', 'a:0:{}', '2025-07-09 07:19:33', '2025-07-09 16:19:33'),
(9, 'a2fc1e46bfc37e651b0796b51992d8678d449c09', NULL, '172.23.0.1', 'curl/7.29.0', '2025-07-09 16:21:10', 'a:0:{}', '2025-07-09 07:21:10', '2025-07-09 16:21:10'),
(10, '9d592b50f4342a94b60bffc5391d8c22d1ab97a6', NULL, '172.23.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 8_1_1) AppleWebKit/568.38 (KHTML, like Gecko) Chrome/90.0.2055 Safari/537.36', '2025-07-09 16:21:55', 'a:0:{}', '2025-07-09 07:21:55', '2025-07-09 16:21:55'),
(11, 'fd93ec82d68fd3dcb8187fa63d3986052c9502ca', NULL, '172.23.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 8_2_2) AppleWebKit/589.54 (KHTML, like Gecko) Chrome/77.0.2052 Safari/537.36', '2025-07-09 16:22:00', 'a:1:{s:11:\"_csrf_token\";s:64:\"cfcd906b94930fe6d72185534c0fa17d3eec3138921efa3261697ebf7b68d960\";}', '2025-07-09 07:22:00', '2025-07-09 16:22:00'),
(12, '463526ff29d138ffee5ff02bd5449bf1182e4800', NULL, '172.23.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', '2025-07-09 16:23:34', 'a:0:{}', '2025-07-09 07:23:34', '2025-07-09 16:23:34'),
(13, 'b70aac41f6522965e1762155c7cc331ea6f13455', NULL, '172.23.0.1', NULL, '2025-07-09 16:43:10', 'a:0:{}', '2025-07-09 07:43:10', '2025-07-09 16:43:10'),
(15, '04c201655856ae43a5ca89e7b8fae858ac3b6f94', 9, '172.23.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-09 18:08:04', 'a:2:{s:11:\"_csrf_token\";s:64:\"09a5d3960a66f03d10f95d5923131c858b68c14c05b6aed8e7cca58abf4b5cb9\";s:7:\"user_id\";i:9;}', '2025-07-09 07:46:42', '2025-07-09 18:08:04'),
(16, '915f45d8d9d14f150d56ca14172058db2ac1e245', NULL, '172.23.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', '2025-07-09 16:54:59', 'a:0:{}', '2025-07-09 07:54:59', '2025-07-09 16:54:59'),
(17, '32fc931a08efbee94aaffa01883f44e0c8a8ceff', NULL, '172.23.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', '2025-07-09 17:34:21', 'a:0:{}', '2025-07-09 08:34:21', '2025-07-09 17:34:21'),
(18, 'a3ad75a2b9bb9bf256b34295a2273480e35f9215', NULL, '172.23.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', '2025-07-09 18:01:21', 'a:0:{}', '2025-07-09 09:01:21', '2025-07-09 18:01:21');

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
(9, 'Defser', 'dealer', 'defser@defser.com', '010231221', '$2y$10$bGFJpwMNwh3FicwLMTQZc.X5iMLBXAvZSmfrP6F10nxQV5iormSp2', 'U', NULL, 1, NULL, NULL, '2025-07-09 07:46:14', '2025-07-09 07:46:14');

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 테이블의 AUTO_INCREMENT `claims`
--
ALTER TABLE `claims`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `file_attachments`
--
ALTER TABLE `file_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 테이블의 AUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- 테이블의 AUTO_INCREMENT `notices`
--
ALTER TABLE `notices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 테이블의 AUTO_INCREMENT `order_documents`
--
ALTER TABLE `order_documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 테이블의 AUTO_INCREMENT `order_document_commercial_invoices`
--
ALTER TABLE `order_document_commercial_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 테이블의 AUTO_INCREMENT `order_document_packing_lists`
--
ALTER TABLE `order_document_packing_lists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 테이블의 AUTO_INCREMENT `order_document_product_requests`
--
ALTER TABLE `order_document_product_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 테이블의 AUTO_INCREMENT `order_histories`
--
ALTER TABLE `order_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `serial_numbers`
--
ALTER TABLE `serial_numbers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- 테이블의 AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
