-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2026 at 10:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bakery_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `account_number` varchar(64) DEFAULT NULL,
  `current_balance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `chart_of_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `company_id`, `branch_id`, `code`, `name`, `type`, `account_number`, `current_balance`, `chart_of_account_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'KAS-PUSAT', 'Kas Operasional Pusat', 'cash', NULL, 25000000.00, 1, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 2, 'KAS-CBG1', 'Kas Kasir Menteng', 'cash', NULL, 5000000.00, 1, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 3, 'KAS-CBG2', 'Kas Kasir Senopati', 'cash', NULL, 4000000.00, 1, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(4, 1, 1, 'BANK-BCA', 'Bank BCA PT Roti Nusantara', 'bank', '5420991823', 150000000.00, 2, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(64) NOT NULL,
  `entity_type` varchar(128) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_central` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `manager_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `company_id`, `code`, `name`, `is_central`, `phone`, `email`, `address`, `manager_user_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'PUSAT', 'Kantor Pusat & Central Kitchen', 1, '021-5558801', 'pusat@bakeryerp.local', 'Jl. Industri Bakery No. 1, Jakarta Timur', NULL, 1, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(2, 1, 'CBG001', 'Cabang Menteng (Bakery & Cafe)', 0, '021-5558802', 'menteng@bakeryerp.local', 'Jl. HOS Cokroaminoto No. 24, Menteng, Jakarta Pusat', NULL, 1, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(3, 1, 'CBG002', 'Cabang Senopati (Pastry & Coffee)', 0, '021-5558803', 'senopati@bakeryerp.local', 'Jl. Senopati No. 78, Kebayoran Baru, Jakarta Selatan', NULL, 1, '2026-10-09 21:44:54', '2026-10-09 21:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `branch_sequences`
--

CREATE TABLE `branch_sequences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(32) NOT NULL,
  `period` varchar(16) NOT NULL,
  `current_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_settings`
--

CREATE TABLE `branch_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_users`
--

CREATE TABLE `branch_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_users`
--

INSERT INTO `branch_users` (`id`, `branch_id`, `user_id`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-10-09 21:44:55', '2026-10-09 21:44:55'),
(2, 2, 1, 0, '2026-10-09 21:44:55', '2026-10-09 21:44:55'),
(3, 3, 1, 0, '2026-10-09 21:44:55', '2026-10-09 21:44:55'),
(4, 1, 2, 1, '2026-10-09 21:44:55', '2026-10-09 21:44:55'),
(5, 2, 3, 1, '2026-10-09 21:44:55', '2026-10-09 21:44:55'),
(6, 2, 4, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(7, 1, 5, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(8, 1, 6, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_transactions`
--

CREATE TABLE `cash_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_number` varchar(64) NOT NULL,
  `transaction_date` date NOT NULL,
  `type` varchar(16) NOT NULL,
  `category` varchar(64) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `reference_type` varchar(64) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_transfers`
--

CREATE TABLE `cash_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `transfer_number` varchar(64) NOT NULL,
  `transfer_date` date NOT NULL,
  `from_account_id` bigint(20) UNSIGNED NOT NULL,
  `to_account_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `company_id`, `name`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tepung & Bahan Kering', 'ingredient', 'Bahan baku kering dasar roti', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 'Mentega, Susu & Dairy', 'ingredient', 'Produk susu dan olahan lemak', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 'Topping & Isian', 'ingredient', 'Cokelat, keju, selai, dan pasta', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(4, 1, 'Kemasan & Packaging', 'ingredient', 'Kardus, plastik pembungkus, mika', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(5, 1, 'Roti Manis & Sobek', 'product', 'Aneka roti manis isi dan sobek', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(6, 1, 'Croissant & Pastry', 'product', 'Viennoiserie dan puff pastry renyah', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(7, 1, 'Cake & Tart', 'product', 'Kue basah, bolu, dan slice cake', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(8, 1, 'Beban Operasional Toko', 'expense', 'Listrik, air, perlengkapan kasir', '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `normal_balance` varchar(10) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`id`, `company_id`, `code`, `name`, `type`, `normal_balance`, `parent_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, '1110', 'Kas Tunai & Kasir', 'asset', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, '1120', 'Bank BCA Operasional', 'asset', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, '1121', 'Bank Mandiri', 'asset', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(4, 1, '1130', 'Piutang Usaha (AR)', 'asset', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(5, 1, '1141', 'Persediaan Bahan Baku', 'asset', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(6, 1, '1142', 'Persediaan Dalam Proses (WIP)', 'asset', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(7, 1, '1143', 'Persediaan Produk Jadi', 'asset', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(8, 1, '1149', 'Persediaan Dalam Perjalanan (In-Transit)', 'asset', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(9, 1, '2110', 'Hutang Usaha (AP)', 'liability', 'credit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(10, 1, '2130', 'Hutang Pajak / PPN', 'liability', 'credit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(11, 1, '3100', 'Modal Pemilik', 'equity', 'credit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(12, 1, '3200', 'Laba Ditahan', 'equity', 'credit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(13, 1, '3300', 'Prive Pemilik', 'equity', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(14, 1, '4100', 'Pendapatan Penjualan Bakery', 'revenue', 'credit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(15, 1, '4200', 'Pendapatan Lain-lain', 'revenue', 'credit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(16, 1, '5100', 'Harga Pokok Penjualan (HPP)', 'cogs', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(17, 1, '5200', 'Biaya Kerusakan & Waste Produksi', 'cogs', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(18, 1, '6100', 'Beban Gaji & Upah Karyawan', 'expense', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(19, 1, '6200', 'Beban Listrik, Air, & Gas', 'expense', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(20, 1, '6300', 'Beban Sewa Outlet', 'expense', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(21, 1, '6500', 'Beban Operasional Lain-lain', 'expense', 'debit', NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `legal_name` varchar(255) DEFAULT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'IDR',
  `timezone` varchar(50) NOT NULL DEFAULT 'Asia/Jakarta',
  `logo_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `legal_name`, `tax_id`, `phone`, `email`, `address`, `currency`, `timezone`, `logo_path`, `is_active`, `settings`, `created_at`, `updated_at`) VALUES
(1, 'PT Roti & Kue Nusantara', 'PT Roti & Kue Nusantara Sejahtera', '01.234.567.8-012.000', '021-5558899', 'corporate@bakeryerp.local', 'Jl. Sudirman Kav. 45, Jakarta Pusat', 'IDR', 'Asia/Jakarta', NULL, 1, NULL, '2026-10-09 21:44:54', '2026-10-09 21:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `credit_limit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `company_id`, `code`, `name`, `phone`, `email`, `address`, `credit_limit`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'CUST-GENERAL', 'Pelanggan Umum (Walk-in)', '-', NULL, '-', 0.00, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 'CUST001', 'Hotel Santika Premiere (B2B)', '021-5361234', NULL, 'Slipi, Jakarta Barat', 20000000.00, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 'CUST002', 'Ibu Ratna (Langganan Arisan)', '081299887766', NULL, 'Menteng, Jakarta Pusat', 5000000.00, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `expense_number` varchar(64) NOT NULL,
  `expense_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `company_id`, `name`, `chart_of_account_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Listrik, Air & Gas', 19, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 'Gaji & Uang Lembur', 18, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 'Sewa Gedung Cabang', 20, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `min_stock` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `cost_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `company_id`, `code`, `name`, `category_id`, `unit_id`, `purchase_unit_id`, `min_stock`, `cost_price`, `is_active`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'ING-TPG01', 'Tepung Cakra Kembar Premium', 1, 1, NULL, 50.0000, 14000.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 'ING-MTG01', 'Mentega Anchor Unsalted', 2, 1, NULL, 20.0000, 95000.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 'ING-GLA01', 'Gula Pasir Kristal Putih', 1, 1, NULL, 30.0000, 17500.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(4, 1, 'ING-RGI01', 'Ragi Instan Saf-Instant', 1, 2, NULL, 500.0000, 120.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(5, 1, 'ING-TLR01', 'Telur Ayam Negeri Segar', 1, 7, NULL, 100.0000, 2200.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(6, 1, 'ING-SSU01', 'Susu Segar Pasteurisasi', 2, 4, NULL, 25.0000, 21000.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(7, 1, 'ING-CKL01', 'Cokelat Meises Ceres', 3, 1, NULL, 15.0000, 65000.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(8, 1, 'ING-KJU01', 'Keju Cheddar Olahan Prochiz', 3, 1, NULL, 15.0000, 85000.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(9, 1, 'ING-BOX01', 'Kotak Roti Box Exclusive', 4, 6, NULL, 200.0000, 2500.00, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_batches`
--

CREATE TABLE `ingredient_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `ingredient_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(64) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `cost_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `entry_number` varchar(64) NOT NULL,
  `entry_date` date NOT NULL,
  `source_type` varchar(64) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 1,
  `posted_at` timestamp NULL DEFAULT NULL,
  `posted_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `company_id`, `branch_id`, `entry_number`, `entry_date`, `source_type`, `source_id`, `description`, `is_posted`, `posted_at`, `posted_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'PUSAT-OB-20261010-0001', '2026-10-10', 'opening_balance', 1, 'Pencatatan Saldo Awal Kas, Bank, dan Nilai Persediaan Usaha', 1, '2026-10-09 21:44:57', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57');

-- --------------------------------------------------------

--
-- Table structure for table `journal_lines`
--

CREATE TABLE `journal_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_entry_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_lines`
--

INSERT INTO `journal_lines` (`id`, `journal_entry_id`, `account_id`, `debit`, `credit`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 34000000.00, 0.00, 'Saldo Kas Tunai Kasir & Toko', '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(2, 1, 2, 150000000.00, 0.00, 'Saldo Rekening Bank BCA', '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(3, 1, 5, 40440000.00, 0.00, 'Saldo Persediaan Bahan Baku Awal', '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(4, 1, 7, 2910000.00, 0.00, 'Saldo Persediaan Produk Jadi Awal', '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(5, 1, 11, 0.00, 227350000.00, 'Modal Disetor Pemilik Usaha', '2026-10-09 21:44:57', '2026-10-09 21:44:57');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_10_000001_create_companies_table', 1),
(5, '2026_10_10_000002_create_branches_and_sequences_table', 1),
(6, '2026_10_10_000003_create_roles_permissions_and_assignments_table', 1),
(7, '2026_10_10_000004_create_master_data_tables', 1),
(8, '2026_10_10_000005_create_accounting_and_finance_tables', 1),
(9, '2026_10_10_000006_create_inventory_and_transfer_tables', 1),
(10, '2026_10_10_000007_create_procurement_tables', 1),
(11, '2026_10_10_000008_create_recipe_and_production_tables', 1),
(12, '2026_10_10_000009_create_sales_and_pos_tables', 1),
(13, '2026_10_10_000010_create_audit_logs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `group`, `created_at`, `updated_at`) VALUES
(1, 'users.view', 'Lihat Pengguna', 'Pengguna', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(2, 'users.create', 'Tambah Pengguna', 'Pengguna', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(3, 'users.update', 'Ubah Pengguna', 'Pengguna', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(4, 'branches.view', 'Lihat Cabang', 'Cabang', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(5, 'branches.manage', 'Kelola Cabang', 'Cabang', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(6, 'ingredients.view', 'Lihat Bahan Baku', 'Master Data', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(7, 'ingredients.manage', 'Kelola Bahan Baku', 'Master Data', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(8, 'products.view', 'Lihat Produk Jadi', 'Master Data', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(9, 'products.manage', 'Kelola Produk Jadi', 'Master Data', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(10, 'recipes.view', 'Lihat Resep (BOM)', 'Master Data', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(11, 'recipes.manage', 'Kelola Resep (BOM)', 'Master Data', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(12, 'purchases.view', 'Lihat Pembelian', 'Pembelian', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(13, 'purchases.create', 'Buat Pembelian', 'Pembelian', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(14, 'purchases.approve', 'Setujui Pembelian', 'Pembelian', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(15, 'purchases.post', 'Posting Penerimaan Pembelian', 'Pembelian', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(16, 'production.view', 'Lihat Produksi', 'Produksi', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(17, 'production.create', 'Buat Order Produksi', 'Produksi', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(18, 'production.post', 'Posting Hasil Produksi', 'Produksi', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(19, 'inventory.view', 'Lihat Inventaris', 'Inventaris', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(20, 'inventory.adjust', 'Penyesuaian Stok (Opname)', 'Inventaris', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(21, 'transfers.create', 'Buat Transfer Antarcabang', 'Transfer Stok', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(22, 'transfers.approve', 'Setujui Transfer Antarcabang', 'Transfer Stok', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(23, 'transfers.ship', 'Kirim Transfer Stok', 'Transfer Stok', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(24, 'transfers.receive', 'Terima Transfer Stok', 'Transfer Stok', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(25, 'sales.view', 'Lihat Penjualan', 'Penjualan', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(26, 'sales.create', 'Buat Penjualan (POS)', 'Penjualan', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(27, 'sales.post', 'Posting Penjualan', 'Penjualan', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(28, 'sales.cancel', 'Batalkan Penjualan', 'Penjualan', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(29, 'payments.create', 'Terima Pembayaran', 'Penjualan', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(30, 'expenses.manage', 'Kelola Beban Pengeluaran', 'Keuangan', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(31, 'accounting.view', 'Lihat Jurnal & Buku Besar', 'Akuntansi', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(32, 'accounting.post', 'Posting Jurnal Manual', 'Akuntansi', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(33, 'reports.branch', 'Lihat Laporan Cabang', 'Laporan', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(34, 'reports.consolidated', 'Lihat Laporan Konsolidasi', 'Laporan', '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(35, 'settings.manage', 'Kelola Pengaturan Sistem', 'Pengaturan', '2026-10-09 21:44:54', '2026-10-09 21:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(2, 32),
(2, 33),
(2, 34),
(2, 35),
(3, 4),
(3, 6),
(3, 8),
(3, 10),
(3, 12),
(3, 13),
(3, 16),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 23),
(3, 24),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 33),
(4, 8),
(4, 25),
(4, 26),
(4, 27),
(4, 29),
(5, 6),
(5, 12),
(5, 15),
(5, 19),
(5, 20),
(5, 24),
(6, 6),
(6, 10),
(6, 16),
(6, 17),
(6, 18),
(6, 19);

-- --------------------------------------------------------

--
-- Table structure for table `production_costs`
--

CREATE TABLE `production_costs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `production_order_id` bigint(20) UNSIGNED NOT NULL,
  `cost_type` varchar(32) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_materials`
--

CREATE TABLE `production_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `production_order_id` bigint(20) UNSIGNED NOT NULL,
  `ingredient_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `planned_quantity` decimal(18,4) NOT NULL,
  `actual_quantity` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_orders`
--

CREATE TABLE `production_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(64) NOT NULL,
  `batch_number` varchar(64) NOT NULL,
  `recipe_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `target_quantity` decimal(18,4) NOT NULL,
  `actual_good_quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `actual_waste_quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `actual_sample_quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `planned_date` date NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `material_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `labor_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `overhead_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `pic_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_outputs`
--

CREATE TABLE `production_outputs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `production_order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `good_quantity` decimal(18,4) NOT NULL,
  `waste_quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `sample_quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `batch_number` varchar(64) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_wastes`
--

CREATE TABLE `production_wastes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `production_order_id` bigint(20) UNSIGNED NOT NULL,
  `item_type` varchar(32) NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `cost_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `barcode` varchar(64) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `sale_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `min_stock` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `company_id`, `code`, `barcode`, `name`, `category_id`, `unit_id`, `sale_price`, `min_stock`, `image_path`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'PRD-ROTI-SOBEK', '8991001001', 'Roti Sobek Cokelat Keju', 5, 6, 32000.00, 20.0000, NULL, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 'PRD-CROISSANT', '8991001002', 'Butter Croissant Klasik', 6, 6, 28000.00, 15.0000, NULL, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 'PRD-ROTI-ABON', '8991001003', 'Roti Manis Floss Abon Sapi', 5, 6, 18000.00, 25.0000, NULL, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(4, 1, 'PRD-RED-VELVET', '8991001004', 'Red Velvet Cake Slice', 7, 6, 45000.00, 10.0000, NULL, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `product_batches`
--

CREATE TABLE `product_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(64) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `cost_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_number` varchar(64) NOT NULL,
  `supplier_invoice_no` varchar(64) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `additional_fee` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `payment_status` varchar(32) NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(32) NOT NULL DEFAULT 'tempo',
  `notes` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `ingredient_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `discount_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(18,2) NOT NULL,
  `received_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_payments`
--

CREATE TABLE `purchase_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `payment_number` varchar(64) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_method` varchar(32) NOT NULL,
  `reference_number` varchar(64) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_receipts`
--

CREATE TABLE `purchase_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `receipt_number` varchar(64) NOT NULL,
  `receipt_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `received_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_receipt_items`
--

CREATE TABLE `purchase_receipt_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_receipt_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_item_id` bigint(20) UNSIGNED NOT NULL,
  `ingredient_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_received` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL,
  `batch_number` varchar(64) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `return_number` varchar(64) NOT NULL,
  `return_date` date NOT NULL,
  `total_amount` decimal(18,2) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'posted',
  `reason` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_return_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `version` varchar(16) NOT NULL DEFAULT '1.0',
  `yield_quantity` decimal(18,4) NOT NULL DEFAULT 1.0000,
  `yield_unit_id` bigint(20) UNSIGNED NOT NULL,
  `standard_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `labor_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `overhead_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `instructions` text DEFAULT NULL,
  `prep_time_minutes` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `company_id`, `product_id`, `name`, `version`, `yield_quantity`, `yield_unit_id`, `standard_cost`, `labor_cost`, `overhead_cost`, `instructions`, `prep_time_minutes`, `is_active`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Resep Roti Sobek Cokelat Keju V1', '1.0', 10.0000, 6, 125000.00, 15000.00, 10000.00, '1. Aduk tepung, ragi, gula, susu hingga kalis.\\n2. Masukkan mentega dan garam, uleni elastis.\\n3. Fermentasi 45 menit.\\n4. Timbang @50g, isi cokelat dan keju, tata di loyang.\\n5. Panggang 180C selama 25 menit.', 120, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 2, 'Resep Butter Croissant French Style V1', '1.0', 12.0000, 6, 95000.00, 20000.00, 15000.00, '1. Campur tepung, gula, ragi, susu dingin.\\n2. Istirahatkan adonan di chiller.\\n3. Lakukan laminasi mentega tawar 3 kali single fold.\\n4. Potong segitiga dan gulung.\\n5. Proofing 2 jam pada 26C, panggang 200C selama 18 menit.', 240, 1, NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_items`
--

CREATE TABLE `recipe_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recipe_id` bigint(20) UNSIGNED NOT NULL,
  `ingredient_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `standard_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipe_items`
--

INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `unit_id`, `quantity`, `standard_cost`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1.0000, 14000.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 2, 1, 0.2000, 19000.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 3, 1, 0.1500, 2625.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(4, 1, 4, 2, 15.0000, 1800.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(5, 1, 5, 7, 2.0000, 4400.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(6, 1, 6, 4, 0.3000, 6300.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(7, 1, 7, 1, 0.2000, 13000.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(8, 1, 8, 1, 0.2000, 17000.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(9, 1, 9, 6, 10.0000, 25000.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(10, 2, 1, 1, 0.8000, 11200.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(11, 2, 2, 1, 0.4500, 42750.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(12, 2, 3, 1, 0.1000, 1750.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(13, 2, 4, 2, 10.0000, 1200.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(14, 2, 6, 4, 0.2500, 5250.00, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'owner', 'Pemilik Usaha (Owner)', 'Akses penuh ke seluruh cabang dan laporan konsolidasi.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(2, 'admin_pusat', 'Administrator Pusat', 'Akses administratif operasional kantor pusat.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(3, 'branch_manager', 'Manajer Cabang', 'Mengawasi operasional dan keuangan cabang tertentu.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(4, 'cashier', 'Kasir POS', 'Melayani transaksi penjualan toko dan cetak nota kasir.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(5, 'warehouse_staff', 'Staf Gudang', 'Penerimaan barang dan stok opname.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(6, 'production_staff', 'Staf Produksi Bakery', 'Menjalankan batch produksi dan resep.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(7, 'procurement_staff', 'Staf Pembelian', 'Mengelola pesanan bahan baku ke supplier.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(8, 'accounting_staff', 'Staf Keuangan & Akunting', 'Menangani kas, hutang, piutang, dan jurnal.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(9, 'auditor', 'Auditor (Read-Only)', 'Hak akses pantau laporan dan log audit.', 0, '2026-10-09 21:44:54', '2026-10-09 21:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 6),
(6, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(64) NOT NULL,
  `sale_date` date NOT NULL,
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `change_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_cogs` decimal(18,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(32) NOT NULL DEFAULT 'paid',
  `status` varchar(32) NOT NULL DEFAULT 'completed',
  `payment_method` varchar(32) NOT NULL DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `cashier_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `discount_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(18,2) NOT NULL,
  `unit_cogs` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_cogs` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_payments`
--

CREATE TABLE `sale_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `payment_number` varchar(64) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_method` varchar(32) NOT NULL,
  `reference_number` varchar(64) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `cashier_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_returns`
--

CREATE TABLE `sale_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `return_number` varchar(64) NOT NULL,
  `return_date` date NOT NULL,
  `total_refund_amount` decimal(18,2) NOT NULL,
  `refund_method` varchar(32) NOT NULL DEFAULT 'cash',
  `is_restocked` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_items`
--

CREATE TABLE `sale_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_return_id` bigint(20) UNSIGNED NOT NULL,
  `sale_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `refund_subtotal` decimal(18,2) NOT NULL,
  `is_good_condition` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('6StFo1q7bIByUChNVCHoS7DcnyerWhkR8Z6CwQ1x', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.28000.2952', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYjhadkxaaGVneHJjNFBCeHk5Umxnd045SlJSdnVWWHRjbXR0TjU3SSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791607530),
('kk1mJJaDWlpIvRW80ULoACciTVemaksDZgwAcalG', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiRmRSV3ZPenhsaTVQNkc1aHhLazl1SzdYeXNRUnZNeVRLTkR6TU9YTSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY5OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvaW52ZW50b3J5L2xlZGdlcj9pdGVtX2lkPTImaXRlbV90eXBlPWluZ3JlZGllbnQiO3M6NToicm91dGUiO3M6MTY6ImludmVudG9yeS5sZWRnZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJjMWY1ODFkMzc0YTdiNjMyZjY1MWNiN2RiMDE4YzgxZGMxZWI1MTc2MDJlOTg3M2I1NjIxYjY0MDYwNTMwMGM4IjtzOjE2OiJhY3RpdmVfYnJhbmNoX2lkIjtpOjE7fQ==', 1791609080);

-- --------------------------------------------------------

--
-- Table structure for table `stock_balances`
--

CREATE TABLE `stock_balances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_type` varchar(32) NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `avg_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_balances`
--

INSERT INTO `stock_balances` (`id`, `company_id`, `branch_id`, `warehouse_id`, `item_type`, `item_id`, `quantity`, `avg_cost`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'ingredient', 1, 500.0000, 14000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(2, 1, 1, NULL, 'ingredient', 2, 100.0000, 95000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(3, 1, 1, NULL, 'ingredient', 3, 200.0000, 17500.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(4, 1, 1, NULL, 'ingredient', 4, 5000.0000, 120.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(5, 1, 1, NULL, 'ingredient', 5, 1200.0000, 2200.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(6, 1, 1, NULL, 'ingredient', 6, 150.0000, 21000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(7, 1, 1, NULL, 'ingredient', 7, 80.0000, 65000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(8, 1, 1, NULL, 'ingredient', 8, 60.0000, 85000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(9, 1, 1, NULL, 'ingredient', 9, 1500.0000, 2500.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(10, 1, 1, NULL, 'product', 1, 30.0000, 15000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(11, 1, 2, NULL, 'product', 1, 25.0000, 15000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(12, 1, 1, NULL, 'product', 2, 40.0000, 11000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(13, 1, 2, NULL, 'product', 2, 30.0000, 11000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(14, 1, 1, NULL, 'product', 3, 50.0000, 8500.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(15, 1, 2, NULL, 'product', 3, 40.0000, 8500.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(16, 1, 1, NULL, 'product', 4, 15.0000, 22000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(17, 1, 2, NULL, 'product', 4, 10.0000, 22000.00, '2026-10-09 21:44:57', '2026-10-09 21:44:57');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_type` varchar(32) NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(64) DEFAULT NULL,
  `movement_type` varchar(32) NOT NULL,
  `reference_type` varchar(64) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity_in` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `quantity_out` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance_after` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `notes` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `company_id`, `branch_id`, `warehouse_id`, `item_type`, `item_id`, `batch_number`, `movement_type`, `reference_type`, `reference_id`, `quantity_in`, `quantity_out`, `unit_cost`, `total_cost`, `balance_after`, `notes`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'ingredient', 1, NULL, 'opening_balance', NULL, NULL, 500.0000, 0.0000, 14000.00, 7000000.00, 500.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(2, 1, 1, NULL, 'ingredient', 2, NULL, 'opening_balance', NULL, NULL, 100.0000, 0.0000, 95000.00, 9500000.00, 100.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(3, 1, 1, NULL, 'ingredient', 3, NULL, 'opening_balance', NULL, NULL, 200.0000, 0.0000, 17500.00, 3500000.00, 200.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(4, 1, 1, NULL, 'ingredient', 4, NULL, 'opening_balance', NULL, NULL, 5000.0000, 0.0000, 120.00, 600000.00, 5000.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(5, 1, 1, NULL, 'ingredient', 5, NULL, 'opening_balance', NULL, NULL, 1200.0000, 0.0000, 2200.00, 2640000.00, 1200.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(6, 1, 1, NULL, 'ingredient', 6, NULL, 'opening_balance', NULL, NULL, 150.0000, 0.0000, 21000.00, 3150000.00, 150.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(7, 1, 1, NULL, 'ingredient', 7, NULL, 'opening_balance', NULL, NULL, 80.0000, 0.0000, 65000.00, 5200000.00, 80.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(8, 1, 1, NULL, 'ingredient', 8, NULL, 'opening_balance', NULL, NULL, 60.0000, 0.0000, 85000.00, 5100000.00, 60.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(9, 1, 1, NULL, 'ingredient', 9, NULL, 'opening_balance', NULL, NULL, 1500.0000, 0.0000, 2500.00, 3750000.00, 1500.0000, 'Saldo Awal Gudang Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(10, 1, 1, NULL, 'product', 1, NULL, 'opening_balance', NULL, NULL, 30.0000, 0.0000, 15000.00, 450000.00, 30.0000, 'Saldo Awal Produk Jadi Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(11, 1, 2, NULL, 'product', 1, NULL, 'opening_balance', NULL, NULL, 25.0000, 0.0000, 15000.00, 375000.00, 25.0000, 'Saldo Awal Display Toko Menteng', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(12, 1, 1, NULL, 'product', 2, NULL, 'opening_balance', NULL, NULL, 40.0000, 0.0000, 11000.00, 440000.00, 40.0000, 'Saldo Awal Produk Jadi Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(13, 1, 2, NULL, 'product', 2, NULL, 'opening_balance', NULL, NULL, 30.0000, 0.0000, 11000.00, 330000.00, 30.0000, 'Saldo Awal Display Toko Menteng', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(14, 1, 1, NULL, 'product', 3, NULL, 'opening_balance', NULL, NULL, 50.0000, 0.0000, 8500.00, 425000.00, 50.0000, 'Saldo Awal Produk Jadi Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(15, 1, 2, NULL, 'product', 3, NULL, 'opening_balance', NULL, NULL, 40.0000, 0.0000, 8500.00, 340000.00, 40.0000, 'Saldo Awal Display Toko Menteng', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(16, 1, 1, NULL, 'product', 4, NULL, 'opening_balance', NULL, NULL, 15.0000, 0.0000, 22000.00, 330000.00, 15.0000, 'Saldo Awal Produk Jadi Pusat', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57'),
(17, 1, 2, NULL, 'product', 4, NULL, 'opening_balance', NULL, NULL, 10.0000, 0.0000, 22000.00, 220000.00, 10.0000, 'Saldo Awal Display Toko Menteng', 1, '2026-10-09 21:44:57', '2026-10-09 21:44:57');

-- --------------------------------------------------------

--
-- Table structure for table `stock_opnames`
--

CREATE TABLE `stock_opnames` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `opname_number` varchar(64) NOT NULL,
  `opname_date` date NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_opname_items`
--

CREATE TABLE `stock_opname_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_opname_id` bigint(20) UNSIGNED NOT NULL,
  `item_type` varchar(32) NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `system_qty` decimal(18,4) NOT NULL,
  `physical_qty` decimal(18,4) NOT NULL,
  `difference_qty` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `adjustment_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `from_branch_id` bigint(20) UNSIGNED NOT NULL,
  `to_branch_id` bigint(20) UNSIGNED NOT NULL,
  `transfer_number` varchar(64) NOT NULL,
  `transfer_date` date NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `shipped_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `total_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `requested_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shipped_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `received_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfer_items`
--

CREATE TABLE `stock_transfer_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_transfer_id` bigint(20) UNSIGNED NOT NULL,
  `item_type` varchar(32) NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_shipped` decimal(18,4) NOT NULL,
  `quantity_received` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_id`, `code`, `name`, `phone`, `email`, `address`, `tax_id`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'SUP001', 'PT Bogasari Flour Mills', '021-4390001', NULL, 'Tanjung Priok, Jakarta', NULL, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 'SUP002', 'PT Megah Aneka Mentega', '021-7788991', NULL, 'Kawasan Industri Pulogadung', NULL, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 'SUP003', 'CV Sumber Telur Segar', '0251-832100', NULL, 'Ciawi, Bogor', NULL, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(4, 1, 'SUP004', 'PT Packaging Box Unggul', '021-5901122', NULL, 'Cikupa, Tangerang', NULL, NULL, 1, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `symbol` varchar(16) NOT NULL,
  `type` varchar(32) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `company_id`, `name`, `symbol`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kilogram', 'kg', 'mass', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 'Gram', 'g', 'mass', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 'Miligram', 'mg', 'mass', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(4, 1, 'Liter', 'l', 'volume', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(5, 1, 'Mililiter', 'ml', 'volume', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(6, 1, 'Pieces', 'pcs', 'count', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(7, 1, 'Butir', 'butir', 'count', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(8, 1, 'Box Kotak', 'box', 'count', '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(9, 1, 'Loyang', 'loyang', 'count', '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `unit_conversions`
--

CREATE TABLE `unit_conversions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `from_unit_id` bigint(20) UNSIGNED NOT NULL,
  `to_unit_id` bigint(20) UNSIGNED NOT NULL,
  `multiplier` decimal(18,6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unit_conversions`
--

INSERT INTO `unit_conversions` (`id`, `company_id`, `from_unit_id`, `to_unit_id`, `multiplier`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 1000.000000, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(2, 1, 2, 3, 1000.000000, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(3, 1, 4, 5, 1000.000000, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `phone`, `is_active`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bapak Hartono (Owner)', 'owner@bakeryerp.local', '081122334455', 1, NULL, '$2y$12$.3pL81sQHQQmLP5dQCPJoeLULXhCw83/dS5kCfPJk.AN4NOZJpLwG', NULL, '2026-10-09 21:44:55', '2026-10-09 21:44:55'),
(2, 1, 'Siti Rahma (Admin Pusat)', 'admin.pusat@bakeryerp.local', '081233445566', 1, NULL, '$2y$12$55HLHNSaYSF2ZF/jyDivqeB1WN4QNYaq4BqeDerwkrTCSTjyW7//W', NULL, '2026-10-09 21:44:55', '2026-10-09 21:44:55'),
(3, 1, 'Budi Santoso (Manajer Menteng)', 'manager.cbg1@bakeryerp.local', '081344556677', 1, NULL, '$2y$12$QMZzlmfGhVWhyTUIj./m0e4aHFFljnTCfOvIw2m9bwsPGuC9.K6oC', NULL, '2026-10-09 21:44:55', '2026-10-09 21:44:55'),
(4, 1, 'Dewi Anggraini (Kasir Menteng)', 'kasir.cbg1@bakeryerp.local', '081455667788', 1, NULL, '$2y$12$ZGe48cp3vp.xL5THoP8KqulYea3Y.J5umBwancW04uKOFTt1Kc3ki', NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(5, 1, 'Agus Baker (Kepala Produksi)', 'produksi.pusat@bakeryerp.local', '081566778899', 1, NULL, '$2y$12$weQ25wifvDDyEE3ldVarFOiCaGW4bjQGl/2aUux3.4b.0SloxAzwS', NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56'),
(6, 1, 'Eko Prasetyo (Gudang Pusat)', 'gudang.pusat@bakeryerp.local', '081677889900', 1, NULL, '$2y$12$u896plSfoV4Cbo15pQeaTuSYHjd2JkV89bPsmR7AtuuU5x74BL2Za', NULL, '2026-10-09 21:44:56', '2026-10-09 21:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `company_id`, `branch_id`, `code`, `name`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'GD-PUSAT', 'Gudang Utama Kantor Pusat & Central Kitchen', 1, 1, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(2, 1, 2, 'GD-CBG001', 'Gudang Utama Cabang Menteng (Bakery & Cafe)', 1, 1, '2026-10-09 21:44:54', '2026-10-09 21:44:54'),
(3, 1, 3, 'GD-CBG002', 'Gudang Utama Cabang Senopati (Pastry & Coffee)', 1, 1, '2026-10-09 21:44:54', '2026-10-09 21:44:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounts_company_id_code_unique` (`company_id`,`code`),
  ADD KEY `accounts_branch_id_foreign` (`branch_id`),
  ADD KEY `accounts_chart_of_account_id_foreign` (`chart_of_account_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_company_id_foreign` (`company_id`),
  ADD KEY `audit_logs_branch_id_foreign` (`branch_id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  ADD KEY `audit_logs_action_index` (`action`),
  ADD KEY `audit_logs_created_at_index` (`created_at`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_company_id_code_unique` (`company_id`,`code`),
  ADD KEY `branches_manager_user_id_foreign` (`manager_user_id`);

--
-- Indexes for table `branch_sequences`
--
ALTER TABLE `branch_sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_sequences_branch_id_type_period_unique` (`branch_id`,`type`,`period`);

--
-- Indexes for table `branch_settings`
--
ALTER TABLE `branch_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_settings_branch_id_key_unique` (`branch_id`,`key`);

--
-- Indexes for table `branch_users`
--
ALTER TABLE `branch_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_users_branch_id_user_id_unique` (`branch_id`,`user_id`),
  ADD KEY `branch_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cash_transactions_branch_id_transaction_number_unique` (`branch_id`,`transaction_number`),
  ADD KEY `cash_transactions_company_id_foreign` (`company_id`),
  ADD KEY `cash_transactions_account_id_foreign` (`account_id`),
  ADD KEY `cash_transactions_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `cash_transfers`
--
ALTER TABLE `cash_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cash_transfers_transfer_number_unique` (`transfer_number`),
  ADD KEY `cash_transfers_company_id_foreign` (`company_id`),
  ADD KEY `cash_transfers_from_account_id_foreign` (`from_account_id`),
  ADD KEY `cash_transfers_to_account_id_foreign` (`to_account_id`),
  ADD KEY `cash_transfers_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_company_id_type_index` (`company_id`,`type`);

--
-- Indexes for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chart_of_accounts_company_id_code_unique` (`company_id`,`code`),
  ADD KEY `chart_of_accounts_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_company_id_code_unique` (`company_id`,`code`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expenses_branch_id_expense_number_unique` (`branch_id`,`expense_number`),
  ADD KEY `expenses_company_id_foreign` (`company_id`),
  ADD KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  ADD KEY `expenses_account_id_foreign` (`account_id`),
  ADD KEY `expenses_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_categories_company_id_foreign` (`company_id`),
  ADD KEY `expense_categories_chart_of_account_id_foreign` (`chart_of_account_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ingredients_company_id_code_unique` (`company_id`,`code`),
  ADD KEY `ingredients_category_id_foreign` (`category_id`),
  ADD KEY `ingredients_unit_id_foreign` (`unit_id`),
  ADD KEY `ingredients_purchase_unit_id_foreign` (`purchase_unit_id`);

--
-- Indexes for table `ingredient_batches`
--
ALTER TABLE `ingredient_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ing_batch_unique` (`branch_id`,`ingredient_id`,`batch_number`),
  ADD KEY `ingredient_batches_company_id_foreign` (`company_id`),
  ADD KEY `ingredient_batches_ingredient_id_foreign` (`ingredient_id`),
  ADD KEY `ingredient_batches_expiry_date_index` (`expiry_date`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entries_company_id_foreign` (`company_id`),
  ADD KEY `journal_entries_branch_id_foreign` (`branch_id`),
  ADD KEY `journal_entries_posted_by_user_id_foreign` (`posted_by_user_id`),
  ADD KEY `journal_entries_source_type_source_id_index` (`source_type`,`source_id`),
  ADD KEY `journal_entries_entry_number_index` (`entry_number`),
  ADD KEY `journal_entries_entry_date_index` (`entry_date`);

--
-- Indexes for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_lines_journal_entry_id_foreign` (`journal_entry_id`),
  ADD KEY `journal_lines_account_id_foreign` (`account_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_role_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `production_costs`
--
ALTER TABLE `production_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_costs_production_order_id_foreign` (`production_order_id`);

--
-- Indexes for table `production_materials`
--
ALTER TABLE `production_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_materials_production_order_id_foreign` (`production_order_id`),
  ADD KEY `production_materials_ingredient_id_foreign` (`ingredient_id`),
  ADD KEY `production_materials_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `production_orders`
--
ALTER TABLE `production_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `production_orders_branch_id_order_number_unique` (`branch_id`,`order_number`),
  ADD KEY `production_orders_company_id_foreign` (`company_id`),
  ADD KEY `production_orders_recipe_id_foreign` (`recipe_id`),
  ADD KEY `production_orders_product_id_foreign` (`product_id`),
  ADD KEY `production_orders_pic_user_id_foreign` (`pic_user_id`),
  ADD KEY `production_orders_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `production_outputs`
--
ALTER TABLE `production_outputs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_outputs_production_order_id_foreign` (`production_order_id`),
  ADD KEY `production_outputs_product_id_foreign` (`product_id`);

--
-- Indexes for table `production_wastes`
--
ALTER TABLE `production_wastes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_wastes_production_order_id_foreign` (`production_order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_company_id_code_unique` (`company_id`,`code`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_unit_id_foreign` (`unit_id`),
  ADD KEY `products_barcode_index` (`barcode`);

--
-- Indexes for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prod_batch_unique` (`branch_id`,`product_id`,`batch_number`),
  ADD KEY `product_batches_company_id_foreign` (`company_id`),
  ADD KEY `product_batches_product_id_foreign` (`product_id`),
  ADD KEY `product_batches_expiry_date_index` (`expiry_date`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchases_branch_id_purchase_number_unique` (`branch_id`,`purchase_number`),
  ADD KEY `purchases_company_id_foreign` (`company_id`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`),
  ADD KEY `purchases_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_items_ingredient_id_foreign` (`ingredient_id`),
  ADD KEY `purchase_items_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_payments_branch_id_payment_number_unique` (`branch_id`,`payment_number`),
  ADD KEY `purchase_payments_company_id_foreign` (`company_id`),
  ADD KEY `purchase_payments_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_payments_account_id_foreign` (`account_id`),
  ADD KEY `purchase_payments_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `purchase_receipts`
--
ALTER TABLE `purchase_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_receipts_branch_id_receipt_number_unique` (`branch_id`,`receipt_number`),
  ADD KEY `purchase_receipts_company_id_foreign` (`company_id`),
  ADD KEY `purchase_receipts_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_receipts_received_by_user_id_foreign` (`received_by_user_id`);

--
-- Indexes for table `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_receipt_items_purchase_receipt_id_foreign` (`purchase_receipt_id`),
  ADD KEY `purchase_receipt_items_purchase_item_id_foreign` (`purchase_item_id`),
  ADD KEY `purchase_receipt_items_ingredient_id_foreign` (`ingredient_id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_returns_branch_id_return_number_unique` (`branch_id`,`return_number`),
  ADD KEY `purchase_returns_company_id_foreign` (`company_id`),
  ADD KEY `purchase_returns_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_returns_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  ADD KEY `purchase_return_items_purchase_item_id_foreign` (`purchase_item_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recipes_product_id_version_unique` (`product_id`,`version`),
  ADD KEY `recipes_company_id_foreign` (`company_id`),
  ADD KEY `recipes_yield_unit_id_foreign` (`yield_unit_id`);

--
-- Indexes for table `recipe_items`
--
ALTER TABLE `recipe_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_items_recipe_id_foreign` (`recipe_id`),
  ADD KEY `recipe_items_ingredient_id_foreign` (`ingredient_id`),
  ADD KEY `recipe_items_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_branch_id_invoice_number_unique` (`branch_id`,`invoice_number`),
  ADD KEY `sales_company_id_foreign` (`company_id`),
  ADD KEY `sales_customer_id_foreign` (`customer_id`),
  ADD KEY `sales_cashier_user_id_foreign` (`cashier_user_id`),
  ADD KEY `sales_sale_date_branch_id_index` (`sale_date`,`branch_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_items_product_id_foreign` (`product_id`),
  ADD KEY `sale_items_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_payments_branch_id_payment_number_unique` (`branch_id`,`payment_number`),
  ADD KEY `sale_payments_company_id_foreign` (`company_id`),
  ADD KEY `sale_payments_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_payments_account_id_foreign` (`account_id`),
  ADD KEY `sale_payments_cashier_user_id_foreign` (`cashier_user_id`);

--
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_returns_branch_id_return_number_unique` (`branch_id`,`return_number`),
  ADD KEY `sale_returns_company_id_foreign` (`company_id`),
  ADD KEY `sale_returns_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_returns_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_items_sale_return_id_foreign` (`sale_return_id`),
  ADD KEY `sale_return_items_sale_item_id_foreign` (`sale_item_id`),
  ADD KEY `sale_return_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stock_balances`
--
ALTER TABLE `stock_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_balances_branch_id_item_type_item_id_unique` (`branch_id`,`item_type`,`item_id`),
  ADD KEY `stock_balances_company_id_foreign` (`company_id`),
  ADD KEY `stock_balances_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_company_id_foreign` (`company_id`),
  ADD KEY `stock_movements_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_movements_created_by_user_id_foreign` (`created_by_user_id`),
  ADD KEY `stock_movements_branch_id_item_type_item_id_index` (`branch_id`,`item_type`,`item_id`),
  ADD KEY `stock_movements_movement_type_created_at_index` (`movement_type`,`created_at`);

--
-- Indexes for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_opnames_branch_id_opname_number_unique` (`branch_id`,`opname_number`),
  ADD KEY `stock_opnames_company_id_foreign` (`company_id`),
  ADD KEY `stock_opnames_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_opname_items_stock_opname_id_foreign` (`stock_opname_id`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_transfers_transfer_number_unique` (`transfer_number`),
  ADD KEY `stock_transfers_company_id_foreign` (`company_id`),
  ADD KEY `stock_transfers_from_branch_id_foreign` (`from_branch_id`),
  ADD KEY `stock_transfers_to_branch_id_foreign` (`to_branch_id`),
  ADD KEY `stock_transfers_requested_by_user_id_foreign` (`requested_by_user_id`),
  ADD KEY `stock_transfers_approved_by_user_id_foreign` (`approved_by_user_id`),
  ADD KEY `stock_transfers_shipped_by_user_id_foreign` (`shipped_by_user_id`),
  ADD KEY `stock_transfers_received_by_user_id_foreign` (`received_by_user_id`);

--
-- Indexes for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transfer_items_stock_transfer_id_foreign` (`stock_transfer_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_company_id_code_unique` (`company_id`,`code`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_company_id_symbol_unique` (`company_id`,`symbol`);

--
-- Indexes for table `unit_conversions`
--
ALTER TABLE `unit_conversions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_conversions_company_id_from_unit_id_to_unit_id_unique` (`company_id`,`from_unit_id`,`to_unit_id`),
  ADD KEY `unit_conversions_from_unit_id_foreign` (`from_unit_id`),
  ADD KEY `unit_conversions_to_unit_id_foreign` (`to_unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_company_id_index` (`company_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouses_branch_id_code_unique` (`branch_id`,`code`),
  ADD KEY `warehouses_company_id_foreign` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `branch_sequences`
--
ALTER TABLE `branch_sequences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_settings`
--
ALTER TABLE `branch_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_users`
--
ALTER TABLE `branch_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_transfers`
--
ALTER TABLE `cash_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ingredient_batches`
--
ALTER TABLE `ingredient_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `journal_lines`
--
ALTER TABLE `journal_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `production_costs`
--
ALTER TABLE `production_costs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_materials`
--
ALTER TABLE `production_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_orders`
--
ALTER TABLE `production_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_outputs`
--
ALTER TABLE `production_outputs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_wastes`
--
ALTER TABLE `production_wastes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_batches`
--
ALTER TABLE `product_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_receipts`
--
ALTER TABLE `purchase_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recipe_items`
--
ALTER TABLE `recipe_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_payments`
--
ALTER TABLE `sale_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_balances`
--
ALTER TABLE `stock_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `unit_conversions`
--
ALTER TABLE `unit_conversions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accounts_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accounts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `audit_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branches_manager_user_id_foreign` FOREIGN KEY (`manager_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branch_sequences`
--
ALTER TABLE `branch_sequences`
  ADD CONSTRAINT `branch_sequences_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branch_settings`
--
ALTER TABLE `branch_settings`
  ADD CONSTRAINT `branch_settings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branch_users`
--
ALTER TABLE `branch_users`
  ADD CONSTRAINT `branch_users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branch_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  ADD CONSTRAINT `cash_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `cash_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cash_transactions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cash_transactions_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cash_transfers`
--
ALTER TABLE `cash_transfers`
  ADD CONSTRAINT `cash_transfers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cash_transfers_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cash_transfers_from_account_id_foreign` FOREIGN KEY (`from_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `cash_transfers_to_account_id_foreign` FOREIGN KEY (`to_account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD CONSTRAINT `chart_of_accounts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chart_of_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`);

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `expense_categories_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expense_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ingredients_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ingredients_purchase_unit_id_foreign` FOREIGN KEY (`purchase_unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ingredients_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `ingredient_batches`
--
ALTER TABLE `ingredient_batches`
  ADD CONSTRAINT `ingredient_batches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ingredient_batches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ingredient_batches_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_entries_posted_by_user_id_foreign` FOREIGN KEY (`posted_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD CONSTRAINT `journal_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`),
  ADD CONSTRAINT `journal_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `production_costs`
--
ALTER TABLE `production_costs`
  ADD CONSTRAINT `production_costs_production_order_id_foreign` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `production_materials`
--
ALTER TABLE `production_materials`
  ADD CONSTRAINT `production_materials_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  ADD CONSTRAINT `production_materials_production_order_id_foreign` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_materials_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `production_orders`
--
ALTER TABLE `production_orders`
  ADD CONSTRAINT `production_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_orders_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_orders_pic_user_id_foreign` FOREIGN KEY (`pic_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_orders_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `production_orders_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`);

--
-- Constraints for table `production_outputs`
--
ALTER TABLE `production_outputs`
  ADD CONSTRAINT `production_outputs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `production_outputs_production_order_id_foreign` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `production_wastes`
--
ALTER TABLE `production_wastes`
  ADD CONSTRAINT `production_wastes_production_order_id_foreign` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD CONSTRAINT `product_batches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_batches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  ADD CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD CONSTRAINT `purchase_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `purchase_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_payments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_payments_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_receipts`
--
ALTER TABLE `purchase_receipts`
  ADD CONSTRAINT `purchase_receipts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_receipts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_receipts_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_receipts_received_by_user_id_foreign` FOREIGN KEY (`received_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  ADD CONSTRAINT `purchase_receipt_items_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  ADD CONSTRAINT `purchase_receipt_items_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchase_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_receipt_items_purchase_receipt_id_foreign` FOREIGN KEY (`purchase_receipt_id`) REFERENCES `purchase_receipts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_returns_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_returns_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_returns_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `purchase_return_items_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchase_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_yield_unit_id_foreign` FOREIGN KEY (`yield_unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `recipe_items`
--
ALTER TABLE `recipe_items`
  ADD CONSTRAINT `recipe_items_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  ADD CONSTRAINT `recipe_items_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_cashier_user_id_foreign` FOREIGN KEY (`cashier_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD CONSTRAINT `sale_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `sale_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_payments_cashier_user_id_foreign` FOREIGN KEY (`cashier_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_payments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD CONSTRAINT `sale_returns_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_returns_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_returns_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_returns_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD CONSTRAINT `sale_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_return_items_sale_item_id_foreign` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_return_items_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_balances`
--
ALTER TABLE `stock_balances`
  ADD CONSTRAINT `stock_balances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_balances_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_balances_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD CONSTRAINT `stock_opnames_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_opnames_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_opnames_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  ADD CONSTRAINT `stock_opname_items_stock_opname_id_foreign` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `stock_transfers_approved_by_user_id_foreign` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transfers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_from_branch_id_foreign` FOREIGN KEY (`from_branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `stock_transfers_received_by_user_id_foreign` FOREIGN KEY (`received_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transfers_requested_by_user_id_foreign` FOREIGN KEY (`requested_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transfers_shipped_by_user_id_foreign` FOREIGN KEY (`shipped_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transfers_to_branch_id_foreign` FOREIGN KEY (`to_branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  ADD CONSTRAINT `stock_transfer_items_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `unit_conversions`
--
ALTER TABLE `unit_conversions`
  ADD CONSTRAINT `unit_conversions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `unit_conversions_from_unit_id_foreign` FOREIGN KEY (`from_unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `unit_conversions_to_unit_id_foreign` FOREIGN KEY (`to_unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `warehouses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
