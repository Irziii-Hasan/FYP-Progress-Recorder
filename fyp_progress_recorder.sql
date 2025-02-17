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

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `message`, `created_at`, `audience_type`) VALUES
(9, 'FYP-I Mid Presentations will starts from May 8, 2024', '2024-10-30 10:33:11', 'all'),
(10, 'Submit all approved documents. Don&#039;t forget the deadlines... this may cause grades degradation.', '2024-10-30 10:35:24', 'students'),
(11, 'FYP-I Mid Term exam is expected on April 27, 2024', '2024-10-30 10:35:42', 'all'),
(12, 'Fill this form by tonight.\r\n\r\nhttps://forms.gle/x3AQ5F2ya6Brs54L8', '2024-10-30 10:36:20', 'students'),
(13, 'Only one group member should fill out the form \r\nfrom each group', '2024-10-30 10:37:05', 'students'),
(14, 'Submit hardcopy of FYP MiD report deadline is May 6, 2024', '2024-10-30 10:37:37', 'students'),
(15, 'FYP-I Mid will start from  May 8, 2024. Schedule will be shared later.\r\nStudents must attend the presentation in proper uniform...', '2024-10-30 10:38:32', 'all'),
(16, 'All groups Must Report at 9:00 am to supervisor. Supervisors will inform you about the time.', '2024-10-30 10:38:58', 'all'),
(17, 'FYP-I Mid will be on May 11, 2024.\r\nThe schedule will be shared later.', '2024-10-30 10:39:17', 'all'),
(18, 'FYP Veriface contact me', '2024-10-30 10:40:11', 'students'),
(19, 'BSSE FYP presentation will be held tomorrow August 3, 2024. \r\nAll these students must Report at 1:30 - @ Mentioned Venue.', '2024-10-30 10:42:04', 'all'),
(20, 'All groups are required to fill out this form for required data by 10 PM tonight. \r\nOnly one member shall fill the form for each group\r\n\r\nhttps://forms.gle/hC3DHEHxbq7vNwJv8', '2024-10-30 10:42:37', 'students'),
(22, 'testing 123 hello world', '2024-11-21 20:12:06', 'supervisor');

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

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `assignment_name`, `description`, `deadline`, `document_path`, `created_at`) VALUES
(25, 'Detailed FYP Proposal', 'Submit Detailed FYP Proposal\r\n', '2024-04-04 23:00:00', NULL, '2024-11-21 16:03:58'),
(26, 'Software Requirement Specification', 'Submit this file ASAP', '2024-04-08 23:00:00', NULL, '2024-11-21 16:05:59'),
(27, 'Ignite Proposal', 'Submit Ignite Proposal ASAP', '2024-04-14 23:00:00', NULL, '2024-11-21 16:07:15'),
(29, 'Testing docx', 'this is for testing purpose', '2024-11-25 10:21:00', 'uploads/FYP_proposal.docx', '2024-11-25 04:22:02');

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

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`BatchID`, `BatchName`, `abbreviation`, `course_duration_id`) VALUES
(16, 'CS 2021', 'Computer Science', 3),
(17, 'SE 2021', 'Software Engineering', 3),
(18, 'DS 2021', 'Data Science', 3);

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

--
-- Dumping data for table `coordinator`
--

INSERT INTO `coordinator` (`coordinator_id`, `faculty_id`, `batch_id`, `year`) VALUES
(8, '27', '18', '2024'),
(12, '27', '16', '2024'),
(13, '27', '17', '2024'),
(14, '23', '16', '2024'),
(17, '23', '18', '2024');

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

--
-- Dumping data for table `course_durations`
--

INSERT INTO `course_durations` (`id`, `title`, `start_date`, `end_date`) VALUES
(2, 'FYP 2025', '2025-01-18', '2026-01-02'),
(3, 'FYP 2024', '2024-01-11', '2025-01-11');

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

--
-- Dumping data for table `customized_form`
--

