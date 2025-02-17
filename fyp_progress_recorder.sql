-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2024 at 06:46 AM
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
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `audience_type` enum('all','supervisor','students','external') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `assignment_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `deadline` datetime NOT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `BatchID` int(100) NOT NULL,
  `BatchName` varchar(100) NOT NULL,
  `abbreviation` varchar(255) NOT NULL,
  `course_duration_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `clearance`
--

CREATE TABLE `clearance` (
  `clearance_id` int(100) NOT NULL,
  `project_id` int(6) NOT NULL,
  `status` varchar(20) NOT NULL,
  `clearance_date` date NOT NULL,
  `remarks` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coordinator`
--

CREATE TABLE `coordinator` (
  `coordinator_id` int(100) NOT NULL,
  `faculty_id` varchar(20) NOT NULL,
  `batch_id` varchar(50) NOT NULL,
  `year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `course_durations`
--

CREATE TABLE `course_durations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `customized_form`
--

CREATE TABLE `customized_form` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `passing_marks` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `visible` varchar(3) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `duration_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `EventID` int(11) NOT NULL,
  `EventName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `external`
--

CREATE TABLE `external` (
  `external_id` int(100) NOT NULL,
  `juw_id` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `postal_address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `external_total_student_marks`
--

CREATE TABLE `external_total_student_marks` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `external_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `project_id` int(100) NOT NULL,
  `total_marks` decimal(10,2) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `faculty_id` int(11) NOT NULL,
  `juw_id` varchar(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `professional_email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `designation` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

 --------------------------------------------------------

--
-- Table structure for table `form_detail`
--

CREATE TABLE `form_detail` (
  `id` int(11) NOT NULL,
  `form_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `max_marks` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(255) NOT NULL,
  `form_id` int(11) NOT NULL,
  `description_id` int(11) NOT NULL,
  `project_id` int(255) NOT NULL,
  `student_id` int(11) NOT NULL,
  `marks` decimal(5,2) NOT NULL,
  `supervisor` int(11) DEFAULT 0,
  `external` int(11) DEFAULT 0,
  `internal` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `supervisor_id` int(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `project_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `marks` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `attendance_status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presentations`
--

CREATE TABLE `presentations` (
  `room_id` int(25) NOT NULL,
  `presentation_id` int(100) NOT NULL,
  `batch` int(100) NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `internal_evaluator_id` varchar(100) DEFAULT NULL,
  `external_evaluator_id` varchar(100) DEFAULT NULL,
  `send_to` text DEFAULT NULL,
  `type` int(11) NOT NULL,
  `form_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) UNSIGNED NOT NULL,
  `project_id` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `student1` int(50) NOT NULL,
  `student2` int(50) NOT NULL,
  `student3` int(50) DEFAULT NULL,
  `student4` int(50) DEFAULT NULL,
  `supervisor` int(50) NOT NULL,
  `co_supervisor` int(50) DEFAULT NULL,
  `external_supervisor` int(50) DEFAULT NULL,
  `batch` varchar(255) NOT NULL,
  `duration` int(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_predictions`
--

CREATE TABLE `project_predictions` (
  `id` int(11) NOT NULL,
  `project_id` varchar(255) DEFAULT NULL,
  `predicted_progress` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_path` varchar(255) NOT NULL,
  `recipient` enum('external','internal','students','faculty','external_internal') NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `batch` varchar(255) DEFAULT NULL,
  `event_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `result_detail`
--

CREATE TABLE `result_detail` (
  `result_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(20) NOT NULL,
  `room_number` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

 
-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(255) NOT NULL,
  `juw_id` varchar(20) NOT NULL,
  `username` varchar(200) NOT NULL,
  `email` varchar(35) NOT NULL,
  `enrollment` varchar(50) NOT NULL,
  `academic_year` int(8) NOT NULL,
  `degree_program` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `seat_number` int(10) NOT NULL,
  `admission_year` int(8) NOT NULL,
  `batch` int(20) NOT NULL,
  `password` varchar(12) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `student_grand_totals`
--

CREATE TABLE `student_grand_totals` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `total_marks` decimal(10,2) NOT NULL,
  `result_id` int(11) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `grade` varchar(2) DEFAULT NULL,
  `total` int(255) NOT NULL,
  `publish` tinyint(4) DEFAULT 0,
  `published_to` enum('supervisor','student','admin','all') DEFAULT NULL,
  `publish_status` enum('unpublished','published') DEFAULT 'unpublished',
  `Audience_Type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submission`
--

CREATE TABLE `submission` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `submission_path` varchar(255) DEFAULT NULL,
  `submission_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT NULL,
  `project_id` int(11) UNSIGNED DEFAULT NULL,
  `marks` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `Template_id` int(11) NOT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `send_to` enum('All','student','faculty') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `total_student_marks`
--

CREATE TABLE `total_student_marks` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `project_id` int(100) NOT NULL,
  `total_marks` decimal(10,2) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_data`
--

CREATE TABLE `training_data` (
  `id` int(11) NOT NULL,
  `project_id` int(255) DEFAULT NULL,
  `assignments` text DEFAULT NULL,
  `assignment_status` varchar(255) DEFAULT NULL,
  `meetings_attended` int(11) DEFAULT NULL,
  `avg_meeting_feedback` text DEFAULT NULL,
  `presentations_attended` int(11) DEFAULT NULL,
  `avg_presentation_feedback` text DEFAULT NULL,
  `current_progress` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visible_pages`
--

CREATE TABLE `visible_pages` (
  `id` int(255) NOT NULL,
  `fyp_i_mid` varchar(10) NOT NULL,
  `fyp_ii_mid` varchar(10) NOT NULL,
  `fyp_i_final` varchar(10) NOT NULL,
  `fyp_ii_final` varchar(10) NOT NULL,
  `mid_form` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`BatchID`);

--
-- Indexes for table `clearance`
--
ALTER TABLE `clearance`
  ADD PRIMARY KEY (`clearance_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `coordinator`
--
ALTER TABLE `coordinator`
  ADD PRIMARY KEY (`coordinator_id`);

--
-- Indexes for table `course_durations`
--
ALTER TABLE `course_durations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customized_form`
--
ALTER TABLE `customized_form`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`EventID`);

--
-- Indexes for table `external`
--
ALTER TABLE `external`
  ADD PRIMARY KEY (`external_id`);

--
-- Indexes for table `external_total_student_marks`
--
ALTER TABLE `external_total_student_marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `form_detail`
--
ALTER TABLE `form_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_detail_ibfk_1` (`form_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presentations`
--
ALTER TABLE `presentations`
  ADD PRIMARY KEY (`presentation_id`),
  ADD KEY `fk_type_event` (`type`),
  ADD KEY `fk_batch_batch` (`batch`),
  ADD KEY `fk_room_room` (`room_id`),
  ADD KEY `fk_form_id` (`form_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `student1` (`student1`);

--
-- Indexes for table `project_predictions`
--
ALTER TABLE `project_predictions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project` (`project_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `result_detail`
--
ALTER TABLE `result_detail`
  ADD PRIMARY KEY (`result_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `student_grand_totals`
--
ALTER TABLE `student_grand_totals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `submission`
--
ALTER TABLE `submission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`Template_id`);

--
-- Indexes for table `total_student_marks`
--
ALTER TABLE `total_student_marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_data`
--
ALTER TABLE `training_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_id` (`project_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visible_pages`
--
ALTER TABLE `visible_pages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `BatchID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `coordinator`
--
ALTER TABLE `coordinator`
  MODIFY `coordinator_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `course_durations`
--
ALTER TABLE `course_durations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customized_form`
--
ALTER TABLE `customized_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `external`
--
ALTER TABLE `external`
  MODIFY `external_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `external_total_student_marks`
--
ALTER TABLE `external_total_student_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `form_detail`
--
ALTER TABLE `form_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1700;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `presentations`
--
ALTER TABLE `presentations`
  MODIFY `presentation_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `project_predictions`
--
ALTER TABLE `project_predictions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7712;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `result_detail`
--
ALTER TABLE `result_detail`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT for table `student_grand_totals`
--
ALTER TABLE `student_grand_totals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5830;

--
-- AUTO_INCREMENT for table `submission`
--
ALTER TABLE `submission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `Template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `total_student_marks`
--
ALTER TABLE `total_student_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `training_data`
--
ALTER TABLE `training_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=561;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `visible_pages`
--
ALTER TABLE `visible_pages`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `form_detail`
--
ALTER TABLE `form_detail`
  ADD CONSTRAINT `form_detail_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `customized_form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_grand_totals`
--
ALTER TABLE `student_grand_totals`
  ADD CONSTRAINT `student_grand_totals_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
