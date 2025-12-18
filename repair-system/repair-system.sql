-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2025 at 05:16 PM
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
-- Database: `repair-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

CREATE TABLE `auth` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','technical') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth`
--

INSERT INTO `auth` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-12-18 14:57:01'),
(2, 'student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2025-12-18 14:57:01'),
(3, 'student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2025-12-18 14:57:01'),
(4, 'tech1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technical', '2025-12-18 14:57:01'),
(5, 'tech2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technical', '2025-12-18 14:57:01'),
(6, 'sale', '$2y$10$aAXWXODyb4CzEGg3NOd5WOKt98ba2eoerUWtBFFnoKiE43NZN9EUO', 'technical', '2025-12-18 15:22:21'),
(7, 'phoom', '$2y$10$9eSITZsFciyCzfcNHL6o.u5kTVHdkvdZxaZu/aWc4uHSjR7RBd5Qq', 'student', '2025-12-18 15:39:31'),
(8, 'phoom2', '$2y$10$K04s5GCTVs.3ez2i8CfDCO3lSyBy6bowJQ6aNOFT8PTzorMVHkvw6', 'student', '2025-12-18 15:39:46'),
(9, 'student4', '$2y$10$jlDBPreEFd25SfDmildET.5Mrb6vl1ZC0wuJBpGRszrB1xQNQu2ya', 'student', '2025-12-18 15:42:01'),
(10, 'test2', '$2y$10$NgPmerL0OjSO6.xbleB/4enLLoLSsWf7ePbpxPj49wL2fXfxCVl8O', 'technical', '2025-12-18 16:14:00');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `created_at`) VALUES
(1, 'โปรเจคเตอร์', '2025-12-18 14:57:01'),
(2, 'เครื่องคอมพิวเตอร์', '2025-12-18 14:57:01'),
(3, 'เครื่องพิมพ์', '2025-12-18 14:57:01'),
(4, 'เครื่องเสียง', '2025-12-18 14:57:01'),
(5, 'ลำโพง', '2025-12-18 14:57:01'),
(6, 'ไมค์', '2025-12-18 14:57:01'),
(7, 'กล้องวงจรปิด', '2025-12-18 14:57:01'),
(8, 'เครื่องปรับอากาศ', '2025-12-18 14:57:01'),
(9, 'เครื่องทำน้ำอุ่น', '2025-12-18 14:57:01'),
(10, 'กล้องถ่ายรูป', '2025-12-18 14:57:01');

-- --------------------------------------------------------

--
-- Table structure for table `repair`
--

CREATE TABLE `repair` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `details` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair`
--

INSERT INTO `repair` (`id`, `student_id`, `equipment_id`, `details`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'โปรเจคเตอร์ไม่ติด ไฟไม่เขียว', 'upload/repair/proj1.jpg', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(2, 1, 3, 'เครื่องพิมพ์พิมพ์ไม่ออก สีจาง', 'upload/repair/printer1.jpg', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(3, 2, 2, 'เครื่องคอมพิวเตอร์ค้างตลอดเวลา', 'upload/repair/pc1.jpg', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(4, 2, 4, 'เครื่องเสียงไม่มีเสียงตอนเช้า', 'upload/repair/sound1.jpg', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(5, 1, 6, 'xcdsfdsds', 'upload/repair/69442237e0924.jpg', '2025-12-18 15:48:07', '2025-12-18 15:48:07');

-- --------------------------------------------------------

--
-- Table structure for table `repair_detail`
--

CREATE TABLE `repair_detail` (
  `id` int(11) NOT NULL,
  `repair_id` int(11) NOT NULL,
  `technical_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_detail`
--

INSERT INTO `repair_detail` (`id`, `repair_id`, `technical_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'รอซ่อม', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(2, 2, 1, 'กำลังซ่อม', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(3, 3, 2, 'รอซ่อม', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(4, 3, 2, 'กำลังซ่อม', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(5, 3, 2, 'เสร็จสิ้น', '2025-12-18 14:57:01', '2025-12-18 14:57:01'),
(6, 1, 1, 'รอซ่อม', '2025-12-18 15:51:54', '2025-12-18 15:51:54'),
(7, 5, 2, 'รอซ่อม', '2025-12-18 15:52:39', '2025-12-18 15:52:39'),
(8, 4, 3, 'รอซ่อม', '2025-12-18 15:54:24', '2025-12-18 15:54:24'),
(9, 4, 3, 'เสร็จสิ้น', '2025-12-18 15:54:32', '2025-12-18 15:54:32');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `title` varchar(10) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `auth_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `title`, `firstname`, `lastname`, `student_id`, `auth_id`, `created_at`) VALUES
(1, 'นาย', 'สมชาย', 'ใจดี', '63010001', 2, '2025-12-18 14:57:01'),
(2, 'นางสาว', 'สมหญิง', 'งามสง่า', '63010002', 3, '2025-12-18 14:57:01');

-- --------------------------------------------------------

--
-- Table structure for table `technical`
--

CREATE TABLE `technical` (
  `id` int(11) NOT NULL,
  `title` varchar(10) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `auth_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `technical`
--

INSERT INTO `technical` (`id`, `title`, `firstname`, `lastname`, `phone`, `auth_id`, `created_at`) VALUES
(1, 'นาย', 'ช่างห่วย', 'ซ่อมเก่ง', '0812345678', 4, '2025-12-18 14:57:01'),
(2, 'นาง', 'ช่างเก่ง', 'ซ่อมไว', '0823456789', 5, '2025-12-18 14:57:01'),
(3, 'นาย', 'sale', 'sale', '0888888888', 6, '2025-12-18 15:22:21'),
(4, 'นาง', 'test2', 'test2', '0856666666', 10, '2025-12-18 16:14:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `repair`
--
ALTER TABLE `repair`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `repair_detail`
--
ALTER TABLE `repair_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_id` (`repair_id`),
  ADD KEY `technical_id` (`technical_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `auth_id` (`auth_id`);

--
-- Indexes for table `technical`
--
ALTER TABLE `technical`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auth_id` (`auth_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth`
--
ALTER TABLE `auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `repair`
--
ALTER TABLE `repair`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `repair_detail`
--
ALTER TABLE `repair_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `technical`
--
ALTER TABLE `technical`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `repair`
--
ALTER TABLE `repair`
  ADD CONSTRAINT `repair_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`),
  ADD CONSTRAINT `repair_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`);

--
-- Constraints for table `repair_detail`
--
ALTER TABLE `repair_detail`
  ADD CONSTRAINT `repair_detail_ibfk_1` FOREIGN KEY (`repair_id`) REFERENCES `repair` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repair_detail_ibfk_2` FOREIGN KEY (`technical_id`) REFERENCES `technical` (`id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`auth_id`) REFERENCES `auth` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `technical`
--
ALTER TABLE `technical`
  ADD CONSTRAINT `technical_ibfk_1` FOREIGN KEY (`auth_id`) REFERENCES `auth` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
