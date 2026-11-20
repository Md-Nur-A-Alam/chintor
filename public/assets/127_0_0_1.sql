-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2026 at 09:01 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chintor_db`
--
DROP DATABASE IF EXISTS `chintor_db`;
CREATE DATABASE IF NOT EXISTS `chintor_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `chintor_db`;

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ulid` char(26) NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `scheduled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'requested',
  `scheduled_at` datetime DEFAULT NULL,
  `duration_minutes` smallint(5) UNSIGNED DEFAULT NULL,
  `meeting_url` varchar(255) DEFAULT NULL,
  `result_level` varchar(20) DEFAULT NULL,
  `teacher_notes` text DEFAULT NULL,
  `recommended_course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `bio` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bio`)),
  `photo_url` text DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `slug`, `name`, `bio`, `photo_url`, `teacher_id`, `created_at`, `updated_at`) VALUES
(1, 'demo-author', '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb \\u09b2\\u09c7\\u0996\\u0995\",\"en\":\"Demo author\"}', NULL, NULL, NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30');

-- --------------------------------------------------------

--
-- Table structure for table `author_book`
--

CREATE TABLE `author_book` (
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `author_book`
--

INSERT INTO `author_book` (`author_id`, `book_id`) VALUES
(1, 1),
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `subtitle` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subtitle`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `table_of_contents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_of_contents`)),
  `class_from` tinyint(3) UNSIGNED DEFAULT NULL,
  `class_to` tinyint(3) UNSIGNED DEFAULT NULL,
  `cover_url` text DEFAULT NULL,
  `sample_pages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sample_pages`)),
  `pages` smallint(5) UNSIGNED DEFAULT NULL,
  `edition` varchar(50) DEFAULT NULL,
  `published_year` smallint(5) UNSIGNED DEFAULT NULL,
  `language` varchar(5) NOT NULL DEFAULT 'bn',
  `regular_price` int(10) UNSIGNED NOT NULL,
  `discounted_price` int(10) UNSIGNED DEFAULT NULL,
  `stock_quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `seo_title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seo_title`)),
  `seo_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seo_description`)),
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `slug`, `isbn`, `program_id`, `title`, `subtitle`, `description`, `table_of_contents`, `class_from`, `class_to`, `cover_url`, `sample_pages`, `pages`, `edition`, `published_year`, `language`, `regular_price`, `discounted_price`, `stock_quantity`, `seo_title`, `seo_description`, `status`, `published_at`, `is_featured`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'olympiad-problem-book-primary', NULL, 1, '{\"bn\":\"\\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8 \\u2014 \\u09aa\\u09cd\\u09b0\\u09be\\u0987\\u09ae\\u09be\\u09b0\\u09bf\",\"en\":\"Olympiad Problem Solving \\u2014 Primary\"}', NULL, '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb \\u09ac\\u0987 \\u2014 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u2192 \\u09ac\\u0987 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u0995\\u09ad\\u09be\\u09b0, \\u09b8\\u09c2\\u099a\\u09bf\\u09aa\\u09a4\\u09cd\\u09b0 \\u0993 \\u09ad\\u09c7\\u09a4\\u09b0\\u09c7\\u09b0 \\u09aa\\u09be\\u09a4\\u09be \\u09a6\\u09bf\\u09a8\\u0964\",\"en\":\"Demo book \\u2014 replace from Admin \\u2192 Books.\"}', '{\"bn\":[\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e7: \\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be\\u09b0 \\u0996\\u09c7\\u09b2\\u09be\",\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e8: \\u099c\\u09cd\\u09af\\u09be\\u09ae\\u09bf\\u09a4\\u09bf\\u09b0 \\u09ae\\u099c\\u09be\",\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e9: \\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf\\u09b0 \\u09a7\\u09be\\u0981\\u09a7\\u09be\"],\"en\":[\"Chapter 1: Playing with numbers\",\"Chapter 2: Fun with geometry\",\"Chapter 3: Logic puzzles\"]}', NULL, NULL, NULL, NULL, 180, NULL, NULL, 'bn', 380, 320, 50, NULL, NULL, 'published', '2026-11-19 16:06:30', 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(2, 'olympiad-problem-book-junior', NULL, 1, '{\"bn\":\"\\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8 \\u2014 \\u099c\\u09c1\\u09a8\\u09bf\\u09af\\u09bc\\u09b0\",\"en\":\"Olympiad Problem Solving \\u2014 Junior\"}', NULL, '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb \\u09ac\\u0987 \\u2014 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u2192 \\u09ac\\u0987 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u0995\\u09ad\\u09be\\u09b0, \\u09b8\\u09c2\\u099a\\u09bf\\u09aa\\u09a4\\u09cd\\u09b0 \\u0993 \\u09ad\\u09c7\\u09a4\\u09b0\\u09c7\\u09b0 \\u09aa\\u09be\\u09a4\\u09be \\u09a6\\u09bf\\u09a8\\u0964\",\"en\":\"Demo book \\u2014 replace from Admin \\u2192 Books.\"}', '{\"bn\":[\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e7: \\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be\\u09b0 \\u0996\\u09c7\\u09b2\\u09be\",\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e8: \\u099c\\u09cd\\u09af\\u09be\\u09ae\\u09bf\\u09a4\\u09bf\\u09b0 \\u09ae\\u099c\\u09be\",\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e9: \\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf\\u09b0 \\u09a7\\u09be\\u0981\\u09a7\\u09be\"],\"en\":[\"Chapter 1: Playing with numbers\",\"Chapter 2: Fun with geometry\",\"Chapter 3: Logic puzzles\"]}', NULL, NULL, NULL, NULL, 220, NULL, NULL, 'bn', 420, 350, 50, NULL, NULL, 'published', '2026-11-19 16:06:30', 1, 1, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(3, 'general-math-practice-9-10', NULL, 2, '{\"bn\":\"\\u09b8\\u09be\\u09a7\\u09be\\u09b0\\u09a3 \\u0997\\u09a3\\u09bf\\u09a4 \\u0985\\u09a8\\u09c1\\u09b6\\u09c0\\u09b2\\u09a8 (\\u09ef\\u09ae\\u2013\\u09e7\\u09e6\\u09ae)\",\"en\":\"General Math Practice (Class 9\\u201310)\"}', NULL, '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb \\u09ac\\u0987 \\u2014 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u2192 \\u09ac\\u0987 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u0995\\u09ad\\u09be\\u09b0, \\u09b8\\u09c2\\u099a\\u09bf\\u09aa\\u09a4\\u09cd\\u09b0 \\u0993 \\u09ad\\u09c7\\u09a4\\u09b0\\u09c7\\u09b0 \\u09aa\\u09be\\u09a4\\u09be \\u09a6\\u09bf\\u09a8\\u0964\",\"en\":\"Demo book \\u2014 replace from Admin \\u2192 Books.\"}', '{\"bn\":[\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e7: \\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be\\u09b0 \\u0996\\u09c7\\u09b2\\u09be\",\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e8: \\u099c\\u09cd\\u09af\\u09be\\u09ae\\u09bf\\u09a4\\u09bf\\u09b0 \\u09ae\\u099c\\u09be\",\"\\u0985\\u09a7\\u09cd\\u09af\\u09be\\u09af\\u09bc \\u09e9: \\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf\\u09b0 \\u09a7\\u09be\\u0981\\u09a7\\u09be\"],\"en\":[\"Chapter 1: Playing with numbers\",\"Chapter 2: Fun with geometry\",\"Chapter 3: Logic puzzles\"]}', NULL, NULL, NULL, NULL, 260, NULL, NULL, 'bn', 450, NULL, 50, NULL, NULL, 'published', '2026-11-19 16:06:30', 1, 2, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('chintor-cache-05aa61c18d2ea5a6342da1c76fd618eb', 'i:1;', 1795126074),
('chintor-cache-05aa61c18d2ea5a6342da1c76fd618eb:timer', 'i:1795126074;', 1795126074),
('chintor-cache-campaigns.announcement_bar', 'a:1:{s:8:\"campaign\";O:19:\"App\\Models\\Campaign\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:9:\"campaigns\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:19:{s:2:\"id\";i:1;s:5:\"title\";s:182:\"{\"bn\":\"\\u09a8\\u09a4\\u09c1\\u09a8 \\u09ac\\u099b\\u09b0\\u09c7\\u09b0 \\u09e7\\u09e7.\\u09e7\\u09e7 \\u0995\\u09cd\\u09af\\u09be\\u09ae\\u09cd\\u09aa\\u09c7\\u0987\\u09a8\",\"en\":\"New year 11.11 campaign\"}\";s:7:\"summary\";s:184:\"{\"bn\":\"\\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u0995\\u09bf\\u099b\\u09c1 \\u098f\\u0995\\u099f\\u09be \\u09b2\\u09c7\\u0996\\u09be \\u09a5\\u09be\\u0995\\u09ac\\u09c7\",\"en\":\"here, something will be written\"}\";s:9:\"image_url\";s:85:\"https://i.ibb.co/CKkpqbYF/images-1vm-Ps-Ofgm1j-ZCECEYIao-YLb-CETQ8468-I7-OMyj2-U7.jpg\";s:10:\"banner_url\";N;s:17:\"banner_mobile_url\";N;s:12:\"banner_align\";s:4:\"left\";s:14:\"banner_overlay\";i:55;s:8:\"link_url\";s:44:\"https://www.facebook.com/share/v/1ES9aCY2TX/\";s:12:\"button_label\";s:77:\"{\"bn\":\"\\u09ad\\u09bf\\u099c\\u09bf\\u099f \\u0995\\u09b0\\u09bf\",\"en\":\"Let\'s visit\"}\";s:7:\"channel\";s:8:\"facebook\";s:9:\"starts_at\";s:19:\"2026-11-19 21:24:00\";s:7:\"ends_at\";s:19:\"2026-11-27 21:24:00\";s:12:\"show_in_hero\";i:1;s:11:\"show_in_bar\";i:1;s:9:\"is_active\";i:1;s:10:\"sort_order\";i:5;s:10:\"created_at\";s:19:\"2026-11-20 21:24:35\";s:10:\"updated_at\";s:19:\"2026-11-20 23:06:19\";}s:11:\"\0*\0original\";a:19:{s:2:\"id\";i:1;s:5:\"title\";s:182:\"{\"bn\":\"\\u09a8\\u09a4\\u09c1\\u09a8 \\u09ac\\u099b\\u09b0\\u09c7\\u09b0 \\u09e7\\u09e7.\\u09e7\\u09e7 \\u0995\\u09cd\\u09af\\u09be\\u09ae\\u09cd\\u09aa\\u09c7\\u0987\\u09a8\",\"en\":\"New year 11.11 campaign\"}\";s:7:\"summary\";s:184:\"{\"bn\":\"\\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u0995\\u09bf\\u099b\\u09c1 \\u098f\\u0995\\u099f\\u09be \\u09b2\\u09c7\\u0996\\u09be \\u09a5\\u09be\\u0995\\u09ac\\u09c7\",\"en\":\"here, something will be written\"}\";s:9:\"image_url\";s:85:\"https://i.ibb.co/CKkpqbYF/images-1vm-Ps-Ofgm1j-ZCECEYIao-YLb-CETQ8468-I7-OMyj2-U7.jpg\";s:10:\"banner_url\";N;s:17:\"banner_mobile_url\";N;s:12:\"banner_align\";s:4:\"left\";s:14:\"banner_overlay\";i:55;s:8:\"link_url\";s:44:\"https://www.facebook.com/share/v/1ES9aCY2TX/\";s:12:\"button_label\";s:77:\"{\"bn\":\"\\u09ad\\u09bf\\u099c\\u09bf\\u099f \\u0995\\u09b0\\u09bf\",\"en\":\"Let\'s visit\"}\";s:7:\"channel\";s:8:\"facebook\";s:9:\"starts_at\";s:19:\"2026-11-19 21:24:00\";s:7:\"ends_at\";s:19:\"2026-11-27 21:24:00\";s:12:\"show_in_hero\";i:1;s:11:\"show_in_bar\";i:1;s:9:\"is_active\";i:1;s:10:\"sort_order\";i:5;s:10:\"created_at\";s:19:\"2026-11-20 21:24:35\";s:10:\"updated_at\";s:19:\"2026-11-20 23:06:19\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:10:{s:5:\"title\";s:5:\"array\";s:7:\"summary\";s:5:\"array\";s:12:\"button_label\";s:5:\"array\";s:7:\"channel\";s:25:\"App\\Enums\\CampaignChannel\";s:9:\"starts_at\";s:8:\"datetime\";s:7:\"ends_at\";s:8:\"datetime\";s:14:\"banner_overlay\";s:7:\"integer\";s:12:\"show_in_hero\";s:7:\"boolean\";s:11:\"show_in_bar\";s:7:\"boolean\";s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:16:{i:0;s:5:\"title\";i:1;s:7:\"summary\";i:2;s:9:\"image_url\";i:3;s:10:\"banner_url\";i:4;s:17:\"banner_mobile_url\";i:5;s:12:\"banner_align\";i:6;s:14:\"banner_overlay\";i:7;s:8:\"link_url\";i:8;s:12:\"button_label\";i:9;s:7:\"channel\";i:10;s:9:\"starts_at\";i:11;s:7:\"ends_at\";i:12;s:12:\"show_in_hero\";i:13;s:11:\"show_in_bar\";i:14;s:9:\"is_active\";i:15;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}', 1795200126),
('chintor-cache-nav.programs', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:18:\"App\\Models\\Program\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"programs\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:4:{s:2:\"id\";i:1;s:4:\"slug\";s:13:\"math-olympiad\";s:4:\"name\";s:127:\"{\"bn\":\"\\u09ae\\u09cd\\u09af\\u09be\\u09a5 \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1\",\"en\":\"Math Olympiad\"}\";s:4:\"icon\";s:6:\"target\";}s:11:\"\0*\0original\";a:4:{s:2:\"id\";i:1;s:4:\"slug\";s:13:\"math-olympiad\";s:4:\"name\";s:127:\"{\"bn\":\"\\u09ae\\u09cd\\u09af\\u09be\\u09a5 \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1\",\"en\":\"Math Olympiad\"}\";s:4:\"icon\";s:6:\"target\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:5:{s:4:\"name\";s:5:\"array\";s:7:\"tagline\";s:5:\"array\";s:11:\"description\";s:5:\"array\";s:6:\"status\";s:23:\"App\\Enums\\PublishStatus\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:9:{i:0;s:4:\"slug\";i:1;s:4:\"name\";i:2;s:7:\"tagline\";i:3;s:11:\"description\";i:4;s:4:\"icon\";i:5;s:6:\"accent\";i:6;s:9:\"image_url\";i:7;s:6:\"status\";i:8;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:18:\"App\\Models\\Program\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"programs\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:4:{s:2:\"id\";i:2;s:4:\"slug\";s:13:\"academic-9-10\";s:4:\"name\";s:130:\"{\"bn\":\"\\u098f\\u0995\\u09be\\u09a1\\u09c7\\u09ae\\u09bf\\u0995 (\\u09ef\\u09ae\\u2013\\u09e7\\u09e6\\u09ae)\",\"en\":\"Academic (Class 9\\u201310)\"}\";s:4:\"icon\";s:4:\"book\";}s:11:\"\0*\0original\";a:4:{s:2:\"id\";i:2;s:4:\"slug\";s:13:\"academic-9-10\";s:4:\"name\";s:130:\"{\"bn\":\"\\u098f\\u0995\\u09be\\u09a1\\u09c7\\u09ae\\u09bf\\u0995 (\\u09ef\\u09ae\\u2013\\u09e7\\u09e6\\u09ae)\",\"en\":\"Academic (Class 9\\u201310)\"}\";s:4:\"icon\";s:4:\"book\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:5:{s:4:\"name\";s:5:\"array\";s:7:\"tagline\";s:5:\"array\";s:11:\"description\";s:5:\"array\";s:6:\"status\";s:23:\"App\\Enums\\PublishStatus\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:9:{i:0;s:4:\"slug\";i:1;s:4:\"name\";i:2;s:7:\"tagline\";i:3;s:11:\"description\";i:4;s:4:\"icon\";i:5;s:6:\"accent\";i:6;s:9:\"image_url\";i:7;s:6:\"status\";i:8;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1795203426),
('chintor-cache-settings.contact', 'a:0:{}', 2110486000),
('chintor-cache-settings.homepage', 'a:3:{s:4:\"hero\";a:7:{s:7:\"eyebrow\";N;s:5:\"title\";N;s:8:\"subtitle\";N;s:14:\"rotating_words\";N;s:7:\"buttons\";a:2:{i:0;a:3:{s:5:\"label\";a:2:{s:2:\"bn\";s:41:\"চলো একসাথে শিখি\";s:2:\"en\";s:20:\"Let\'s learn together\";}s:3:\"url\";s:6:\"/start\";s:5:\"style\";s:7:\"primary\";}i:1;a:3:{s:5:\"label\";a:2:{s:2:\"bn\";s:72:\"ফ্রি অ্যাসেসমেন্ট বুক করুন\";s:2:\"en\";s:22:\"Book a free assessment\";}s:3:\"url\";s:19:\"/start?assessment=1\";s:5:\"style\";s:9:\"secondary\";}}s:9:\"video_url\";N;s:16:\"show_trust_chips\";b:0;}s:5:\"stats\";a:0:{}s:8:\"sections\";a:14:{i:0;a:4:{s:3:\"key\";s:10:\"highlights\";s:7:\"visible\";b:0;s:5:\"title\";N;s:8:\"subtitle\";N;}i:1;a:4:{s:3:\"key\";s:5:\"stats\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:2;a:4:{s:3:\"key\";s:9:\"campaigns\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:3;a:4:{s:3:\"key\";s:8:\"programs\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:4;a:4:{s:3:\"key\";s:7:\"courses\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:5;a:4:{s:3:\"key\";s:5:\"steps\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:6;a:4:{s:3:\"key\";s:3:\"why\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:7;a:4:{s:3:\"key\";s:8:\"teachers\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:8;a:4:{s:3:\"key\";s:14:\"parent_reviews\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:9;a:4:{s:3:\"key\";s:15:\"student_reviews\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:10;a:4:{s:3:\"key\";s:5:\"books\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:11;a:4:{s:3:\"key\";s:9:\"community\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:12;a:4:{s:3:\"key\";s:3:\"faq\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}i:13;a:4:{s:3:\"key\";s:3:\"cta\";s:7:\"visible\";b:1;s:5:\"title\";N;s:8:\"subtitle\";N;}}}', 2110548716),
('chintor-cache-settings.refund_policy', 'a:0:{}', 2110486000);

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
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary`)),
  `image_url` text DEFAULT NULL,
  `banner_url` text DEFAULT NULL,
  `banner_mobile_url` text DEFAULT NULL,
  `banner_align` varchar(10) NOT NULL DEFAULT 'left',
  `banner_overlay` tinyint(3) UNSIGNED NOT NULL DEFAULT 55,
  `link_url` varchar(500) DEFAULT NULL,
  `button_label` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`button_label`)),
  `channel` varchar(20) NOT NULL DEFAULT 'website',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `show_in_hero` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_bar` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `title`, `summary`, `image_url`, `banner_url`, `banner_mobile_url`, `banner_align`, `banner_overlay`, `link_url`, `button_label`, `channel`, `starts_at`, `ends_at`, `show_in_hero`, `show_in_bar`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, '{\"bn\":\"\\u09a8\\u09a4\\u09c1\\u09a8 \\u09ac\\u099b\\u09b0\\u09c7\\u09b0 \\u09e7\\u09e7.\\u09e7\\u09e7 \\u0995\\u09cd\\u09af\\u09be\\u09ae\\u09cd\\u09aa\\u09c7\\u0987\\u09a8\",\"en\":\"New year 11.11 campaign\"}', '{\"bn\":\"\\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u0995\\u09bf\\u099b\\u09c1 \\u098f\\u0995\\u099f\\u09be \\u09b2\\u09c7\\u0996\\u09be \\u09a5\\u09be\\u0995\\u09ac\\u09c7\",\"en\":\"here, something will be written\"}', 'https://i.ibb.co/CKkpqbYF/images-1vm-Ps-Ofgm1j-ZCECEYIao-YLb-CETQ8468-I7-OMyj2-U7.jpg', NULL, NULL, 'left', 55, 'https://www.facebook.com/share/v/1ES9aCY2TX/', '{\"bn\":\"\\u09ad\\u09bf\\u099c\\u09bf\\u099f \\u0995\\u09b0\\u09bf\",\"en\":\"Let\'s visit\"}', 'facebook', '2026-11-19 15:24:00', '2026-11-27 15:24:00', 1, 1, 1, 5, '2026-11-20 15:24:35', '2026-11-20 17:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `subtitle` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subtitle`)),
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `class_from` tinyint(3) UNSIGNED DEFAULT NULL,
  `class_to` tinyint(3) UNSIGNED DEFAULT NULL,
  `age_from` tinyint(3) UNSIGNED DEFAULT NULL,
  `age_to` tinyint(3) UNSIGNED DEFAULT NULL,
  `live_class_count` smallint(5) UNSIGNED DEFAULT NULL,
  `class_duration_minutes` smallint(5) UNSIGNED DEFAULT NULL,
  `duration_months` tinyint(3) UNSIGNED DEFAULT NULL,
  `learning_outcomes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_outcomes`)),
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlights`)),
  `syllabus` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`syllabus`)),
  `explainer_video_url` varchar(255) DEFAULT NULL,
  `thumbnail_url` text DEFAULT NULL,
  `refund_policy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`refund_policy`)),
  `seo_title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seo_title`)),
  `seo_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seo_description`)),
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `slug`, `program_id`, `title`, `subtitle`, `summary`, `description`, `class_from`, `class_to`, `age_from`, `age_to`, `live_class_count`, `class_duration_minutes`, `duration_months`, `learning_outcomes`, `highlights`, `syllabus`, `explainer_video_url`, `thumbnail_url`, `refund_policy`, `seo_title`, `seo_description`, `status`, `published_at`, `is_featured`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'math-olympiad-primary', 1, '{\"bn\":\"\\u09ae\\u09cd\\u09af\\u09be\\u09a5 \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u2014 \\u09aa\\u09cd\\u09b0\\u09be\\u0987\\u09ae\\u09be\\u09b0\\u09bf\",\"en\":\"Math Olympiad \\u2014 Primary\"}', NULL, '{\"bn\":\"\\u0985\\u09ad\\u09bf\\u099c\\u09cd\\u099e \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7 \\u09a7\\u09be\\u09b0\\u09a3\\u09be \\u09a5\\u09c7\\u0995\\u09c7 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8 \\u2014 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7\\u0964\",\"en\":\"From concepts to problem solving in live classes with experienced teachers.\"}', '{\"bn\":\"\\u098f\\u099f\\u09bf \\u09a1\\u09c7\\u09ae\\u09cb \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09ac\\u09bf\\u09ac\\u09b0\\u09a3\\u0964 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u2192 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u09ac\\u09bf\\u09ac\\u09b0\\u09a3, \\u09b8\\u09bf\\u09b2\\u09c7\\u09ac\\u09be\\u09b8 \\u0993 \\u09ad\\u09bf\\u09a1\\u09bf\\u0993 \\u09a6\\u09bf\\u09a8\\u0964\\n\\n\\u09aa\\u09cd\\u09b0\\u09a4\\u09bf \\u09b8\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09c7 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8, \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09b6\\u09c7\\u09b7\\u09c7 \\u0985\\u09a8\\u09c1\\u09b6\\u09c0\\u09b2\\u09a8 \\u0986\\u09b0 \\u09ae\\u09be\\u09b8\\u09bf\\u0995 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8\\u0964\",\"en\":\"Demo course description. Replace it from Admin \\u2192 Courses.\"}', 3, 5, 8, 11, 48, 60, 6, '{\"bn\":[\"\\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09ac\\u09c1\\u099d\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8\\u09c7\\u09b0 \\u0995\\u09cc\\u09b6\\u09b2\",\"\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0993 \\u09ac\\u09bf\\u09b6\\u09cd\\u09b2\\u09c7\\u09b7\\u09a3\\u09c0 \\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09be\",\"\\u09b8\\u09ae\\u09af\\u09bc \\u09a7\\u09b0\\u09c7 \\u09aa\\u09b0\\u09c0\\u0995\\u09cd\\u09b7\\u09be \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be\\u09b0 \\u0985\\u09ad\\u09cd\\u09af\\u09be\\u09b8\",\"\\u09ad\\u09c1\\u09b2 \\u09a5\\u09c7\\u0995\\u09c7 \\u09b6\\u09c7\\u0996\\u09be\\u09b0 \\u0986\\u09a4\\u09cd\\u09ae\\u09ac\\u09bf\\u09b6\\u09cd\\u09ac\\u09be\\u09b8\"],\"en\":[\"Step-by-step problem-solving strategies\",\"Logical and analytical thinking\",\"Timed exam practice\",\"Confidence to learn from mistakes\"]}', '{\"bn\":[\"\\u09b8\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09c7 \\u09e8\\u099f\\u09bf \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8\",\"\\u09aa\\u09cd\\u09b0\\u09a4\\u09bf \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7\\u09b0 \\u09b0\\u09c7\\u0995\\u09b0\\u09cd\\u09a1\\u09bf\\u0982\",\"\\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09b6\\u09c7\\u09b7\\u09c7 PDF \\u09b2\\u09c7\\u0995\\u099a\\u09be\\u09b0 \\u09b6\\u09bf\\u099f\",\"\\u09ae\\u09be\\u09b8\\u09bf\\u0995 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8 \\u0993 \\u0985\\u09ad\\u09bf\\u09ad\\u09be\\u09ac\\u0995 \\u09b0\\u09bf\\u09aa\\u09cb\\u09b0\\u09cd\\u099f\"],\"en\":[\"2 live classes a week\",\"Recording of every class\",\"PDF lecture sheet after each class\",\"Monthly assessment and parent report\"]}', '{\"bn\":[\"\\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be \\u0993 \\u09aa\\u09cd\\u09af\\u09be\\u099f\\u09be\\u09b0\\u09cd\\u09a8: \\u09ae\\u09cc\\u09b2\\u09bf\\u0995 \\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be, \\u09ad\\u09be\\u099c\\u09cd\\u09af\\u09a4\\u09be, \\u09a7\\u09be\\u09b0\\u09be\",\"\\u099c\\u09cd\\u09af\\u09be\\u09ae\\u09bf\\u09a4\\u09bf: \\u0995\\u09cb\\u09a3, \\u09a4\\u09cd\\u09b0\\u09bf\\u09ad\\u09c1\\u099c, \\u0995\\u09cd\\u09b7\\u09c7\\u09a4\\u09cd\\u09b0\\u09ab\\u09b2\",\"\\u0997\\u09a3\\u09a8\\u09be \\u0993 \\u09b8\\u09ae\\u09cd\\u09ad\\u09be\\u09ac\\u09a8\\u09be: \\u09ac\\u09bf\\u09a8\\u09cd\\u09af\\u09be\\u09b8, \\u09b8\\u09ae\\u09be\\u09ac\\u09c7\\u09b6\",\"\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0993 \\u09a7\\u09be\\u0981\\u09a7\\u09be: \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u09a7\\u09b0\\u09a8\\u09c7\\u09b0 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be\"],\"en\":[\"Numbers & patterns: primes, divisibility, sequences\",\"Geometry: angles, triangles, area\",\"Counting & probability\",\"Logic & puzzles: olympiad-style problems\"]}', 'https://youtu.be/aqz-KE-bpKQ', NULL, NULL, NULL, NULL, 'published', '2026-11-19 16:06:30', 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(2, 'math-olympiad-junior', 1, '{\"bn\":\"\\u09ae\\u09cd\\u09af\\u09be\\u09a5 \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u2014 \\u099c\\u09c1\\u09a8\\u09bf\\u09af\\u09bc\\u09b0\",\"en\":\"Math Olympiad \\u2014 Junior\"}', NULL, '{\"bn\":\"\\u0985\\u09ad\\u09bf\\u099c\\u09cd\\u099e \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7 \\u09a7\\u09be\\u09b0\\u09a3\\u09be \\u09a5\\u09c7\\u0995\\u09c7 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8 \\u2014 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7\\u0964\",\"en\":\"From concepts to problem solving in live classes with experienced teachers.\"}', '{\"bn\":\"\\u098f\\u099f\\u09bf \\u09a1\\u09c7\\u09ae\\u09cb \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09ac\\u09bf\\u09ac\\u09b0\\u09a3\\u0964 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u2192 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u09ac\\u09bf\\u09ac\\u09b0\\u09a3, \\u09b8\\u09bf\\u09b2\\u09c7\\u09ac\\u09be\\u09b8 \\u0993 \\u09ad\\u09bf\\u09a1\\u09bf\\u0993 \\u09a6\\u09bf\\u09a8\\u0964\\n\\n\\u09aa\\u09cd\\u09b0\\u09a4\\u09bf \\u09b8\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09c7 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8, \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09b6\\u09c7\\u09b7\\u09c7 \\u0985\\u09a8\\u09c1\\u09b6\\u09c0\\u09b2\\u09a8 \\u0986\\u09b0 \\u09ae\\u09be\\u09b8\\u09bf\\u0995 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8\\u0964\",\"en\":\"Demo course description. Replace it from Admin \\u2192 Courses.\"}', 6, 8, 11, 14, 48, 60, 6, '{\"bn\":[\"\\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09ac\\u09c1\\u099d\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8\\u09c7\\u09b0 \\u0995\\u09cc\\u09b6\\u09b2\",\"\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0993 \\u09ac\\u09bf\\u09b6\\u09cd\\u09b2\\u09c7\\u09b7\\u09a3\\u09c0 \\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09be\",\"\\u09b8\\u09ae\\u09af\\u09bc \\u09a7\\u09b0\\u09c7 \\u09aa\\u09b0\\u09c0\\u0995\\u09cd\\u09b7\\u09be \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be\\u09b0 \\u0985\\u09ad\\u09cd\\u09af\\u09be\\u09b8\",\"\\u09ad\\u09c1\\u09b2 \\u09a5\\u09c7\\u0995\\u09c7 \\u09b6\\u09c7\\u0996\\u09be\\u09b0 \\u0986\\u09a4\\u09cd\\u09ae\\u09ac\\u09bf\\u09b6\\u09cd\\u09ac\\u09be\\u09b8\"],\"en\":[\"Step-by-step problem-solving strategies\",\"Logical and analytical thinking\",\"Timed exam practice\",\"Confidence to learn from mistakes\"]}', '{\"bn\":[\"\\u09b8\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09c7 \\u09e8\\u099f\\u09bf \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8\",\"\\u09aa\\u09cd\\u09b0\\u09a4\\u09bf \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7\\u09b0 \\u09b0\\u09c7\\u0995\\u09b0\\u09cd\\u09a1\\u09bf\\u0982\",\"\\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09b6\\u09c7\\u09b7\\u09c7 PDF \\u09b2\\u09c7\\u0995\\u099a\\u09be\\u09b0 \\u09b6\\u09bf\\u099f\",\"\\u09ae\\u09be\\u09b8\\u09bf\\u0995 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8 \\u0993 \\u0985\\u09ad\\u09bf\\u09ad\\u09be\\u09ac\\u0995 \\u09b0\\u09bf\\u09aa\\u09cb\\u09b0\\u09cd\\u099f\"],\"en\":[\"2 live classes a week\",\"Recording of every class\",\"PDF lecture sheet after each class\",\"Monthly assessment and parent report\"]}', '{\"bn\":[\"\\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be \\u0993 \\u09aa\\u09cd\\u09af\\u09be\\u099f\\u09be\\u09b0\\u09cd\\u09a8: \\u09ae\\u09cc\\u09b2\\u09bf\\u0995 \\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be, \\u09ad\\u09be\\u099c\\u09cd\\u09af\\u09a4\\u09be, \\u09a7\\u09be\\u09b0\\u09be\",\"\\u099c\\u09cd\\u09af\\u09be\\u09ae\\u09bf\\u09a4\\u09bf: \\u0995\\u09cb\\u09a3, \\u09a4\\u09cd\\u09b0\\u09bf\\u09ad\\u09c1\\u099c, \\u0995\\u09cd\\u09b7\\u09c7\\u09a4\\u09cd\\u09b0\\u09ab\\u09b2\",\"\\u0997\\u09a3\\u09a8\\u09be \\u0993 \\u09b8\\u09ae\\u09cd\\u09ad\\u09be\\u09ac\\u09a8\\u09be: \\u09ac\\u09bf\\u09a8\\u09cd\\u09af\\u09be\\u09b8, \\u09b8\\u09ae\\u09be\\u09ac\\u09c7\\u09b6\",\"\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0993 \\u09a7\\u09be\\u0981\\u09a7\\u09be: \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u09a7\\u09b0\\u09a8\\u09c7\\u09b0 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be\"],\"en\":[\"Numbers & patterns: primes, divisibility, sequences\",\"Geometry: angles, triangles, area\",\"Counting & probability\",\"Logic & puzzles: olympiad-style problems\"]}', 'https://youtu.be/aqz-KE-bpKQ', NULL, NULL, NULL, NULL, 'published', '2026-11-19 16:06:30', 1, 1, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(3, 'class-9-10-science', 2, '{\"bn\":\"\\u09ef\\u09ae\\u2013\\u09e7\\u09e6\\u09ae \\u09ac\\u09bf\\u099c\\u09cd\\u099e\\u09be\\u09a8 (\\u09b8\\u09be\\u09a7\\u09be\\u09b0\\u09a3 \\u0997\\u09a3\\u09bf\\u09a4\\u09b8\\u09b9)\",\"en\":\"Class 9\\u201310 Science (with General Math)\"}', NULL, '{\"bn\":\"\\u0985\\u09ad\\u09bf\\u099c\\u09cd\\u099e \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7 \\u09a7\\u09be\\u09b0\\u09a3\\u09be \\u09a5\\u09c7\\u0995\\u09c7 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8 \\u2014 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7\\u0964\",\"en\":\"From concepts to problem solving in live classes with experienced teachers.\"}', '{\"bn\":\"\\u098f\\u099f\\u09bf \\u09a1\\u09c7\\u09ae\\u09cb \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09ac\\u09bf\\u09ac\\u09b0\\u09a3\\u0964 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u2192 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u09ac\\u09bf\\u09ac\\u09b0\\u09a3, \\u09b8\\u09bf\\u09b2\\u09c7\\u09ac\\u09be\\u09b8 \\u0993 \\u09ad\\u09bf\\u09a1\\u09bf\\u0993 \\u09a6\\u09bf\\u09a8\\u0964\\n\\n\\u09aa\\u09cd\\u09b0\\u09a4\\u09bf \\u09b8\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09c7 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8, \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09b6\\u09c7\\u09b7\\u09c7 \\u0985\\u09a8\\u09c1\\u09b6\\u09c0\\u09b2\\u09a8 \\u0986\\u09b0 \\u09ae\\u09be\\u09b8\\u09bf\\u0995 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8\\u0964\",\"en\":\"Demo course description. Replace it from Admin \\u2192 Courses.\"}', 9, 10, 14, 16, 48, 60, 6, '{\"bn\":[\"\\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09ac\\u09c1\\u099d\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8\\u09c7\\u09b0 \\u0995\\u09cc\\u09b6\\u09b2\",\"\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0993 \\u09ac\\u09bf\\u09b6\\u09cd\\u09b2\\u09c7\\u09b7\\u09a3\\u09c0 \\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09be\",\"\\u09b8\\u09ae\\u09af\\u09bc \\u09a7\\u09b0\\u09c7 \\u09aa\\u09b0\\u09c0\\u0995\\u09cd\\u09b7\\u09be \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be\\u09b0 \\u0985\\u09ad\\u09cd\\u09af\\u09be\\u09b8\",\"\\u09ad\\u09c1\\u09b2 \\u09a5\\u09c7\\u0995\\u09c7 \\u09b6\\u09c7\\u0996\\u09be\\u09b0 \\u0986\\u09a4\\u09cd\\u09ae\\u09ac\\u09bf\\u09b6\\u09cd\\u09ac\\u09be\\u09b8\"],\"en\":[\"Step-by-step problem-solving strategies\",\"Logical and analytical thinking\",\"Timed exam practice\",\"Confidence to learn from mistakes\"]}', '{\"bn\":[\"\\u09b8\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09c7 \\u09e8\\u099f\\u09bf \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8\",\"\\u09aa\\u09cd\\u09b0\\u09a4\\u09bf \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7\\u09b0 \\u09b0\\u09c7\\u0995\\u09b0\\u09cd\\u09a1\\u09bf\\u0982\",\"\\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09b6\\u09c7\\u09b7\\u09c7 PDF \\u09b2\\u09c7\\u0995\\u099a\\u09be\\u09b0 \\u09b6\\u09bf\\u099f\",\"\\u09ae\\u09be\\u09b8\\u09bf\\u0995 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8 \\u0993 \\u0985\\u09ad\\u09bf\\u09ad\\u09be\\u09ac\\u0995 \\u09b0\\u09bf\\u09aa\\u09cb\\u09b0\\u09cd\\u099f\"],\"en\":[\"2 live classes a week\",\"Recording of every class\",\"PDF lecture sheet after each class\",\"Monthly assessment and parent report\"]}', '{\"bn\":[\"\\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be \\u0993 \\u09aa\\u09cd\\u09af\\u09be\\u099f\\u09be\\u09b0\\u09cd\\u09a8: \\u09ae\\u09cc\\u09b2\\u09bf\\u0995 \\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be, \\u09ad\\u09be\\u099c\\u09cd\\u09af\\u09a4\\u09be, \\u09a7\\u09be\\u09b0\\u09be\",\"\\u099c\\u09cd\\u09af\\u09be\\u09ae\\u09bf\\u09a4\\u09bf: \\u0995\\u09cb\\u09a3, \\u09a4\\u09cd\\u09b0\\u09bf\\u09ad\\u09c1\\u099c, \\u0995\\u09cd\\u09b7\\u09c7\\u09a4\\u09cd\\u09b0\\u09ab\\u09b2\",\"\\u0997\\u09a3\\u09a8\\u09be \\u0993 \\u09b8\\u09ae\\u09cd\\u09ad\\u09be\\u09ac\\u09a8\\u09be: \\u09ac\\u09bf\\u09a8\\u09cd\\u09af\\u09be\\u09b8, \\u09b8\\u09ae\\u09be\\u09ac\\u09c7\\u09b6\",\"\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0993 \\u09a7\\u09be\\u0981\\u09a7\\u09be: \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u09a7\\u09b0\\u09a8\\u09c7\\u09b0 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be\"],\"en\":[\"Numbers & patterns: primes, divisibility, sequences\",\"Geometry: angles, triangles, area\",\"Counting & probability\",\"Logic & puzzles: olympiad-style problems\"]}', 'https://youtu.be/aqz-KE-bpKQ', NULL, NULL, NULL, NULL, 'published', '2026-11-19 16:06:30', 1, 2, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(4, 'class-9-10-business-studies', 2, '{\"bn\":\"\\u09ef\\u09ae\\u2013\\u09e7\\u09e6\\u09ae \\u09ac\\u09cd\\u09af\\u09ac\\u09b8\\u09be\\u09af\\u09bc \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u09be (\\u09b8\\u09be\\u09a7\\u09be\\u09b0\\u09a3 \\u0997\\u09a3\\u09bf\\u09a4\\u09b8\\u09b9)\",\"en\":\"Class 9\\u201310 Business Studies (with General Math)\"}', NULL, '{\"bn\":\"\\u0985\\u09ad\\u09bf\\u099c\\u09cd\\u099e \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7 \\u09a7\\u09be\\u09b0\\u09a3\\u09be \\u09a5\\u09c7\\u0995\\u09c7 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8 \\u2014 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7\\u0964\",\"en\":\"From concepts to problem solving in live classes with experienced teachers.\"}', '{\"bn\":\"\\u098f\\u099f\\u09bf \\u09a1\\u09c7\\u09ae\\u09cb \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09ac\\u09bf\\u09ac\\u09b0\\u09a3\\u0964 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u2192 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u09ac\\u09bf\\u09ac\\u09b0\\u09a3, \\u09b8\\u09bf\\u09b2\\u09c7\\u09ac\\u09be\\u09b8 \\u0993 \\u09ad\\u09bf\\u09a1\\u09bf\\u0993 \\u09a6\\u09bf\\u09a8\\u0964\\n\\n\\u09aa\\u09cd\\u09b0\\u09a4\\u09bf \\u09b8\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09c7 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8, \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09b6\\u09c7\\u09b7\\u09c7 \\u0985\\u09a8\\u09c1\\u09b6\\u09c0\\u09b2\\u09a8 \\u0986\\u09b0 \\u09ae\\u09be\\u09b8\\u09bf\\u0995 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8\\u0964\",\"en\":\"Demo course description. Replace it from Admin \\u2192 Courses.\"}', 9, 10, 14, 16, 48, 60, 6, '{\"bn\":[\"\\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09ac\\u09c1\\u099d\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8\\u09c7\\u09b0 \\u0995\\u09cc\\u09b6\\u09b2\",\"\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0993 \\u09ac\\u09bf\\u09b6\\u09cd\\u09b2\\u09c7\\u09b7\\u09a3\\u09c0 \\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09be\",\"\\u09b8\\u09ae\\u09af\\u09bc \\u09a7\\u09b0\\u09c7 \\u09aa\\u09b0\\u09c0\\u0995\\u09cd\\u09b7\\u09be \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be\\u09b0 \\u0985\\u09ad\\u09cd\\u09af\\u09be\\u09b8\",\"\\u09ad\\u09c1\\u09b2 \\u09a5\\u09c7\\u0995\\u09c7 \\u09b6\\u09c7\\u0996\\u09be\\u09b0 \\u0986\\u09a4\\u09cd\\u09ae\\u09ac\\u09bf\\u09b6\\u09cd\\u09ac\\u09be\\u09b8\"],\"en\":[\"Step-by-step problem-solving strategies\",\"Logical and analytical thinking\",\"Timed exam practice\",\"Confidence to learn from mistakes\"]}', '{\"bn\":[\"\\u09b8\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09c7 \\u09e8\\u099f\\u09bf \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8\",\"\\u09aa\\u09cd\\u09b0\\u09a4\\u09bf \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7\\u09b0 \\u09b0\\u09c7\\u0995\\u09b0\\u09cd\\u09a1\\u09bf\\u0982\",\"\\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09b6\\u09c7\\u09b7\\u09c7 PDF \\u09b2\\u09c7\\u0995\\u099a\\u09be\\u09b0 \\u09b6\\u09bf\\u099f\",\"\\u09ae\\u09be\\u09b8\\u09bf\\u0995 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8 \\u0993 \\u0985\\u09ad\\u09bf\\u09ad\\u09be\\u09ac\\u0995 \\u09b0\\u09bf\\u09aa\\u09cb\\u09b0\\u09cd\\u099f\"],\"en\":[\"2 live classes a week\",\"Recording of every class\",\"PDF lecture sheet after each class\",\"Monthly assessment and parent report\"]}', '{\"bn\":[\"\\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be \\u0993 \\u09aa\\u09cd\\u09af\\u09be\\u099f\\u09be\\u09b0\\u09cd\\u09a8: \\u09ae\\u09cc\\u09b2\\u09bf\\u0995 \\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be, \\u09ad\\u09be\\u099c\\u09cd\\u09af\\u09a4\\u09be, \\u09a7\\u09be\\u09b0\\u09be\",\"\\u099c\\u09cd\\u09af\\u09be\\u09ae\\u09bf\\u09a4\\u09bf: \\u0995\\u09cb\\u09a3, \\u09a4\\u09cd\\u09b0\\u09bf\\u09ad\\u09c1\\u099c, \\u0995\\u09cd\\u09b7\\u09c7\\u09a4\\u09cd\\u09b0\\u09ab\\u09b2\",\"\\u0997\\u09a3\\u09a8\\u09be \\u0993 \\u09b8\\u09ae\\u09cd\\u09ad\\u09be\\u09ac\\u09a8\\u09be: \\u09ac\\u09bf\\u09a8\\u09cd\\u09af\\u09be\\u09b8, \\u09b8\\u09ae\\u09be\\u09ac\\u09c7\\u09b6\",\"\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0993 \\u09a7\\u09be\\u0981\\u09a7\\u09be: \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u09a7\\u09b0\\u09a8\\u09c7\\u09b0 \\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be\"],\"en\":[\"Numbers & patterns: primes, divisibility, sequences\",\"Geometry: angles, triangles, area\",\"Counting & probability\",\"Logic & puzzles: olympiad-style problems\"]}', 'https://youtu.be/aqz-KE-bpKQ', NULL, NULL, NULL, NULL, 'published', '2026-11-19 16:06:30', 1, 3, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_batches`
--

CREATE TABLE `course_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(30) NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`name`)),
  `class_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`class_days`)),
  `class_start_time` time DEFAULT NULL,
  `class_end_time` time DEFAULT NULL,
  `orientation_at` datetime DEFAULT NULL,
  `starts_on` date DEFAULT NULL,
  `ends_on` date DEFAULT NULL,
  `enrollment_deadline` date DEFAULT NULL,
  `seat_limit` smallint(5) UNSIGNED DEFAULT NULL,
  `seats_taken` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'upcoming',
  `meeting_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_batches`
--

INSERT INTO `course_batches` (`id`, `course_id`, `code`, `name`, `class_days`, `class_start_time`, `class_end_time`, `orientation_at`, `starts_on`, `ends_on`, `enrollment_deadline`, `seat_limit`, `seats_taken`, `status`, `meeting_url`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'MAT-101', '{\"bn\":\"\\u09ac\\u09bf\\u0995\\u09c7\\u09b2\\u09c7\\u09b0 \\u09ac\\u09cd\\u09af\\u09be\\u099a\",\"en\":\"Afternoon batch\"}', '[\"sat\",\"mon\",\"wed\"]', '16:30:00', '17:30:00', NULL, '2026-11-29', NULL, '2026-11-28', 30, 12, 'enrolling', NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(2, 1, 'MAT-102', '{\"bn\":\"\\u09b8\\u09a8\\u09cd\\u09a7\\u09cd\\u09af\\u09be\\u09b0 \\u09ac\\u09cd\\u09af\\u09be\\u099a\",\"en\":\"Evening batch\"}', '[\"sun\",\"tue\",\"thu\"]', '19:00:00', '20:00:00', NULL, '2026-12-13', NULL, '2026-12-11', 30, 4, 'upcoming', NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(3, 2, 'MAT-201', '{\"bn\":\"\\u09ac\\u09bf\\u0995\\u09c7\\u09b2\\u09c7\\u09b0 \\u09ac\\u09cd\\u09af\\u09be\\u099a\",\"en\":\"Afternoon batch\"}', '[\"sat\",\"mon\",\"wed\"]', '16:30:00', '17:30:00', NULL, '2026-11-29', NULL, '2026-11-28', 30, 12, 'enrolling', NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(4, 2, 'MAT-202', '{\"bn\":\"\\u09b8\\u09a8\\u09cd\\u09a7\\u09cd\\u09af\\u09be\\u09b0 \\u09ac\\u09cd\\u09af\\u09be\\u099a\",\"en\":\"Evening batch\"}', '[\"sun\",\"tue\",\"thu\"]', '19:00:00', '20:00:00', NULL, '2026-12-13', NULL, '2026-12-11', 30, 4, 'upcoming', NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(5, 3, 'CLA-301', '{\"bn\":\"\\u09ac\\u09bf\\u0995\\u09c7\\u09b2\\u09c7\\u09b0 \\u09ac\\u09cd\\u09af\\u09be\\u099a\",\"en\":\"Afternoon batch\"}', '[\"sat\",\"mon\",\"wed\"]', '16:30:00', '17:30:00', NULL, '2026-11-29', NULL, '2026-11-28', 30, 12, 'enrolling', NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(6, 3, 'CLA-302', '{\"bn\":\"\\u09b8\\u09a8\\u09cd\\u09a7\\u09cd\\u09af\\u09be\\u09b0 \\u09ac\\u09cd\\u09af\\u09be\\u099a\",\"en\":\"Evening batch\"}', '[\"sun\",\"tue\",\"thu\"]', '19:00:00', '20:00:00', NULL, '2026-12-13', NULL, '2026-12-11', 30, 4, 'upcoming', NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(7, 4, 'CLA-401', '{\"bn\":\"\\u09ac\\u09bf\\u0995\\u09c7\\u09b2\\u09c7\\u09b0 \\u09ac\\u09cd\\u09af\\u09be\\u099a\",\"en\":\"Afternoon batch\"}', '[\"sat\",\"mon\",\"wed\"]', '16:30:00', '17:30:00', NULL, '2026-11-29', NULL, '2026-11-28', 30, 12, 'enrolling', NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(8, 4, 'CLA-402', '{\"bn\":\"\\u09b8\\u09a8\\u09cd\\u09a7\\u09cd\\u09af\\u09be\\u09b0 \\u09ac\\u09cd\\u09af\\u09be\\u099a\",\"en\":\"Evening batch\"}', '[\"sun\",\"tue\",\"thu\"]', '19:00:00', '20:00:00', NULL, '2026-12-13', NULL, '2026-12-11', 30, 4, 'upcoming', NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_teacher`
--

CREATE TABLE `course_teacher` (
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_teacher`
--

INSERT INTO `course_teacher` (`course_id`, `teacher_id`, `sort_order`) VALUES
(1, 1, 0),
(2, 1, 0),
(3, 2, 0),
(4, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `course_variations`
--

CREATE TABLE `course_variations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `label` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`label`)),
  `duration_months` tinyint(3) UNSIGNED DEFAULT NULL,
  `billing_type` varchar(20) NOT NULL DEFAULT 'one_time',
  `regular_price` int(10) UNSIGNED NOT NULL,
  `discounted_price` int(10) UNSIGNED DEFAULT NULL,
  `badge` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`badge`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_variations`
--

INSERT INTO `course_variations` (`id`, `course_id`, `label`, `duration_months`, `billing_type`, `regular_price`, `discounted_price`, `badge`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, '{\"bn\":\"\\u09ae\\u09be\\u09b8\\u09bf\\u0995\",\"en\":\"Monthly\"}', 1, 'monthly', 1500, NULL, NULL, 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(2, 1, '{\"bn\":\"\\u09e9 \\u09ae\\u09be\\u09b8\",\"en\":\"3 months\"}', 3, 'one_time', 4500, 3900, NULL, 1, 1, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(3, 1, '{\"bn\":\"\\u09ab\\u09c1\\u09b2 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 (\\u09ec \\u09ae\\u09be\\u09b8)\",\"en\":\"Full course (6 months)\"}', 6, 'one_time', 9000, 6900, '{\"bn\":\"\\u09b8\\u09c7\\u09b0\\u09be \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\",\"en\":\"Best value\"}', 1, 2, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(4, 2, '{\"bn\":\"\\u09ae\\u09be\\u09b8\\u09bf\\u0995\",\"en\":\"Monthly\"}', 1, 'monthly', 1500, NULL, NULL, 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(5, 2, '{\"bn\":\"\\u09e9 \\u09ae\\u09be\\u09b8\",\"en\":\"3 months\"}', 3, 'one_time', 4500, 3900, NULL, 1, 1, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(6, 2, '{\"bn\":\"\\u09ab\\u09c1\\u09b2 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 (\\u09ec \\u09ae\\u09be\\u09b8)\",\"en\":\"Full course (6 months)\"}', 6, 'one_time', 9000, 6900, '{\"bn\":\"\\u09b8\\u09c7\\u09b0\\u09be \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\",\"en\":\"Best value\"}', 1, 2, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(7, 3, '{\"bn\":\"\\u09ae\\u09be\\u09b8\\u09bf\\u0995\",\"en\":\"Monthly\"}', 1, 'monthly', 1500, NULL, NULL, 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(8, 3, '{\"bn\":\"\\u09e9 \\u09ae\\u09be\\u09b8\",\"en\":\"3 months\"}', 3, 'one_time', 4500, 3900, NULL, 1, 1, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(9, 3, '{\"bn\":\"\\u09ab\\u09c1\\u09b2 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 (\\u09ec \\u09ae\\u09be\\u09b8)\",\"en\":\"Full course (6 months)\"}', 6, 'one_time', 9000, 6900, '{\"bn\":\"\\u09b8\\u09c7\\u09b0\\u09be \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\",\"en\":\"Best value\"}', 1, 2, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(10, 4, '{\"bn\":\"\\u09ae\\u09be\\u09b8\\u09bf\\u0995\",\"en\":\"Monthly\"}', 1, 'monthly', 1500, NULL, NULL, 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(11, 4, '{\"bn\":\"\\u09e9 \\u09ae\\u09be\\u09b8\",\"en\":\"3 months\"}', 3, 'one_time', 4500, 3900, NULL, 1, 1, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(12, 4, '{\"bn\":\"\\u09ab\\u09c1\\u09b2 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 (\\u09ec \\u09ae\\u09be\\u09b8)\",\"en\":\"Full course (6 months)\"}', 6, 'one_time', 9000, 6900, '{\"bn\":\"\\u09b8\\u09c7\\u09b0\\u09be \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\",\"en\":\"Best value\"}', 1, 2, '2026-11-19 16:06:30', '2026-11-19 16:06:30');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_zones`
--

