-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 31, 2026 at 12:20 PM
-- Server version: 8.0.46-37
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mceioe21_iot`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_logs`
--

CREATE TABLE `api_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `device_id` int UNSIGNED DEFAULT NULL,
  `request_method` varchar(10) NOT NULL DEFAULT 'GET',
  `request_data` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `response_status` smallint UNSIGNED NOT NULL DEFAULT '200',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int UNSIGNED NOT NULL,
  `device_uuid` char(36) NOT NULL,
  `device_name` varchar(150) NOT NULL,
  `device_type` varchar(100) NOT NULL,
  `description` text,
  `owner_id` int UNSIGNED NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `last_value` varchar(255) DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `device_uuid`, `device_name`, `device_type`, `description`, `owner_id`, `status`, `last_value`, `last_seen`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '550e8400-e29b-41d4-a716-446655440000', 'Demo Temperature Sensor', 'DECIMAL', 'Sample IoT temperature monitoring device', 3, 1, '22.5', NULL, '2026-07-30 11:36:27', '2026-07-31 11:26:01', NULL),
(2, '7a72365b-97f2-4995-8ffb-e63fc6ba6fae', 'New', 'BOOLEAN', 'sadasda', 2, 1, '1', '2026-07-30 16:11:22', '2026-07-30 13:57:12', '2026-07-31 11:24:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `device_logs`
--

CREATE TABLE `device_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `device_id` int UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `device_logs`
--

INSERT INTO `device_logs` (`id`, `device_id`, `value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, '1', '192.168.1.100', 'ESP32 HTTP Client', '2026-07-30 11:36:27'),
(2, 1, '26.1', '192.168.1.100', 'ESP32 HTTP Client', '2026-07-30 11:31:27'),
(3, 1, '24.8', '192.168.1.100', 'ESP32 HTTP Client', '2026-07-30 11:26:27'),
(4, 1, '25.5', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:04:28'),
(5, 2, '0', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:04:37'),
(6, 1, '100', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:04:42'),
(7, 2, '0', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:08:33'),
(8, 2, '1', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:10:39'),
(9, 2, '0', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:11:06'),
(10, 2, '1', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:11:12'),
(11, 2, '0', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:11:15'),
(12, 2, '1', '106.219.182.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 16:11:22');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'MCEIoT', '2026-07-30 11:36:27', '2026-07-30 11:36:27'),
(2, 'timezone', 'Asia/Kolkata', '2026-07-30 11:36:27', '2026-07-30 11:36:27'),
(3, 'api_authentication', 'disabled', '2026-07-30 11:36:27', '2026-07-30 11:36:27'),
(4, 'api_method', 'GET', '2026-07-30 11:36:27', '2026-07-30 11:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` tinyint UNSIGNED NOT NULL DEFAULT '3',
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `failed_attempts` int UNSIGNED NOT NULL DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `status`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin', 'admin@mceiot.local', '$2y$10$XS7SDBsWGRWlzBPyXNLp1O86Pps4eKYcQFGnfVxJFtdD8aa9QpbqC', 1, 1, 0, NULL, '2026-07-31 11:59:41', '2026-07-30 11:36:27', '2026-07-31 11:59:41', NULL),
(2, 'Device Owner', 'owner@mceiot.local', '$2y$10$eNfFIiIQG4gfTmRzuCxGCeHztnzqlejD/v2gILYbUQ/RBdjvsRKlu', 3, 1, 0, NULL, '2026-07-31 11:26:11', '2026-07-30 11:36:27', '2026-07-31 11:26:11', NULL),
(3, 'Read Only User', 'readonly@mceiot.local', '$2y$10$w5gcB3ajG6GKIe4d2feCYusx1eGSVZSJVf1w.S92nfPXm.hKYjtIC', 4, 1, 0, NULL, '2026-07-31 11:26:54', '2026-07-30 11:36:27', '2026-07-31 11:26:54', NULL),
(4, 'siva', 'siva@mceiot.local', '$2y$10$Kbm9m84UQzYLCYBOJi1SMOEg6RH2AOUSyWGImxyAlCAdQDIse41rq', 3, 1, 0, NULL, NULL, '2026-07-30 15:32:02', '2026-07-30 15:32:02', NULL),
(5, 'Admin1', 'admin1@mceiot.local', '$2y$10$5o8ZcuVHgxIPb2IdgN7TnOoNr.ysUxGsSK4KWsKE9gVN.NeWnCxcS', 2, 1, 0, NULL, '2026-07-31 11:23:57', '2026-07-31 11:23:40', '2026-07-31 11:23:57', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_logs`
--
ALTER TABLE `api_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_api_logs_device` (`device_id`),
  ADD KEY `idx_api_logs_created` (`created_at`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_uuid` (`device_uuid`),
  ADD KEY `idx_devices_uuid` (`device_uuid`),
  ADD KEY `idx_devices_owner` (`owner_id`),
  ADD KEY `idx_devices_status` (`status`),
  ADD KEY `idx_device_owner_status` (`owner_id`,`status`);

--
-- Indexes for table `device_logs`
--
ALTER TABLE `device_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_device_logs_device` (`device_id`),
  ADD KEY `idx_device_logs_created` (`created_at`),
  ADD KEY `idx_device_time` (`device_id`,`created_at`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_status` (`status`),
  ADD KEY `idx_user_active` (`status`,`deleted_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_logs`
--
ALTER TABLE `api_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `device_logs`
--
ALTER TABLE `device_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_logs`
--
ALTER TABLE `api_logs`
  ADD CONSTRAINT `fk_api_logs_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `fk_devices_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `device_logs`
--
ALTER TABLE `device_logs`
  ADD CONSTRAINT `fk_device_logs_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
