-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 11, 2019 at 10:11 PM
-- Server version: 10.0.38-MariaDB
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sunny_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(10) NOT NULL,
  `account_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `account_details` text NOT NULL,
  `initial_balance` decimal(25,4) NOT NULL,
  `account_no` varchar(255) NOT NULL,
  `contact_person` varchar(255) CHARACTER SET utf8 NOT NULL,
  `phone_number` varchar(14) DEFAULT NULL,
  `opening_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `url` text CHARACTER SET utf8,
  `total_deposit` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `total_withdraw` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `total_transfer_from_other` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `total_transfer_to_other` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_account_to_store`
--

CREATE TABLE `bank_account_to_store` (
  `ba2s` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `deposit` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `withdraw` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `transfer_from_other` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `transfer_to_other` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_transaction_info`
--

CREATE TABLE `bank_transaction_info` (
  `info_id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `account_id` int(10) UNSIGNED NOT NULL,
  `ref_no` varchar(100) NOT NULL COMMENT 'e.g. Transaction ID, Check No.',
  `transaction_type` text NOT NULL,
  `title` varchar(250) NOT NULL,
  `details` text,
  `from_account_id` int(10) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `image` text,
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bank_transaction_price`
--

CREATE TABLE `bank_transaction_price` (
  `price_id` int(10) NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `ref_no` varchar(100) NOT NULL COMMENT 'e.g. Transaction ID, Check No.',
  `amount` decimal(25,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `boxes`
--

CREATE TABLE `boxes` (
  `box_id` int(10) UNSIGNED NOT NULL,
  `box_name` varchar(100) NOT NULL,
  `box_details` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `boxes`
--

INSERT INTO `boxes` (`box_id`, `box_name`, `box_details`) VALUES
(1, 'Common Box', 'Common Box details here...');

-- --------------------------------------------------------

--
-- Table structure for table `box_to_store`
--

CREATE TABLE `box_to_store` (
  `id` int(11) NOT NULL,
  `box_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `box_to_store`
--

INSERT INTO `box_to_store` (`id`, `box_id`, `store_id`, `status`, `sort_order`) VALUES
(1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `buying_info`
--

CREATE TABLE `buying_info` (
  `info_id` int(10) UNSIGNED NOT NULL,
  `invoice_id` varchar(100) CHARACTER SET latin1 NOT NULL,
  `inv_type` enum('buy','transfer','') NOT NULL DEFAULT 'buy',
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `total_item` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('stock','using','','') NOT NULL DEFAULT 'stock',
  `total_sell` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `buy_date` date NOT NULL,
  `buy_time` time DEFAULT NULL,
  `sup_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `invoice_note` text,
  `attachment` text,
  `is_visible` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `payment_status` varchar(20) NOT NULL DEFAULT 'due',
  `checkout_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `buying_item`
--

CREATE TABLE `buying_item` (
  `id` int(10) NOT NULL,
  `invoice_id` varchar(100) CHARACTER SET latin1 NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `item_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `category_id` int(10) NOT NULL DEFAULT '0',
  `item_name` varchar(100) NOT NULL,
  `item_buying_price` decimal(25,4) NOT NULL,
  `item_selling_price` decimal(25,4) NOT NULL,
  `item_quantity` int(10) UNSIGNED NOT NULL,
  `total_sell` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('stock','active','sold','') NOT NULL DEFAULT 'stock',
  `item_total` decimal(25,4) NOT NULL,
  `item_tax` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `tax_method` enum('exclusive','inclusive','','') DEFAULT 'exclusive',
  `tax` varchar(20) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `buying_payments`
--

CREATE TABLE `buying_payments` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'purchase',
  `store_id` int(10) UNSIGNED NOT NULL,
  `invoice_id` varchar(100) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `pmethod_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `amount` decimal(25,4) NOT NULL,
  `details` text,
  `attachment` text,
  `note` text,
  `total_paid` decimal(25,4) DEFAULT '0.0000',
  `balance` decimal(25,4) DEFAULT '0.0000',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `buying_price`
--

CREATE TABLE `buying_price` (
  `price_id` int(10) NOT NULL,
  `invoice_id` varchar(100) CHARACTER SET latin1 NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `order_tax` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `item_tax` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `payable_amount` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `paid_amount` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `due_paid` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `due` decimal(25,4) NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `buying_returns`
--

CREATE TABLE `buying_returns` (
  `id` int(11) NOT NULL,
  `store_id` int(10) NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `sup_id` int(10) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `total_item` int(10) DEFAULT NULL,
  `total_quantity` int(10) NOT NULL,
  `total_amount` decimal(25,4) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `buying_return_items`
--

CREATE TABLE `buying_return_items` (
  `id` int(11) NOT NULL,
  `store_id` int(10) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `amount` decimal(25,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `categorys`
--

CREATE TABLE `categorys` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(60) CHARACTER SET utf8 NOT NULL,
  `category_slug` varchar(60) CHARACTER SET utf8 NOT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `category_details` longtext CHARACTER SET utf8,
  `created_at` timestamp NULL DEFAULT NULL,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categorys`
--

INSERT INTO `categorys` (`category_id`, `category_name`, `category_slug`, `parent_id`, `category_details`, `created_at`, `update_at`) VALUES
(1, 'Default category', 'default_category', 0, '', '2018-08-17 05:28:16', '2019-02-11 11:00:20');

-- --------------------------------------------------------

--
-- Table structure for table `category_to_store`
--

CREATE TABLE `category_to_store` (
  `c2s_id` int(10) NOT NULL,
  `ccategory_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category_to_store`
--

INSERT INTO `category_to_store` (`c2s_id`, `ccategory_id`, `store_id`, `status`, `sort_order`) VALUES
(1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `currency_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `code` varchar(3) NOT NULL,
  `symbol_left` varchar(12) NOT NULL,
  `symbol_right` varchar(12) NOT NULL,
  `decimal_place` char(1) NOT NULL,
  `value` decimal(25,4) NOT NULL DEFAULT '1.0000',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`currency_id`, `title`, `code`, `symbol_left`, `symbol_right`, `decimal_place`, `value`, `created_at`) VALUES
(1, 'Pound Sterling', 'GBP', '£', '', '2', '0.6125', '2018-09-19 14:40:00'),
(2, 'United States Dollar', 'USD', '$', '', '2', '1.0000', '2018-09-19 14:40:00'),
(3, 'Euro', 'EUR', '', '€', '2', '0.7846', '2018-09-19 14:40:00'),
(4, 'Hong Kong Dollar', 'HKD', 'HK$', '', '2', '7.8222', '2018-09-19 12:00:00'),
(5, 'Indian Rupee', 'INR', '₹', '', '2', '64.4000', '2018-09-19 12:00:00'),
(6, 'Russian Ruble', 'RUB', '₽', '', '2', '56.4036', '2018-09-19 12:00:00'),
(7, 'Chinese Yuan Renminbi', 'CNY', '¥', '', '2', '6.3451', '2018-09-19 12:00:00'),
(8, 'Australian Dollar', 'AUD', '$', '', '2', '1.2654', '2018-09-19 12:00:00'),
(9, 'Bangladeshi Taka', 'BDT', '৳', '', '3', '0.0000', '2018-09-29 05:25:00'),
(10, 'Pakistani Rupee ', 'PKR', '₨', '', '2', '0.0000', '2018-09-29 05:31:49'),
(11, 'Croatian Kuna', 'HRK', 'kn', '', '2', '0.0000', '2018-09-29 05:33:22'),
(12, 'Malaysian Ringgit', 'MYR', 'RM', '', '2', '0.0000', '2018-09-29 05:35:15'),
(13, 'Saudi riyal', 'SAR', 'SR', '', '2', '0.0000', '2018-09-29 05:35:57'),
(14, 'Canadian Dollar', 'CAD', 'CAD $', '', '2', '0.0000', '2018-09-29 05:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `currency_to_store`
--

CREATE TABLE `currency_to_store` (
  `ca2s_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `currency_to_store`
--

INSERT INTO `currency_to_store` (`ca2s_id`, `currency_id`, `store_id`, `status`, `sort_order`) VALUES
(1, 1, 1, 1, 0),
(3, 3, 1, 1, 0),
(4, 4, 1, 1, 0),
(5, 5, 1, 1, 0),
(6, 6, 1, 1, 0),
(7, 7, 1, 1, 0),
(8, 8, 1, 1, 0),
(17, 9, 1, 1, 0),
(19, 2, 1, 1, 0),
(23, 10, 1, 1, 0),
(25, 11, 1, 1, 0),
(27, 12, 1, 1, 0),
(31, 13, 1, 1, 0),
(33, 14, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(10) UNSIGNED NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_mobile` varchar(14) DEFAULT NULL,
  `customer_address` text,
  `customer_sex` tinyint(1) NOT NULL DEFAULT '1',
  `customer_age` int(10) UNSIGNED DEFAULT NULL,
  `customer_city` varchar(100) DEFAULT NULL,
  `customer_state` varchar(100) DEFAULT NULL,
  `customer_country` varchar(100) DEFAULT NULL,
  `is_giftcard` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_mobile`, `customer_address`, `customer_sex`, `customer_age`, `customer_city`, `customer_state`, `customer_country`, `is_giftcard`, `created_at`) VALUES
(1, 'Walking Customer', 'walker@itsolution24.com', '0170000000000', 'Earth', 1, 89, '', 'AN', 'AL', 0, '2018-03-24 14:18:37');

-- --------------------------------------------------------

--
-- Table structure for table `customer_to_store`
--

CREATE TABLE `customer_to_store` (
  `c2s_id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `balance` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customer_to_store`
--

INSERT INTO `customer_to_store` (`c2s_id`, `customer_id`, `store_id`, `balance`, `status`, `sort_order`) VALUES
(1, 1, 1, '0.0000', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `customer_transactions`
--

CREATE TABLE `customer_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `reference_no` varchar(55) DEFAULT NULL,
  `ref_invoice_id` varchar(55) DEFAULT NULL,
  `type` varchar(55) NOT NULL,
  `pmethod_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `amount` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `store_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `amount` decimal(25,4) NOT NULL,
  `note` longtext CHARACTER SET utf8 NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categorys`
--

CREATE TABLE `expense_categorys` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(60) CHARACTER SET utf8 NOT NULL,
  `category_slug` varchar(60) CHARACTER SET utf8 NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `category_details` longtext CHARACTER SET utf8,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `expense_categorys`
--

INSERT INTO `expense_categorys` (`category_id`, `category_name`, `category_slug`, `parent_id`, `category_details`, `status`, `sort_order`, `created_at`, `update_at`) VALUES
(3, 'Expense Category', 'expense_category', 0, '', 1, 0, '2019-02-06 12:59:08', '2019-02-06 12:59:08');

-- --------------------------------------------------------

--
-- Table structure for table `gift_cards`
--

CREATE TABLE `gift_cards` (
  `id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `card_no` varchar(20) NOT NULL,
  `value` decimal(25,4) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `balance` decimal(25,4) NOT NULL,
  `expiry` date DEFAULT NULL,
  `created_by` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gift_card_topups`
--

CREATE TABLE `gift_card_topups` (
  `id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `card_id` varchar(20) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loan_id` int(10) UNSIGNED NOT NULL,
  `ref_no` varchar(50) NOT NULL,
  `loan_from` varchar(100) CHARACTER SET utf8 NOT NULL,
  `title` varchar(250) CHARACTER SET utf8 NOT NULL,
  `amount` decimal(25,4) UNSIGNED NOT NULL,
  `interest` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `payable` decimal(25,4) UNSIGNED NOT NULL,
  `paid` decimal(25,4) UNSIGNED NOT NULL DEFAULT '0.0000',
  `due` decimal(25,4) UNSIGNED NOT NULL DEFAULT '0.0000',
  `details` longtext CHARACTER SET utf8 NOT NULL,
  `attachment` text NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `ref_no`, `loan_from`, `title`, `amount`, `interest`, `payable`, `paid`, `due`, `details`, `attachment`, `created_by`, `created_at`, `updated_at`) VALUES
(11, '2222', 'bank', '43434', '3000.0000', '1.0000', '3030.0000', '0.0000', '3030.0000', '', '', 1, '2019-02-11 18:00:00', '2019-02-11 11:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `loan_payments`
--

CREATE TABLE `loan_payments` (
  `id` int(11) UNSIGNED NOT NULL,
  `lloan_id` int(11) UNSIGNED NOT NULL,
  `ref_no` varchar(50) DEFAULT NULL,
  `paid` decimal(25,4) NOT NULL,
  `note` text CHARACTER SET utf8,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `loan_payments`
--

INSERT INTO `loan_payments` (`id`, `lloan_id`, `ref_no`, `paid`, `note`, `created_by`, `created_at`, `updated_at`) VALUES
(10, 10, '232423', '20000.0000', '', 1, '2019-02-10 12:44:08', '2019-02-10 12:44:08');

-- --------------------------------------------------------

--
-- Table structure for table `loan_to_store`
--

CREATE TABLE `loan_to_store` (
  `id` int(11) UNSIGNED NOT NULL,
  `lloan_id` int(11) UNSIGNED NOT NULL,
  `store_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'sell',
  `store_id` int(10) UNSIGNED NOT NULL,
  `invoice_id` varchar(100) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `pmethod_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `amount` decimal(25,4) NOT NULL,
  `details` text,
  `attachment` text,
  `note` text,
  `total_paid` decimal(25,4) DEFAULT '0.0000',
  `pos_balance` decimal(25,4) DEFAULT '0.0000',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pmethods`
--

CREATE TABLE `pmethods` (
  `pmethod_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `details` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pmethods`
--

INSERT INTO `pmethods` (`pmethod_id`, `name`, `details`, `created_at`, `updated_at`) VALUES
(1, 'Cash on Delivery', 'Payment method details goes here...', '2018-03-23 18:00:00', '2019-01-29 12:39:21'),
(2, 'VISA Card', '', '2018-10-05 18:00:00', '2019-01-29 12:39:21'),
(4, 'Bkash', 'Bkash a Brack Bank Service', '2019-01-09 18:00:00', '2019-01-29 12:39:21'),
(7, 'Master Card', '', '2019-01-14 18:00:00', '2019-01-29 12:39:21'),
(11, 'Giftcard', 'Details of giftcard payment method', '2019-02-04 11:32:44', '2019-02-04 11:32:44');

-- --------------------------------------------------------

--
-- Table structure for table `pmethod_to_store`
--

CREATE TABLE `pmethod_to_store` (
  `p2s_id` int(11) NOT NULL,
  `ppmethod_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pmethod_to_store`
--

INSERT INTO `pmethod_to_store` (`p2s_id`, `ppmethod_id`, `store_id`, `status`, `sort_order`) VALUES
(3, 2, 1, 1, 0),
(9, 1, 1, 1, 0),
(17, 4, 1, 1, 0),
(21, 7, 1, 1, 0),
(25, 11, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pos_receipt_template`
--

CREATE TABLE `pos_receipt_template` (
  `id` int(10) NOT NULL,
  `title` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `content` longtext CHARACTER SET ucs2 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sort_order` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `store_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pos_receipt_template`
--

INSERT INTO `pos_receipt_template` (`id`, `title`, `content`, `created_at`, `updated_at`, `sort_order`, `status`, `created_by`, `is_active`, `store_id`) VALUES
(1, 'Default', '<div class=\"col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3\">\r\n  <table class=\"table\">\r\n    <tbody>\r\n      <tr class=\"invoice-header\">\r\n        <td class=\"text-center\" colspan=\"2\">\r\n          <div class=\"invoice-header-info\">\r\n            <div class=\"logo\">\r\n              {logo}\r\n            </div>\r\n            <h2 class=\"invoice-title\">{store_name}</h2>\r\n            <div>GST Reg: {gst_reg}</div>\r\n            <div>VAT Reg: {vat_reg}</div>\r\n            <h6 class=\"font-size18\">\r\n              Invoice ID: {invoice_id}                                                            \r\n            </h6>\r\n            <h5>\r\n              Date: {date_time}\r\n            </h5>\r\n            <h4><b>Tax Invoice</b></h4></div>\r\n        </td>\r\n      </tr>\r\n      <tr class=\"invoice-address\">\r\n        <td class=\"w-50 invoice-from\">\r\n          <div>\r\n            <div class=\"com-details\">\r\n              <div>\r\n                <strong>\r\n                  From                              \r\n                </strong>\r\n              </div>\r\n              <span class=\"invoice-address\">\r\n                {store_address}                           \r\n              </span>\r\n              <br>\r\n              <span>\r\n                Mobile: {store_contact}                          \r\n              </span>\r\n            </div>\r\n          </div>\r\n        </td>\r\n        <td class=\"text-right invoice-to w-50\">  \r\n          <div>\r\n            <strong>\r\n              To                          \r\n            </strong>\r\n          </div>\r\n          <div>\r\n            Customer: {customer_name} \r\n            <a class=\"view-link\" href=\"customer_profile.php?customer_id=1\">\r\n              <span class=\"fa fa-eye\"></span>\r\n            </a>\r\n          </div>\r\n          <div>\r\n            Customer Contact: {customer_contact}                        \r\n          </div>\r\n        </td>\r\n      </tr>\r\n    </tbody>\r\n  </table>\r\n  <div class=\"table-responsive invoice-items\">  \r\n    <table class=\"table table-bordered table-striped table-hover mb-0\">\r\n      <thead>\r\n        <tr class=\"active\">\r\n  				<th class=\"w-5 text-center\">\r\n            Sl.No.                        \r\n          </th>\r\n  				<th class=\"w-50\">\r\n            Product Name                        \r\n          </th>\r\n          <th class=\"w-10\">\r\n            Quantity                        \r\n          </th>\r\n  				<th class=\"text-right w-15\">\r\n            Price                        \r\n          </th>\r\n  				<th class=\"text-right w-20\">\r\n            Total                        \r\n          </th>\r\n  			</tr>\r\n      </thead>\r\n      <tbody>\r\n        <tr>\r\n          <td class=\"text-center\" data-title=\"Sl.\">\r\n            #1                            \r\n          </td>\r\n          <td data-title=\"Product Name\">\r\n            ajke 2                                                                                                              \r\n          </td>\r\n          <td data-title=\"Quantity\">\r\n            x 1 box                            \r\n          </td>\r\n          <td class=\"text-right\" data-title=\"Price\">\r\n            120.00                            \r\n          </td>\r\n          <td class=\"text-right\" data-title=\"Total\">\r\n            120.00                            \r\n          </td>\r\n        </tr>              \r\n      </tbody>\r\n    </table>\r\n  </div>\r\n  <div class=\"table-responsive bt-1\">\r\n    <table id=\"selling_bill\" class=\"table\">\r\n      <tbody>\r\n        <tr class=\"active\">\r\n        	<td class=\"w-80 text-right\">\r\n            Sub Total                        \r\n          </td>\r\n        	<td class=\"w-20 text-right\">\r\n            120.00                        \r\n          </td>\r\n        </tr>\r\n        <tr class=\"active\">\r\n        	<td class=\"w-80 text-right\">\r\n            Discount                        \r\n          </td>\r\n        	<td class=\"w-20 text-right\">\r\n            0.00                        \r\n          </td>\r\n        </tr>\r\n        <tr class=\"active\">\r\n          <td class=\"w-80 text-right\">\r\n            Order Tax                        \r\n          </td>\r\n          <td class=\"w-20 text-right\">\r\n            0.00                        \r\n          </td>\r\n        </tr>\r\n        <tr class=\"active\">\r\n        	<td class=\"w-80 text-right\">\r\n            Payable Amount                        \r\n          </td>\r\n        	<td class=\"w-20 text-right\">\r\n            120.00                        \r\n          </td>\r\n        </tr>\r\n        <tr class=\"active\">\r\n          <td class=\"w-80 text-right\">\r\n            Paid                        \r\n          </td>\r\n          <td class=\"w-20 text-right\">\r\n            120.00                        \r\n          </td>\r\n        </tr>\r\n        <tr class=\"active\">\r\n          <td class=\"w-80 text-right\">\r\n            Due                        \r\n          </td>\r\n          <td class=\"w-20 text-right\">\r\n            0.00                        \r\n          </td>\r\n        </tr>\r\n      </tbody>\r\n    </table>\r\n  </div>\r\n  <div class=\"table-responsive\">\r\n    <table class=\"table table-striped\">\r\n      <tbody>\r\n        <tr class=\"bt-1 success\">\r\n          <td class=\"w-40 text-right\">\r\n            <small><i>Paid on</i></small> 2019-01-30 11:47:18 \r\n                                                  (via Cash on Delivery)\r\n                                                  by Admin                                                          \r\n          </td>\r\n          <td class=\"w-30 text-right\">\r\n            Amount:&nbsp; 120.00                                                          \r\n          </td>\r\n          <td class=\"w-30 text-right\">\r\n            &nbsp;\r\n          </td>\r\n        </tr>                                     \r\n      </tbody>\r\n    </table>\r\n  </div>\r\n  <div class=\"text-center\"><h5><b>Tax Summary</b></h5></div>\r\n  <table class=\"table table-bordered table-striped print-table order-table table-condensed mb-0\">\r\n    <thead>\r\n      <tr class=\"active\">\r\n        <th class=\"w-35 text-center\"> Name</th>\r\n        <th class=\"w-20 text-center\">Code</th>\r\n        <th class=\"w-15 text-center\">Qty</th>\r\n        <th class=\"w-15 text-right\">Tax Excl</th>\r\n        <th class=\"w-15 text-right\">Tax Amount</th>\r\n      </tr>\r\n    </thead>\r\n    <tbody>\r\n      <tr>\r\n        <td class=\"text-center\">Tax @20%</td>\r\n        <td class=\"text-center\">TTX</td>\r\n        <td class=\"text-center\">1</td>\r\n        <td class=\"text-right\">120.00</td>\r\n        <td class=\"text-right\">0.00</td>\r\n      </tr>\r\n    </tbody>\r\n    <tfoot>\r\n      <tr class=\"active\">\r\n        <th colspan=\"4\" class=\"text-right\">Total Tax Amount</th>\r\n        <th class=\"text-right\">0.00</th>\r\n      </tr>\r\n    </tfoot>\r\n  </table>\r\n  <p class=\"text-center\">\r\n    <i>Thank you for choosing us!</i>\r\n  </p>\r\n  <div class=\"invoice-header-info barcodes\">\r\n    {qucode}\r\n  </div>\r\n  <div class=\"table-responsive\">\r\n    <table class=\"table\">\r\n      <tbody>\r\n        <tr class=\"invoice-authority-cotainer\">\r\n          <td class=\"w-50\">\r\n            <div class=\"invoice_authority invoice_created_by\">\r\n              <div class=\"name\">\r\n                Admin                            \r\n              </div>\r\n              <div>\r\n                Created By                            \r\n              </div>\r\n            </div>\r\n          </td>\r\n          <td class=\"w-50\">\r\n            <div class=\"invoice_authority invoice_created_by\">\r\n              <div class=\"name\">\r\n                Cashier                            \r\n              </div>\r\n              <div>\r\n                Cashier                            \r\n              </div>\r\n            </div>\r\n          </td>\r\n        </tr>\r\n      </tbody>\r\n    </table>\r\n  </div>\r\n</div>', '2019-01-30 07:50:29', '2019-01-30 07:50:29', 0, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE `printers` (
  `printer_id` int(11) NOT NULL,
  `title` varchar(55) NOT NULL,
  `type` varchar(25) NOT NULL,
  `profile` varchar(25) DEFAULT NULL,
  `char_per_line` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `printers`
--

INSERT INTO `printers` (`printer_id`, `title`, `type`, `profile`, `char_per_line`, `created_at`) VALUES
(1, 'Common Printer', 'network', '', 200, '2018-09-27 13:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `printer_to_store`
--

CREATE TABLE `printer_to_store` (
  `p2s_id` int(10) NOT NULL,
  `pprinter_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `path` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `printer_to_store`
--

INSERT INTO `printer_to_store` (`p2s_id`, `pprinter_id`, `store_id`, `path`, `ip_address`, `port`, `status`, `sort_order`) VALUES
(1, 1, 1, '', '192.234.43.3', '9100', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `p_id` int(10) UNSIGNED NOT NULL,
  `p_code` varchar(50) NOT NULL,
  `hsn_code` varchar(100) DEFAULT NULL,
  `p_name` varchar(100) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `unit_id` int(10) UNSIGNED NOT NULL,
  `p_image` varchar(250) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_to_store`
--

CREATE TABLE `product_to_store` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `buy_price` float NOT NULL DEFAULT '0',
  `sell_price` float NOT NULL DEFAULT '0',
  `quantity_in_stock` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `alert_quantity` int(10) UNSIGNED NOT NULL DEFAULT '10',
  `sup_id` int(10) UNSIGNED NOT NULL,
  `box_id` int(10) UNSIGNED NOT NULL,
  `taxrate_id` int(10) UNSIGNED DEFAULT NULL,
  `tax_method` varchar(55) NOT NULL DEFAULT 'inclusive',
  `e_date` date NOT NULL,
  `p_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `store_id` int(10) NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `total_item` int(10) DEFAULT NULL,
  `total_quantity` int(10) NOT NULL,
  `total_amount` decimal(25,4) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `return_items`
--

CREATE TABLE `return_items` (
  `id` int(11) NOT NULL,
  `store_id` int(10) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `amount` decimal(25,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `selling_info`
--

CREATE TABLE `selling_info` (
  `info_id` int(10) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `edit_counter` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `inv_type` enum('sell') NOT NULL DEFAULT 'sell',
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `customer_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `customer_mobile` varchar(20) DEFAULT NULL,
  `ref_invoice_id` varchar(100) DEFAULT NULL,
  `ref_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `invoice_note` text,
  `total_items` smallint(6) DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `pmethod_id` int(10) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `checkout_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `selling_item`
--

CREATE TABLE `selling_item` (
  `id` int(10) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `sup_id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `item_id` int(10) UNSIGNED NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_price` decimal(25,4) NOT NULL,
  `item_discount` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `item_tax` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `tax_method` enum('exclusive','inclusive','','') NOT NULL DEFAULT 'exclusive',
  `taxrate_id` int(10) UNSIGNED DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `item_quantity` int(10) UNSIGNED NOT NULL,
  `total_buying_price` decimal(25,4) DEFAULT NULL,
  `item_total` decimal(25,4) NOT NULL,
  `buying_invoice_id` varchar(100) DEFAULT NULL,
  `print_counter` int(10) UNSIGNED DEFAULT '0',
  `print_counter_time` timestamp NULL DEFAULT NULL,
  `printed_by` int(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `selling_price`
--

CREATE TABLE `selling_price` (
  `price_id` int(10) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `subtotal` decimal(25,4) DEFAULT NULL,
  `discount_type` enum('plain','percentage','','') NOT NULL DEFAULT 'plain',
  `discount_amount` decimal(25,4) DEFAULT '0.0000',
  `order_tax` decimal(25,4) DEFAULT '0.0000',
  `item_tax` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `shipping_type` enum('plain','percentage','','') NOT NULL DEFAULT 'plain',
  `shipping_amount` decimal(25,4) DEFAULT '0.0000',
  `payable_amount` decimal(25,4) DEFAULT NULL,
  `paid_amount` decimal(25,4) NOT NULL,
  `due_paid` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `due` decimal(25,4) NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) NOT NULL,
  `version` varchar(10) NOT NULL,
  `is_update_available` tinyint(1) NOT NULL DEFAULT '0',
  `update_version` varchar(100) DEFAULT NULL,
  `update_link` text CHARACTER SET utf8,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `version`, `is_update_available`, `update_version`, `update_link`, `created_at`, `updated_at`) VALUES
(1, '2.0', 0, NULL, NULL, '2018-09-14 18:00:00', '2018-09-14 14:20:58');

-- --------------------------------------------------------

--
-- Table structure for table `sms_schedule`
--

CREATE TABLE `sms_schedule` (
  `id` int(11) UNSIGNED NOT NULL,
  `schedule_datetime` timestamp NULL DEFAULT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `people_type` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `people_name` varchar(100) NOT NULL,
  `sms_text` text NOT NULL,
  `sms_type` varchar(50) NOT NULL DEFAULT 'TEXT',
  `campaign_name` varchar(100) DEFAULT NULL,
  `process_status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `response_text` varchar(250) DEFAULT NULL,
  `delivery_status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sms_setting`
--

CREATE TABLE `sms_setting` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `api_id` varchar(100) DEFAULT NULL,
  `auth_key` varchar(100) DEFAULT NULL,
  `sender_id` varchar(100) NOT NULL,
  `contact` text,
  `username` varchar(150) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `maskname` varchar(100) DEFAULT NULL,
  `campaignname` varchar(100) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sms_setting`
--

INSERT INTO `sms_setting` (`id`, `type`, `api_id`, `auth_key`, `sender_id`, `contact`, `username`, `password`, `maskname`, `campaignname`, `url`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 'Clickatell', '', NULL, '', NULL, '', '', NULL, NULL, NULL, 1, 0, NULL, '2018-12-07 15:08:57'),
(3, 'Twilio', '', NULL, '', NULL, '', '', NULL, NULL, NULL, 1, 0, NULL, '2018-12-04 15:25:14'),
(4, 'Msg91', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '2018-12-04 15:25:07'),
(5, 'ITS24', 'dGhpbms6QXNpZi40MzQz', NULL, 'think', NULL, NULL, NULL, NULL, NULL, 'https://sms.one9.one/sms/api', 1, 0, NULL, '2018-12-05 02:53:56'),
(6, 'Onnorokomsms', NULL, NULL, '', NULL, '', '', '', '', 'https://api2.onnorokomsms.com/HttpSendSms.ashx?', 1, 0, NULL, '2019-02-10 05:54:34');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `store_id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(14) NOT NULL,
  `country` varchar(50) NOT NULL,
  `zip_code` varchar(50) NOT NULL,
  `currency` varchar(100) NOT NULL DEFAULT 'USD',
  `vat_reg_no` varchar(250) DEFAULT NULL,
  `cashier_id` int(10) UNSIGNED NOT NULL,
  `address` longtext NOT NULL,
  `receipt_printer` varchar(100) DEFAULT NULL,
  `cash_drawer_codes` varchar(100) DEFAULT NULL,
  `char_per_line` tinyint(4) NOT NULL DEFAULT '42',
  `remote_printing` tinyint(1) NOT NULL DEFAULT '1',
  `printer` int(11) DEFAULT NULL,
  `order_printers` varchar(100) DEFAULT NULL,
  `auto_print` tinyint(1) NOT NULL DEFAULT '0',
  `local_printers` tinyint(1) DEFAULT NULL,
  `logo` text,
  `favicon` varchar(250) DEFAULT NULL,
  `preference` longtext,
  `sound_effect` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `feedback_status` varchar(100) NOT NULL DEFAULT 'ready',
  `feedback_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`store_id`, `name`, `mobile`, `country`, `zip_code`, `currency`, `vat_reg_no`, `cashier_id`, `address`, `receipt_printer`, `cash_drawer_codes`, `char_per_line`, `remote_printing`, `printer`, `order_printers`, `auto_print`, `local_printers`, `logo`, `favicon`, `preference`, `sound_effect`, `sort_order`, `feedback_status`, `feedback_at`, `status`, `created_at`) VALUES
(1, 'Sunny Demo', '01401563722', 'US', '1200', 'EUR', '987654321', 2, 'dhaka,bangladesh', '1', 'x1C', 42, 0, 1, '[\"1\",\"2\"]', 0, 1, '1_logo.png', '1_favicon.png', 'a:18:{s:8:\"timezone\";s:10:\"Asia/Dhaka\";s:21:\"invoice_edit_lifespan\";i:1440;s:26:\"invoice_edit_lifespan_unit\";s:6:\"minute\";s:23:\"invoice_delete_lifespan\";i:1440;s:28:\"invoice_delete_lifespan_unit\";s:6:\"minute\";s:3:\"tax\";i:0;s:20:\"stock_alert_quantity\";i:10;s:20:\"datatable_item_limit\";i:25;s:15:\"after_sell_page\";s:3:\"pos\";s:19:\"invoice_footer_text\";s:26:\"Thank you for choosing us!\";s:10:\"email_from\";s:10:\"Sunny Demo\";s:13:\"email_address\";s:2:\"US\";s:12:\"email_driver\";s:11:\"smtp_server\";s:9:\"smtp_host\";s:15:\"smtp.google.com\";s:13:\"smtp_username\";s:0:\"\";s:13:\"smtp_password\";s:0:\"\";s:9:\"smtp_port\";i:465;s:7:\"ssl_tls\";s:3:\"ssl\";}', 1, 0, 'ready', '2018-12-22 12:29:05', 1, '2018-09-24 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `sup_id` int(10) UNSIGNED NOT NULL,
  `sup_name` varchar(100) NOT NULL,
  `sup_mobile` varchar(14) DEFAULT NULL,
  `sup_email` varchar(100) DEFAULT NULL,
  `sup_address` text,
  `sup_city` varchar(100) DEFAULT NULL,
  `sup_state` varchar(55) DEFAULT NULL,
  `sup_country` varchar(100) DEFAULT NULL,
  `sup_details` longtext,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`sup_id`, `sup_name`, `sup_mobile`, `sup_email`, `sup_address`, `sup_city`, `sup_state`, `sup_country`, `sup_details`, `created_at`) VALUES
(9, 'Default supplier', '11111111111111', 'default@default.com', 'Address', 'City', 'AN', 'AD', '', '2019-02-11 16:55:16');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_to_store`
--

CREATE TABLE `supplier_to_store` (
  `s2s_id` int(10) NOT NULL,
  `sup_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `balance` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `supplier_to_store`
--

INSERT INTO `supplier_to_store` (`s2s_id`, `sup_id`, `store_id`, `balance`, `status`, `sort_order`) VALUES
(20, 9, 1, '0.0000', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_transactions`
--

CREATE TABLE `supplier_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `sup_id` int(10) UNSIGNED NOT NULL,
  `reference_no` varchar(55) DEFAULT NULL,
  `ref_invoice_id` varchar(55) DEFAULT NULL,
  `type` varchar(55) NOT NULL,
  `pmethod_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `amount` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `store_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sync`
--

CREATE TABLE `sync` (
  `id` int(10) NOT NULL,
  `sql_statement` longtext NOT NULL,
  `sql_args` longtext NOT NULL,
  `created_by` int(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `taxrates`
--

CREATE TABLE `taxrates` (
  `taxrate_id` int(11) NOT NULL,
  `taxrate_name` varchar(55) CHARACTER SET utf8 NOT NULL,
  `taxrate_code` varchar(55) CHARACTER SET utf8 DEFAULT NULL,
  `taxrate` decimal(25,4) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `taxrates`
--

INSERT INTO `taxrates` (`taxrate_id`, `taxrate_name`, `taxrate_code`, `taxrate`, `status`, `sort_order`) VALUES
(1, 'GST @0%', 'GG0', '0.0000', 1, 0),
(2, 'GST @18%', 'GGX', '18.0000', 1, 0),
(3, 'No Tax', 'NNX', '0.0000', 1, 0),
(4, 'VAT @10%', 'VVX', '10.0000', 1, 0),
(5, 'Tax @20%', 'TTX', '20.0000', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `transfers`
--

CREATE TABLE `transfers` (
  `id` int(11) NOT NULL,
  `ref_no` varchar(55) NOT NULL,
  `invoice_id` varchar(100) DEFAULT NULL,
  `from_store_id` int(11) NOT NULL,
  `to_store_id` int(11) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `total_item` int(10) DEFAULT NULL,
  `total_quantity` int(10) NOT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'pending',
  `attachment` varchar(55) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_items`
--

CREATE TABLE `transfer_items` (
  `id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `store_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(10) NOT NULL,
  `unit_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `unit_details` text CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`, `unit_details`) VALUES
(2, 'kg', ''),
(9, 'gram', ''),
(10, 'Pcs', 'Pieces'),
(11, 'Plate', ''),
(12, 'box', 'box unit');

-- --------------------------------------------------------

--
-- Table structure for table `unit_to_store`
--

CREATE TABLE `unit_to_store` (
  `unit2s_id` int(10) UNSIGNED NOT NULL,
  `uunit_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `store_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `unit_to_store`
--

INSERT INTO `unit_to_store` (`unit2s_id`, `uunit_id`, `store_id`, `status`, `sort_order`) VALUES
(2, 2, 1, 1, 0),
(8, 9, 1, 1, 2),
(10, 10, 1, 1, 0),
(12, 11, 1, 1, 0),
(15, 12, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `raw_password` varchar(100) DEFAULT NULL,
  `pass_reset_code` varchar(32) DEFAULT NULL,
  `reset_code_time` timestamp NULL DEFAULT NULL,
  `ip` varchar(40) DEFAULT NULL,
  `preference` longtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `group_id`, `username`, `email`, `mobile`, `password`, `raw_password`, `pass_reset_code`, `reset_code_time`, `ip`, `preference`, `created_at`, `updated_at`) VALUES
(1, 1, 'Your Name', 'sunny@7rong.net', '01401563722', 'e10adc3949ba59abbe56e057f20f883e', '123456', '', '0000-00-00 00:00:00', '160.202.160.104', 'a:4:{s:8:\"language\";s:7:\"english\";s:10:\"base_color\";s:5:\"green\";s:14:\"pos_side_panel\";s:5:\"right\";s:11:\"pos_pattern\";s:13:\"brickwall.jpg\";}', '2019-06-11 22:52:43', '2019-06-11 21:55:51'),
(2, 2, 'Cashier', 'cashier@7rong.net', '0113743723456', 'e10adc3949ba59abbe56e057f20f883e', '123456', '', '0000-00-00 00:00:00', '116.58.205.204', 'a:4:{s:8:\"language\";s:7:\"english\";s:10:\"base_color\";s:5:\"green\";s:14:\"pos_side_panel\";s:5:\"right\";s:11:\"pos_pattern\";s:13:\"brickwall.jpg\";}', '2019-06-11 22:52:43', '2019-06-11 18:52:43'),
(3, 3, 'Salesman', 'salesman@7rong.net', '0113743700', 'e10adc3949ba59abbe56e057f20f883e', '123456', '', '0000-00-00 00:00:00', '116.58.205.204', 'a:4:{s:8:\"language\";s:7:\"english\";s:10:\"base_color\";s:5:\"black\";s:14:\"pos_side_panel\";s:4:\"left\";s:11:\"pos_pattern\";s:13:\"brickwall.jpg\";}', '2019-06-11 22:52:43', '2019-06-11 18:52:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE `user_group` (
  `group_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `sort_order` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `permission` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_group`
--

INSERT INTO `user_group` (`group_id`, `name`, `slug`, `sort_order`, `status`, `permission`) VALUES
(1, 'Admin', 'admin', 1, 1, 'a:1:{s:6:\"access\";a:146:{s:16:\"read_sell_report\";s:4:\"true\";s:22:\"read_accounting_report\";s:4:\"true\";s:20:\"read_overview_report\";s:4:\"true\";s:22:\"read_collection_report\";s:4:\"true\";s:27:\"read_full_collection_report\";s:4:\"true\";s:35:\"read_customer_due_collection_report\";s:4:\"true\";s:29:\"read_supplier_due_paid_report\";s:4:\"true\";s:14:\"read_analytics\";s:4:\"true\";s:21:\"send_report_via_email\";s:4:\"true\";s:19:\"read_summary_report\";s:4:\"true\";s:15:\"read_buy_report\";s:4:\"true\";s:23:\"read_buy_payment_report\";s:4:\"true\";s:24:\"read_sell_payment_report\";s:4:\"true\";s:20:\"read_sell_tax_report\";s:4:\"true\";s:19:\"read_buy_tax_report\";s:4:\"true\";s:24:\"read_tax_overview_report\";s:4:\"true\";s:17:\"read_stock_report\";s:4:\"true\";s:17:\"send_report_email\";s:4:\"true\";s:8:\"withdraw\";s:4:\"true\";s:7:\"deposit\";s:4:\"true\";s:8:\"transfer\";s:4:\"true\";s:19:\"create_bank_account\";s:4:\"true\";s:19:\"update_bank_account\";s:4:\"true\";s:19:\"delete_bank_account\";s:4:\"true\";s:17:\"read_bank_account\";s:4:\"true\";s:23:\"read_bank_account_sheet\";s:4:\"true\";s:18:\"read_bank_transfer\";s:4:\"true\";s:22:\"read_bank_transactions\";s:4:\"true\";s:18:\"read_purchase_list\";s:4:\"true\";s:23:\"create_purchase_invoice\";s:4:\"true\";s:28:\"update_purchase_invoice_info\";s:4:\"true\";s:23:\"delete_purchase_invoice\";s:4:\"true\";s:16:\"purchase_payment\";s:4:\"true\";s:15:\"purchase_return\";s:4:\"true\";s:20:\"read_buy_transaction\";s:4:\"true\";s:21:\"read_sell_transaction\";s:4:\"true\";s:14:\"create_invoice\";s:4:\"true\";s:17:\"read_invoice_list\";s:4:\"true\";s:12:\"view_invoice\";s:4:\"true\";s:11:\"return_item\";s:4:\"true\";s:13:\"email_invoice\";s:4:\"true\";s:19:\"update_invoice_info\";s:4:\"true\";s:14:\"delete_invoice\";s:4:\"true\";s:11:\"sell_return\";s:4:\"true\";s:7:\"payment\";s:4:\"true\";s:10:\"create_due\";s:4:\"true\";s:13:\"read_transfer\";s:4:\"true\";s:12:\"add_transfer\";s:4:\"true\";s:15:\"update_transfer\";s:4:\"true\";s:13:\"read_giftcard\";s:4:\"true\";s:12:\"add_giftcard\";s:4:\"true\";s:15:\"update_giftcard\";s:4:\"true\";s:15:\"delete_giftcard\";s:4:\"true\";s:14:\"giftcard_topup\";s:4:\"true\";s:19:\"read_giftcard_topup\";s:4:\"true\";s:21:\"delete_giftcard_topup\";s:4:\"true\";s:12:\"read_product\";s:4:\"true\";s:14:\"create_product\";s:4:\"true\";s:14:\"update_product\";s:4:\"true\";s:14:\"delete_product\";s:4:\"true\";s:14:\"import_product\";s:4:\"true\";s:19:\"product_bulk_action\";s:4:\"true\";s:18:\"delete_all_product\";s:4:\"true\";s:13:\"read_category\";s:4:\"true\";s:15:\"create_category\";s:4:\"true\";s:15:\"update_category\";s:4:\"true\";s:15:\"delete_category\";s:4:\"true\";s:16:\"read_stock_alert\";s:4:\"true\";s:20:\"read_expired_product\";s:4:\"true\";s:13:\"print_barcode\";s:4:\"true\";s:19:\"restore_all_product\";s:4:\"true\";s:13:\"read_supplier\";s:4:\"true\";s:15:\"create_supplier\";s:4:\"true\";s:15:\"update_supplier\";s:4:\"true\";s:15:\"delete_supplier\";s:4:\"true\";s:21:\"read_supplier_profile\";s:4:\"true\";s:8:\"read_box\";s:4:\"true\";s:10:\"create_box\";s:4:\"true\";s:10:\"update_box\";s:4:\"true\";s:10:\"delete_box\";s:4:\"true\";s:9:\"read_unit\";s:4:\"true\";s:11:\"create_unit\";s:4:\"true\";s:11:\"update_unit\";s:4:\"true\";s:11:\"delete_unit\";s:4:\"true\";s:12:\"read_taxrate\";s:4:\"true\";s:14:\"create_taxrate\";s:4:\"true\";s:14:\"update_taxrate\";s:4:\"true\";s:14:\"delete_taxrate\";s:4:\"true\";s:12:\"read_expense\";s:4:\"true\";s:14:\"create_expense\";s:4:\"true\";s:14:\"update_expense\";s:4:\"true\";s:14:\"delete_expense\";s:4:\"true\";s:9:\"read_loan\";s:4:\"true\";s:17:\"read_loan_summary\";s:4:\"true\";s:9:\"take_loan\";s:4:\"true\";s:11:\"update_loan\";s:4:\"true\";s:11:\"delete_loan\";s:4:\"true\";s:8:\"loan_pay\";s:4:\"true\";s:13:\"read_customer\";s:4:\"true\";s:21:\"read_customer_profile\";s:4:\"true\";s:15:\"create_customer\";s:4:\"true\";s:15:\"update_customer\";s:4:\"true\";s:15:\"delete_customer\";s:4:\"true\";s:9:\"read_user\";s:4:\"true\";s:11:\"create_user\";s:4:\"true\";s:11:\"update_user\";s:4:\"true\";s:11:\"delete_user\";s:4:\"true\";s:15:\"change_password\";s:4:\"true\";s:14:\"read_usergroup\";s:4:\"true\";s:16:\"create_usergroup\";s:4:\"true\";s:16:\"update_usergroup\";s:4:\"true\";s:16:\"delete_usergroup\";s:4:\"true\";s:13:\"read_currency\";s:4:\"true\";s:15:\"create_currency\";s:4:\"true\";s:15:\"update_currency\";s:4:\"true\";s:15:\"change_currency\";s:4:\"true\";s:15:\"delete_currency\";s:4:\"true\";s:16:\"read_filemanager\";s:4:\"true\";s:12:\"read_pmethod\";s:4:\"true\";s:14:\"create_pmethod\";s:4:\"true\";s:14:\"update_pmethod\";s:4:\"true\";s:14:\"delete_pmethod\";s:4:\"true\";s:10:\"read_store\";s:4:\"true\";s:12:\"create_store\";s:4:\"true\";s:12:\"update_store\";s:4:\"true\";s:12:\"delete_store\";s:4:\"true\";s:14:\"activate_store\";s:4:\"true\";s:14:\"upload_favicon\";s:4:\"true\";s:11:\"upload_logo\";s:4:\"true\";s:12:\"read_printer\";s:4:\"true\";s:14:\"create_printer\";s:4:\"true\";s:14:\"update_printer\";s:4:\"true\";s:14:\"delete_printer\";s:4:\"true\";s:16:\"read_sms_setting\";s:4:\"true\";s:18:\"update_sms_setting\";s:4:\"true\";s:8:\"send_sms\";s:4:\"true\";s:20:\"read_user_preference\";s:4:\"true\";s:22:\"update_user_preference\";s:4:\"true\";s:9:\"filtering\";s:4:\"true\";s:22:\"read_keyboard_shortcut\";s:4:\"true\";s:13:\"language_sync\";s:4:\"true\";s:6:\"backup\";s:4:\"true\";s:7:\"restore\";s:4:\"true\";s:14:\"show_buy_price\";s:4:\"true\";s:11:\"show_profit\";s:4:\"true\";s:10:\"show_graph\";s:4:\"true\";}}'),
(2, 'Cashier', 'cashier', 2, 1, 'a:1:{s:6:\"access\";a:136:{s:16:\"read_sell_report\";s:4:\"true\";s:22:\"read_accounting_report\";s:4:\"true\";s:20:\"read_overview_report\";s:4:\"true\";s:22:\"read_collection_report\";s:4:\"true\";s:27:\"read_full_collection_report\";s:4:\"true\";s:35:\"read_customer_due_collection_report\";s:4:\"true\";s:29:\"read_supplier_due_paid_report\";s:4:\"true\";s:14:\"read_analytics\";s:4:\"true\";s:21:\"send_report_via_email\";s:4:\"true\";s:15:\"read_buy_report\";s:4:\"true\";s:23:\"read_buy_payment_report\";s:4:\"true\";s:24:\"read_sell_payment_report\";s:4:\"true\";s:20:\"read_sell_tax_report\";s:4:\"true\";s:19:\"read_buy_tax_report\";s:4:\"true\";s:17:\"read_stock_report\";s:4:\"true\";s:17:\"send_report_email\";s:4:\"true\";s:8:\"withdraw\";s:4:\"true\";s:7:\"deposit\";s:4:\"true\";s:8:\"transfer\";s:4:\"true\";s:19:\"create_bank_account\";s:4:\"true\";s:19:\"update_bank_account\";s:4:\"true\";s:19:\"delete_bank_account\";s:4:\"true\";s:17:\"read_bank_account\";s:4:\"true\";s:23:\"read_bank_account_sheet\";s:4:\"true\";s:18:\"read_bank_transfer\";s:4:\"true\";s:22:\"read_bank_transactions\";s:4:\"true\";s:18:\"read_purchase_list\";s:4:\"true\";s:23:\"create_purchase_invoice\";s:4:\"true\";s:28:\"update_purchase_invoice_info\";s:4:\"true\";s:23:\"delete_purchase_invoice\";s:4:\"true\";s:16:\"purchase_payment\";s:4:\"true\";s:15:\"purchase_return\";s:4:\"true\";s:14:\"create_invoice\";s:4:\"true\";s:17:\"read_invoice_list\";s:4:\"true\";s:12:\"view_invoice\";s:4:\"true\";s:11:\"return_item\";s:4:\"true\";s:13:\"email_invoice\";s:4:\"true\";s:19:\"update_invoice_info\";s:4:\"true\";s:14:\"delete_invoice\";s:4:\"true\";s:11:\"sell_return\";s:4:\"true\";s:7:\"payment\";s:4:\"true\";s:10:\"create_due\";s:4:\"true\";s:13:\"read_transfer\";s:4:\"true\";s:12:\"add_transfer\";s:4:\"true\";s:15:\"update_transfer\";s:4:\"true\";s:13:\"read_giftcard\";s:4:\"true\";s:12:\"add_giftcard\";s:4:\"true\";s:15:\"update_giftcard\";s:4:\"true\";s:15:\"delete_giftcard\";s:4:\"true\";s:14:\"giftcard_topup\";s:4:\"true\";s:19:\"read_giftcard_topup\";s:4:\"true\";s:21:\"delete_giftcard_topup\";s:4:\"true\";s:12:\"read_product\";s:4:\"true\";s:14:\"create_product\";s:4:\"true\";s:14:\"update_product\";s:4:\"true\";s:14:\"delete_product\";s:4:\"true\";s:14:\"import_product\";s:4:\"true\";s:19:\"product_bulk_action\";s:4:\"true\";s:18:\"delete_all_product\";s:4:\"true\";s:13:\"read_category\";s:4:\"true\";s:15:\"create_category\";s:4:\"true\";s:15:\"update_category\";s:4:\"true\";s:15:\"delete_category\";s:4:\"true\";s:16:\"read_stock_alert\";s:4:\"true\";s:20:\"read_expired_product\";s:4:\"true\";s:13:\"print_barcode\";s:4:\"true\";s:19:\"restore_all_product\";s:4:\"true\";s:13:\"read_supplier\";s:4:\"true\";s:15:\"create_supplier\";s:4:\"true\";s:15:\"update_supplier\";s:4:\"true\";s:15:\"delete_supplier\";s:4:\"true\";s:21:\"read_supplier_profile\";s:4:\"true\";s:8:\"read_box\";s:4:\"true\";s:10:\"create_box\";s:4:\"true\";s:10:\"update_box\";s:4:\"true\";s:10:\"delete_box\";s:4:\"true\";s:9:\"read_unit\";s:4:\"true\";s:11:\"create_unit\";s:4:\"true\";s:11:\"update_unit\";s:4:\"true\";s:11:\"delete_unit\";s:4:\"true\";s:12:\"read_taxrate\";s:4:\"true\";s:14:\"create_taxrate\";s:4:\"true\";s:14:\"update_taxrate\";s:4:\"true\";s:14:\"delete_taxrate\";s:4:\"true\";s:12:\"read_expense\";s:4:\"true\";s:14:\"create_expense\";s:4:\"true\";s:14:\"update_expense\";s:4:\"true\";s:14:\"delete_expense\";s:4:\"true\";s:9:\"read_loan\";s:4:\"true\";s:17:\"read_loan_summary\";s:4:\"true\";s:9:\"take_loan\";s:4:\"true\";s:11:\"update_loan\";s:4:\"true\";s:11:\"delete_loan\";s:4:\"true\";s:13:\"read_customer\";s:4:\"true\";s:21:\"read_customer_profile\";s:4:\"true\";s:15:\"create_customer\";s:4:\"true\";s:15:\"update_customer\";s:4:\"true\";s:15:\"delete_customer\";s:4:\"true\";s:9:\"read_user\";s:4:\"true\";s:11:\"create_user\";s:4:\"true\";s:11:\"update_user\";s:4:\"true\";s:11:\"delete_user\";s:4:\"true\";s:15:\"change_password\";s:4:\"true\";s:14:\"read_usergroup\";s:4:\"true\";s:16:\"create_usergroup\";s:4:\"true\";s:16:\"update_usergroup\";s:4:\"true\";s:16:\"delete_usergroup\";s:4:\"true\";s:13:\"read_currency\";s:4:\"true\";s:15:\"create_currency\";s:4:\"true\";s:15:\"update_currency\";s:4:\"true\";s:15:\"change_currency\";s:4:\"true\";s:15:\"delete_currency\";s:4:\"true\";s:16:\"read_filemanager\";s:4:\"true\";s:10:\"read_store\";s:4:\"true\";s:12:\"create_store\";s:4:\"true\";s:12:\"update_store\";s:4:\"true\";s:12:\"delete_store\";s:4:\"true\";s:14:\"activate_store\";s:4:\"true\";s:14:\"upload_favicon\";s:4:\"true\";s:11:\"upload_logo\";s:4:\"true\";s:12:\"read_printer\";s:4:\"true\";s:14:\"create_printer\";s:4:\"true\";s:14:\"update_printer\";s:4:\"true\";s:14:\"delete_printer\";s:4:\"true\";s:16:\"read_sms_setting\";s:4:\"true\";s:18:\"update_sms_setting\";s:4:\"true\";s:8:\"send_sms\";s:4:\"true\";s:20:\"read_user_preference\";s:4:\"true\";s:22:\"update_user_preference\";s:4:\"true\";s:9:\"filtering\";s:4:\"true\";s:22:\"read_keyboard_shortcut\";s:4:\"true\";s:6:\"backup\";s:4:\"true\";s:7:\"restore\";s:4:\"true\";s:14:\"show_buy_price\";s:4:\"true\";s:11:\"show_profit\";s:4:\"true\";s:10:\"show_graph\";s:4:\"true\";}}'),
(3, 'Salesman', 'salesman', 3, 1, 'a:1:{s:6:\"access\";a:28:{s:22:\"read_accounting_report\";s:4:\"true\";s:8:\"withdraw\";s:4:\"true\";s:7:\"deposit\";s:4:\"true\";s:8:\"transfer\";s:4:\"true\";s:19:\"create_bank_account\";s:4:\"true\";s:19:\"update_bank_account\";s:4:\"true\";s:19:\"delete_bank_account\";s:4:\"true\";s:17:\"read_bank_account\";s:4:\"true\";s:23:\"read_bank_account_sheet\";s:4:\"true\";s:18:\"read_bank_transfer\";s:4:\"true\";s:22:\"read_bank_transactions\";s:4:\"true\";s:14:\"create_invoice\";s:4:\"true\";s:17:\"read_invoice_list\";s:4:\"true\";s:12:\"view_invoice\";s:4:\"true\";s:19:\"add_item_to_invoice\";s:4:\"true\";s:24:\"remove_item_from_invoice\";s:4:\"true\";s:14:\"delete_invoice\";s:4:\"true\";s:13:\"email_invoice\";s:4:\"true\";s:10:\"create_due\";s:4:\"true\";s:14:\"due_collection\";s:4:\"true\";s:12:\"read_product\";s:4:\"true\";s:13:\"read_supplier\";s:4:\"true\";s:8:\"read_box\";s:4:\"true\";s:19:\"read_payment_method\";s:4:\"true\";s:20:\"read_user_preference\";s:4:\"true\";s:22:\"update_user_preference\";s:4:\"true\";s:9:\"filtering\";s:4:\"true\";s:22:\"read_keyboard_shortcut\";s:4:\"true\";}}');

-- --------------------------------------------------------

--
-- Table structure for table `user_to_store`
--

CREATE TABLE `user_to_store` (
  `u2s_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_to_store`
--

INSERT INTO `user_to_store` (`u2s_id`, `user_id`, `store_id`, `status`, `sort_order`) VALUES
(1, 2, 1, 1, 0),
(2, 3, 1, 1, 2),
(3, 1, 1, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_account_to_store`
--
ALTER TABLE `bank_account_to_store`
  ADD PRIMARY KEY (`ba2s`);

--
-- Indexes for table `bank_transaction_info`
--
ALTER TABLE `bank_transaction_info`
  ADD PRIMARY KEY (`info_id`),
  ADD UNIQUE KEY `bank_transaction_id` (`ref_no`);

--
-- Indexes for table `bank_transaction_price`
--
ALTER TABLE `bank_transaction_price`
  ADD PRIMARY KEY (`price_id`);

--
-- Indexes for table `boxes`
--
ALTER TABLE `boxes`
  ADD PRIMARY KEY (`box_id`);

--
-- Indexes for table `box_to_store`
--
ALTER TABLE `box_to_store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buying_info`
--
ALTER TABLE `buying_info`
  ADD PRIMARY KEY (`info_id`);

--
-- Indexes for table `buying_item`
--
ALTER TABLE `buying_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buying_payments`
--
ALTER TABLE `buying_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buying_price`
--
ALTER TABLE `buying_price`
  ADD PRIMARY KEY (`price_id`);

--
-- Indexes for table `buying_returns`
--
ALTER TABLE `buying_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `buying_return_items`
--
ALTER TABLE `buying_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfer_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categorys`
--
ALTER TABLE `categorys`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `category_to_store`
--
ALTER TABLE `category_to_store`
  ADD PRIMARY KEY (`c2s_id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`currency_id`);

--
-- Indexes for table `currency_to_store`
--
ALTER TABLE `currency_to_store`
  ADD PRIMARY KEY (`ca2s_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_to_store`
--
ALTER TABLE `customer_to_store`
  ADD PRIMARY KEY (`c2s_id`);

--
-- Indexes for table `customer_transactions`
--
ALTER TABLE `customer_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_categorys`
--
ALTER TABLE `expense_categorys`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `gift_cards`
--
ALTER TABLE `gift_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `card_no` (`card_no`);

--
-- Indexes for table `gift_card_topups`
--
ALTER TABLE `gift_card_topups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `card_id` (`card_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_id`);

--
-- Indexes for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_to_store`
--
ALTER TABLE `loan_to_store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pmethods`
--
ALTER TABLE `pmethods`
  ADD PRIMARY KEY (`pmethod_id`);

--
-- Indexes for table `pmethod_to_store`
--
ALTER TABLE `pmethod_to_store`
  ADD PRIMARY KEY (`p2s_id`);

--
-- Indexes for table `pos_receipt_template`
--
ALTER TABLE `pos_receipt_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `printers`
--
ALTER TABLE `printers`
  ADD PRIMARY KEY (`printer_id`);

--
-- Indexes for table `printer_to_store`
--
ALTER TABLE `printer_to_store`
  ADD PRIMARY KEY (`p2s_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `p_code` (`p_code`) USING BTREE;

--
-- Indexes for table `product_to_store`
--
ALTER TABLE `product_to_store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `return_items`
--
ALTER TABLE `return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfer_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `selling_info`
--
ALTER TABLE `selling_info`
  ADD PRIMARY KEY (`info_id`);

--
-- Indexes for table `selling_item`
--
ALTER TABLE `selling_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `selling_price`
--
ALTER TABLE `selling_price`
  ADD PRIMARY KEY (`price_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_schedule`
--
ALTER TABLE `sms_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_setting`
--
ALTER TABLE `sms_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`store_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`sup_id`);

--
-- Indexes for table `supplier_to_store`
--
ALTER TABLE `supplier_to_store`
  ADD PRIMARY KEY (`s2s_id`);

--
-- Indexes for table `supplier_transactions`
--
ALTER TABLE `supplier_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sync`
--
ALTER TABLE `sync`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxrates`
--
ALTER TABLE `taxrates`
  ADD PRIMARY KEY (`taxrate_id`);

--
-- Indexes for table `transfers`
--
ALTER TABLE `transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `transfer_items`
--
ALTER TABLE `transfer_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfer_id` (`transfer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `unit_to_store`
--
ALTER TABLE `unit_to_store`
  ADD PRIMARY KEY (`unit2s_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `user_to_store`
--
ALTER TABLE `user_to_store`
  ADD PRIMARY KEY (`u2s_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_account_to_store`
--
ALTER TABLE `bank_account_to_store`
  MODIFY `ba2s` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_transaction_info`
--
ALTER TABLE `bank_transaction_info`
  MODIFY `info_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `bank_transaction_price`
--
ALTER TABLE `bank_transaction_price`
  MODIFY `price_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `boxes`
--
ALTER TABLE `boxes`
  MODIFY `box_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `box_to_store`
--
ALTER TABLE `box_to_store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `buying_info`
--
ALTER TABLE `buying_info`
  MODIFY `info_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buying_item`
--
ALTER TABLE `buying_item`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buying_payments`
--
ALTER TABLE `buying_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buying_price`
--
ALTER TABLE `buying_price`
  MODIFY `price_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buying_returns`
--
ALTER TABLE `buying_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buying_return_items`
--
ALTER TABLE `buying_return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categorys`
--
ALTER TABLE `categorys`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `category_to_store`
--
ALTER TABLE `category_to_store`
  MODIFY `c2s_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `currency_to_store`
--
ALTER TABLE `currency_to_store`
  MODIFY `ca2s_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_to_store`
--
ALTER TABLE `customer_to_store`
  MODIFY `c2s_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_transactions`
--
ALTER TABLE `customer_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categorys`
--
ALTER TABLE `expense_categorys`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gift_cards`
--
ALTER TABLE `gift_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gift_card_topups`
--
ALTER TABLE `gift_card_topups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `loan_to_store`
--
ALTER TABLE `loan_to_store`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pmethods`
--
ALTER TABLE `pmethods`
  MODIFY `pmethod_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pmethod_to_store`
--
ALTER TABLE `pmethod_to_store`
  MODIFY `p2s_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `pos_receipt_template`
--
ALTER TABLE `pos_receipt_template`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `printers`
--
ALTER TABLE `printers`
  MODIFY `printer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `printer_to_store`
--
ALTER TABLE `printer_to_store`
  MODIFY `p2s_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `p_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `product_to_store`
--
ALTER TABLE `product_to_store`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_items`
--
ALTER TABLE `return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `selling_info`
--
ALTER TABLE `selling_info`
  MODIFY `info_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `selling_item`
--
ALTER TABLE `selling_item`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `selling_price`
--
ALTER TABLE `selling_price`
  MODIFY `price_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sms_schedule`
--
ALTER TABLE `sms_schedule`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `sms_setting`
--
ALTER TABLE `sms_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `store_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `sup_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `supplier_to_store`
--
ALTER TABLE `supplier_to_store`
  MODIFY `s2s_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `supplier_transactions`
--
ALTER TABLE `supplier_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sync`
--
ALTER TABLE `sync`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxrates`
--
ALTER TABLE `taxrates`
  MODIFY `taxrate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transfers`
--
ALTER TABLE `transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfer_items`
--
ALTER TABLE `transfer_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `unit_to_store`
--
ALTER TABLE `unit_to_store`
  MODIFY `unit2s_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_group`
--
ALTER TABLE `user_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_to_store`
--
ALTER TABLE `user_to_store`
  MODIFY `u2s_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