CREATE TABLE `delivery_zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `charge` int(10) UNSIGNED NOT NULL,
  `districts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`districts`)),
  `estimated_days` varchar(30) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_zones`
--

INSERT INTO `delivery_zones` (`id`, `name`, `charge`, `districts`, `estimated_days`, `is_default`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, '{\"bn\":\"\\u09a2\\u09be\\u0995\\u09be\\u09b0 \\u09ad\\u09c7\\u09a4\\u09b0\\u09c7\",\"en\":\"Inside Dhaka\"}', 70, '[\"Dhaka\"]', '1-2', 0, 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(2, '{\"bn\":\"\\u09a2\\u09be\\u0995\\u09be\\u09b0 \\u09ac\\u09be\\u0987\\u09b0\\u09c7\",\"en\":\"Outside Dhaka\"}', 120, '[]', '3-5', 1, 1, 1, '2026-11-19 16:06:30', '2026-11-19 16:06:30');

-- --------------------------------------------------------

--
-- Table structure for table `developer_query_logs`
--

CREATE TABLE `developer_query_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `statement` text NOT NULL,
  `is_write` tinyint(1) NOT NULL DEFAULT 0,
  `affected_rows` int(11) DEFAULT NULL,
  `duration_ms` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `error` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ulid` char(26) NOT NULL,
  `reference` varchar(30) NOT NULL,
  `guardian_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `course_batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_variation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sold_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending_payment',
  `list_price` int(10) UNSIGNED NOT NULL,
  `discount_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `payable_amount` int(10) UNSIGNED NOT NULL,
  `refund_policy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`refund_policy`)),
  `first_class_at` datetime DEFAULT NULL,
  `refund_eligible_until` datetime DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_level` tinyint(3) UNSIGNED DEFAULT NULL,
  `duration_minutes` smallint(5) UNSIGNED NOT NULL,
  `question_count` smallint(5) UNSIGNED NOT NULL,
  `results_at` timestamp NULL DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `slug`, `title`, `description`, `subject_id`, `class_level`, `duration_minutes`, `question_count`, `results_at`, `is_published`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'demo-weekly-math', '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb: \\u09b8\\u09be\\u09aa\\u09cd\\u09a4\\u09be\\u09b9\\u09bf\\u0995 \\u0997\\u09a3\\u09bf\\u09a4 \\u09aa\\u09b0\\u09c0\\u0995\\u09cd\\u09b7\\u09be\",\"en\":\"Demo: weekly maths test\"}', NULL, 1, NULL, 5, 4, NULL, 1, 0, '2026-11-20 16:01:51', '2026-11-20 16:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ulid` char(26) NOT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `question_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`question_ids`)),
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `duration_minutes` smallint(5) UNSIGNED NOT NULL,
  `started_at` datetime NOT NULL,
  `deadline_at` datetime NOT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `correct_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `wrong_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `skipped_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_attempts`