INSERT INTO `customized_form` (`id`, `title`, `passing_marks`, `total_marks`, `visible`, `created_at`, `duration_id`) VALUES
(23, 'FYP-I MID', 20, 40, 'yes', '2024-09-11 16:26:22', 3),
(24, 'FYP-I Terminal', 30, 60, 'yes', '2024-09-11 16:27:44', 3);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `EventID` int(11) NOT NULL,
  `EventName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`EventID`, `EventName`) VALUES
(2, 'FYP-I Mid'),
(4, 'FYP-II Mid'),
(16, 'FYP-II Terminal'),
(17, 'FYP-I Terminal');

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

--
-- Dumping data for table `external`
--

INSERT INTO `external` (`external_id`, `juw_id`, `name`, `contact`, `organization`, `designation`, `postal_address`, `email`, `created_at`, `password`) VALUES
(7, 'sana.jamal', 'Sana Jamal', '03483659542', 'Contour', 'Senior Developer', 'karachi pakistan', 'sanajamal119@gmail.com', '2024-12-20 05:43:54', '$2y$10$oHp');

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

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `juw_id`, `username`, `email`, `professional_email`, `password`, `designation`) VALUES
(16, 'nz.bawany', 'Prof. Dr. Narmeen Zakaria Bawany', 'nzbawany@gmail.com', 'narmeen.bawany@juw.edu.pk', '333e78a66667', 'Professor'),
(17, 'rahu.sikander', 'Dr. Rahu Sikander	', 'sikander.rahu2013@gmail.com', 'rahu.sikander@juw.pk', '0ca272040675', 'Assistant Professor'),
(18, 'samia.ghazala', 'Ms. Samia Ghazala	', 'samia_ghazala@yahoo.com', 'samia.ghazala@juw.edu.pk', '521df1317abf', 'Assistant Professor'),
(19, 'sadia.javed', 'Ms. Sadia Javed	', 'sadi0921@gmail.com', 'Sadia.Javed@juw.edu.pk', '28981b6bbe64', 'Assistant Professor'),
(20, 'ummay.faseeha', 'Ms. Ummay Faseeha	', 'ummay.faseeha@gmail.com', 'ummay.faseeha@juw.edu.pk', 'bca3ab89dc12', 'Assistant Professor'),
(21, 'hafiza.anisa', 'Ms. Hafiza Anisa Ahmed	', 'hafizaanisaahmed@gmail.com', 'hafiza.anisa@juw.edu.pk', '$10qecFNqmTjI', 'Lecturer'),
(22, 'anum.zamir', 'Ms. Syeda Anum Zamir	', 'anumzameer227@gmail.com', 'anum.zamir@juw.edu.pk', 'f78840842106', 'Lecturer'),
(23, 'hira.tariq', 'Ms. Hira Tariq	', 'htariquzair42@gmail.com', 'hira.tariq@juw.edu.pk', '980e0fcfc4fa', 'Lecturer'),
(24, 'kanwal.zahoor', 'Ms. Kanwal Zahoor	', 'kanwalzahoor92@gmail.com', 'kanwal.zahoor@juw.edu.pk', '6108e9521553', 'Lecturer'),
(25, 'tehreem.qamar', 'Ms. Tehreem Qamar	', 'tehreem_qamar10@hotmail.com', 'tehreem.qamar@juw.edu.pk', 'e7db05b6c26f', 'Lecturer'),
(26, 'saba.mazhar', 'Ms. Saba Mazhar	', 'sabarizwan2@yahoo.com', 'saba.mazhar@juw.edu.pk', 'd570e6e5018e', 'Lecturer'),
(27, 'soomaiya.hamid', 'Ms. Soomaiya Hamid	', 'soomaiya.hamid@gmail.com', 'soomaiya.hamid@juw.edu.pk', '$2ys3HNvN3CK', 'Lecturer'),
(28, 'ayesha.zulfiqar', 'Ms. Ayesha Zulfiqar	', 'ayesha001.az@gmail.com', 'ayesha.zulfiqar@juw.edu.pk', '35ba630df365', 'Lecturer'),
(29, 'arifa.shamim', 'Ms. Arifa Shamim	', 'arifashamim188@gmail.com', 'arifa.shamim@juw.edu.pk', '709df52db91c', 'Lab Instructor'),
(30, 'surayya.obaid', 'Ms. Surayya Obaid	', 'surayya.mahrukh@gmail.com', 'surayya.obaid@juw.edu.pk', '2193540471b8', 'Lab Instructor'),
(31, 'anum.ilyas', 'Ms. Anum Ilyas	', 'ianumilyaszia@gmail.com', 'anum.ilyas@juw.edu.pk', '2b3211b6e6f4', 'Lab Instructor'),
(32, 'mehak.abbas', 'Ms. Mehak Abbas	', 'mehakabbas500@gmail.com', 'mehak.abbas@juw.edu.pk', 'd548aaf1a004', 'Lab Instructor'),
(33, 'hira.sultan', 'Ms. Hira Sultan	', 'mughalhira199@gmail.com', 'hira.sultan@juw.edu.pk', '288235a4e0e9', 'Lab Instructor'),
(34, 'ushna.tasleem', 'Ms. Ushna Tasleem	', 'ushnatasleemjuw23@gmail.com', 'ushna.tasleem@juw.edu.pk', '5243f64fe23f', 'Lab Instructor'),
(35, 'manahil.aly', 'Ms. Manahil	', 'manahilaly21@gmail.com', '', '927f67a86c6d', 'Lab Instructor'),
(39, 't777', 'Zareen Khan', 'irzahasanlap@gmail.com', 'abc@gmail.com', '583639b4f282', 'Chairperson'),
(41, 'jaweria.imran', 'Ms. Jaweria Imran', 'kjaweria62@gmail.com', 'jaweria.imran@juw.edu.pk', 'f78a61a8d86e', 'Lab Instructor'),
(42, 'amir.imran', 'Sir. Syed Amir Imran', 'smaimam@gmail.com', 'amir.imran@juw.edu.pk', '0bd5d2f80818', 'Lecturer'),
(43, 'M.kamran', 'Muhammad Kamran', 'mkamran@gmail.com', 'm.kamran@juw.edu.pk', '82aa89ade8ac', 'Lecturer');

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
(44, 23, 'Diagrams', 10),
(45, 23, 'Implementations', 10),
(46, 23, 'Presentation', 10),
(47, 23, 'Viva', 10),
(48, 24, 'Mobile App', 20),
(49, 24, 'MS Test', 20),
(50, 24, 'Internship', 20);

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

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_id`, `title`, `description`, `student1`, `student2`, `student3`, `student4`, `supervisor`, `co_supervisor`, `external_supervisor`, `batch`, `duration`, `created_at`) VALUES
(18, 'FYP-CS2021-1', 'ReviewOnChain', 'This project introduces EthReview ReviewOnChain, agroundbreaking solution leveraging Ethereum blockchain technology to tackle the vulnerabilities of centralized review systems in e-commerce. Traditional systems are plagued by issues like fake reviews and manipulation, eroding trust among consumers. EthReview provides a decentralized alternative, eliminating the need for a central authority and fostering integrity through transparent and tamper-proof reviews. EthReview operates on a peer-to-peer network, where users validate and verify reviews, ensuring transparency and trustworthiness. To incentivize honesty, users are rewarded for providing genuine feedback. Smart contracts play a pivotal role in automating processes, ensuring the efficient authentication of reviews and transactions.', 35, 72, 108, 142, 16, 24, 0, '16', 3, '2024-11-21 15:03:09'),
(23, 'FYP-CS2021-2', 'CyberSheild', 'Cybershield aims to provide a knowledge based platform to the common people that how a user could be affected by the cyber-attacks and how a user can get secure by these. CyberShield ensures an interactive and friendly learning experience by providing the knowledge about security tools and techniques.', 16, 124, 53, 89, 16, 27, 0, '16', 3, '2024-11-21 15:03:09'),
(24, 'FYP-CS2021-3', 'Smart Aquaculture', 'The Smart Aquaculture project aims to improve the care and management of aquatic environments using advanced technology. It monitors key parameters like pH, oxygen, and temperature to ensure optimal conditions for fish. The system also includes automated feeding schedules and water change alerts, making it easier for users to maintain their aquariums. Accessible via web and mobile apps, this project is designed to promote sustainable aquaculture practices and improve the health and growth of aquatic life.', 22, 59, 95, 130, 18, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(25, 'FYP-CS2021-4', 'StrokEscape', 'StrokEscape tackles the challenge of low adherence in post-stroke and accident physiotherapy by creating a more engaging and personalized platform. It combines fun online games designed for rehabilitation exercises with VR oculus headset. A user-friendly Android app allows patients to access the platform, track their progress, and receive guidance throughout their journey. This innovative approach aims to increase motivation and ultimately improve patient outcomes.', 36, 73, 109, 143, 20, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(26, 'FYP-CS2021-5', 'Stich Sync', 'The goal of this project is to develop an AI-powered platform that recognizes the type of a bridal or party wear dress from an uploaded image. Subsequently, the system will allow users to customize recognized dresses using visual representations inspired by famous brands. This customization will provide users with a realistic preview of their desired dress, ensuring a seamless and personalized experience. The purpose of this project is to transform the traditional bridal and party wear dress customization process by leveraging AI-powered technology. Through intelligent image recognition and virtual customization features, the platform aims to empower users to personalize their dress designs with ease and accuracy. By offering a seamless and immersive experience, the project seeks to enhance user engagement and satisfaction, ultimately redefining the way individuals interact with and customize their attire for special occasions.', 63, 26, 133, 29, 20, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(27, 'FYP-CS2021-6', 'Nawai Urdu', 'Our applications provides hands on writing experience for practicing Urdu letters as well as interactive videos. The user can also track progress and attempt quizzes to improve their Urdu language skills', 14, 87, 122, 51, 20, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(28, 'FYP-CS2021-7', 'Mindflex Rehabilitation', 'MindFlex Rehabilitation is a specialized program designed to aid individuals in recovering from psychological and psychological conditions. It typically employs a combination of advanced cognitive-behavioral therapy, and other therapeutic interventions. The goal is to enhance brain function, improve mental health, and support the overall rehabilitation process for conditions such as physical injuries, mental health disorders. This program is often personalized to meet the specific needs of each patient, aiming to restore their cognitive abilities and improve their quality of life using AI agent and VR Kinect Technology.', 44, 46, 118, 81, 21, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(29, 'FYP-CS2021-8', 'The IOT Lab', 'Welcome to the future of laboratories – the Smart Lab Management System. Our project is all about making labs smart using IoT devices. Imagine a lab where you can save energy, control temperature, and secure access, all with a simple mobile app. No more worrying about lights left on or finding a lost remote; our system is here to make labs efficient and easy to manage.', 75, 38, 111, 144, 24, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(30, 'FYP-CS2021-9', 'Nephro Health Coach', 'The NephroHealth Coach App revolutionizes Chronic Kidney Disease (CKD) management by offering a comprehensive platform that empowers users to make positive lifestyle changes, monitor health metrics, and access personalized recommendations. Key features include secure authentication, BMI calculator, medical history input, patient profiling, lab test monitoring, emergency doctor contact, medicine substitutes, personalized diet plans, water intake tracker, exercise guidance, stress reduction techniques, and educational content. This holistic approach enhances informed decision-making and overall well-being for CKD patients.', 84, 47, 45, 0, 23, 0, 0, '16', 3, '2024-11-21 14:47:40'),
(31, 'FYP-CS2021-10', 'Inner Bliss', 'Providing a complete life style for emotionally distressed people through our mobile app including online consultation with psychologist, coping strategies, chatbot support and cognitive games.', 34, 71, 141, 107, 23, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(32, 'FYP-CS2021-11', 'HireLeap', 'HireLeap will be an AI-powered hiring software which will revolutionize hiring process using Artificial Intelligence (AI) and Natural Language Processing (NLP) algorithms. The existing semi-automated hiring system in organizations consumes significant HR resources. Their prolonged hiring timelines lead to increased costs for organizations which may result in hiring candidates who may not be the best fit for the role. Our goal is to provide an all-in-one software solution which will increase productivity, reduce workload on HR workers and make hiring process more effective and efficient.', 21, 129, 94, 58, 30, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(34, 'FYP-CS2021-12', 'VR Healer', 'VR Healer is an innovative virtual reality project designed to provide effective therapy for various phobias. Harnessing the immersive power of virtual reality, this project aims to create a safe and controlled environment where individuals can confront and overcome their fears.', 56, 92, 127, 19, 41, 0, 0, '16', 3, '2024-11-21 15:03:09'),
(35, 'FYP-CS2021-13', 'SAS Segmify', 'This Python-based project focuses on broad customer analysis for business optimization including RFM analysis. It encompasses customer segmentation based on demographics and behavior, classification for tailored marketing strategies, and revenue analysis for growth insights. Additionally, sentiment analysis on customer reviews provides valuable feedback. The project achieve in dynamic presentations summarizing key findings.The key features include clustering customers to target marketing efforts, categorizingthem for personalized approaches, tracking monthly recurring revenue trends for growth strategies, analyzing sentiment in customer reviews for feedback, and generating dynamic slides for stakeholder communication. Enable businesses with data-driven insights for enhancedcustomer satisfaction, targeted marketing, and revenue growth. The project offers a extensive solution to provide data for strategic decision-making and sustainable business development.', 31, 68, 138, 104, 43, 29, 0, '16', 3, '2024-11-21 15:03:09'),
(40, 'FYP-CS2021-14', 'Infographics', 'This project aims to create an intuitive platform for visualizing diverse data, making complex information easy to understand. By leveraging advanced analytics, it enhances insights in areas such as cricket statistics and stock market analysis', 137, 67, 103, 30, 42, 33, 0, '16', 3, '2024-11-21 15:03:09'),
(41, 'FYP-SE2021-1', 'Academic Accelerator', 'Academic Accelerator introduces a comprehensive solution for academic guidance, including predictive analytics, personalized recommendations, and automated grading features. Through personalized mentoring, both students and faculty benefit from insightful analysis and tailored suggestions, promoting individualized learning experiences and performance. Additionally, the inclusion of an AI chatbot provides instant support, ensuring accessibility and efficiency for all users.', 15, 88, 52, 123, 16, 21, 0, '17', 3, '2024-11-21 15:03:09'),
(42, 'FYP-SE2021-2', 'Campus Vigil Watch', 'The project aims to enhance campus security using computer vision technology. By employing advanced visual recognition systems, the goal is to create a safer environment on campus. This involves the development and implementation of surveillance systems that can automatically detect and alert authorities to potential security threats. The computer vision technology will enable real-time monitoring of various areas, helping to prevent and respond to security incidents more effectively. Overall, the project seeks to leverage cutting-edge technology to ensure the well-being and safety of individuals within the campus community.', 27, 64, 100, 134, 16, 24, 0, '17', 3, '2024-11-21 15:03:09'),
(43, 'FYP-SE2021-3', 'Werky', 'The project aims to create an integrated platform leveraging AI technology to offer career counseling to students, connect talented individuals with potential projects opportunities, and facilitate recruitment for small businesses and startups. By providing personalized guidance and showcasing students work, the platform aims to foster a symbiotic relationship between individuals and companies, empowering young talent to find suitable projects while meeting the needs of businesses seeking fresh skills. Ultimately, the goal is to establish a connected environment where talent meets opportunity, benefiting both parties involved.', 110, 74, 37, 0, 16, 30, 0, '17', 3, '2024-11-21 15:03:09'),
(44, 'FYP-SE2021-4', 'BeginWise', '\"BeginWISE\" is an educational platform that streamlines school selection for parents and students using advanced technology like machine learning and web scraping. It provides personalized recommendations based on user preferences and includes features like exam prep, quizzes, and interview preparation. BeginWISE empowers users to make informed educational decisions, revolutionizing school selection.', 13, 50, 86, 121, 19, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(45, 'FYP-SE2021-5', 'Stammerapy', 'Stammerapy is a digital solution designed to support individuals facing speech challenges as stammering. With a range of innovative features, Stammerapy aims to empower users to improve their speech fluency, build confidence, and enhance their overall quality of life.', 40, 77, 113, 0, 19, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(46, 'FYP-SE2021-6', 'kids InGrow', 'Kids InGrow is a bilingual child growth monitoring system that tracks communication, gross-motor, fine-motor, problem-solving, and personal-social skills using ASQ-3 standardized questionnaires. It offers age-appropriate assessments and suggestions, with secure role-based access for parents and organizations. The system also provides score visualizations to display results clearly.', 106, 33, 140, 70, 21, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(47, 'FYP-SE2021-7', 'Safer Sight', 'Safer Sight is a smart surveillance system that uses AI to detect human illegal activity such as robbery and snatching and harmful objects like gun and knife in real-time that can cause damage to human life. Safer Sight’s provide real-time monitoring, raising alarms and notifying authorities for prompt action in case of any event.', 23, 60, 96, 131, 25, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(48, 'FYP-SE2021-8', 'DOWNSY LEARNS', 'A learning app based on gamification designed for special kids down syndrome consisting of games that would improve their basic lacking', 12, 49, 0, 0, 25, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(49, 'FYP-SE2021-9', 'Optifruit', 'OptiFruit is an advanced fruit quality management system designed to streamline the grading, inventory, and analysis of fruit produce.', 105, 69, 139, 32, 25, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(50, 'FYP-SE2021-10', 'FYP Progress  Recorder', 'The proposed automated system will streamline the final year project process in the CSSE department by enhancing supervisor-student collaboration, automating notifications, and using AI for grade prediction based on project progress. This solution aims to save time and improve efficiency in managing presentations, meetings, and deadlines.', 41, 114, 166, 146, 27, 0, 7, '17', 3, '2024-12-20 05:44:24'),
(51, 'FYP-SE2021-11', 'Envisage Modesty', 'The project is driven by the need to bridge the gap between traditional in-store hijab shopping experiences and online shopping platforms. The Envisage Modesty platform will leverage augmented reality (AR) technology to allow users to virtually try on hijabs in real-time, enabling them to visualize how different styles, colors, and patterns will look on their face. This immersive and personalized experience will enhance user confidence in their purchasing decisions and provide a unique value proposition in the online hijab market.', 42, 79, 0, 0, 27, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(52, 'FYP-SE2021-12', 'Enviro Vision Mate', 'The project aims to empower visually impaired individuals by developing Smart Glasses equipped with advanced AI technologies to offer real-time audio assistance, navigation support, and descriptive information.', 39, 76, 112, 145, 27, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(53, 'FYP-SE2021-13', 'Formatify', 'The project proposes a web-based platform to simplify research paper creation using LaTeX. It offers a user-friendly interface that streamlines writing, formatting, and real-time collaboration among researchers. This makes LaTeX accessible even to those who find its syntax challenging.', 18, 91, 55, 126, 23, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(54, 'FYP-SE2021-14', 'Oldvoy', 'Older adults significantly rely on others for most of their routine activities, from vitals monitoring to medicine intake. Usually, the caretakers have to assist in smaller tasks such as navigating in close neighborhoods or operating appliances at home. Caregiving and monitoring techniques are beneficial but may prove less effective over longer periods and are restricted in certain situations. Therefore, there is a need for a comprehensive and ethical monitoring system for elderly adults.', 62, 132, 98, 25, 30, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(55, 'FYP-SE2021-15', 'Veriface (verify your  face)', 'Veriface utilizes facial recognition to streamline student attendance in educational settings, replacing traditional methods like roll calls. While offering efficiency, it requires attention to privacy and data security to ensure student comfort and data protection.', 54, 125, 90, 17, 31, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(56, 'FYP-SE2021-16', 'Virtual Jewellery Try-On', 'For those who feel hesitant about purchasing jewelry items from physical outlets due to uncertainty or inconvenience, our virtual jewelry try on solution provides an online facility to try on any item from the comfort of your home. This hassle free solution allow customer to see how a particular item look on them before making a purchase, enhancing shopping experience without the need to visit store.', 61, 24, 97, 0, 31, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(57, 'FYP-SE2021-17', 'Calm Horizon', 'Pre-operative anxiety involves worry before surgery, while pre-operative therapy (pre-habilitation) prepares individuals for surgery. PTSD is a mental health condition triggered by traumatic events, with symptoms like flashbacks and severe anxiety. Virtual reality (VR) technology is used to enhance treatments for both pre-operative therapy and PTSD by providing immersive, interactive environments.', 80, 43, 147, 115, 41, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(58, 'FYP-SE2021-18', 'Image captioning in  Urdu', 'The image captioning system enhances technology accessibility for non-English speakers, emphasizing cultural sensitivity and a user-friendly interface. It promotes technology adoption and digital ownership, potentially influencing social media trends and online content creation.', 28, 65, 101, 135, 32, 0, 0, '17', 3, '2024-11-21 15:03:09'),
(59, 'FYP-SE2021-19', 'EduConnect Pro.', 'E-learning and E-earning platform', 11, 48, 85, 120, 42, 33, 0, '17', 3, '2024-11-21 15:03:09'),
(60, 'FYP-SE2021-20', 'Career Mentor', 'Our project is like a helpful friend for computer science students. We will create a simple platform where you can make great resumes, prepare for interviews, and find jobs that fit your skills. It’s like having a personal coach guide you through your career journey, making everything easy and familiar.', 20, 57, 93, 128, 43, 29, 0, '17', 3, '2024-11-21 15:03:09'),
(67, 'testing-123', 'Testing Management System', 'This is for testing purpose', 159, 160, 0, 0, 27, 0, 0, '17', 3, '2024-11-21 14:59:14');

-- --------------------------------------------------------

--
-- Table structure for table `project_predictions`
--

CREATE TABLE `project_predictions` (
  `id` int(11) NOT NULL,
  `project_id` varchar(255) DEFAULT NULL,
  `predicted_progress` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_predictions`
--

INSERT INTO `project_predictions` (`id`, `project_id`, `predicted_progress`) VALUES
(7642, '18', 14),
(7643, '23', 0),
(7644, '24', 0),
(7645, '25', 0),
(7646, '26', 0),
(7647, '27', 0),
(7648, '28', 0),
(7649, '29', 0),
(7650, '30', 0),
(7651, '31', 0),
(7652, '32', 0),
(7653, '34', 0),
(7654, '35', 0),
(7655, '40', 0),
(7656, '41', 0),
(7657, '42', 0),
(7658, '43', 0),
(7659, '44', 0),
(7660, '45', 0),
(7661, '46', 0),
(7662, '47', 0),
(7663, '48', 0),
(7664, '49', 0),
(7665, '50', 11),
(7666, '51', 0),
(7667, '52', 0),
(7668, '53', 0),
(7669, '54', 0),
(7670, '55', 0),
(7671, '56', 0),
(7672, '57', 0),
(7673, '58', 0),
(7674, '59', 0),
(7675, '60', 0),
(7676, '61', 0);

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

--
-- Dumping data for table `result_detail`
--

INSERT INTO `result_detail` (`result_id`, `title`, `created_at`) VALUES
(30, 'SE-2021 Results', '2024-11-23 08:08:44');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(20) NOT NULL,
  `room_number` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`) VALUES
(2, 'B-19'),
(3, 'B-17'),
(6, 'B-18'),
(7, 'B-20'),
(8, 'D-18'),
(9, 'D- 20');

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

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `juw_id`, `username`, `email`, `enrollment`, `academic_year`, `degree_program`, `phone_number`, `seat_number`, `admission_year`, `batch`, `password`, `created_at`) VALUES
(11, 'juw07337', 'Wajiha Kashaf Ali 1234', 'wajihakashaf945@gmail.com', '2020/Comp/BS(SE)/25324', 2024, 'Software Engineering', '2147483647', 2024537, 0, 2020, '104e305ef63e', '2024-12-02 04:46:27'),
(12, 'juw06244', 'Umme Hani', 'ummehanibhati@gmail.com', '2020/Comp/BS(SE)/25321', 2024, 'Software Engineering', '2147483647', 2024534, 0, 0, 'f6cad52af545', '2024-12-02 04:46:27'),
(13, 'juw14067', 'Syeda Rimsha Atiq', 'atiqramsha0@gmail.com', '2021/Comp/BS(SE)/27077', 2024, 'Software Engineering', '2147483647', 2126460, 0, 0, '7e1b97ccc8ed', '2024-12-02 04:46:27'),
(14, 'juw19414', 'Areesha Zafar', 'areeshazafar5@gmail.com', '2021/Comp/BS(CS)/26944', 2024, 'Computer Science', '2147483647', 2126327, 0, 0, '8f84f4f73b7b', '2024-12-02 04:46:27'),
(15, 'juw11846', 'Kainat Iqbal', 'kainatjiq@gmail.com', '2021/Comp/BS(SE)/27039', 2024, 'Software Engineering', '2147483647', 2126422, 0, 0, 'ff4d1a15bac1', '2024-12-02 04:46:27'),
(16, 'juw16885', 'Arisha Fatima', 'arishafatima5050@gmail.com', '2021/Comp/BS(CS)/26946', 2024, 'Computer Science', '2147483647', 2126329, 0, 0, 'bec7aca64d74', '2024-12-02 04:46:27'),
(17, 'juw13529', 'Maha Aleem', 'mahaaleem44@gmail.com', '2021/Comp/BS(SE)/27042', 2024, 'Software Engineering', '2147483647', 2126425, 0, 0, '4f75657d7ac1', '2024-12-02 04:46:27'),
(18, 'juw13747', 'Sawera Rasheed', 'sawerarasheed997@gmail.com', '2021/Comp/BS(SE)/27071', 2024, 'Software Engineering', '2147483647', 2126454, 0, 0, 'c0303eb3673f', '2024-12-02 04:46:27'),
(19, 'juw19098', 'Muskan Abdul Razzak', 'muskanrazzak32@gmail.com', '2021/Comp/BS(CS)/26982', 2024, 'Computer Science', '2147483647', 2126365, 0, 0, '6ea74a14224f', '2024-12-02 04:46:27'),
(20, 'juw17985', 'Fiza Sahar', 'fizasahar66@gmail.com', '2021/Comp/BS(SE)/27030', 2024, 'Software Engineering', '2147483647', 2126413, 0, 0, '76472ac3b1d7', '2024-12-02 04:46:27'),
(21, 'juw13534', 'Ashfia Binte Waqar', 'ashfia.waquar18@gmail.com', '2021/Comp/BS(CS)/26947', 2024, 'Computer Science', '2147483647', 2126330, 0, 0, 'c20830345b81', '2024-12-02 04:46:27'),
(22, 'juw12857', 'Ayesha Kanwal', 'ayeshakanwal1007@gmail.com', '2021/Comp/ BS(CS)/26954', 2024, 'Computer Science', '2147483647', 2126337, 0, 0, '81166cf84d7c', '2024-12-02 04:46:27'),
(23, 'juw14565', 'Pooja Harijiwan', 'poojapatelhj1201@gmail.com', '2021/Comp/BS(SE)/27062', 2024, 'Software Engineering', '2147483647', 2126445, 0, 0, '00655cf8c17c', '2024-12-02 04:46:27'),
(24, 'juw14601', 'Samreena Hameed', 'samreenahameed13@gmail.com', '2021/Comp/BS(SE)/27070', 2024, 'Software Engineering', '2147483647', 2126453, 0, 0, 'ec8891cbd2f8', '2024-12-02 04:46:27'),
(25, 'juw13102', 'Fizza Tariq', 'tariqfizza70@gmail.com', '2021/Comp/BS(SE)/27032', 2024, 'Software Engineering', '2147483647', 2126415, 0, 0, '9521936d9e73', '2024-12-02 04:46:27'),
(26, 'juw13361', 'Areesha Amir', 'areeshaamir2611@gmail.com', '2021/Comp/BS(CS) /26943', 2024, 'Computer Science', '2147483647', 2126326, 0, 0, '35382a785b6f', '2024-12-02 04:46:27'),
(27, 'juw17272', 'Dua Aijaz', 'duaaijaz7@gmail.com', '2021/Comp/BS(SE)/27026', 2024, 'Software Engineering', '2147483647', 2126409, 0, 0, '2906f00a47a4', '2024-12-02 04:46:27'),
(28, 'juw15068', 'Ambreen Fatima', 'ambreenfatima85@yahoo.com', '2021/Comp/BS(SE)/27014', 2024, 'Software Engineering', '2147483647', 2126397, 0, 0, '1d5f3376ec0d', '2024-12-02 04:46:27'),
(29, 'juw16667', 'Mahnoor Kazim', 'mahnoorkazim60@gmail.com', '2021/Comp/BS(CS)/26996', 2024, 'Computer Science', '2147483647', 2126379, 0, 0, '446f90e79bd0', '2024-12-02 04:46:27'),
(30, 'juw17151', 'Insharah Naeem', 'insharahkhi081@gmail.com', '2021/Comp/BS(CS)/26969', 2024, 'Computer Science', '2147483647', 2126352, 0, 0, 'eaa24c0d1021', '2024-12-02 04:46:27'),
(31, 'juw18153', 'Areesha Akram', 'Areeshaakram526@gmail.com', '2021/Comp/BS(CS)/26942', 2024, 'Computer Science', '2147483647', 2126325, 0, 0, 'd7adb503f51b', '2024-12-02 04:46:27'),
(32, 'juw17144', 'Umm e Ruman Asif', 'ruman24june2000@gmail.com', '2021/Comp/BS(SE)/27084', 2024, 'Software Engineering', '2147483647', 2126467, 0, 0, 'c31c164efff7', '2024-12-02 04:46:27'),
(33, 'juw18065', 'Omaima Naeem', 'omaimanaeem322@gmail.com', '2021/Comp/BS(SE)/27061', 2024, 'Software Engineering', '2147483647', 2126444, 0, 0, 'bba9283d417e', '2024-12-02 04:46:27'),
(34, 'juw17501', 'Hafiza Arshmah', 'arshkazmi246@gmail.com', '2021/Comp/BS(CS)/26963', 2024, 'Computer Science', '2147483647', 2126346, 0, 0, 'a62b00807b71', '2024-12-02 04:46:27'),
(35, 'juw15207', 'Anum Furqan', 'anumfurqanakbani2018@gmail.com', '2021/Comp/BS(CS)/26939', 2024, 'Computer Science', '2147483647', 2126322, 0, 0, 'c58ce9efad9c', '2024-12-02 04:46:27'),
(36, 'juw16517', 'Syeda Urooj Muzaffar', 'sumuzaffar21@gmail.com', '2021/Comp/BS(CS)/26998', 2024, 'Computer Science', '2147483647', 2126381, 0, 0, '653a3e029595', '2024-12-02 04:46:27'),
(37, 'juw18622', 'Hafsah Shahzad', 'hafsahshahzad123645@gmail.com', '2021/Comp/BS(SE)/27034', 2024, 'Software Engineering', '2147483647', 2126417, 0, 0, '68c29572459e', '2024-12-02 04:46:27'),
(38, 'juw19690', 'Arham Shaikh', 'arhamshaikh2626@gmail.com', '2021/Comp/BS(CS)/26945', 2024, 'Computer Science', '2147483647', 2126328, 0, 0, '43cd6d6cac27', '2024-12-02 04:46:27'),
(39, 'juw14785', 'Maryam Abdul Aziz', 'maryamaziz050@gmail.com', '2021/Comp/BS(SE)/27048', 2024, 'Software Engineering', '2147483647', 2126431, 0, 0, '9a92bcd30261', '2024-12-02 04:46:27'),
(40, 'juw 15800', 'Syeda Maria', 'syedamaria.shahid@gmail.com', '2021/Comp/BS(SE)/27075', 2024, 'Software Engineering', '2147483647', 2126458, 0, 0, 'd5e96732e269', '2024-12-02 04:46:27'),
(41, 'juw14836', 'Aimun Akbar', 'aimanakbar761@gmail.com', '2021/Comp/BS(SE)/27012', 2024, 'Software Engineering', '2147483647', 2126395, 0, 0, '569cbedfd586', '2024-12-02 04:46:27'),
(42, 'juw12453', 'Ramsha Imran', 'ramshaimran8056@gmail.com', '2021/Comp/BS(SE)/27066', 2024, 'Software Engineering', '2147483647', 2126449, 0, 0, '33e2c0571ce2', '2024-12-02 04:46:27'),
(43, 'juw15052', 'Maheen Shoaib', 'maheenshoaib02@gmail.com', '2021/Comp/BS(SE)/27044', 2024, 'Software Engineering', '2147483647', 2126427, 0, 0, '2c59e7e8c41d', '2024-12-02 04:46:27'),
(44, 'juw14568', 'Maryam Raza', 'maryamraza738@gmail.com', '2021/Comp/BS(CS)/26981', 2024, 'Computer Science', '2147483647', 2126364, 0, 0, 'c125512e4b15', '2024-12-02 04:46:27'),
(45, 'juw17297', 'Samra Sadaqat', 'smrsadaqat@gmail.com', '2021/Comp/BS(CS)/26990', 2024, 'Computer Science', '2147483647', 2126373, 0, 0, '985cd1b32020', '2024-12-02 04:46:27'),
(46, 'juw13895', 'Nameera Siddiqui', 'nameerasiddiqui11@gmail.com', '2021/Comp/BS(CS)/26984', 2024, 'Computer Science', '2147483647', 2126367, 0, 0, '4015d3fc51f1', '2024-12-02 04:46:27'),
(47, 'juw19804', 'Ayesha Jamil', 'ayesha.jamil07@gmail.com', '2021/Comp/BS(CS)/26953', 2024, 'Computer Science', '2147483647', 2126336, 0, 0, '5948677e3448', '2024-12-02 04:46:27'),
(48, 'juw16760', 'Sehrish Fatima', 'sehrishf155@gmail.com', '2021/Comp/BS(SE)/27072', 2024, 'Software Engineering', '2147483647', 2126455, 0, 0, '86cfb719025f', '2024-12-02 04:46:27'),
(49, 'Juw11259', 'Arooba Salaahudin', 'aarusheikh96@gmail.com', '2020/Comp/BS(SE)/25268', 2024, 'Software Engineering', '2147483647', 2024461, 0, 0, '3d5ad4d47562', '2024-12-02 04:46:27'),
(50, ' juw16469  ', 'Yamna Fazil', ' Yamnafazil2002@gmail.com', '2021/Comp/BS(SE)/27089', 2024, 'Software Engineering', '2147483647', 2126472, 0, 0, '86465bfc4255', '2024-12-02 04:46:27'),
(51, 'juw14075', 'Sheeza Saleem ', 'saleemsheeza21@gmail.com', '2021/Comp/BS(CS)/26992', 2024, 'Computer Science', '2147483647', 2126375, 0, 0, '145292928705', '2024-12-02 04:46:27'),
(52, 'juw13637', 'Bismah Kulsoom', 'bismah2653@gmail.com', '2021/Comp/BS(SE)/26956', 2024, 'Software Engineering', '2147483647', 2126339, 0, 0, '549f132e1c22', '2024-12-02 04:46:27'),
(53, 'Juw17068', 'Mahnoor Asif ', 'mahnoorasif0220@gmail.com  ', '2021/Comp/BS(CS)/26976', 2024, 'Computer Science', '2147483647', 2126359, 0, 0, '2c902bd04e1a', '2024-12-02 04:46:27'),
(54, 'juw16319', 'Sajal Ali Khan', 'sajal.khan2812@gmail.com', '2021/Comp/BS(SE)/27068', 2024, 'Software Engineering', '2147483647', 2126451, 0, 0, 'b34b412ad73f', '2024-12-02 04:46:27'),
(55, 'Juw16680', 'Nashwa Shahid ', 'nashwashahid221@gmail.com', '2021/Comp/BS(SE)/27056', 2024, 'Software Engineering', '2147483647', 2126439, 0, 0, 'c60d922e8a25', '2024-12-02 04:46:27'),
(56, 'juw16562', 'Ayesha Zubairi', 'uayesha735@gmail.com', '2021/Comp/BS(CS)/26955', 2024, 'Computer Science', '2147483647', 2126338, 0, 0, '8da01f23483a', '2024-12-02 04:46:27'),
(57, 'juw17191', 'Humna Siddiqui ', 'humnasiddiqui2020@gmail.com', '2021/Comp/BS(SE)/27036', 2024, 'Software Engineering', '2147483647', 2126419, 0, 0, '189b0dfdc3c6', '2024-12-02 04:46:27'),
(58, 'JUW13416', 'Nimra Jawed', 'nimrajawed467@gmail.com', '2021/Comp/BS(CS)/26986', 2024, 'Computer Science', '2147483647', 2126369, 0, 0, 'aceea2e8c1ff', '2024-12-02 04:46:27'),
(59, 'juw14719', 'Iqra Aabid', 'iqra15722@gmail.com', '2021/Comp/BS(CS)/26971', 2024, 'Computer Science', '2147483647', 2126354, 0, 0, '204aa89cd454', '2024-12-02 04:46:27'),
(60, 'juw14911', 'Kanza Batool', 'bkanza559@gmail.com', '2021/Comp/BS(SE)/27040', 2024, 'Software Engineering', '2147483647', 2126423, 0, 0, '1ae8b7780303', '2024-12-02 04:46:27'),
(61, 'juw17726', 'Aqsa Rani', 'aqsar9177@gmail.com', '2021/Comp/BS(SE)/27019', 2024, 'Software Engineering', '2147483647', 2126402, 0, 0, 'a9654ae97b2a', '2024-12-02 04:46:27'),
(62, 'Juw13560', 'Amna Khanani ', 'amnakhanani2002@gmail.com ', '2021/Comp/BS(SE)/27015', 2024, 'Software Engineering', '2147483647', 2126398, 0, 0, '366cd859da27', '2024-12-02 04:46:27'),
(63, ' juw16506', 'Hafsah Shakeel', 'hafsah12329@gmail.com  ', '2021/Comp/BS(CS)/26965', 2024, 'Computer Science', '2147483647', 2126348, 0, 0, '9774de615852', '2024-12-02 04:46:27'),
(64, 'Juw14038', 'MIDHAT KHAN	    ', ' midhatkhan342@gmail.com', '  2021/Comp/BS(SE)/27051', 2024, 'Software Engineering', '2147483647', 2126431, 0, 0, 'd8320b6b8424', '2024-12-02 04:46:27'),
(65, 'juw1691', 'Areeba Saif ', 'Areebasaif34@gmail.com', '2018/Comp/BS(SE)/1691', 2024, 'Software Engineering', '2147483647', 1801, 0, 0, '011e8c8e60e1', '2024-12-02 04:46:27'),
(67, 'juw13542', 'Aiman Javed', 'aimanjaved060@gmail.com', '2021/Comp/BS(CS)/26936', 2024, 'Computer Science', '322626296', 2126319, 0, 0, '1a8998b00c16', '2024-12-02 04:46:27'),
(68, 'juw13045', 'Maliha Azmat', 'malihaazmat10@gmail.com', '2021/Comp/BS(CS)/26979', 2024, 'Computer Science', '2147483647', 2126362, 0, 0, 'b17d3117e7b3', '2024-12-02 04:46:27'),
(69, 'juw16615 ', 'Areeba Khan', 'areebakhanak8851212@gmail.com', '2021/Comp/BS(SE)/27022', 2024, 'Software Engineering', '2147483647', 2126405, 0, 0, '9cd383ba3f11', '2024-12-02 04:46:27'),
(70, 'juw14527', 'Tooba Ali', 'alituba961@gmail.com', '2021/Comp/BS(SE)/27082', 2024, 'Software Engineering', '2147483647', 2126465, 0, 0, '9464e2d9f79e', '2024-12-02 04:46:27'),
(71, 'juw14180', 'Rimsha Asgher', 'rimshaasgher21@gmail.com', '2021/Comp/BS(CS)/26987', 2024, 'Computer Science', '2147483647', 2126370, 0, 0, '1e1345c8bf11', '2024-12-02 04:46:27'),
(72, 'juw13891', 'Iqra Muhammad Amir', 'Iqraaamirkukda7@gmail.com', '2021/Comp/BS(CS)/26970', 2024, 'Computer Science', '2147483647', 2126353, 0, 0, 'a368295131c1', '2024-12-02 04:46:27'),
(73, 'juw-19177', 'Efrah Ali', 'efrahali02@gmail.com', '2021/Comp/BS(CS)/26959', 2024, 'Computer Science', '2147483647', 2126342, 0, 0, 'bd9f02b32b06', '2024-12-02 04:46:27'),
(74, 'juw18245', 'Maheen Khalid', 'maheenkhalid76@gmail.com', '2021/Comp/BS(SE)/27043', 2024, 'Software Engineering', '2147483647', 2126426, 0, 0, 'd9d16d9fef52', '2024-12-02 04:46:27'),
(75, 'juw16155', 'Asra Aijaz', 'haniiaijaz17@gmail.com', '2021/Comp/BS(CS)/26948', 2024, 'Computer Science', '2147483647', 2126331, 0, 0, '9b1f57e4e775', '2024-12-02 04:46:27'),
(76, 'Juw20232', 'Rabiya Jawed', 'Rabiyajawed12@gmail.com', '2021/Comp/BS(SE)/27064', 2024, 'Software Engineering', '2147483647', 2126447, 0, 0, 'e9046579a678', '2024-12-02 04:46:27'),
(77, 'juw 19165', 'Anusha Noor', 'anushanoor0348@gmail.com', '2021/Comp/BS(SE)/27017', 2024, 'Software Engineering', '2147483647', 2126400, 0, 0, 'e2702aa15b4f', '2024-12-02 04:46:27'),
(79, 'Juw15629', 'Dilawaiz Memon', 'Dilawazmemon@gmail.com', '2021/Comp/BS(SE)/27025', 2024, 'Software Engineering', '2147483647', 2126408, 0, 0, '6ec7d48b7cc1', '2024-12-02 04:46:27'),
(80, 'juw13874', 'Samia mehreen', 'samiamehreen.2000@gmail.com', '2021/BS(SE)/Comp/27069', 2024, 'Software Engineering', '2147483647', 2126452, 0, 0, 'e408414a578e', '2024-12-02 04:46:27'),
(81, 'juw15707', 'Bushra Jamal', 'bushrajay@gmail.com', '2021/Comp/BS(CS)/26957', 2024, 'Computer Science', '2147483647', 2126340, 0, 0, 'fb77e85ba445', '2024-12-02 04:46:27'),
(84, 'juw16863', 'Filzah Tahir', 'filzah.syed2001@gmail.com', '2021/Comp/BS(CS)/26961', 2024, 'Computer Science', '2147483647', 2126344, 0, 0, 'f880b490232d', '2024-12-02 04:46:27'),
(85, 'juw17645', 'Aiman Tufail ', 'aimantufail831@gmail.com', '2021/Comp/BS(SE)/27011', 2024, 'Software Engineering', '2147483647', 2126394, 0, 0, 'c1fe821ec246', '2024-12-02 04:46:27'),
(86, ' juw17312 ', 'Fariha Shafiq ', 'farihashafiq396@gmail.com', '2021/Comp/BS(SE) /27029', 2024, 'Software Engineering', '2147483647', 2126412, 0, 0, '55bd169a9532', '2024-12-02 04:46:27'),
(87, 'juw14770', 'Aleeza Ahmed', 'aleezamehboob18@gmail.com', '2021/Comp/BS(CS)/26938', 2024, 'Computer Science', '2147483647', 2126321, 0, 0, '3e8d444cf653', '2024-12-02 04:46:27'),
(88, 'juw15997', 'Areeba Ejaz', 'areeba26ejaz@gmail.com', '2021/Comp/BS(SE)/27021', 2024, 'Software Engineering', '2147483647', 2126404, 0, 0, 'd71098107524', '2024-12-02 04:46:27'),
(89, 'Juw17599', 'Unsa Fahad ', 'unsafahad24@gmail.com  ', '2021/Comp/BS(CS)/27001', 2024, 'Computer Science', '2147483647', 2126384, 0, 0, 'cab06c3790a0', '2024-12-02 04:46:27'),
(90, 'juw15224', 'Noorma Naz', 'noormanaz284@gmail.com ', '2021/Comp/BS(SE)/27060', 2024, 'Software Engineering', '2147483647', 2126443, 0, 0, 'dcab4a599bbb', '2024-12-02 04:46:27'),
(91, 'Juw13916', 'Momna Attari ', 'mominaattari959@gmail.com', '2021/Comp/BS(SE)27053', 2024, 'Software Engineering', '2147483647', 2126436, 0, 0, '72587bd8c439', '2024-12-02 04:46:27'),
(92, 'juw17923', 'Khadija Faheem', 'khadijafaheem2020@gmail.com', '2021/Comp/BS(CS)/26972', 2024, 'Computer Science', '2147483647', 2126355, 0, 0, 'c1197c42d480', '2024-12-02 04:46:27'),
(93, '14427', 'Sidra Hafeez', 'hsidra381@gmail.com', '2021/Comp/BS(SE)/27073', 2024, 'Software Engineering', '2147483647', 2126456, 0, 0, 'abf13ebbd86d', '2024-12-02 04:46:27'),
(94, 'JUW15543', 'Syeda Urooba Amjad', 'uroobamjad1504@gmail.com', '2021/Comp/BS(CS)/26977', 2024, 'Computer Science', '2147483647', 2126380, 0, 0, '5eb4181f3380', '2024-12-02 04:46:27'),
(95, 'juw19820', 'Maria Aqdas', 'mariaaqdas6@gmail.com', '2021/Comp/?BS(CS)/26980', 2024, 'Computer Science', '2147483647', 2126363, 0, 0, '35c4385dfd4f', '2024-12-02 04:46:27'),
(96, 'juw19185', 'Hiba Kanwal', 'hibakanwal106@gmail.com', '2021/Comp/BS(SE)/27035', 2024, 'Software Engineering', '2147483647', 2126418, 0, 0, '674cb18ce9e7', '2024-12-02 04:46:27'),
(97, ' juw18264', 'Ayesha Manzoor', 'ayeshamanzoor015@gmail.com', '2021/Comp/BS(SE)/27024', 2024, 'Software Engineering', '2147483647', 2126407, 0, 0, '53828b75d158', '2024-12-02 04:46:27'),
(98, 'Juw13514', 'Eshal Shahid ', 'eshalshahidk41@gmail.com ', '2021/Comp/BS(SE)/27027', 2024, 'Software Engineering', '2147483647', 2126410, 0, 0, '2d5f32b32bb0', '2024-12-02 04:46:27'),
(100, 'Juw14039', ' MIFRA TANVEER', 'mifratanveer09@gmail.com', '2021/Comp/BS(SE)/27052', 2024, 'Software Engineering', '2147483647', 2126434, 0, 0, 'e3c31f972160', '2024-12-02 04:46:27'),
(101, 'Juw18762', 'Fizza Ahmed', 'Fizzaahmed128@gmail.com', '2021/Comp/BS(SE)/27031', 2024, 'Software Engineering', '2147483647', 2126414, 0, 0, '9a966a511ba5', '2024-12-02 04:46:27'),
(103, 'juw18840', 'Ayesha Arif', 'ayeshaarif249@gmail.com', '2021/Comp/BS(CS)/26951', 2024, 'Computer Science', '2147483647', 2126334, 0, 0, '0bd9ba1cc0e3', '2024-12-02 04:46:27'),
(104, 'juw14146', 'safa ahsan', 'Safaahsan0805@gmail.com', '2021/Comp/BS(CS)/26988', 2024, 'Computer Science', '2147483647', 2126371, 0, 0, '6d997913b706', '2024-12-02 04:46:27'),
(105, ' juw12625', 'Anzila Alvi', 'anzilaalizna@gmail.com', '2021/Comp/BS(SE)/27022', 2024, 'Software Engineering', '03142788728', 2126401, 2021, 0, 'd2af55fcfd36', '2024-12-02 04:46:27'),
(106, 'juw 20305', 'Aisha Jaweria', 'rafiaisha76@gmail.com', '2021/Comp/BS(SE)/27013', 2024, 'Software Engineering', '2147483647', 2126396, 0, 0, 'af5849fb6e9c', '2024-12-02 04:46:27'),
(107, 'juw19419', 'Uroosa Batool', 'uroosabatool35@gmail.com', '2021/Comp/BS(CS)/27004', 2024, 'Computer Science', '2147483647', 2126387, 0, 0, 'b07202c6b33f', '2024-12-02 04:46:27'),
(108, 'juw17262', 'Nida Naz', 'nidanaz099@gmail.com', '2021/Comp/BS(CS)/26985', 2024, 'Computer Science', '2147483647', 2126368, 0, 0, '784fe3838d4b', '2024-12-02 04:46:27'),
(109, 'juw-13830', 'Khaula Azhar', 'Khaulaazhar123@gmail.com', '2021/Comp/BS(CS)/26973', 2024, 'Computer Science', '2147483647', 2126356, 0, 0, 'c83de2e9a04f', '2024-12-02 04:46:27'),
(110, 'juw 20056', 'Zoya Rizvi', 'Syedazoyarizvi1998@gmail.com', '2021/Comp/BS(SE)/27092', 2024, 'Software Engineering', '2147483647', 2126475, 0, 0, '37e755b02f06', '2024-12-02 04:46:27'),
(111, 'juw 14462', 'Areeba Salam', 'areebakhanofficial1@gmail.com', '2021/Comp/BS(CS)/26941', 2024, 'Computer Science', '2147483647', 2126324, 0, 0, 'c6e36e90144e', '2024-12-02 04:46:27'),
(112, 'Juw12355 ', 'Tooba Khanam', 'shahtuba321@gmail.com ', '2021/Comp/BS(SE)/27083', 2024, 'Software Engineering', '2147483647', 2126466, 0, 0, '068c5d222f65', '2024-12-02 04:46:27'),
(113, 'juw 20275', 'Nimra Rao', 'nimrarao548@gmail.com', '2021/Comp/BS(SE)/27059', 2024, 'Software Engineering', '2147483647', 2126442, 0, 0, '8b69f974f9c3', '2024-12-02 04:46:27'),
(114, 'Juw12770', 'Rida Shaikh', 'ridazain96@gmail.com', '2021/Comp/BS(SE)/27067', 2024, 'Software Engineering', '2147483647', 2126450, 0, 0, '21d29d5653fa', '2024-12-02 04:46:27'),
(115, 'Juw20022', 'Warisha yaseen ', 'Warishayaseen00@gmail.com', '2021/Comp/BS(SE)/27087', 2024, 'Software Engineering', '2147483647', 2126470, 0, 0, 'bfa294ceccb5', '2024-12-02 04:46:27'),
(118, 'Juw16596', 'Hiba Ghazal ', 'hibaghazal32@gmail.com', ' 2021/Comp/BS(CS)/26968   ', 2024, 'Computer Science', '2147483647', 2126351, 0, 0, '040acd6dc219', '2024-12-02 04:46:27'),
(120, 'juw12198', 'Maria Aslam ', 'mariaaslam1721@gamil.com', '2021/Comp/BS(SE)/27046', 2024, 'Software Engineering', '2147483647', 2126429, 0, 0, '23974744b6f3', '2024-12-02 04:46:27'),
(121, 'Juw14080', 'Khatiba Amir', '2021/Comp/BSSE/27041', ' 2021/Comp/BS(SE)/27041 ', 2024, 'Software Engineering', '2147483647', 2126424, 0, 0, 'c95e605c8f1e', '2024-12-02 04:46:27'),
(122, 'juw17476', 'Nabeera Nasir ', 'nabeeeera@gmail.com', '2021/Comp/BS(CS)/26983', 2024, 'Computer Science', '2147483647', 2126366, 0, 0, '92e6260b81d1', '2024-12-02 04:46:27'),
(123, 'juw15744', 'Farah Fatima', 'farahfatima.fs@gmail.con', '2021/Comp/BS(SE)/27028', 2024, 'Software Engineering', '2147483647', 2126411, 0, 0, 'f0cd696c1764', '2024-12-02 04:46:27'),
(124, 'Juw17594', 'Anusha Zahid ', ' anushazahid204@gmail.com ', '2021/Comp/BS(CS)/26940', 2024, 'Computer Science', '2147483647', 2126323, 0, 0, '4fd3566c1c49', '2024-12-02 04:46:27'),
(125, 'juw15237', 'Warisha Abdul Qadir', 'warisha.qadir8801@gmail.com ', '2021/Comp/BS(SE)/27086', 2024, 'Software Engineering', '2147483647', 2126469, 0, 0, '9e76529faec6', '2024-12-02 04:46:27'),
(126, 'Juw16290', 'Muqaddas Ahsan', 'muqiahsan1234@gmail.com', '2021/Comp/BS(SE)/27054', 2024, 'Software Engineering', '2147483647', 2126437, 0, 0, '6ab8430d5b1f', '2024-12-02 04:46:27'),
(127, 'juw18531', 'Laiba Khalid', 'laibak379@gmail.com', '2021/Comp/BS(CS)/26974', 2024, 'Computer Science', '311', 2126357, 0, 0, 'ca63f61069a4', '2024-12-02 04:46:27'),
(128, '13783', 'Wasifa Asad Khan', 'wasifaasadk@gmail.com', '2021/Comp/BS(SE)/27088', 2024, 'Software Engineering', '2147483647', 2126471, 0, 0, '88ef15195f7d', '2024-12-02 04:46:27'),
(129, 'JUW15580', 'Zoya Dilshad', 'zoyashad3@gmail.com', '2021/Comp/BS(CS)/27010', 2024, 'Computer Science', '313', 2126393, 0, 0, '7589617c06f5', '2024-12-02 04:46:27'),
(130, 'juw17738', 'Unzila Rasheed', 'Unzilarasheed285@gmail.com', '2021/Comp/BS(CS)/27002', 2024, 'Computer Science', '320', 2126385, 0, 0, '18cb0753c4ef', '2024-12-02 04:46:27'),
(131, 'juw15099', 'Zainab Fatima', 'zainab6112002@gmail.com', '2021/Comp/BS(SE)/27090', 2024, 'Software Engineering', '306', 2126473, 0, 0, 'e9618bb7304d', '2024-12-02 04:46:27'),
(132, 'Juw13474', 'Areeba Muhammad Sohail ', 'memonareeba30@gmail.com ', '2021/Comp/BS(SE)/27020', 2024, 'Software Engineering', '2147483647', 1111, 0, 0, 'b5d815963fdb', '2024-12-02 04:46:27'),
(133, 'juw13917 ', 'Laiba Rehan', 'laibarehan566@gmail.com ', '2021/Comp/BS(CS)/26975', 2024, 'Computer Science', '345', 2126358, 0, 0, '3094224a19b1', '2024-12-02 04:46:27'),
(134, 'juw12899', 'NIDA MAQBOOL  ', 'nidamaqbool2@gmail.com', ' 2021/Comp/BS(SE)/27057', 2024, 'Software Engineering', '2147483647', 2126440, 0, 0, 'effeb2e420ba', '2024-12-02 04:46:27'),
(135, 'Juw15066', 'Jannat Siddique', 'Jannatsiddiqui420@gmail.com', '2021/Comp/BS(SE)/27038', 2024, 'Software Engineering', '316', 2126421, 0, 0, '114ed102eb1b', '2024-12-02 04:46:27'),
(137, 'juw15914', 'Maleeha Pervaiz', 'maleeha999999@gmail.com', '2021/Comp/BS(CS)/26978 ', 2024, 'Computer Science', '2147483647', 2126361, 0, 0, 'd35b1892ba1e', '2024-12-02 04:46:27'),
(138, 'juw14131', 'Zehira perveen', 'zahi.Hussain26@gmail.com', '2021/Comp/BS(CS)/27009', 2024, 'Computer Science', '2147483647', 2126392, 0, 0, 'a7c9f2b40860', '2024-12-02 04:46:27'),
(139, 'juw10837', 'Tehreem Altaf', 'bellatehreem@gmail.com', '2021/Comp/BS(SE)/27081', 2024, 'Software Engineering', '2147483647', 2126464, 0, 0, 'a37cae2222a2', '2024-12-02 04:46:27'),
(140, 'juw 16794', 'Musfira Ansari', 'ansarimusfira789@gmail.com', '2021/Comp/BS(SE)/27055', 2024, 'Software Engineering', '2147483647', 2126438, 0, 0, 'a038c02304a5', '2024-12-02 04:46:27'),
(141, 'juw17940', 'Sidra Asghar', 'asgharsidra977@gmail.com', '2021/Comp/BS(CS)/26994', 2024, 'Computer Science', '322', 2126377, 0, 0, '4eb7063bdc46', '2024-12-02 04:46:27'),
(142, 'juw15206', 'Safia Feroz', 'safiaferoz2002@gmail.com', '2021/Comp/BS(CS)/26989', 2024, 'Computer Science', '301', 2126372, 0, 0, '829b4d93121b', '2024-12-02 04:46:27'),
(143, 'juw-14714', 'Hadiya Manzar Ali', 'hadiyamanzarali@gmail.com', '2021/Comp/BS(CS)/26962', 2024, 'Computer Science', '2147483647', 2126345, 0, 0, '2e4ba820f094', '2024-12-02 04:46:27'),
(144, 'juw 12428', 'Ayesha Ghayour', 'ayeshaghayour171@gmail.com', '2021/Comp/BS(CS)/26952', 2024, 'Computer Science', '2147483647', 2126335, 0, 0, 'b882b3128109', '2024-12-02 04:46:27'),
(145, 'juw16384', 'Urooba Sattar ', 'uroobaa10@gmail.com', '2021/Comp/BS(SE)/27085', 2024, 'Software Engineering', '2147483647', 2126468, 0, 0, 'bc22e4c14fa7', '2024-12-02 04:46:27'),
(146, 'Juw14531', 'Zareen Khan', 'zareen3918@gmail.com', '2021/Comp/BS(SE)/27091', 2024, 'Software Engineering', '2147483647', 2126474, 2021, 0, '272a37917f80', '2024-12-02 04:46:27'),
(147, 'Juw14518', 'Hafiza Ayesha naseer', 'hafizaayesha751@gmail.com', '2021/Comp/BS(SE)/270337', 2024, 'Software Engineering', '2147483647', 2126416, 0, 0, 'c129c4bd0b6a', '2024-12-02 04:46:27'),
(159, 'juw111', 'Zareen', 'zareen3918@gmail.com', '2021/Comp/BS(SE)/000000', 2024, 'Software Engineering', '03123456789', 2126439, 2020, 0, '83e53219c05a', '2024-12-02 04:46:27'),
(160, 'juw222', 'Irza', 'irzahasanlap@gmail.com', '2021/Comp/BS(SE)/11111', 2024, 'Software Engineering', '03123456789', 2126439, 2021, 0, '0b7fc0162140', '2024-12-02 04:46:27'),
(162, 'juw1452', 'ayesha', 'stoneagepk@outlook.com', 'comp201998477', 2024, 'BSCS', '03434067797', 222222, 2021, 2021, 'deb14413c2f6', '2024-12-02 04:46:27'),
(166, 'juw14581', 'Irza Hasan', 'irzahasan07@gmail.com', 'comp201998477', 2024, 'Software Engineering', '03434067797', 2126420, 2021, 2021, '3a853ee35102', '2024-12-02 04:46:27');

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

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`Template_id`, `document_name`, `file_path`, `upload_date`, `send_to`) VALUES
(8, '  FYP-Proposal-Deck', '../coordinator/uploads/FYP-Proposal-Deck.pptx', '2024-10-29 19:00:00', 'All'),
(9, '  FYP Proposal Template', '../coordinator/uploads/FYP_proposal.docx', '2024-10-29 19:00:00', 'All'),
(10, 'SRS Template', '../coordinator/uploads/SRS_Template.doc', '2024-10-29 19:00:00', 'All'),
(11, 'Ignite Proposal Template', '../coordinator/uploads/IgniteProposalTemplate.pdf', '2024-10-29 19:00:00', 'All'),
(12, 'FYP+Report+Guidelines', '../coordinator/uploads/FYP+Report+Guidelines.docx', '2024-10-29 19:00:00', 'All'),
(13, 'FYP Report Template', '../coordinator/uploads/FYP+Report+Complete+Format-2024.docx', '2024-10-29 19:00:00', 'All'),
(14, 'Chapter 4 Project Plan Guide', '../coordinator/uploads/Chapter+4+Guide+-+Project+Plan.pdf', '2024-10-29 19:00:00', 'All'),
(15, 'FYP-Presentation-DeckFile', '../coordinator/uploads/FYP-Presentation-Deck.pptx', '2024-10-29 19:00:00', 'All');

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

--
-- Dumping data for table `training_data`
--

INSERT INTO `training_data` (`id`, `project_id`, `assignments`, `assignment_status`, `meetings_attended`, `avg_meeting_feedback`, `presentations_attended`, `avg_presentation_feedback`, `current_progress`) VALUES
(421, 18, '', '', 3, 'poor; excellent; very good', 1, '', 14),
(422, 23, '', '', 0, '', 0, '', 0),
(423, 24, '', '', 0, '', 0, '', 0),
(424, 25, '', '', 0, '', 0, '', 0),
(425, 26, '', '', 0, '', 0, '', 0),
(426, 27, '', '', 0, '', 0, '', 0),
(427, 28, '', '', 0, '', 0, '', 0),
(428, 29, '', '', 0, '', 0, '', 0),
(429, 30, '', '', 0, '', 0, '', 0),
(430, 31, '', '', 0, '', 0, '', 0),
(431, 32, '', '', 0, '', 0, '', 0),
(432, 34, '', '', 0, '', 0, '', 0),
(433, 35, '', '', 0, '', 0, '', 0),
(434, 40, '', '', 0, '', 0, '', 0),
(435, 41, '', '', 0, '', 0, '', 0),
(436, 42, '', '', 0, '', 0, '', 0),
(437, 43, '', '', 0, '', 0, '', 0),
(438, 44, '', '', 0, '', 0, '', 0),
(439, 45, '', '', 0, '', 0, '', 0),
(440, 46, '', '', 0, '', 0, '', 0),
(441, 47, '', '', 0, '', 0, '', 0),
(442, 48, '', '', 0, '', 0, '', 0),
(443, 49, '', '', 0, '', 0, '', 0),
(444, 50, 'mm, proposal, helo, new, jdbnddnbbn, try, jadshdshjdsj, jadshdshjdsj, new2, new2, propo, zzzzzzzzz, zzzzzzzzz, mm, proposal, helo, new, jdbnddnbbn, try, jadshdshjdsj, jadshdshjdsj, new2, new2, propo, zzzzzzzzz, zzzzzzzzz, mm, proposal, helo, new, jdbnddnbbn, try, jadshdshjdsj, jadshdshjdsj, new2, new2, propo, zzzzzzzzz, zzzzzzzzz, mm, proposal, helo, new, jdbnddnbbn, try, jadshdshjdsj, jadshdshjdsj, new2, new2, propo, zzzzzzzzz, zzzzzzzzz, mm, proposal, helo, new, jdbnddnbbn, try, jadshdshjdsj, jadshdshjdsj, new2, new2, propo, zzzzzzzzz, zzzzzzzzz, mm, proposal, helo, new, jdbnddnbbn, try, jadshdshjdsj, jadshdshjdsj, new2, new2, propo, zzzzzzzzz, zzzzzzzzz', 'Late, Late, Late, Early, Late, Late, Early, Early, Late, Late, Late, Early, Late, Late, Late, Late, Late, Early, Late, Late, Early, Early, Late, Late, Late, Early, Late, Late, Late, Late, Late, Early, Late, Late, Early, Early, Late, Late, Late, Early, Lat', 2, 'excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; good; good; good; good; good; good; good; good; good; good; good; good; good; good; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; good; good; good; good; good; good; good; good; good; good; good; good; good; good; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; excellent; good; good; good; good; good; good; good; good; good; good; good; good; good; good', 3, 'very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; very good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good; good', 11),
(445, 51, '', '', 0, '', 0, '', 0),
(446, 52, '', '', 0, '', 0, '', 0),
(447, 53, '', '', 0, '', 0, '', 0),
(448, 54, '', '', 0, '', 0, '', 0),
(449, 55, '', '', 0, '', 0, '', 0),
(450, 56, '', '', 0, '', 0, '', 0),
(451, 57, '', '', 0, '', 0, '', 0),
(452, 58, '', '', 0, '', 0, '', 0),
(453, 59, 'proposal, helo, mm, new2', 'Late, Late, Late, Late', 1, '', 0, '', 0),
(454, 60, '', '', 0, '', 0, '', 0),
(455, 61, '', 'Early', 0, '', 0, '', 0);

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

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `role`) VALUES
(76, 'pagal', 'pagal@email.com', 'g^j*K*8BcRZ)', 'Student'),
(77, 'pagal2', 'pagal2@email.com', 'fsq&OPQTcs!Q', 'Student'),
(78, 'zareenkhan', 'zareenkhan3918@gmail.com', 'wtf!tH9&4*0W', 'Student'),
(80, 'Prof. Dr. Narmeen Zakaria Bawany', 'nzbawany@gmail.com', '$2y$10$WuMncA0ZkBfyOZh61xUzSu4cBSIKJX0trD4JnRzL6Y7.cmd7GmIHC', 'faculty'),
(81, 'Dr. Rahu Sikander	', 'sikander.rahu2013@gmail.com', '$2y$10$mWzBZDdFvv1i5wAY3RS83.xTY57kPmR61VsSur5jAVXoLDvfxbhRe', 'faculty'),
(82, 'Ms. Samia Ghazala	', 'samia_ghazala@yahoo.com', '$2y$10$QBm9Pv3isBpmrbg8rhMgouX0o/V/81UqlZ7E4vrufZz38.PpFDQ3S', 'faculty'),
(83, 'Ms. Sadia Javed	', 'sadi0921@gmail.com', '$2y$10$7ZDUp9zSd5gnDVVGMCYgQuwbhmpcmf9Xiaaq.U4j.XKuA6mry8.i.', 'faculty'),
(84, 'Ms. Ummay Faseeha	', 'ummay.faseeha@gmail.com', '$2y$10$/2XhRokYJUxUnLdNylyC4.vTrIta33BYZoimSVrDuULVxHX9s8opa', 'faculty'),
(85, 'Ms. Hafiza Anisa Ahmed	', 'hafizaanisaahmed@gmail.com', '$2y$10$DpqecFN.qmTjIr5VyJPVCOZTMmGBBei2U6k72IkDN1AqX6fk8MM6y', 'faculty'),
(86, 'Ms. Syeda Anum Zamir	', 'anumzameer227@gmail.com', '$2y$10$sezg6g8qCeg7wMHo4zDr.OGuQdpJycApjuNMkB1UhO7VLxZhafNN2', 'faculty'),
(87, 'Ms. Hira Tariq	', 'htariquzair42@gmail.com', '$2y$10$ryt8H9jxvX/uuDxxBNrw4.ovwq0W2vLCyIgSfkMwkORa3dOem/yNe', 'faculty'),
(88, 'Ms. Kanwal Zahoor	', 'kanwalzahoor92@gmail.com', '$2y$10$cE6aZVhj3.EW7p8yvHlERe334Pr/6.ASldd8K5smICPGv8/tRs/we', 'faculty'),
(89, 'Ms. Tehreem Qamar	', 'tehreem_qamar10@hotmail.com', '$2y$10$5ZGyp21a0zGJQxM29wnQ5usiX9390DWvwYxNRkT8.mm6yFNUxFfIy', 'faculty'),
(90, 'Ms. Saba Mazhar	', 'sabarizwan2@yahoo.com', '$2y$10$zOEMUx/n0UgdeuYN5erEy.bPbI..o7tTP8Uyb491mszqgfnwrI2Vy', 'faculty'),
(91, 'Ms. Soomaiya Hamid	', 'soomaiya.hamid@gmail.com', '$2y$10$c2oeTC4NUbKadr1na8Q3o.eOu7SV.pnyleEemYbF/rpAs3HNvN3CK', 'faculty'),
(92, 'Ms. Ayesha Zulfiqar	', 'ayesha001.az@gmail.com', '$2y$10$hG81NJavB5GCKfn2D0fvdesMGIjtfKyT3DNrRdCF/FJcyGyVmtLHy', 'faculty'),
(93, 'Ms. Arifa Shamim	', 'arifashamim188@gmail.com', '$2y$10$P0ywvwJ.ktiC351NVuHJxeOnX/mbVZtgUQ7hL.4k.xiDkp/uN8Lb2', 'faculty'),
(94, 'Ms. Surayya Obaid	', 'surayya.mahrukh@gmail.com', '$2y$10$kGx8OnXXxEnTYbqHvEZc4OTQ7sg5rnV.whSo2yl8DCsuWEnm.5MeS', 'faculty'),
(95, 'Ms. Anum Ilyas	', 'ianumilyaszia@gmail.com', '$2y$10$LQ/VzZ8hk1Z/g.6szI21P.dgDqDXO/UiQqZfhKXh/vukokUOzIh5G', 'faculty'),
(96, 'Ms. Mehak Abbas	', 'mehakabbas500@gmail.com', '$2y$10$YlGYKu8UrP.wq5StCCFEWev3Z8XRGbPh50hutsKXN8e6RPx49WYCm', 'faculty'),
(97, 'Ms. Hira Sultan	', 'mughalhira199@gmail.com', '$2y$10$8omuEYf6HGykc2Bmlfqd7e6Ab7zi9guhO7O2p9MjfWoIt6kMKWYTG', 'faculty'),
(98, 'Ms. Ushna Tasleem	', 'ushnatasleemjuw23@gmail.com', '$2y$10$dbMsmhU16pmozWi4PyTYEOL8k5P6CMOoq6PbZ2zBIR2eeDOBjGFHG', 'faculty'),
(99, 'Ms. Manahil	', 'manahilaly21@gmail.com', '$2y$10$jGIwkua8F90sPN7KNIXi6eJ3JNX6iCaSy7n4IsOdtcm5sEIy6si/G', 'faculty'),
(101, 'zareen khan', 'zareenkhan@gmail.com', '$2y$10$nVMzw0DZA.TV6U9nau9odOEH.EKiTaTp4sh1FY6KAUmewl8ydl6w6', 'faculty'),
(102, 'ayesha', 'stoneagepk@outlook.com', 'UH*5XuYuPRfL', 'Student'),
(103, 'sss', 'zari@gmail.com', '&Dmj*&*Kpulb', 'Student'),
(104, 'sss', 'zareenkhakhamn@gmail.com', '!o*b^l$7b9gj', 'Student'),
(105, 'aaa', 'zareenkhan_00@gmail.com', 'ZxW0aVa*yb7@', 'Student'),
(106, 'irzahasan', 'irzahasanla07@gmail.com', 'QffcI!q)Up&Y', 'Student'),
(108, 'Zareen Khan', 'irzahasanlap@gmail.com', 'qcI5!Y!1zKqD', 'faculty'),
(109, 'irziii', 'irzahasan07@gmail.com', 'ivGh!lJEA*fh', 'faculty'),
(110, 'maria', 'example@gmail.com', '6I0iC5&CS(wg', 'faculty'),
(112, '123', '123@gmail.com', '&vQ1L&dTyFLP', 'Student');

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

--
-- Dumping data for table `visible_pages`
--

INSERT INTO `visible_pages` (`id`, `fyp_i_mid`, `fyp_ii_mid`, `fyp_i_final`, `fyp_ii_final`, `mid_form`) VALUES
(1, 'yes', 'yes', 'yes', 'no', 'no');

--
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
