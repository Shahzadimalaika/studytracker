-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2025 at 08:59 AM
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
-- Database: `study_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `study_logs`
--

CREATE TABLE `study_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `study_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_logs`
--

INSERT INTO `study_logs` (`id`, `user_id`, `subject`, `duration_minutes`, `study_date`, `created_at`) VALUES
(1, 1, 'Biology', 120, '2025-06-29', '2025-06-29 12:13:06'),
(2, 2, 'Computer science', 30, '2025-06-30', '2025-06-29 12:27:04'),
(3, 2, 'Physic', 90, '2025-07-01', '2025-06-29 12:34:00'),
(4, 2, 'English', 80, '2025-02-07', '2025-06-29 12:34:51'),
(5, 2, 'Math', 120, '2025-06-29', '2025-06-29 12:48:46'),
(6, 2, 'Urdu', 120, '2025-06-29', '2025-06-29 13:11:22'),
(7, 2, 'Pak Study', 120, '2025-06-29', '2025-06-29 13:24:48'),
(8, 3, 'Biology', 30, '2025-06-29', '2025-06-29 14:39:05'),
(9, 3, 'Computer science', 60, '2025-07-25', '2025-06-29 15:55:27'),
(10, 4, 'Ecnomic', 1000, '2025-07-04', '2025-07-04 16:46:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `daily_goal` int(11) DEFAULT 120
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `daily_goal`) VALUES
(1, 'ShahzadiMalaika', 'malaikashahzadi977@gmail.com', '$2y$10$paskvm4yL/GtDMd0lDNIWe7zK1fNsS9WEslPhRXU0iZWEDOV/s1fO', '2025-06-29 11:39:05', 120),
(2, 'Musa', 'ms5966168@gmail.com', '$2y$10$nQnKNuKRc63stYfEU2pruO8a5v0RwWEKy3N736Ija7958rCp7FuHm', '2025-06-29 12:11:53', 130),
(3, 'Kashaf', 'Kashaf333@gmail.com', '$2y$10$b1SaLFVLsCAMkOBSsIHBHuVG77WtZgrBErglkd3hwaD6oG2.EPwma', '2025-06-29 14:38:18', 90),
(4, 'ShahzadiMalaika', 'shahzadimalaika173@gmail.com', '$2y$10$saQlN23Ug4RkofMU84cqa.J0/hqYRQ/cQqZlFIA4DBHvQ5vyX///6', '2025-07-04 16:46:05', 120);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `study_logs`
--
ALTER TABLE `study_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `study_logs`
--
ALTER TABLE `study_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `study_logs`
--
ALTER TABLE `study_logs`
  ADD CONSTRAINT `study_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