--

INSERT INTO `exam_attempts` (`id`, `ulid`, `exam_id`, `lead_id`, `student_name`, `phone`, `settings`, `question_ids`, `answers`, `duration_minutes`, `started_at`, `deadline_at`, `submitted_at`, `correct_count`, `wrong_count`, `skipped_count`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, '01m7wv31r8fejjr4m19jnwez7j', 1, NULL, 'ডেমো শিক্ষার্থী', NULL, NULL, '[1,2,3,4]', NULL, 5, '2026-11-20 22:02:03', '2026-11-20 22:07:03', NULL, 0, 0, 0, NULL, '2026-11-20 16:02:03', '2026-11-20 16:02:03'),
(2, '01m7wv31rdq6h2tahsb5gkp0rs', 1, NULL, 'ডেমো শিক্ষার্থী', NULL, NULL, '[1,2,3,4]', '{\"1\":1,\"2\":0,\"3\":2}', 5, '2026-11-20 22:02:03', '2026-11-20 22:07:03', '2026-11-20 16:02:03', 2, 1, 1, NULL, '2026-11-20 16:02:03', '2026-11-20 16:02:03');

-- --------------------------------------------------------

--
-- Table structure for table `exam_question`
--

CREATE TABLE `exam_question` (
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_question`
--

INSERT INTO `exam_question` (`exam_id`, `question_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4);

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
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faqable_type` varchar(255) DEFAULT NULL,
  `faqable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`question`)),
  `answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`answer`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `faqable_type`, `faqable_id`, `question`, `answer`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, '{\"bn\":\"\\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u0997\\u09c1\\u09b2\\u09cb \\u0995\\u09c0\\u09ad\\u09be\\u09ac\\u09c7 \\u09b9\\u09af\\u09bc?\"}', '{\"bn\":\"\\u09b8\\u09ac \\u0995\\u09cd\\u09b2\\u09be\\u09b8 \\u0985\\u09a8\\u09b2\\u09be\\u0987\\u09a8\\u09c7 \\u09b2\\u09be\\u0987\\u09ad \\u09b9\\u09af\\u09bc\\u0964 \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7\\u09b0 \\u09b2\\u09bf\\u0982\\u0995 \\u09ad\\u09b0\\u09cd\\u09a4\\u09bf\\u09b0 \\u09aa\\u09b0 \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be \\u09b9\\u09af\\u09bc, \\u0986\\u09b0 \\u09aa\\u09cd\\u09b0\\u09a4\\u09bf\\u099f\\u09bf \\u0995\\u09cd\\u09b2\\u09be\\u09b8\\u09c7\\u09b0 \\u09b0\\u09c7\\u0995\\u09b0\\u09cd\\u09a1\\u09bf\\u0982\\u0993 \\u09aa\\u09be\\u0993\\u09af\\u09bc\\u09be \\u09af\\u09be\\u09af\\u09bc\\u0964\"}', 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(2, NULL, NULL, '{\"bn\":\"\\u09ab\\u09cd\\u09b0\\u09bf \\u0985\\u09cd\\u09af\\u09be\\u09b8\\u09c7\\u09b8\\u09ae\\u09c7\\u09a8\\u09cd\\u099f \\u0995\\u09c0?\"}', '{\"bn\":\"\\u09ad\\u09b0\\u09cd\\u09a4\\u09bf\\u09b0 \\u0986\\u0997\\u09c7 \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09b8\\u09be\\u09a5\\u09c7 \\u098f\\u0995\\u099f\\u09bf \\u09e7:\\u09e7 \\u09b8\\u09c7\\u09b6\\u09a8, \\u09af\\u09c7\\u0996\\u09be\\u09a8\\u09c7 \\u0986\\u09aa\\u09a8\\u09be\\u09b0 \\u09b8\\u09a8\\u09cd\\u09a4\\u09be\\u09a8\\u09c7\\u09b0 \\u09ac\\u09b0\\u09cd\\u09a4\\u09ae\\u09be\\u09a8 \\u0985\\u09ac\\u09b8\\u09cd\\u09a5\\u09be \\u09ac\\u09cb\\u099d\\u09be \\u09af\\u09be\\u09af\\u09bc \\u0993 \\u09b8\\u09a0\\u09bf\\u0995 \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09b8\\u09be\\u099c\\u09c7\\u09b8\\u09cd\\u099f \\u0995\\u09b0\\u09be \\u09b9\\u09af\\u09bc\\u0964 \\u098f\\u099f\\u09bf \\u09b8\\u09ae\\u09cd\\u09aa\\u09c2\\u09b0\\u09cd\\u09a3 \\u09ab\\u09cd\\u09b0\\u09bf \\u0993 \\u0990\\u099a\\u09cd\\u099b\\u09bf\\u0995\\u0964\"}', 1, 1, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(3, NULL, NULL, '{\"bn\":\"\\u09aa\\u09c7\\u09ae\\u09c7\\u09a8\\u09cd\\u099f \\u0995\\u09c0\\u09ad\\u09be\\u09ac\\u09c7 \\u0995\\u09b0\\u09ac?\"}', '{\"bn\":\"\\u09ac\\u09bf\\u0995\\u09be\\u09b6, \\u09a8\\u0997\\u09a6, \\u0989\\u09aa\\u09be\\u09af\\u09bc \\u09ac\\u09be \\u09ac\\u09cd\\u09af\\u09be\\u0982\\u0995 \\u099f\\u09cd\\u09b0\\u09be\\u09a8\\u09cd\\u09b8\\u09ab\\u09be\\u09b0\\u09c7 \\u09aa\\u09c7\\u09ae\\u09c7\\u09a8\\u09cd\\u099f \\u0995\\u09b0\\u09c7 \\u099f\\u09cd\\u09b0\\u09be\\u09a8\\u099c\\u09c7\\u0995\\u09b6\\u09a8 \\u0986\\u0987\\u09a1\\u09bf \\u099c\\u09ae\\u09be \\u09a6\\u09bf\\u09a8\\u0964 \\u0986\\u09ae\\u09be\\u09a6\\u09c7\\u09b0 \\u099f\\u09bf\\u09ae \\u09af\\u09be\\u099a\\u09be\\u0987 \\u0995\\u09b0\\u09c7 \\u0995\\u09a8\\u09ab\\u09be\\u09b0\\u09cd\\u09ae \\u0995\\u09b0\\u09ac\\u09c7\\u0964\"}', 1, 2, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(4, NULL, NULL, '{\"bn\":\"\\u09aa\\u099b\\u09a8\\u09cd\\u09a6 \\u09a8\\u09be \\u09b9\\u09b2\\u09c7 \\u0995\\u09c0 \\u09b9\\u09ac\\u09c7?\"}', '{\"bn\":\"\\u09aa\\u09cd\\u09b0\\u09a5\\u09ae \\u0995\\u09cd\\u09b2\\u09be\\u09b8 \\u09a5\\u09c7\\u0995\\u09c7 \\u09a8\\u09bf\\u09b0\\u09cd\\u09a6\\u09bf\\u09b7\\u09cd\\u099f \\u09b8\\u09ae\\u09af\\u09bc\\u09c7\\u09b0 \\u09ae\\u09a7\\u09cd\\u09af\\u09c7 \\u099c\\u09be\\u09a8\\u09be\\u09b2\\u09c7 \\u09a8\\u09c0\\u09a4\\u09bf\\u09ae\\u09be\\u09b2\\u09be \\u0985\\u09a8\\u09c1\\u09af\\u09be\\u09af\\u09bc\\u09c0 \\u09b0\\u09bf\\u09ab\\u09be\\u09a8\\u09cd\\u09a1 \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be \\u09b9\\u09af\\u09bc\\u0964 \\u09ac\\u09bf\\u09b8\\u09cd\\u09a4\\u09be\\u09b0\\u09bf\\u09a4 \\u09b0\\u09bf\\u09ab\\u09be\\u09a8\\u09cd\\u09a1 \\u09a8\\u09c0\\u09a4\\u09bf \\u09aa\\u09c7\\u099c\\u09c7\\u0964\"}', 1, 3, '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(5, NULL, NULL, '{\"bn\":\"\\u09ac\\u0987 \\u0995\\u09bf \\u09b8\\u09ab\\u099f \\u0995\\u09aa\\u09bf \\u09aa\\u09be\\u0993\\u09af\\u09bc\\u09be \\u09af\\u09be\\u09af\\u09bc?\"}', '{\"bn\":\"\\u09a8\\u09be, \\u0986\\u09ae\\u09be\\u09a6\\u09c7\\u09b0 \\u09ac\\u0987 \\u09b6\\u09c1\\u09a7\\u09c1 \\u09ae\\u09c1\\u09a6\\u09cd\\u09b0\\u09bf\\u09a4 \\u0986\\u0995\\u09be\\u09b0\\u09c7 \\u0995\\u09c1\\u09b0\\u09bf\\u09af\\u09bc\\u09be\\u09b0\\u09c7 \\u09aa\\u09be\\u09a0\\u09be\\u09a8\\u09cb \\u09b9\\u09af\\u09bc\\u0964\"}', 1, 4, '2026-11-19 16:06:30', '2026-11-19 16:06:30');

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

CREATE TABLE `guardians` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ulid` char(26) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT 'BD',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
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
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ulid` char(26) NOT NULL,
  `reference` varchar(30) NOT NULL,
  `type` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'new',
  `student_name` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `class_level` tinyint(3) UNSIGNED DEFAULT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `message` text DEFAULT NULL,
  `guardian_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `lost_reason` varchar(255) DEFAULT NULL,
  `next_follow_up_at` datetime DEFAULT NULL,
  `source` varchar(20) NOT NULL DEFAULT 'website',
  `landing_page` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `utm_source` varchar(255) DEFAULT NULL,
  `utm_medium` varchar(255) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `utm_content` varchar(255) DEFAULT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'bn',
  `consent_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `ulid`, `reference`, `type`, `status`, `student_name`, `guardian_name`, `phone`, `whatsapp`, `email`, `class_level`, `age`, `message`, `guardian_id`, `student_id`, `assigned_to`, `assigned_at`, `lost_reason`, `next_follow_up_at`, `source`, `landing_page`, `referrer`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term`, `locale`, `consent_at`, `ip_address`, `user_agent`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '01m7txhnm90j049zxe8hc1e7d5', 'CH-L-2611-0001', 'wizard', 'new', 'আয়ান', 'রফিকুল ইসলাম', '+8801811000001', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'website', NULL, NULL, 'facebook', NULL, 'demo', NULL, NULL, 'bn', '2026-11-19 16:06:31', NULL, NULL, '2026-11-19 16:06:31', '2026-11-19 16:06:31', NULL),
