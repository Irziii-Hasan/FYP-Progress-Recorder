-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2024 at 11:55 AM
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
-- Database: `fyp_progress_recorder`
--

-- --------------------------------------------------------

--
-- Table structure for table `form_detail`
--

CREATE TABLE `form_detail` (
  `id` int(11) NOT NULL,
  `form_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `max_marks` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_detail`
--

INSERT INTO `form_detail` (`id`, `form_id`, `description`, `max_marks`) VALUES
(1, 1, 'erd diagram', 5),
(2, 1, 'prototype', 5),
(3, 2, 'internship', 5),
(4, 2, 'implementation', 10),
(5, 3, 'internship', 5),
(6, 3, 'implementation', 10),
(7, 4, 'erd diagram', 10),
(8, 4, 'internship', 20),
(10, 6, 'internship', 60),
(11, 7, 'internship', 80),
(12, 8, 'internship', 40),
(13, 8, 'competition', 20),
(14, 9, 'internship', 80),
(21, 10, 'internship', 20),
(22, 10, 'erd diagram', 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `form_detail`
--
ALTER TABLE `form_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_detail_ibfk_1` (`form_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `form_detail`
--
ALTER TABLE `form_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `form_detail`
--
ALTER TABLE `form_detail`
  ADD CONSTRAINT `form_detail_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `customized_form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
