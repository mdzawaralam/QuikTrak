-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2024 at 05:23 PM
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
-- Database: `qt_inspection`
--

-- --------------------------------------------------------

--
-- Table structure for table `assigned_job_to_user`
--

CREATE TABLE `assigned_job_to_user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `timezone_id` int(11) NOT NULL,
  `assigned_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `job_deadline` date DEFAULT NULL,
  `completed_job_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `job_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assigned_job_to_user`
--

INSERT INTO `assigned_job_to_user` (`id`, `user_id`, `job_id`, `timezone_id`, `assigned_at`, `updated_at`, `job_deadline`, `completed_job_at`, `job_status`) VALUES
(1, 2, 3, 3, '2024-12-23 14:54:35', '2024-12-23 15:03:40', '2025-01-15', '2024-12-23 15:03:40', 'Completed'),
(2, 1, 1, 2, '2024-12-23 14:54:43', '2024-12-23 14:54:43', '2025-01-10', NULL, 'Pending'),
(3, 2, 1, 2, '2024-12-23 14:54:51', '2024-12-23 14:54:51', '2025-01-10', NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20241219103855', '2024-12-19 11:42:14', 10),
('DoctrineMigrations\\Version20241219141717', '2024-12-19 15:18:57', 8),
('DoctrineMigrations\\Version20241220130904', '2024-12-20 14:10:16', 38),
('DoctrineMigrations\\Version20241221031104', '2024-12-21 04:11:47', 66),
('DoctrineMigrations\\Version20241222041916', '2024-12-22 05:19:55', 62),
('DoctrineMigrations\\Version20241222042624', '2024-12-22 05:26:55', 17),
('DoctrineMigrations\\Version20241222061827', '2024-12-22 07:18:42', 13);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `name`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Job 1', '2024-12-23 14:40:14', '2024-12-23 14:43:45', 1),
(2, 'Job 2', '2024-12-23 14:40:20', '2024-12-23 14:40:20', 1),
(3, 'Job 3', '2024-12-23 14:40:25', '2024-12-23 14:40:25', 1);

-- --------------------------------------------------------

--
-- Table structure for table `timezone`
--

CREATE TABLE `timezone` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `status` tinyint(1) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timezone`
--

INSERT INTO `timezone` (`id`, `name`, `created_at`, `updated_at`, `status`, `location`) VALUES
(1, 'Asia/Kolkata', '2024-12-22 07:30:34', '2024-12-22 07:30:34', 1, 'India'),
(2, 'America/Mexico_City', '2024-12-22 07:33:55', '2024-12-22 07:33:55', 1, 'Mexico'),
(3, 'Europe/London', '2024-12-22 07:36:17', '2024-12-22 08:16:21', 1, 'UK');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `phone`, `created_at`, `updated_at`, `status`, `username`, `password`, `token`) VALUES
(1, 'Allan John', 'ajohn@gmail.com', 546575675, '2024-12-23', '2024-12-23', 1, 'allanjohn', '$2y$10$KvdO/sD60XmIbX04c.0aVOrAZtMvNMp8Z9eZd5SXqzoBRwELbphti', 'c0403e8c79a0fbc442162278ef658f01'),
(2, 'Mr kumar', 'kumar@gmail.com', 55678897, '2024-12-23', '2024-12-23', 1, 'kumar', '$2y$10$aGFm4hAcZU.uioLOQe0ciel7uHASRAvbdWKrr.VqgHJjPTaDxpwcG', 'ca0bfad2d2ac3f9db1781859d8ce6325');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assigned_job_to_user`
--
ALTER TABLE `assigned_job_to_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timezone`
--
ALTER TABLE `timezone`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assigned_job_to_user`
--
ALTER TABLE `assigned_job_to_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `timezone`
--
ALTER TABLE `timezone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