(2, '01m7txhnmk2409f5b61epx41ym', 'CH-L-2611-0002', 'assessment', 'new', 'নুসরাত', 'শাহনাজ পারভীন', '+8801811000002', NULL, NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'website', NULL, NULL, 'facebook', NULL, 'demo', NULL, NULL, 'bn', '2026-11-19 16:06:31', NULL, NULL, '2026-11-19 16:06:31', '2026-11-19 16:06:31', NULL),
(3, '01m7txhnmtxaqtbwyn1j5kmcxs', 'CH-L-2611-0003', 'callback', 'contacted', 'তাহসিন', 'মাহমুদুল হাসান', '+8801811000003', NULL, NULL, 9, NULL, NULL, NULL, NULL, 4, '2026-11-19 16:06:31', NULL, NULL, 'website', NULL, NULL, 'facebook', NULL, 'demo', NULL, NULL, 'bn', '2026-11-19 16:06:31', NULL, NULL, '2026-11-19 16:06:31', '2026-11-19 16:06:31', NULL),
(4, '01m7txhnmzcshw6tkvhdcr64ha', 'CH-L-2611-0004', 'wizard', 'follow_up', 'রাইসা', 'ফারহানা আক্তার', '+8801811000004', NULL, NULL, 10, NULL, NULL, NULL, NULL, 4, '2026-11-19 16:06:31', NULL, '2026-11-20 01:06:31', 'website', NULL, NULL, 'facebook', NULL, 'demo', NULL, NULL, 'bn', '2026-11-19 16:06:31', NULL, NULL, '2026-11-19 16:06:31', '2026-11-19 16:06:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_activities`
--

CREATE TABLE `lead_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(30) NOT NULL,
  `outcome` varchar(30) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_interests`
--

CREATE TABLE `lead_interests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `book_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_interests`
--

INSERT INTO `lead_interests` (`id`, `lead_id`, `program_id`, `course_id`, `book_id`) VALUES
(1, 1, NULL, 1, NULL),
(2, 2, NULL, 2, NULL),
(3, 3, NULL, 3, NULL),
(4, 4, NULL, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `learning_topics`
--

CREATE TABLE `learning_topics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_topics`
--

INSERT INTO `learning_topics` (`id`, `parent_id`, `slug`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, NULL, 'math', '{\"bn\":\"\\u0997\\u09a3\\u09bf\\u09a4\",\"en\":\"Maths\"}', 1, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(2, 1, 'geometry', '{\"bn\":\"\\u099c\\u09cd\\u09af\\u09be\\u09ae\\u09bf\\u09a4\\u09bf\",\"en\":\"Geometry\"}', 0, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(3, 1, 'puzzles', '{\"bn\":\"\\u0997\\u09be\\u09a3\\u09bf\\u09a4\\u09bf\\u0995 \\u09a7\\u09be\\u0981\\u09a7\\u09be\",\"en\":\"Maths puzzles\"}', 0, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(4, NULL, 'science', '{\"bn\":\"\\u09ac\\u09bf\\u099c\\u09cd\\u099e\\u09be\\u09a8\",\"en\":\"Science\"}', 2, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(5, NULL, 'iq', '{\"bn\":\"\\u0986\\u0987\\u0995\\u09bf\\u0989\",\"en\":\"IQ\"}', 3, '2026-11-20 16:01:51', '2026-11-20 16:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `learning_videos`
--

CREATE TABLE `learning_videos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `youtube_url` varchar(255) NOT NULL,
  `orientation` varchar(12) NOT NULL DEFAULT 'vertical',
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `topic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_videos`
--

INSERT INTO `learning_videos` (`id`, `title`, `description`, `youtube_url`, `orientation`, `subject_id`, `topic_id`, `is_featured`, `is_published`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb: \\u09a4\\u09cd\\u09b0\\u09bf\\u09ad\\u09c1\\u099c\\u09c7\\u09b0 \\u09a4\\u09bf\\u09a8 \\u0995\\u09cb\\u09a3\\u09c7\\u09b0 \\u09af\\u09cb\\u0997\\u09ab\\u09b2 \\u0995\\u09c7\\u09a8 \\u09e7\\u09ee\\u09e6\\u00b0?\"}', NULL, 'https://www.youtube.com/shorts/aqz-KE-bpKQ', 'vertical', 1, 2, 1, 1, 0, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(2, '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb: \\u09ae\\u09cd\\u09af\\u09be\\u099a\\u09c7\\u09b0 \\u0995\\u09be\\u09a0\\u09bf\\u09b0 \\u09a7\\u09be\\u0981\\u09a7\\u09be \\u2014 \\u09e9\\u099f\\u09bf \\u09b8\\u09b0\\u09bf\\u09af\\u09bc\\u09c7 \\u09ac\\u09b0\\u09cd\\u0997 \\u09ac\\u09be\\u09a8\\u09be\\u0993\"}', NULL, 'https://www.youtube.com/shorts/aqz-KE-bpKQ', 'vertical', 1, 3, 1, 1, 1, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(3, '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb: \\u099a\\u09c1\\u09ae\\u09cd\\u09ac\\u0995 \\u0995\\u09c7\\u09a8 \\u09b2\\u09cb\\u09b9\\u09be \\u099f\\u09be\\u09a8\\u09c7?\"}', NULL, 'https://www.youtube.com/shorts/aqz-KE-bpKQ', 'vertical', 4, NULL, 1, 1, 2, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(4, '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb: \\u09b8\\u0982\\u0996\\u09cd\\u09af\\u09be\\u09b0 \\u09aa\\u09cd\\u09af\\u09be\\u099f\\u09be\\u09b0\\u09cd\\u09a8: \\u09aa\\u09b0\\u09c7\\u09b0\\u099f\\u09be \\u0995\\u09c0?\"}', NULL, 'https://www.youtube.com/shorts/aqz-KE-bpKQ', 'vertical', 5, NULL, 0, 1, 3, '2026-11-20 16:01:51', '2026-11-20 16:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(20) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `url` text NOT NULL,
  `delete_url` text DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `size` int(10) UNSIGNED DEFAULT NULL,
  `width` smallint(5) UNSIGNED DEFAULT NULL,
  `height` smallint(5) UNSIGNED DEFAULT NULL,
  `alt` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`alt`)),
  `mediable_type` varchar(255) DEFAULT NULL,
  `mediable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `collection` varchar(50) DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `provider`, `path`, `url`, `delete_url`, `original_name`, `mime_type`, `size`, `width`, `height`, `alt`, `mediable_type`, `mediable_id`, `collection`, `sort_order`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 'imgbb', 'Q7kHHBxW', 'https://i.ibb.co/gbdWW1c0/images-MIopppbv2-UZt6yd44-KBg5-Qn-Eu-O0j-Xea-E9-B42-Zluo.jpg', 'https://ibb.co/Q7kHHBxW/3a05280d89cd4a032a975670c141634c', '1.jpg', 'image/jpeg', 257316, 3000, 2000, NULL, NULL, NULL, NULL, 0, 5, '2026-11-20 01:59:37', '2026-11-20 01:59:37'),
(2, 'imgbb', 'Q7kHHBxW', 'https://i.ibb.co/gbdWW1c0/images-MIopppbv2-UZt6yd44-KBg5-Qn-Eu-O0j-Xea-E9-B42-Zluo.jpg', 'https://ibb.co/Q7kHHBxW/3a05280d89cd4a032a975670c141634c', '1.jpg', 'image/jpeg', 257316, 3000, 2000, NULL, NULL, NULL, NULL, 0, 1, '2026-11-20 15:24:35', '2026-11-20 15:24:35'),
(3, 'imgbb', '23D0wvCB', 'https://i.ibb.co/CKkpqbYF/images-1vm-Ps-Ofgm1j-ZCECEYIao-YLb-CETQ8468-I7-OMyj2-U7.jpg', 'https://ibb.co/23D0wvCB/e6122dbafb63e1a9c8e1aa19844c3ded', 'HP_00725.jpg', 'image/jpeg', 2145227, 6000, 4000, NULL, NULL, NULL, NULL, 0, 1, '2026-11-20 17:05:37', '2026-11-20 17:05:37'),
(4, 'imgbb', '7d3TzLW8', 'https://i.ibb.co/LXTqPHtb/images-ZFzc-V5-BVo-WHut-BRi-Yf-Le5-Xc2r-SYwiwvj-Hr-VUk2r-T.png', 'https://ibb.co/7d3TzLW8/2a2737961a3d8fb96949bd3dd6956306', 'Banner of Github.png', 'image/png', 1634942, 2172, 724, NULL, NULL, NULL, NULL, 0, 1, '2026-11-20 17:05:40', '2026-11-20 17:05:40'),
(5, 'imgbb', '7d3TzLW8', 'https://i.ibb.co/LXTqPHtb/images-ZFzc-V5-BVo-WHut-BRi-Yf-Le5-Xc2r-SYwiwvj-Hr-VUk2r-T.png', 'https://ibb.co/7d3TzLW8/2a2737961a3d8fb96949bd3dd6956306', 'Banner of Github.png', 'image/png', 1634942, 2172, 724, NULL, NULL, NULL, NULL, 0, 1, '2026-11-20 17:05:42', '2026-11-20 17:05:42');

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
(4, '2026_11_19_205605_add_staff_profile_to_users_table', 2),
(5, '2026_11_19_205605_create_roles_and_permissions_tables', 2),
(6, '2026_11_19_211202_add_soft_deletes_to_users_table', 3),
(7, '2026_11_19_211202_create_media_table', 3),
(8, '2026_11_19_211202_create_settings_table', 3),
(9, '2026_11_19_213117_create_catalog_tables', 4),
(10, '2026_11_19_213118_create_content_tables', 4),
(11, '2026_11_19_213118_create_sales_tables', 4),
(12, '2026_11_20_051902_create_notifications_table', 5),
(13, '2026_11_20_051903_create_developer_query_logs_table', 5),
(14, '2026_11_20_135141_create_campaigns_table', 6),
(15, '2026_11_20_135142_add_audience_to_testimonials_table', 6),
(16, '2026_11_20_214140_add_banner_to_campaigns_table', 7),
(17, '2026_11_20_214141_create_learning_tables', 7),
(18, '2026_11_20_214142_create_exam_tables', 8);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ulid` char(26) NOT NULL,
  `reference` varchar(30) NOT NULL,
  `guardian_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `alt_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `district` varchar(50) NOT NULL,
  `upazila` varchar(50) DEFAULT NULL,
  `delivery_zone_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending_verification',
  `subtotal` int(10) UNSIGNED NOT NULL,
  `discount_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `delivery_charge` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total` int(10) UNSIGNED NOT NULL,
  `courier` varchar(30) DEFAULT NULL,
  `tracking_code` varchar(60) DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `staff_notes` text DEFAULT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'bn',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title_snapshot` varchar(255) NOT NULL,
  `unit_price` int(10) UNSIGNED NOT NULL,
  `quantity` smallint(5) UNSIGNED NOT NULL,
  `line_total` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'page',
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`title`)),
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`body`)),
  `seo_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seo_description`)),
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `type`, `title`, `body`, `seo_description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'about', 'page', '{\"bn\":\"\\u0986\\u09ae\\u09be\\u09a6\\u09c7\\u09b0 \\u09b8\\u09ae\\u09cd\\u09aa\\u09b0\\u09cd\\u0995\\u09c7\",\"en\":\"About us\"}', '{\"bn\":\"## \\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09b0 \\u0995\\u09c0\\n\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09b0 \\u098f\\u0995\\u099f\\u09bf \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u09be-\\u09aa\\u09cd\\u09b0\\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u09aa\\u09cd\\u09b0\\u09a4\\u09bf\\u09b7\\u09cd\\u09a0\\u09be\\u09a8\\u0964 \\u0986\\u09ae\\u09b0\\u09be \\u0985\\u09ad\\u09bf\\u099c\\u09cd\\u099e \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8 \\u0986\\u09b0 \\u09a8\\u09bf\\u099c\\u09b8\\u09cd\\u09ac \\u09b2\\u09c7\\u0996\\u0995\\u09a6\\u09c7\\u09b0 \\u09ac\\u0987 \\u09a6\\u09bf\\u09af\\u09bc\\u09c7 \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u09be\\u09b0\\u09cd\\u09a5\\u09c0\\u09a6\\u09c7\\u09b0 **\\u09ad\\u09be\\u09ac\\u09a4\\u09c7 \\u09b6\\u09c7\\u0996\\u09be\\u0987**\\u0964\\n\\n## \\u0986\\u09ae\\u09be\\u09a6\\u09c7\\u09b0 \\u09aa\\u09a6\\u09cd\\u09a7\\u09a4\\u09bf\\n- \\u09b8\\u09c0\\u09ae\\u09bf\\u09a4 \\u0986\\u09b8\\u09a8\\u09c7\\u09b0 \\u09b2\\u09be\\u0987\\u09ad \\u09ac\\u09cd\\u09af\\u09be\\u099a\\n- \\u09ad\\u09b0\\u09cd\\u09a4\\u09bf\\u09b0 \\u0986\\u0997\\u09c7 \\u09ab\\u09cd\\u09b0\\u09bf \\u09e7:\\u09e7 \\u0985\\u09cd\\u09af\\u09be\\u09b8\\u09c7\\u09b8\\u09ae\\u09c7\\u09a8\\u09cd\\u099f\\n- \\u09a8\\u09bf\\u09af\\u09bc\\u09ae\\u09bf\\u09a4 \\u09ae\\u09c2\\u09b2\\u09cd\\u09af\\u09be\\u09af\\u09bc\\u09a8 \\u0993 \\u0985\\u09ad\\u09bf\\u09ad\\u09be\\u09ac\\u0995 \\u09b0\\u09bf\\u09aa\\u09cb\\u09b0\\u09cd\\u099f\\n\\n*(\\u098f\\u0987 \\u09b2\\u09c7\\u0996\\u09be\\u099f\\u09bf \\u0996\\u09b8\\u09a1\\u09bc\\u09be \\u2014 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u2192 \\u09aa\\u09c7\\u099c \\u09a5\\u09c7\\u0995\\u09c7 \\u09ac\\u09a6\\u09b2\\u09c7 \\u09a6\\u09bf\\u09a8\\u0964)*\"}', NULL, 'published', '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(2, 'contact', 'page', '{\"bn\":\"\\u09af\\u09cb\\u0997\\u09be\\u09af\\u09cb\\u0997\",\"en\":\"Contact\"}', '{\"bn\":\"\\u09af\\u09c7\\u0995\\u09cb\\u09a8\\u09cb \\u09aa\\u09cd\\u09b0\\u09b6\\u09cd\\u09a8\\u09c7 \\u0986\\u09ae\\u09be\\u09a6\\u09c7\\u09b0 \\u09b9\\u09c7\\u09b2\\u09cd\\u09aa\\u09b2\\u09be\\u0987\\u09a8\\u09c7 \\u0995\\u09b2 \\u0995\\u09b0\\u09c1\\u09a8 \\u09ac\\u09be WhatsApp-\\u098f \\u09b2\\u09bf\\u0996\\u09c1\\u09a8\\u0964 \\u09a8\\u09bf\\u099a\\u09c7\\u09b0 \\u09ab\\u09b0\\u09cd\\u09ae\\u09c7 \\u09a8\\u09ae\\u09cd\\u09ac\\u09b0 \\u09a6\\u09bf\\u09b2\\u09c7 \\u0986\\u09ae\\u09b0\\u09be\\u0987 \\u0995\\u09b2 \\u0995\\u09b0\\u09ac\\u0964\"}', NULL, 'published', '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(3, 'refund-policy', 'policy', '{\"bn\":\"\\u09b0\\u09bf\\u09ab\\u09be\\u09a8\\u09cd\\u09a1 \\u09a8\\u09c0\\u09a4\\u09bf\",\"en\":\"Refund policy\"}', '{\"bn\":\"## \\u0995\\u09cb\\u09b0\\u09cd\\u09b8\\n- \\u09aa\\u09cd\\u09b0\\u09a5\\u09ae \\u0995\\u09cd\\u09b2\\u09be\\u09b8 \\u09a5\\u09c7\\u0995\\u09c7 **\\u09ed \\u09a6\\u09bf\\u09a8\\u09c7\\u09b0 \\u09ae\\u09a7\\u09cd\\u09af\\u09c7** \\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u099a\\u09be\\u09b2\\u09bf\\u09af\\u09bc\\u09c7 \\u09af\\u09c7\\u09a4\\u09c7 \\u09a8\\u09be \\u099a\\u09be\\u0987\\u09b2\\u09c7 \\u09aa\\u09b0\\u09bf\\u09b6\\u09cb\\u09a7\\u09bf\\u09a4 \\u0985\\u09b0\\u09cd\\u09a5\\u09c7\\u09b0 **\\u09ed\\u09e6%** \\u09ab\\u09c7\\u09b0\\u09a4 \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be \\u09b9\\u09af\\u09bc\\u0964\\n- \\u09ac\\u09be\\u0995\\u09bf \\u0985\\u0982\\u09b6 \\u0987\\u09a4\\u09bf\\u09ae\\u09a7\\u09cd\\u09af\\u09c7 \\u09b9\\u0993\\u09af\\u09bc\\u09be \\u0995\\u09cd\\u09b2\\u09be\\u09b8 \\u09aa\\u09b0\\u09bf\\u099a\\u09be\\u09b2\\u09a8\\u09be, \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995 \\u0993 \\u0985\\u09aa\\u09be\\u09b0\\u09c7\\u09b6\\u09a8\\u09be\\u09b2 \\u0996\\u09b0\\u099a\\u0964\\n- \\u09b0\\u09bf\\u09ab\\u09be\\u09a8\\u09cd\\u09a1\\u09c7\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09b9\\u09c7\\u09b2\\u09cd\\u09aa\\u09b2\\u09be\\u0987\\u09a8\\u09c7 \\u0995\\u09b2 \\u0995\\u09b0\\u09c1\\u09a8 \\u09ac\\u09be WhatsApp-\\u098f \\u09b0\\u09c7\\u09ab\\u09be\\u09b0\\u09c7\\u09a8\\u09cd\\u09b8 \\u09a8\\u09ae\\u09cd\\u09ac\\u09b0\\u09b8\\u09b9 \\u09b2\\u09bf\\u0996\\u09c1\\u09a8\\u0964\\n\\n## \\u09ac\\u0987\\n- \\u09ad\\u09c1\\u09b2 \\u09ac\\u09be \\u0995\\u09cd\\u09b7\\u09a4\\u09bf\\u0997\\u09cd\\u09b0\\u09b8\\u09cd\\u09a4 \\u09ac\\u0987 \\u09aa\\u09c7\\u09b2\\u09c7 \\u09a1\\u09c7\\u09b2\\u09bf\\u09ad\\u09be\\u09b0\\u09bf\\u09b0 \\u09e9 \\u09a6\\u09bf\\u09a8\\u09c7\\u09b0 \\u09ae\\u09a7\\u09cd\\u09af\\u09c7 \\u099c\\u09be\\u09a8\\u09be\\u09b2\\u09c7 \\u09ac\\u09a6\\u09b2\\u09c7 \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be \\u09b9\\u09ac\\u09c7\\u0964\\n\\n*(\\u0996\\u09b8\\u09a1\\u09bc\\u09be \\u2014 \\u099a\\u09c2\\u09a1\\u09bc\\u09be\\u09a8\\u09cd\\u09a4 \\u0995\\u09b0\\u09be\\u09b0 \\u0986\\u0997\\u09c7 \\u0986\\u0987\\u09a8\\u09bf \\u09aa\\u09b0\\u09be\\u09ae\\u09b0\\u09cd\\u09b6 \\u09a8\\u09bf\\u09a8\\u0964)*\"}', NULL, 'published', '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(4, 'privacy-policy', 'policy', '{\"bn\":\"\\u09aa\\u09cd\\u09b0\\u09be\\u0987\\u09ad\\u09c7\\u09b8\\u09bf \\u09a8\\u09c0\\u09a4\\u09bf\",\"en\":\"Privacy policy\"}', '{\"bn\":\"## \\u0986\\u09ae\\u09b0\\u09be \\u0995\\u09c0 \\u09a4\\u09a5\\u09cd\\u09af \\u09a8\\u09bf\\u0987\\n\\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u09be\\u09b0\\u09cd\\u09a5\\u09c0 \\u0993 \\u0985\\u09ad\\u09bf\\u09ad\\u09be\\u09ac\\u0995\\u09c7\\u09b0 \\u09a8\\u09be\\u09ae, \\u09ab\\u09cb\\u09a8 \\u09a8\\u09ae\\u09cd\\u09ac\\u09b0, \\u09b6\\u09cd\\u09b0\\u09c7\\u09a3\\u09bf \\u098f\\u09ac\\u0982 (\\u09ac\\u0987\\u09af\\u09bc\\u09c7\\u09b0 \\u099c\\u09a8\\u09cd\\u09af) \\u09a1\\u09c7\\u09b2\\u09bf\\u09ad\\u09be\\u09b0\\u09bf \\u09a0\\u09bf\\u0995\\u09be\\u09a8\\u09be\\u0964\\n\\n## \\u0995\\u09c7\\u09a8 \\u09a8\\u09bf\\u0987\\n\\u0995\\u09cb\\u09b0\\u09cd\\u09b8 \\u09b8\\u09ae\\u09cd\\u09aa\\u09b0\\u09cd\\u0995\\u09c7 \\u09af\\u09cb\\u0997\\u09be\\u09af\\u09cb\\u0997, \\u09ad\\u09b0\\u09cd\\u09a4\\u09bf, \\u09aa\\u09c7\\u09ae\\u09c7\\u09a8\\u09cd\\u099f \\u09af\\u09be\\u099a\\u09be\\u0987 \\u0993 \\u09ac\\u0987 \\u09aa\\u09cc\\u0981\\u099b\\u09c7 \\u09a6\\u09bf\\u09a4\\u09c7\\u0964\\n\\n## \\u09b6\\u09bf\\u09b6\\u09c1\\u09a6\\u09c7\\u09b0 \\u09a4\\u09a5\\u09cd\\u09af\\n\\u0986\\u09ae\\u09b0\\u09be \\u09a8\\u09cd\\u09af\\u09c2\\u09a8\\u09a4\\u09ae \\u09a4\\u09a5\\u09cd\\u09af \\u09a8\\u09bf\\u0987 \\u098f\\u09ac\\u0982 \\u0985\\u09ad\\u09bf\\u09ad\\u09be\\u09ac\\u0995\\u09c7\\u09b0 \\u09b8\\u09ae\\u09cd\\u09ae\\u09a4\\u09bf \\u099b\\u09be\\u09a1\\u09bc\\u09be \\u09a4\\u09be \\u0995\\u09be\\u09b0\\u0993 \\u09b8\\u09be\\u09a5\\u09c7 \\u09b6\\u09c7\\u09af\\u09bc\\u09be\\u09b0 \\u0995\\u09b0\\u09bf \\u09a8\\u09be\\u0964\\n\\n*(\\u0996\\u09b8\\u09a1\\u09bc\\u09be)*\"}', NULL, 'published', '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(5, 'terms', 'policy', '{\"bn\":\"\\u09b6\\u09b0\\u09cd\\u09a4\\u09be\\u09ac\\u09b2\\u09bf\",\"en\":\"Terms of service\"}', '{\"bn\":\"\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09b0\\u09c7\\u09b0 \\u0993\\u09af\\u09bc\\u09c7\\u09ac\\u09b8\\u09be\\u0987\\u099f \\u0993 \\u09b8\\u09c7\\u09ac\\u09be \\u09ac\\u09cd\\u09af\\u09ac\\u09b9\\u09be\\u09b0\\u09c7\\u09b0 \\u09ae\\u09be\\u09a7\\u09cd\\u09af\\u09ae\\u09c7 \\u0986\\u09aa\\u09a8\\u09bf \\u098f\\u0987 \\u09b6\\u09b0\\u09cd\\u09a4\\u09be\\u09ac\\u09b2\\u09bf\\u09a4\\u09c7 \\u09b8\\u09ae\\u09cd\\u09ae\\u09a4 \\u09b9\\u099a\\u09cd\\u099b\\u09c7\\u09a8\\u0964\\n\\n*(\\u0996\\u09b8\\u09a1\\u09bc\\u09be \\u2014 \\u0986\\u0987\\u09a8\\u09bf \\u09aa\\u09b0\\u09be\\u09ae\\u09b0\\u09cd\\u09b6 \\u09a8\\u09bf\\u09af\\u09bc\\u09c7 \\u099a\\u09c2\\u09a1\\u09bc\\u09be\\u09a8\\u09cd\\u09a4 \\u0995\\u09b0\\u09c1\\u09a8\\u0964)*\"}', NULL, 'published', '2026-11-19 16:06:30', '2026-11-19 16:06:30'),
(6, 'delivery-policy', 'policy', '{\"bn\":\"\\u09a1\\u09c7\\u09b2\\u09bf\\u09ad\\u09be\\u09b0\\u09bf \\u09a8\\u09c0\\u09a4\\u09bf\",\"en\":\"Delivery policy\"}', '{\"bn\":\"## \\u099a\\u09be\\u09b0\\u09cd\\u099c\\n- \\u09a2\\u09be\\u0995\\u09be\\u09b0 \\u09ad\\u09c7\\u09a4\\u09b0\\u09c7: \\u09f3\\u09ed\\u09e6 (\\u09e7\\u2013\\u09e8 \\u09a6\\u09bf\\u09a8)\\n- \\u09a2\\u09be\\u0995\\u09be\\u09b0 \\u09ac\\u09be\\u0987\\u09b0\\u09c7: \\u09f3\\u09e7\\u09e8\\u09e6 (\\u09e9\\u2013\\u09eb \\u09a6\\u09bf\\u09a8)\\n\\n## \\u09aa\\u09cd\\u09b0\\u0995\\u09cd\\u09b0\\u09bf\\u09af\\u09bc\\u09be\\n\\u09aa\\u09c7\\u09ae\\u09c7\\u09a8\\u09cd\\u099f \\u09af\\u09be\\u099a\\u09be\\u0987\\u09af\\u09bc\\u09c7\\u09b0 \\u09aa\\u09b0 \\u09ac\\u0987 \\u0995\\u09c1\\u09b0\\u09bf\\u09af\\u09bc\\u09be\\u09b0\\u09c7 \\u09aa\\u09be\\u09a0\\u09be\\u09a8\\u09cb \\u09b9\\u09af\\u09bc\\u0964 \\u201c\\u0985\\u09b0\\u09cd\\u09a1\\u09be\\u09b0 \\u099f\\u09cd\\u09b0\\u09cd\\u09af\\u09be\\u0995\\u201d \\u09aa\\u09c7\\u099c\\u09c7 \\u0985\\u09b0\\u09cd\\u09a1\\u09be\\u09b0 \\u09a8\\u09ae\\u09cd\\u09ac\\u09b0 \\u0993 \\u09ae\\u09cb\\u09ac\\u09be\\u0987\\u09b2 \\u09a8\\u09ae\\u09cd\\u09ac\\u09b0 \\u09a6\\u09bf\\u09af\\u09bc\\u09c7 \\u0985\\u09ac\\u09b8\\u09cd\\u09a5\\u09be \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u09af\\u09bc\\u0964\"}', NULL, 'published', '2026-11-19 16:06:30', '2026-11-19 16:06:30');

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ulid` char(26) NOT NULL,
  `reference` varchar(30) NOT NULL,
  `payable_type` varchar(255) NOT NULL,
  `payable_id` bigint(20) UNSIGNED NOT NULL,
  `method` varchar(20) NOT NULL,
  `provider` varchar(20) NOT NULL DEFAULT 'manual',
  `amount` int(10) UNSIGNED NOT NULL,
  `sender_number` varchar(20) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `receipt_url` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `submitted_by` varchar(20) NOT NULL DEFAULT 'customer',
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `gateway_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_accounts`
--

