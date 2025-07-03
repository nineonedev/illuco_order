-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- 호스트: db:3306
-- 생성 시간: 25-07-03 13:29
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
(1, 1, NULL, '홍길동', 'KR', '01012345678', 'hone@hone.com', '홍길동입니다.\r\n', NULL, '2025-07-03 11:40:44', '2025-07-03 11:40:44');

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
(2, 'FR', 'DFS023', 'test', 'dd', NULL, 0, NULL, '2025-07-03 12:10:52', '2025-07-03 12:10:52'),
(3, 'US', 'KA001', 'te', 'tsadsa', NULL, 0, NULL, '2025-07-03 12:12:43', '2025-07-03 12:12:43');

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
(4, 'producttemplate', 4, 'Frame 2.jpg', '11c4303ab70944024adbccc01e8568e1.jpg', 'image/jpeg', 210485, '/var/www/html/storage/uploads/public/producttemplate', 'jpg', '/static/uploads/producttemplate/11c4303ab70944024adbccc01e8568e1.jpg', 'main_image', 0, '2025-07-03 11:39:55', '2025-07-03 11:39:55');

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
(1, '20250608_040545_create_users_table', 1, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(2, '20250608_040617_create_file_attachments_table', 2, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(3, '20250612_040546_create_sessions_table', 3, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(4, '20250612_231703_create_dealers_table', 4, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(5, '20250612_231710_create_customers_table', 5, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(6, '20250612_232603_create_notices_table', 6, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(7, '20250612_234333_create_product_categories_table', 7, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(8, '20250612_234420_create_product_templates_table', 8, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(9, '20250613_002038_create_products_table', 9, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(10, '20250613_002039_create_product_headlights_table', 10, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(11, '20250613_002039_create_product_loupes_table', 11, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(12, '20250613_002932_create_claims_table', 12, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(13, '20250613_011346_create_rules_table', 13, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(14, '20250613_011407_create_permissions_table', 14, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(15, '20250613_011535_create_role_permissions_table', 15, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(16, '20250613_011603_create_role_users_table', 16, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(17, '20250613_012126_create_notifications_table', 17, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(18, '20250613_012251_create_password_reset_tokens_table', 18, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(19, '20250613_012251_create_remember_tokens_table', 19, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(20, '20250613_014848_create_carts_table', 20, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(21, '20250613_015036_create_cart_items_table', 21, '2025-07-03 11:18:27', '2025-07-03 11:18:27'),
(22, '20250613_015109_create_orders_table', 22, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(23, '20250613_015313_create_order_items_table', 23, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(24, '20250617_080212_create_order_documents_table', 24, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(25, '20250617_080213_create_order_document_commercial_invoices_table', 25, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(26, '20250617_080213_create_order_document_packling_lists_table', 26, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(27, '20250617_080213_create_order_document_product_requests_table', 27, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(28, '20250617_080213_create_order_document_proforma_invoices_table', 28, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(29, '20250629_053434_create_serial_numbers_table', 29, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(30, '20250629_062026_create_order_histories_table', 30, '2025-07-03 11:18:28', '2025-07-03 11:18:28'),
(31, '20250629_063032_create_order_receivables_table', 31, '2025-07-03 11:18:28', '2025-07-03 11:18:28');

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
  `order_no` varchar(50) NOT NULL,
  `order_status` varchar(255) NOT NULL DEFAULT 'new',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `memo` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `order_documents`
--

CREATE TABLE `order_documents` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `document_no` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `order_document_commercial_invoices`
--

CREATE TABLE `order_document_commercial_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `buyer_name` varchar(255) DEFAULT NULL,
  `buyer_address` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_model` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `order_document_packing_lists`
--

CREATE TABLE `order_document_packing_lists` (
  `id` bigint UNSIGNED NOT NULL,
  `packing_list_no` varchar(255) DEFAULT NULL,
  `packing_date` date DEFAULT NULL,
  `buyer_name` varchar(255) DEFAULT NULL,
  `box_no` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_model` varchar(255) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `net_weight` decimal(8,2) DEFAULT NULL,
  `gross_weight` decimal(8,2) DEFAULT NULL,
  `volume` varchar(255) DEFAULT NULL,
  `remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `order_document_product_requests`
--

CREATE TABLE `order_document_product_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_model` varchar(255) DEFAULT NULL,
  `box_size` varchar(255) DEFAULT NULL,
  `memo` text,
  `quantity` int DEFAULT NULL,
  `total_qty` int DEFAULT NULL,
  `remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `order_document_proforma_invoices`
--

CREATE TABLE `order_document_proforma_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `buyer_name` varchar(255) DEFAULT NULL,
  `buyer_address` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_model` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `remarks` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `order_receivables`
--

CREATE TABLE `order_receivables` (
  `id` bigint UNSIGNED NOT NULL,
  `dealer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '미수금 잔액',
  `memo` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(1, 'dealer', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(2, 'dealer', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(3, 'dealer', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(4, 'dealer', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(5, 'claim', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(6, 'claim', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(7, 'claim', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(8, 'claim', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(9, 'notice', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(10, 'notice', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(11, 'notice', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(12, 'notice', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(13, 'customer', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(14, 'customer', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(15, 'customer', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(16, 'customer', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(17, 'producttemplate', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(18, 'producttemplate', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(19, 'producttemplate', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(20, 'producttemplate', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(21, 'order', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(22, 'order', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(23, 'order', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(24, 'order', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(25, 'cart', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(26, 'cart', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(27, 'cart', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(28, 'cart', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(29, 'role', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(30, 'role', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(31, 'role', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(32, 'role', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(33, 'category', 'create', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(34, 'category', 'read', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(35, 'category', 'update', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(36, 'category', 'delete', '2025-07-03 11:18:30', '2025-07-03 11:18:30'),
(37, 'employee', 'create', '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(38, 'employee', 'read', '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(39, 'employee', 'update', '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(40, 'employee', 'delete', '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(41, 'product', 'create', '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(42, 'product', 'read', '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(43, 'product', 'update', '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(44, 'product', 'delete', '2025-07-03 12:28:58', '2025-07-03 12:28:58');

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
(4, 2, 'Galilean', 'LP', 'ITL-1040P', 1350.00, 0, 'galilean loupe', NULL, '2025-07-03 11:39:55', '2025-07-03 11:39:55');

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
(1, 'admin', '최고 관리자', '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(2, 'dealer', '대리점 권한입니다. ', '2025-07-03 12:00:55', '2025-07-03 12:01:22'),
(3, 'employee', '직원 권한입니다.', '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(4, 'owner', '영업부서 권한입니다.', '2025-07-03 12:02:49', '2025-07-03 12:02:49');

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
(5, 1, 5, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(6, 1, 6, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(7, 1, 7, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(8, 1, 8, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(9, 1, 9, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(10, 1, 10, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(11, 1, 11, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(12, 1, 12, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(13, 1, 13, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(14, 1, 14, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(15, 1, 15, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(16, 1, 16, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(21, 1, 21, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(22, 1, 22, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(23, 1, 23, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(24, 1, 24, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(25, 1, 25, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(26, 1, 26, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(27, 1, 27, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(28, 1, 28, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(29, 1, 29, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(30, 1, 30, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(31, 1, 31, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(32, 1, 32, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(33, 1, 33, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(34, 1, 34, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(35, 1, 35, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(36, 1, 36, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(37, 2, 5, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(38, 2, 6, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(39, 2, 7, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(40, 2, 8, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(41, 2, 10, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(42, 2, 13, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(43, 2, 14, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(44, 2, 15, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(45, 2, 16, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(46, 2, 21, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(47, 2, 22, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(48, 2, 25, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(49, 2, 26, '2025-07-03 12:00:55', '2025-07-03 12:00:55'),
(50, 2, 5, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(51, 2, 6, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(52, 2, 7, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(53, 2, 8, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(54, 2, 10, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(55, 2, 13, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(56, 2, 14, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(57, 2, 15, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(58, 2, 16, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(59, 2, 21, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(60, 2, 22, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(61, 2, 25, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(62, 2, 26, '2025-07-03 12:01:22', '2025-07-03 12:01:22'),
(63, 3, 1, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(64, 3, 2, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(65, 3, 3, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(66, 3, 4, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(67, 3, 5, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(68, 3, 6, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(69, 3, 7, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(70, 3, 8, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(71, 3, 9, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(72, 3, 10, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(73, 3, 11, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(74, 3, 12, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(75, 3, 13, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(76, 3, 14, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(77, 3, 15, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(78, 3, 16, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(79, 3, 21, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(80, 3, 22, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(81, 3, 23, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(82, 3, 24, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(83, 3, 25, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(84, 3, 26, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(85, 3, 27, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(86, 3, 28, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(87, 3, 33, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(88, 3, 34, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(89, 3, 35, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(90, 3, 36, '2025-07-03 12:02:04', '2025-07-03 12:02:04'),
(91, 4, 1, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(92, 4, 2, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(93, 4, 3, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(94, 4, 4, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(95, 4, 5, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(96, 4, 6, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(97, 4, 7, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(98, 4, 8, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(99, 4, 9, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(100, 4, 10, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(101, 4, 11, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(102, 4, 12, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(103, 4, 13, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(104, 4, 14, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(105, 4, 15, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(106, 4, 16, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(107, 4, 21, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(108, 4, 22, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(109, 4, 23, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(110, 4, 24, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(111, 4, 25, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(112, 4, 26, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(113, 4, 27, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(114, 4, 28, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(115, 4, 33, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(116, 4, 34, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(117, 4, 35, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(118, 4, 36, '2025-07-03 12:02:49', '2025-07-03 12:02:49'),
(123, 1, 5, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(124, 1, 6, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(125, 1, 7, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(126, 1, 8, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(127, 1, 9, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(128, 1, 10, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(129, 1, 11, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(130, 1, 12, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(131, 1, 13, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(132, 1, 14, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(133, 1, 15, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(134, 1, 16, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(135, 1, 21, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(136, 1, 22, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(137, 1, 23, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(138, 1, 24, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(139, 1, 25, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(140, 1, 26, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(141, 1, 27, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(142, 1, 28, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(143, 1, 29, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(144, 1, 30, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(145, 1, 31, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(146, 1, 32, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(147, 1, 33, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(148, 1, 34, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(149, 1, 35, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(150, 1, 36, '2025-07-03 12:21:22', '2025-07-03 12:21:22'),
(155, 1, 5, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(156, 1, 6, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(157, 1, 7, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(158, 1, 8, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(159, 1, 9, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(160, 1, 10, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(161, 1, 11, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(162, 1, 12, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(163, 1, 13, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(164, 1, 14, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(165, 1, 15, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(166, 1, 16, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(167, 1, 21, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(168, 1, 22, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(169, 1, 23, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(170, 1, 24, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(171, 1, 25, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(172, 1, 26, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(173, 1, 27, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(174, 1, 28, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(175, 1, 29, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(176, 1, 30, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(177, 1, 31, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(178, 1, 32, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(179, 1, 33, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(180, 1, 34, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(181, 1, 35, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(182, 1, 36, '2025-07-03 12:25:20', '2025-07-03 12:25:20'),
(183, 1, 5, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(184, 1, 6, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(185, 1, 7, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(186, 1, 8, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(187, 1, 9, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(188, 1, 10, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(189, 1, 11, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(190, 1, 12, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(191, 1, 13, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(192, 1, 14, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(193, 1, 15, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(194, 1, 16, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(195, 1, 21, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(196, 1, 22, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(197, 1, 23, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(198, 1, 24, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(199, 1, 25, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(200, 1, 26, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(201, 1, 27, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(202, 1, 28, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(203, 1, 29, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(204, 1, 30, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(205, 1, 31, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(206, 1, 32, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(207, 1, 33, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(208, 1, 34, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(209, 1, 35, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(210, 1, 36, '2025-07-03 12:25:26', '2025-07-03 12:25:26'),
(211, 1, 37, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(212, 1, 38, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(213, 1, 39, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(214, 1, 40, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(215, 1, 1, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(216, 1, 2, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(217, 1, 3, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(218, 1, 4, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(219, 1, 5, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(220, 1, 6, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(221, 1, 7, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(222, 1, 8, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(223, 1, 9, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(224, 1, 10, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(225, 1, 11, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(226, 1, 12, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(227, 1, 13, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(228, 1, 14, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(229, 1, 15, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(230, 1, 16, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(231, 1, 41, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(232, 1, 42, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(233, 1, 43, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(234, 1, 44, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(235, 1, 21, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(236, 1, 22, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(237, 1, 23, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(238, 1, 24, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(239, 1, 25, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(240, 1, 26, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(241, 1, 27, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(242, 1, 28, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(243, 1, 29, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(244, 1, 30, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(245, 1, 31, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(246, 1, 32, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(247, 1, 33, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(248, 1, 34, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(249, 1, 35, '2025-07-03 12:28:58', '2025-07-03 12:28:58'),
(250, 1, 36, '2025-07-03 12:28:58', '2025-07-03 12:28:58');

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
(1, 1, 1, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(2, 2, 1, '2025-07-03 12:10:52', '2025-07-03 12:10:52'),
(3, 3, 1, '2025-07-03 12:12:43', '2025-07-03 12:12:43');

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
(2, '9057bb73ac15d60b3a4da287720e4d0d305b183a', 1, '192.168.65.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-03 21:38:59', 'a:2:{s:11:\"_csrf_token\";s:64:\"19dc64c951261bf772a312099ac623897f82014b5dcbe764a0020c2b327775f7\";s:7:\"user_id\";i:1;}', '2025-07-03 11:18:46', '2025-07-03 21:38:59');

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
  `birth` date DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 테이블의 덤프 데이터 `users`
--

INSERT INTO `users` (`id`, `name`, `type`, `email`, `phone`, `password`, `gender`, `email_verified_at`, `birth`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '관리자', 'admin', 'test@test.com', NULL, '$2y$10$guoABbi92FxSLXzXlPV1I.xkLlQN1kjTftUHq6Lz1JDImKic/Ankm', 'U', NULL, NULL, NULL, '2025-07-03 11:18:36', '2025-07-03 11:18:36'),
(2, 'defser', 'dealer', 'defser@defser.com', NULL, '$2y$10$SZUAnQARDODkQDBsZzIhjOyTpOlU1/W8IWjkUzrG8xNg7XB9nBLDu', 'U', NULL, NULL, NULL, '2025-07-03 12:10:52', '2025-07-03 12:10:52'),
(3, 'Khan', 'dealer', 'khan@khan.com', NULL, '$2y$10$OkXa7eejY7NbhXsEqr0wtOWB5CnjADIXPCKWsEJRrP.7YtKaDem7.', 'U', NULL, NULL, NULL, '2025-07-03 12:12:43', '2025-07-03 12:12:43');

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
-- 테이블의 인덱스 `order_receivables`
--
ALTER TABLE `order_receivables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_receivables_dealer_id` (`dealer_id`),
  ADD KEY `fk_order_receivables_customer_id` (`customer_id`),
  ADD KEY `fk_order_receivables_order_id` (`order_id`);

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `claims`
--
ALTER TABLE `claims`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 테이블의 AUTO_INCREMENT `file_attachments`
--
ALTER TABLE `file_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 테이블의 AUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `order_documents`
--
ALTER TABLE `order_documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `order_document_commercial_invoices`
--
ALTER TABLE `order_document_commercial_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `order_document_packing_lists`
--
ALTER TABLE `order_document_packing_lists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `order_document_product_requests`
--
ALTER TABLE `order_document_product_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `order_histories`
--
ALTER TABLE `order_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `order_receivables`
--
ALTER TABLE `order_receivables`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- 테이블의 AUTO_INCREMENT `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 테이블의 AUTO_INCREMENT `product_templates`
--
ALTER TABLE `product_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 테이블의 AUTO_INCREMENT `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 테이블의 AUTO_INCREMENT `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 테이블의 AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `fk_order_documents_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- 테이블의 제약사항 `order_receivables`
--
ALTER TABLE `order_receivables`
  ADD CONSTRAINT `fk_order_receivables_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_receivables_dealer_id` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_receivables_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

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
