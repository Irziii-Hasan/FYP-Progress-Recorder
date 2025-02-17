-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2024 at 04:15 PM
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
-- Table structure for table `student_marks`
--

CREATE TABLE `student_marks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `student_marks` int(11) NOT NULL,
  `marks_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_marks`
--

INSERT INTO `student_marks` (`id`, `project_id`, `student_id`, `student_marks`, `marks_title`) VALUES
(86, 0, '166', 0, NULL),
(87, 0, '161', 111, NULL),
(88, 0, '21', 111, NULL),
(89, 0, '160', 21, NULL),
(90, 0, '159', 21, NULL),
(91, 0, '12', 0, NULL),
(92, 0, '49', 0, NULL),
(93, 0, '41', 0, NULL),
(94, 0, '114', 0, NULL),
(95, 0, '146', 0, NULL),
(96, 0, '137', 0, NULL),
(97, 0, '67', 0, NULL),
(98, 0, '103', 0, NULL),
(99, 0, '30', 0, NULL),
(100, 0, '44', 0, NULL),
(101, 0, '46', 0, NULL),
(102, 0, '118', 0, NULL),
(103, 0, '81', 0, NULL),
(104, 0, '35', 0, NULL),
(105, 0, '72', 0, NULL),
(106, 0, '108', 0, NULL),
(107, 0, '142', 0, NULL),
(108, 0, '117', 0, ''),
(109, 0, '47', 0, ''),
(110, 0, '45', 0, ''),
(111, 0, '34', 20, 'FYP I'),
(112, 0, '71', 30, 'FYP I'),
(113, 0, '141', 30, 'FYP I'),
(114, 0, '107', 20, 'FYP I'),
(115, 0, '63', 12, 'FYP I'),
(116, 0, '26', 23, 'FYP I'),
(117, 0, '133', 23, 'FYP I'),
(118, 0, '29', 2147483647, 'FYP I'),
(119, 0, '31', 20, 'FYP I'),
(120, 0, '68', 30, 'FYP I'),
(121, 0, '138', 30, 'FYP I'),
(122, 0, '104', 20, 'FYP I');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student_marks`
--
ALTER TABLE `student_marks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student_marks`
--
ALTER TABLE `student_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
