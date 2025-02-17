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
-- Table structure for table `customized_form`
--

CREATE TABLE `customized_form` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `passing_marks` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customized_form`
--

INSERT INTO `customized_form` (`id`, `title`, `passing_marks`, `total_marks`) VALUES
(1, 'FYP I MID', 15, 30),
(2, 'FYP I MID', 8, 15),
(3, 'FYP I MID', 8, 15),
(4, 'FYP I MID', 15, 30),
(6, 'FYP I MID', 40, 60),
(7, 'FYP I MID', 69, 80),
(8, 'FYP I MID', 40, 60),
(9, 'FYP I MID', 50, 80),
(10, 'FYP I MID', 80, 100);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customized_form`
--
ALTER TABLE `customized_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customized_form`
--
ALTER TABLE `customized_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