CREATE TABLE `payment_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `method` varchar(20) NOT NULL,
  `account_type` varchar(20) NOT NULL DEFAULT 'personal',
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `instructions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`instructions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_accounts`
--

INSERT INTO `payment_accounts` (`id`, `method`, `account_type`, `account_name`, `account_number`, `bank_name`, `branch`, `instructions`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'bkash', 'personal', 'ডেমো নম্বর — বদলে দিন', '01700000000', NULL, NULL, NULL, 1, 0, '2026-11-19 16:06:30', '2026-11-19 16:06:30');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES
(1, 'users.manage', 'স্টাফ অ্যাকাউন্ট', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(2, 'settings.manage', 'সাইট সেটিংস ও স্টোরেজ', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(3, 'media.manage', 'মিডিয়া লাইব্রেরি', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(4, 'catalog.manage', 'প্রোগ্রাম, কোর্স, ব্যাচ ও শিক্ষক', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(5, 'books.manage', 'বই ও লেখক', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(6, 'content.manage', 'পেজ, FAQ ও রিভিউ', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(7, 'leads.view_all', 'সব লিড দেখা (কেন্দ্রীয় কিউ)', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(8, 'leads.assign', 'লিড বণ্টন', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(9, 'leads.work', 'নিজের লিডে কাজ', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(10, 'assessments.manage', 'অ্যাসেসমেন্ট সমন্বয়', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(11, 'orders.manage', 'বইয়ের অর্ডার ও ডেলিভারি', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(12, 'enrollments.manage', 'এনরোলমেন্ট', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(13, 'payments.verify', 'পেমেন্ট যাচাই', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(14, 'reports.view', 'রিপোর্ট', '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(15, 'developer.tools', 'ডেভেলপার টুলস', '2026-11-19 23:28:01', '2026-11-19 23:28:01'),
(16, 'homepage.manage', 'হোমপেজ ও ক্যাম্পেইন', '2026-11-20 14:00:05', '2026-11-20 14:00:05'),
(17, 'learning.manage', 'শেখার ভিডিও, প্রশ্ন ব্যাংক ও পরীক্ষা', '2026-11-20 15:55:05', '2026-11-20 15:55:05');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(3, 2),
(3, 6),
(3, 7),
(4, 2),
(4, 7),
(5, 2),
(5, 6),
(6, 2),
(6, 6),
(7, 2),
(7, 6),
(8, 2),
(8, 6),
(9, 2),
(9, 3),
(9, 6),
(10, 2),
(10, 7),
(11, 2),
(12, 2),
(12, 3),
(12, 7),
(13, 2),
(14, 2),
(14, 6),
(15, 10),
(16, 6),
(17, 2),
(17, 6),
(17, 7);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `tagline` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tagline`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `icon` varchar(30) DEFAULT NULL,
  `accent` varchar(20) NOT NULL DEFAULT 'teal',
  `image_url` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `slug`, `name`, `tagline`, `description`, `icon`, `accent`, `image_url`, `status`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'math-olympiad', '{\"bn\":\"\\u09ae\\u09cd\\u09af\\u09be\\u09a5 \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1\",\"en\":\"Math Olympiad\"}', '{\"bn\":\"\\u09ac\\u09be\\u0982\\u09b2\\u09be\\u09a6\\u09c7\\u09b6 \\u0997\\u09a3\\u09bf\\u09a4 \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1\\u09c7\\u09b0 \\u09a7\\u09be\\u09aa\\u09c7 \\u09a7\\u09be\\u09aa\\u09c7 \\u09aa\\u09cd\\u09b0\\u09b8\\u09cd\\u09a4\\u09c1\\u09a4\\u09bf\",\"en\":\"Step-by-step preparation for the Bangladesh Math Olympiad\"}', '{\"bn\":\"\\u09b8\\u09ae\\u09b8\\u09cd\\u09af\\u09be \\u09b8\\u09ae\\u09be\\u09a7\\u09be\\u09a8\\u09c7\\u09b0 \\u0995\\u09cc\\u09b6\\u09b2, \\u09af\\u09c1\\u0995\\u09cd\\u09a4\\u09bf \\u0986\\u09b0 \\u0986\\u09a4\\u09cd\\u09ae\\u09ac\\u09bf\\u09b6\\u09cd\\u09ac\\u09be\\u09b8 \\u2014 \\u09aa\\u09cd\\u09b0\\u09be\\u0987\\u09ae\\u09be\\u09b0\\u09bf \\u0993 \\u099c\\u09c1\\u09a8\\u09bf\\u09af\\u09bc\\u09b0 \\u0995\\u09cd\\u09af\\u09be\\u099f\\u09be\\u0997\\u09b0\\u09bf\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u0986\\u09b2\\u09be\\u09a6\\u09be \\u0995\\u09cb\\u09b0\\u09cd\\u09b8\\u0964\",\"en\":\"Problem-solving strategies, reasoning and confidence \\u2014 separate courses for the primary and junior categories.\"}', 'target', 'coral', NULL, 'published', 1, '2026-11-19 16:06:29', '2026-11-19 16:06:29', NULL),
(2, 'academic-9-10', '{\"bn\":\"\\u098f\\u0995\\u09be\\u09a1\\u09c7\\u09ae\\u09bf\\u0995 (\\u09ef\\u09ae\\u2013\\u09e7\\u09e6\\u09ae)\",\"en\":\"Academic (Class 9\\u201310)\"}', '{\"bn\":\"\\u09ac\\u09bf\\u099c\\u09cd\\u099e\\u09be\\u09a8 \\u0993 \\u09ac\\u09cd\\u09af\\u09ac\\u09b8\\u09be\\u09af\\u09bc \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u09be \\u2014 \\u09b8\\u09be\\u09a7\\u09be\\u09b0\\u09a3 \\u0997\\u09a3\\u09bf\\u09a4\\u09b8\\u09b9\",\"en\":\"Science and Business Studies \\u2014 with General Math\"}', '{\"bn\":\"\\u09ac\\u09cb\\u09b0\\u09cd\\u09a1 \\u09aa\\u09b0\\u09c0\\u0995\\u09cd\\u09b7\\u09be\\u09b0 \\u09aa\\u09c2\\u09b0\\u09cd\\u09a3 \\u09aa\\u09cd\\u09b0\\u09b8\\u09cd\\u09a4\\u09c1\\u09a4\\u09bf: \\u09a8\\u09bf\\u09af\\u09bc\\u09ae\\u09bf\\u09a4 \\u09b2\\u09be\\u0987\\u09ad \\u0995\\u09cd\\u09b2\\u09be\\u09b8, \\u0985\\u09a8\\u09c1\\u09b6\\u09c0\\u09b2\\u09a8 \\u0993 \\u09ae\\u09a1\\u09c7\\u09b2 \\u099f\\u09c7\\u09b8\\u09cd\\u099f\\u0964\",\"en\":\"Complete board exam preparation: regular live classes, practice and model tests.\"}', 'book', 'teal', NULL, 'published', 2, '2026-11-19 16:06:29', '2026-11-19 16:06:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `topic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_level` tinyint(3) UNSIGNED DEFAULT NULL,
  `body` text NOT NULL,
  `image_url` text DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `correct_option` tinyint(3) UNSIGNED NOT NULL,
  `explanation` text DEFAULT NULL,
  `explanation_image_url` text DEFAULT NULL,
  `explanation_pdf_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `subject_id`, `topic_id`, `class_level`, `body`, `image_url`, `options`, `correct_option`, `explanation`, `explanation_image_url`, `explanation_pdf_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 6, 'ডেমো: একটি ত্রিভুজের দুটি কোণ ৫০° ও ৬০° হলে তৃতীয় কোণ কত?', NULL, '[\"\\u09ec\\u09e6\\u00b0\",\"\\u09ed\\u09e6\\u00b0\",\"\\u09ee\\u09e6\\u00b0\",\"\\u09ef\\u09e6\\u00b0\"]', 1, 'তিন কোণের যোগফল ১৮০°। তাই ১৮০ − (৫০ + ৬০) = ৭০°।', NULL, NULL, 1, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(2, 1, 2, 6, 'ডেমো: একটি বর্গের এক বাহু ৫ সেমি হলে ক্ষেত্রফল কত?', NULL, '[\"\\u09e7\\u09e6 \\u09ac\\u09b0\\u09cd\\u0997\\u09b8\\u09c7\\u09ae\\u09bf\",\"\\u09e8\\u09e6 \\u09ac\\u09b0\\u09cd\\u0997\\u09b8\\u09c7\\u09ae\\u09bf\",\"\\u09e8\\u09eb \\u09ac\\u09b0\\u09cd\\u0997\\u09b8\\u09c7\\u09ae\\u09bf\",\"\\u09e9\\u09e6 \\u09ac\\u09b0\\u09cd\\u0997\\u09b8\\u09c7\\u09ae\\u09bf\"]', 2, 'বর্গের ক্ষেত্রফল = বাহু × বাহু = ৫ × ৫ = ২৫ বর্গসেমি।', NULL, NULL, 1, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(3, 1, 3, 5, 'ডেমো: ২, ৪, ৮, ১৬, … পরের সংখ্যাটি কী?', NULL, '[\"\\u09e8\\u09e6\",\"\\u09e8\\u09ea\",\"\\u09e9\\u09e8\",\"\\u09e9\\u09ec\"]', 2, 'প্রতিবার আগের সংখ্যার দ্বিগুণ: ১৬ × ২ = ৩২।', NULL, NULL, 1, '2026-11-20 16:01:51', '2026-11-20 16:01:51'),
(4, 1, 3, 5, 'ডেমো: একটি ঘড়িতে ৩টা বাজলে ঘণ্টা ও মিনিটের কাঁটার মধ্যে কোণ কত?', NULL, '[\"\\u09ea\\u09eb\\u00b0\",\"\\u09ec\\u09e6\\u00b0\",\"\\u09ef\\u09e6\\u00b0\",\"\\u09e7\\u09e8\\u09e6\\u00b0\"]', 2, 'প্রতি ঘণ্টা-দাগ ৩০°; ৩ দাগ = ৯০°।', NULL, NULL, 1, '2026-11-20 16:01:51', '2026-11-20 16:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `max_discount_percent` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `label`, `description`, `max_discount_percent`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'অ্যাডমিন', 'সব মডিউল ও সেটিংসে পূর্ণ অ্যাক্সেস।', 100, '2026-11-19 14:59:03', '2026-11-19 15:29:35'),
(2, 'manager', 'ম্যানেজার', 'লিড বণ্টন, অর্ডার, এনরোলমেন্ট, পেমেন্ট ও রিপোর্ট।', 30, '2026-11-19 14:59:03', '2026-11-19 15:29:35'),
(3, 'sales_executive', 'সেলস এক্সিকিউটিভ', 'নিজের লিডে কল, ফলো-আপ ও সীমিত ডিসকাউন্ট।', 10, '2026-11-19 14:59:03', '2026-11-19 14:59:03'),
(6, 'marketing', 'মার্কেটিং', 'লিড কিউ, লিড বণ্টন, কনটেন্ট ও ক্যাম্পেইন।', 15, '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(7, 'course_coordinator', 'কোর্স কোঅর্ডিনেটর', 'প্রোগ্রাম, কোর্স, ব্যাচ, শিক্ষক ও অ্যাসেসমেন্ট সমন্বয়।', NULL, '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(8, 'teacher', 'শিক্ষক', 'অ্যাসেসমেন্ট ও (পরে) LMS-এ ক্লাস পরিচালনা।', NULL, '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(9, 'student', 'শিক্ষার্থী', 'LMS চালু হলে কোর্স কনটেন্টে অ্যাক্সেস।', NULL, '2026-11-19 15:29:35', '2026-11-19 15:29:35'),
(10, 'developer', 'ডেভেলপার', 'পূর্ণ অ্যাক্সেস + ডেভেলপার টুলস (ডেটাবেস, ER ডায়াগ্রাম, এরর লগ, প্রোফাইল সুইচ, পাসওয়ার্ড রিসেট)।', 100, '2026-11-19 23:28:01', '2026-11-19 23:28:01');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`role_id`, `user_id`) VALUES
(1, 1),
(2, 2),
(3, 4),
(6, 3),
(7, 5),
(10, 6);

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
('HpwZoU6fWE6WGteI6SmXjGvPCex7YjJAS7yqa8SY', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUXN4MlYyaTI5UU9KQ2hvNjVpMmR5Z3VjTlhxRVF2M2VKc2FrSWZZUyI7czoxMToiYXR0cmlidXRpb24iO2E6Nzp7czoxMjoibGFuZGluZ19wYWdlIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO3M6ODoicmVmZXJyZXIiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9pbmZvL2Fib3V0IjtzOjEwOiJ1dG1fc291cmNlIjtOO3M6MTA6InV0bV9tZWRpdW0iO047czoxMjoidXRtX2NhbXBhaWduIjtOO3M6MTE6InV0bV9jb250ZW50IjtOO3M6ODoidXRtX3Rlcm0iO047fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vbm90aWZpY2F0aW9ucy9zdW1tYXJ5IjtzOjU6InJvdXRlIjtzOjI3OiJhZG1pbi5ub3RpZmljYXRpb25zLnN1bW1hcnkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1795204876);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`value`)),
  `secrets` text DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `secrets`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'media', '{\"driver\":\"imgbb\",\"r2_endpoint\":null,\"r2_bucket\":null,\"r2_public_url\":null}', 'eyJpdiI6Ikh4ckZrR0JDTU0vVThhQW9jaHBEMWc9PSIsInZhbHVlIjoic25hZllUanl3N2kwYXFoU1RBc2FrU1ZYZ1U0cUdyT2F6cEZ1WnhHYm5lRlNUV0pnenVVaVlURTRNOS9neXIyTk93dGN5Q3NJY01sTDltajU2ODhtQ1Jyek5CYzJ2VUJ5VHd2N0tNTk9OSTZsQjBzdlRDWXk1bThqRVdJUlhFQUM3NHdNRXNnOTlpbFpSSjRiS25DaFV3PT0iLCJtYWMiOiJjNjlkZjg5MzYyYmM0ZGNhM2E5YzBlZDM3ZjM2MTkyZWM3ZGI3OTJlMTI1MjI3YmViZDMxM2ZiMmM4MzRlNzYxIiwidGFnIjoiIn0=', 1, '2026-11-19 23:07:44', '2026-11-19 23:08:18'),
(2, 'homepage', '{\"hero\":{\"eyebrow\":null,\"title\":null,\"subtitle\":null,\"rotating_words\":null,\"buttons\":[{\"label\":{\"bn\":\"\\u099a\\u09b2\\u09cb \\u098f\\u0995\\u09b8\\u09be\\u09a5\\u09c7 \\u09b6\\u09bf\\u0996\\u09bf\",\"en\":\"Let\'s learn together\"},\"url\":\"\\/start\",\"style\":\"primary\"},{\"label\":{\"bn\":\"\\u09ab\\u09cd\\u09b0\\u09bf \\u0985\\u09cd\\u09af\\u09be\\u09b8\\u09c7\\u09b8\\u09ae\\u09c7\\u09a8\\u09cd\\u099f \\u09ac\\u09c1\\u0995 \\u0995\\u09b0\\u09c1\\u09a8\",\"en\":\"Book a free assessment\"},\"url\":\"\\/start?assessment=1\",\"style\":\"secondary\"}],\"video_url\":null,\"show_trust_chips\":false},\"stats\":[],\"sections\":[{\"key\":\"highlights\",\"visible\":false,\"title\":null,\"subtitle\":null},{\"key\":\"stats\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"campaigns\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"programs\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"courses\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"steps\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"why\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"teachers\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"parent_reviews\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"student_reviews\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"books\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"community\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"faq\",\"visible\":true,\"title\":null,\"subtitle\":null},{\"key\":\"cta\",\"visible\":true,\"title\":null,\"subtitle\":null}]}', NULL, 1, '2026-11-20 15:31:56', '2026-11-20 15:31:56');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ulid` char(26) NOT NULL,
  `guardian_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `class_level` tinyint(3) UNSIGNED DEFAULT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `assessed_level` varchar(20) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `designation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`designation`)),
  `photo_url` text DEFAULT NULL,
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary`)),
  `details_mode` varchar(10) NOT NULL DEFAULT 'rich',
  `details_rich` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details_rich`)),
  `details_html` longtext DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `slug`, `name`, `designation`, `photo_url`, `summary`, `details_mode`, `details_rich`, `details_html`, `user_id`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'demo-teacher-math', '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995 \\u2014 \\u0997\\u09a3\\u09bf\\u09a4\",\"en\":\"Demo teacher \\u2014 Mathematics\"}', '{\"bn\":\"\\u0997\\u09a3\\u09bf\\u09a4 \\u0993 \\u0985\\u09b2\\u09bf\\u09ae\\u09cd\\u09aa\\u09bf\\u09af\\u09bc\\u09be\\u09a1 \\u09aa\\u09cd\\u09b0\\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\",\"en\":\"Maths & olympiad trainer\"}', 'https://i.ibb.co/gbdWW1c0/images-MIopppbv2-UZt6yd44-KBg5-Qn-Eu-O0j-Xea-E9-B42-Zluo.jpg', '{\"bn\":\"\\u098f\\u099f\\u09bf \\u09a1\\u09c7\\u09ae\\u09cb \\u09aa\\u09cd\\u09b0\\u09cb\\u09ab\\u09be\\u0987\\u09b2 \\u2014 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09a4\\u09a5\\u09cd\\u09af \\u09a6\\u09bf\\u09af\\u09bc\\u09c7 \\u09ac\\u09a6\\u09b2\\u09c7 \\u09a6\\u09bf\\u09a8\\u0964\",\"en\":\"Demo profile \\u2014 replace with the real teacher from the admin panel.\"}', 'rich', '{\"bn\":\"\\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u09be\\u0997\\u09a4 \\u09af\\u09cb\\u0997\\u09cd\\u09af\\u09a4\\u09be, \\u0985\\u09ad\\u09bf\\u099c\\u09cd\\u099e\\u09a4\\u09be \\u0993 \\u0985\\u09b0\\u09cd\\u099c\\u09a8 \\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u09b2\\u09bf\\u0996\\u09c1\\u09a8\\u0964\\r\\n\\u099a\\u09be\\u0987\\u09b2\\u09c7 \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae HTML \\u09aa\\u09c7\\u099c\\u0993 \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be \\u09af\\u09be\\u09af\\u09bc\\u0964\",\"en\":\"Add qualifications, experience and achievements here.\"}', NULL, NULL, 1, 0, '2026-11-19 16:06:29', '2026-11-20 01:59:37', NULL),
(2, 'demo-teacher-science', '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995 \\u2014 \\u09ac\\u09bf\\u099c\\u09cd\\u099e\\u09be\\u09a8\",\"en\":\"Demo teacher \\u2014 Science\"}', '{\"bn\":\"\\u09aa\\u09a6\\u09be\\u09b0\\u09cd\\u09a5 \\u0993 \\u09b0\\u09b8\\u09be\\u09af\\u09bc\\u09a8\",\"en\":\"Physics & chemistry\"}', NULL, '{\"bn\":\"\\u098f\\u099f\\u09bf \\u09a1\\u09c7\\u09ae\\u09cb \\u09aa\\u09cd\\u09b0\\u09cb\\u09ab\\u09be\\u0987\\u09b2 \\u2014 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09a4\\u09a5\\u09cd\\u09af \\u09a6\\u09bf\\u09af\\u09bc\\u09c7 \\u09ac\\u09a6\\u09b2\\u09c7 \\u09a6\\u09bf\\u09a8\\u0964\",\"en\":\"Demo profile \\u2014 replace with the real teacher from the admin panel.\"}', 'rich', '{\"bn\":\"\\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u09be\\u0997\\u09a4 \\u09af\\u09cb\\u0997\\u09cd\\u09af\\u09a4\\u09be, \\u0985\\u09ad\\u09bf\\u099c\\u09cd\\u099e\\u09a4\\u09be \\u0993 \\u0985\\u09b0\\u09cd\\u099c\\u09a8 \\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u09b2\\u09bf\\u0996\\u09c1\\u09a8\\u0964\\n\\u099a\\u09be\\u0987\\u09b2\\u09c7 \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae HTML \\u09aa\\u09c7\\u099c\\u0993 \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be \\u09af\\u09be\\u09af\\u09bc\\u0964\",\"en\":\"Add qualifications, experience and achievements here.\"}', NULL, NULL, 1, 1, '2026-11-19 16:06:29', '2026-11-19 16:06:29', NULL),
(3, 'demo-teacher-commerce', '{\"bn\":\"\\u09a1\\u09c7\\u09ae\\u09cb \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995 \\u2014 \\u09b9\\u09bf\\u09b8\\u09be\\u09ac\\u09ac\\u09bf\\u099c\\u09cd\\u099e\\u09be\\u09a8\",\"en\":\"Demo teacher \\u2014 Accounting\"}', '{\"bn\":\"\\u09b9\\u09bf\\u09b8\\u09be\\u09ac\\u09ac\\u09bf\\u099c\\u09cd\\u099e\\u09be\\u09a8 \\u0993 \\u09ac\\u09cd\\u09af\\u09ac\\u09b8\\u09be\\u09af\\u09bc \\u0989\\u09a6\\u09cd\\u09af\\u09cb\\u0997\",\"en\":\"Accounting & business\"}', NULL, '{\"bn\":\"\\u098f\\u099f\\u09bf \\u09a1\\u09c7\\u09ae\\u09cb \\u09aa\\u09cd\\u09b0\\u09cb\\u09ab\\u09be\\u0987\\u09b2 \\u2014 \\u0985\\u09cd\\u09af\\u09be\\u09a1\\u09ae\\u09bf\\u09a8 \\u09aa\\u09cd\\u09af\\u09be\\u09a8\\u09c7\\u09b2 \\u09a5\\u09c7\\u0995\\u09c7 \\u0986\\u09b8\\u09b2 \\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u0995\\u09c7\\u09b0 \\u09a4\\u09a5\\u09cd\\u09af \\u09a6\\u09bf\\u09af\\u09bc\\u09c7 \\u09ac\\u09a6\\u09b2\\u09c7 \\u09a6\\u09bf\\u09a8\\u0964\",\"en\":\"Demo profile \\u2014 replace with the real teacher from the admin panel.\"}', 'rich', '{\"bn\":\"\\u09b6\\u09bf\\u0995\\u09cd\\u09b7\\u09be\\u0997\\u09a4 \\u09af\\u09cb\\u0997\\u09cd\\u09af\\u09a4\\u09be, \\u0985\\u09ad\\u09bf\\u099c\\u09cd\\u099e\\u09a4\\u09be \\u0993 \\u0985\\u09b0\\u09cd\\u099c\\u09a8 \\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u09b2\\u09bf\\u0996\\u09c1\\u09a8\\u0964\\n\\u099a\\u09be\\u0987\\u09b2\\u09c7 \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae HTML \\u09aa\\u09c7\\u099c\\u0993 \\u09a6\\u09c7\\u0993\\u09af\\u09bc\\u09be \\u09af\\u09be\\u09af\\u09bc\\u0964\",\"en\":\"Add qualifications, experience and achievements here.\"}', NULL, NULL, 1, 2, '2026-11-19 16:06:29', '2026-11-19 16:06:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `author_role` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`author_role`)),
  `audience` varchar(20) NOT NULL DEFAULT 'parent',
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`body`)),
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `photo_url` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `consent_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'staff',
  `office_id` varchar(20) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `joined_on` date DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `type`, `office_id`, `designation`, `joined_on`, `date_of_birth`, `is_active`, `email`, `email_verified_at`, `password`, `remember_token`, `last_login_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Md. Nur A Alam', '+8801307631378', 'staff', 'CNT-01', 'CEO, Chintor', '2026-12-01', '2002-01-01', 1, 'mdnuralam2812@gmail.com', NULL, '$2y$12$CskLUb4Xy.zavmz/iiw2puG/RDdXWc2HBtXsMeOPvY7bQLKoDRRRG', 'zmxi7cwLABwrctY3QsUbPwZXN0Cm13yx3r8NeYy0yqK0tlfWMdPUPWLVTv4M', '2026-11-19 23:05:22', '2026-11-19 14:59:03', '2026-11-19 23:05:22', NULL),
(2, 'ডেমো ম্যানেজার', '+8801700000001', 'staff', 'CNT-D1', 'ডেমো অ্যাকাউন্ট', NULL, NULL, 1, NULL, NULL, '$2y$12$5HCNSQO9K5cn68aH0gKJwedofcxalP7.GKXaIRzKQP5T55eOiGM1O', NULL, NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(3, 'ডেমো মার্কেটিং', '+8801700000002', 'staff', 'CNT-D2', 'ডেমো অ্যাকাউন্ট', NULL, NULL, 1, NULL, NULL, '$2y$12$w3PiciY1FPYuHvOcBe2HjuIiGA70In9K/vFyGbMe/9YbXuIvtaq2.', NULL, NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(4, 'ডেমো সেলস এক্সিকিউটিভ', '+8801700000003', 'staff', 'CNT-D3', 'ডেমো অ্যাকাউন্ট', NULL, NULL, 1, NULL, NULL, '$2y$12$xK4Gd4PTyqlHd6PLZ47nIOa2HBEkdHW68Pg5gsMv6wokxLuYQD5jO', NULL, NULL, '2026-11-19 16:06:30', '2026-11-19 16:06:30', NULL),
(5, 'ডেমো কোর্স কোঅর্ডিনেটর', '+8801700000004', 'staff', 'CNT-D4', 'ডেমো অ্যাকাউন্ট', NULL, NULL, 1, NULL, NULL, '$2y$12$44s7or5HaxClJ1B2wyNaV.7uBCvpI3WVIDZ7yeDCAE.nYMAhig0my', NULL, NULL, '2026-11-19 16:06:31', '2026-11-19 16:06:31', NULL),
(6, 'ডেমো ডেভেলপার', '+8801700000005', 'staff', 'CNT-D5', 'Developer (ডেমো অ্যাকাউন্ট)', NULL, NULL, 1, NULL, NULL, '$2y$12$UrIVQ/CacopQ1mwg0oi/SOEunMDRF/V.kdwvRHNiYaNVXFY.TDMDa', NULL, NULL, '2026-11-19 23:28:28', '2026-11-19 23:28:28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assessments_ulid_unique` (`ulid`),
  ADD KEY `assessments_lead_id_foreign` (`lead_id`),
  ADD KEY `assessments_course_id_foreign` (`course_id`),
  ADD KEY `assessments_teacher_id_foreign` (`teacher_id`),
  ADD KEY `assessments_scheduled_by_foreign` (`scheduled_by`),
  ADD KEY `assessments_recommended_course_id_foreign` (`recommended_course_id`),
  ADD KEY `assessments_status_index` (`status`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `authors_slug_unique` (`slug`),
  ADD KEY `authors_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `author_book`
--
ALTER TABLE `author_book`
  ADD PRIMARY KEY (`author_id`,`book_id`),
  ADD KEY `author_book_book_id_foreign` (`book_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `books_slug_unique` (`slug`),
  ADD KEY `books_program_id_foreign` (`program_id`),
  ADD KEY `books_status_index` (`status`);

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
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaigns_is_active_starts_at_ends_at_index` (`is_active`,`starts_at`,`ends_at`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_slug_unique` (`slug`),
  ADD KEY `courses_program_id_foreign` (`program_id`),
  ADD KEY `courses_status_index` (`status`);

--
-- Indexes for table `course_batches`
--
ALTER TABLE `course_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_batches_code_unique` (`code`),
  ADD KEY `course_batches_course_id_foreign` (`course_id`),
  ADD KEY `course_batches_status_index` (`status`);

--
-- Indexes for table `course_teacher`
--
ALTER TABLE `course_teacher`
  ADD PRIMARY KEY (`course_id`,`teacher_id`),
  ADD KEY `course_teacher_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `course_variations`
--
ALTER TABLE `course_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_variations_course_id_foreign` (`course_id`);

--
-- Indexes for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `developer_query_logs`
--
ALTER TABLE `developer_query_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `developer_query_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollments_ulid_unique` (`ulid`),
  ADD UNIQUE KEY `enrollments_reference_unique` (`reference`),
  ADD KEY `enrollments_guardian_id_foreign` (`guardian_id`),
  ADD KEY `enrollments_student_id_foreign` (`student_id`),
  ADD KEY `enrollments_course_id_foreign` (`course_id`),
  ADD KEY `enrollments_course_batch_id_foreign` (`course_batch_id`),
  ADD KEY `enrollments_course_variation_id_foreign` (`course_variation_id`),
  ADD KEY `enrollments_lead_id_foreign` (`lead_id`),
  ADD KEY `enrollments_sold_by_foreign` (`sold_by`),
  ADD KEY `enrollments_status_index` (`status`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exams_slug_unique` (`slug`),
  ADD KEY `exams_subject_id_foreign` (`subject_id`),
  ADD KEY `exams_is_published_index` (`is_published`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_attempts_ulid_unique` (`ulid`),
  ADD KEY `exam_attempts_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_attempts_lead_id_foreign` (`lead_id`);

--
-- Indexes for table `exam_question`
--
ALTER TABLE `exam_question`
  ADD PRIMARY KEY (`exam_id`,`question_id`),
  ADD KEY `exam_question_question_id_foreign` (`question_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faqs_faqable_type_faqable_id_index` (`faqable_type`,`faqable_id`);

--
-- Indexes for table `guardians`
--
ALTER TABLE `guardians`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guardians_ulid_unique` (`ulid`),
  ADD UNIQUE KEY `guardians_phone_unique` (`phone`),
  ADD KEY `guardians_user_id_foreign` (`user_id`);

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
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leads_ulid_unique` (`ulid`),
  ADD UNIQUE KEY `leads_reference_unique` (`reference`),
  ADD KEY `leads_guardian_id_foreign` (`guardian_id`),
  ADD KEY `leads_student_id_foreign` (`student_id`),
  ADD KEY `leads_type_index` (`type`),
  ADD KEY `leads_status_index` (`status`),
  ADD KEY `leads_phone_index` (`phone`),
  ADD KEY `leads_assigned_to_index` (`assigned_to`),
  ADD KEY `leads_next_follow_up_at_index` (`next_follow_up_at`);

--
-- Indexes for table `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_activities_lead_id_foreign` (`lead_id`),
  ADD KEY `lead_activities_user_id_foreign` (`user_id`);

--
-- Indexes for table `lead_interests`
--
ALTER TABLE `lead_interests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_interests_lead_id_foreign` (`lead_id`),
  ADD KEY `lead_interests_program_id_foreign` (`program_id`),
  ADD KEY `lead_interests_course_id_foreign` (`course_id`),
  ADD KEY `lead_interests_book_id_foreign` (`book_id`);

--
-- Indexes for table `learning_topics`
--
ALTER TABLE `learning_topics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `learning_topics_slug_unique` (`slug`),
  ADD KEY `learning_topics_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `learning_videos`
--
ALTER TABLE `learning_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_videos_subject_id_foreign` (`subject_id`),
  ADD KEY `learning_videos_topic_id_foreign` (`topic_id`),
  ADD KEY `learning_videos_is_featured_index` (`is_featured`),
  ADD KEY `learning_videos_is_published_index` (`is_published`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_mediable_type_mediable_id_index` (`mediable_type`,`mediable_id`),
  ADD KEY `media_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_ulid_unique` (`ulid`),
  ADD UNIQUE KEY `orders_reference_unique` (`reference`),
  ADD KEY `orders_guardian_id_foreign` (`guardian_id`),
  ADD KEY `orders_lead_id_foreign` (`lead_id`),
  ADD KEY `orders_delivery_zone_id_foreign` (`delivery_zone_id`),
  ADD KEY `orders_phone_index` (`phone`),
  ADD KEY `orders_status_index` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_book_id_foreign` (`book_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`),
  ADD KEY `pages_status_index` (`status`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_ulid_unique` (`ulid`),
  ADD UNIQUE KEY `payments_reference_unique` (`reference`),
  ADD UNIQUE KEY `payments_method_transaction_id_unique` (`method`,`transaction_id`),
  ADD KEY `payments_payable_type_payable_id_index` (`payable_type`,`payable_id`),
  ADD KEY `payments_verified_by_foreign` (`verified_by`),
  ADD KEY `payments_status_index` (`status`);

--
-- Indexes for table `payment_accounts`
--
ALTER TABLE `payment_accounts`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `programs_slug_unique` (`slug`),
  ADD KEY `programs_status_index` (`status`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_subject_id_foreign` (`subject_id`),
  ADD KEY `questions_topic_id_foreign` (`topic_id`),
  ADD KEY `questions_class_level_index` (`class_level`),
  ADD KEY `questions_is_active_index` (`is_active`);

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
  ADD PRIMARY KEY (`role_id`,`user_id`),
  ADD KEY `role_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`),
  ADD KEY `settings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_ulid_unique` (`ulid`),
  ADD KEY `students_guardian_id_foreign` (`guardian_id`),
  ADD KEY `students_user_id_foreign` (`user_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teachers_slug_unique` (`slug`),
  ADD KEY `teachers_user_id_foreign` (`user_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `testimonials_course_id_foreign` (`course_id`),
  ADD KEY `testimonials_audience_index` (`audience`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD UNIQUE KEY `users_office_id_unique` (`office_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_batches`
--
ALTER TABLE `course_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_variations`
--
ALTER TABLE `course_variations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `developer_query_logs`
--
ALTER TABLE `developer_query_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `guardians`
--
ALTER TABLE `guardians`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lead_activities`
--
ALTER TABLE `lead_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_interests`
--
ALTER TABLE `lead_interests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `learning_topics`
--
ALTER TABLE `learning_topics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `learning_videos`
--
ALTER TABLE `learning_videos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_accounts`
--
ALTER TABLE `payment_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `assessments_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessments_recommended_course_id_foreign` FOREIGN KEY (`recommended_course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `assessments_scheduled_by_foreign` FOREIGN KEY (`scheduled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `assessments_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `authors`
--
ALTER TABLE `authors`
  ADD CONSTRAINT `authors_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `author_book`
--
ALTER TABLE `author_book`
  ADD CONSTRAINT `author_book_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `author_book_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `course_batches`
--
ALTER TABLE `course_batches`
  ADD CONSTRAINT `course_batches_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_teacher`
--
ALTER TABLE `course_teacher`
  ADD CONSTRAINT `course_teacher_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_variations`
--
ALTER TABLE `course_variations`
  ADD CONSTRAINT `course_variations_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `developer_query_logs`
--
ALTER TABLE `developer_query_logs`
  ADD CONSTRAINT `developer_query_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_course_batch_id_foreign` FOREIGN KEY (`course_batch_id`) REFERENCES `course_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `enrollments_course_variation_id_foreign` FOREIGN KEY (`course_variation_id`) REFERENCES `course_variations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`),
  ADD CONSTRAINT `enrollments_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_sold_by_foreign` FOREIGN KEY (`sold_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `learning_topics` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `exam_attempts_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exam_attempts_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_question`
--
ALTER TABLE `exam_question`
  ADD CONSTRAINT `exam_question_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_question_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guardians`
--
ALTER TABLE `guardians`
  ADD CONSTRAINT `guardians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD CONSTRAINT `lead_activities_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_interests`
--
ALTER TABLE `lead_interests`
  ADD CONSTRAINT `lead_interests_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_interests_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_interests_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_interests_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_topics`
--
ALTER TABLE `learning_topics`
  ADD CONSTRAINT `learning_topics_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `learning_topics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_videos`
--
ALTER TABLE `learning_videos`
  ADD CONSTRAINT `learning_videos_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `learning_topics` (`id`),
  ADD CONSTRAINT `learning_videos_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `learning_topics` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_delivery_zone_id_foreign` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `learning_topics` (`id`),
  ADD CONSTRAINT `questions_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `learning_topics` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
