-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2026 at 04:29 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cdonhs_shs_database_enrollment`
--

-- --------------------------------------------------------

--
-- Table structure for table `activation_settings`
--

CREATE TABLE `activation_settings` (
  `id` int(11) NOT NULL,
  `activation_name` varchar(100) NOT NULL,
  `activation_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Disabled, 1=Enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activation_settings`
--

INSERT INTO `activation_settings` (`id`, `activation_name`, `activation_status`) VALUES
(1, 'Student Enrollment', 1),
(2, 'Form 137 and 138 Page', 1),
(3, 'Student Progress Page', 1),
(4, 'Teacher Registration', 1);

-- --------------------------------------------------------

--
-- Table structure for table `archived_student_strand`
--

CREATE TABLE `archived_student_strand` (
  `archive_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `date_archived` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` enum('PROMOTION','TRANSFER','MANUAL') DEFAULT 'PROMOTION'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clusters`
--

CREATE TABLE `clusters` (
  `cluster_id` int(11) NOT NULL,
  `track_id` int(11) DEFAULT NULL,
  `cluster_name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clusters`
--

INSERT INTO `clusters` (`cluster_id`, `track_id`, `cluster_name`) VALUES
(1, NULL, 'Core Subjects'),
(10, 1, 'Arts, Social Sciences, and Humanities'),
(11, 1, 'Business and Entrepreneurship'),
(14, 1, 'Field Experience'),
(12, 1, 'Science, Technology, Engineering, and Mathematics'),
(13, 1, 'Sports, Health, and Wellness'),
(20, 2, 'Aesthetic, Wellness, and Human Care'),
(21, 2, 'Agri-Fishery Business and Food Innovation'),
(22, 2, 'Artisanry and Creative Enterprise'),
(23, 2, 'Automotive and Small Engine Technologies'),
(24, 2, 'Construction and Building Technologies'),
(25, 2, 'Creative Arts and Design Technologies'),
(26, 2, 'Hospitality and Tourism'),
(28, 2, 'ICT Support and Computer Programming Technologies'),
(27, 2, 'Industrial Technologies'),
(29, 2, 'Maritime Transport');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(2, 'Admin'),
(1, 'Student'),
(3, 'Teacher');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `section_name` varchar(30) NOT NULL,
  `grade_level` int(2) NOT NULL,
  `strand_id` int(11) DEFAULT NULL,
  `track_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `section_name`, `grade_level`, `strand_id`, `track_id`) VALUES
(1, 'A', 11, 1, 1),
(2, 'B', 11, 1, 1),
(3, 'C', 11, 1, 1),
(4, 'D', 11, 1, 1),
(5, 'A', 12, 1, 1),
(6, 'B', 12, 1, 1),
(7, 'C', 12, 1, 1),
(8, 'D', 12, 1, 1),
(9, 'A', 11, 2, 1),
(10, 'B', 11, 2, 1),
(11, 'C', 11, 2, 1),
(12, 'D', 11, 2, 1),
(13, 'A', 12, 2, 1),
(14, 'B', 12, 2, 1),
(15, 'C', 12, 2, 1),
(16, 'D', 12, 2, 1),
(17, 'A', 11, 3, 1),
(18, 'B', 11, 3, 1),
(19, 'C', 11, 3, 1),
(20, 'D', 11, 3, 1),
(21, 'A', 12, 3, 1),
(22, 'B', 12, 3, 1),
(23, 'C', 12, 3, 1),
(24, 'D', 12, 3, 1),
(25, 'A', 11, 4, 1),
(26, 'B', 11, 4, 1),
(27, 'C', 11, 4, 1),
(28, 'D', 11, 4, 1),
(29, 'A', 12, 4, 1),
(30, 'B', 12, 4, 1),
(31, 'C', 12, 4, 1),
(32, 'D', 12, 4, 1),
(33, 'A', 11, 5, 2),
(34, 'B', 11, 5, 2),
(35, 'C', 11, 5, 2),
(36, 'D', 11, 5, 2),
(37, 'A', 12, 5, 2),
(38, 'B', 12, 5, 2),
(39, 'C', 12, 5, 2),
(40, 'D', 12, 5, 2),
(41, 'A', 11, 6, 2),
(42, 'B', 11, 6, 2),
(43, 'C', 11, 6, 2),
(44, 'D', 11, 6, 2),
(45, 'A', 12, 6, 2),
(46, 'B', 12, 6, 2),
(47, 'C', 12, 6, 2),
(48, 'D', 12, 6, 2),
(49, 'A', 11, 7, 2),
(50, 'B', 11, 7, 2),
(51, 'C', 11, 7, 2),
(52, 'D', 11, 7, 2),
(53, 'A', 12, 7, 2),
(54, 'B', 12, 7, 2),
(55, 'C', 12, 7, 2),
(56, 'D', 12, 7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `strand_id` int(11) NOT NULL,
  `strand_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`strand_id`, `strand_name`) VALUES
(1, 'STEM'),
(2, 'ABM'),
(3, 'HUMSS'),
(4, 'GAS'),
(5, 'TVL-ICT'),
(6, 'TVL-EIM'),
(7, 'TVL-HE');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `enrollment_status` enum('Active','Inactive','Graduated','Transferred') DEFAULT 'Active',
  `date_enrolled` date NOT NULL DEFAULT curdate(),
  `enlistment_status` enum('Not Enlisted','Pending','Enlisted','Rejected','Promoted','Finished') DEFAULT 'Not Enlisted',
  `school_year` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `application_id`, `enrollment_status`, `date_enrolled`, `enlistment_status`, `school_year`) VALUES
(17, 40, 1, 'Active', '2026-03-04', 'Not Enlisted', '2025-2026');

-- --------------------------------------------------------

--
-- Table structure for table `student_applications`
--

CREATE TABLE `student_applications` (
  `application_id` int(11) NOT NULL,
  `lrn` varchar(12) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `extension_name` varchar(20) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `place_of_birth` varchar(150) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `mother_tongue` varchar(100) DEFAULT NULL,
  `indigenous_community` enum('Yes','No') DEFAULT 'No',
  `ip_specify` varchar(150) DEFAULT NULL,
  `four_ps_beneficiary` enum('Yes','No') DEFAULT 'No',
  `four_ps_household_id` varchar(50) DEFAULT NULL,
  `house_number` varchar(50) DEFAULT NULL,
  `street` varchar(150) DEFAULT NULL,
  `barangay` varchar(100) NOT NULL,
  `city_municipality` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT 'Philippines',
  `zip_code` varchar(10) DEFAULT NULL,
  `same_as_current` enum('Yes','No') DEFAULT 'Yes',
  `permanent_house_number` varchar(50) DEFAULT NULL,
  `permanent_street` varchar(150) DEFAULT NULL,
  `permanent_barangay` varchar(100) DEFAULT NULL,
  `permanent_city` varchar(100) DEFAULT NULL,
  `permanent_province` varchar(100) DEFAULT NULL,
  `permanent_country` varchar(100) DEFAULT NULL,
  `permanent_zip_code` varchar(10) DEFAULT NULL,
  `father_last_name` varchar(100) DEFAULT NULL,
  `father_first_name` varchar(100) DEFAULT NULL,
  `father_middle_name` varchar(100) DEFAULT NULL,
  `father_contact` varchar(20) DEFAULT NULL,
  `mother_last_name` varchar(100) DEFAULT NULL,
  `mother_first_name` varchar(100) DEFAULT NULL,
  `mother_middle_name` varchar(100) DEFAULT NULL,
  `mother_contact` varchar(20) DEFAULT NULL,
  `guardian_last_name` varchar(100) DEFAULT NULL,
  `guardian_first_name` varchar(100) DEFAULT NULL,
  `guardian_middle_name` varchar(100) DEFAULT NULL,
  `guardian_contact` varchar(20) DEFAULT NULL,
  `with_disability` enum('Yes','No') DEFAULT 'No',
  `disability_type` text DEFAULT NULL,
  `manifestation` text DEFAULT NULL,
  `pwd_id` enum('Yes','No') DEFAULT 'No',
  `pwd_id_number` varchar(50) DEFAULT NULL,
  `last_grade_completed` varchar(50) DEFAULT NULL,
  `last_school_year_completed` varchar(9) DEFAULT NULL,
  `last_school_attended` varchar(255) DEFAULT NULL,
  `school_id` varchar(20) DEFAULT NULL,
  `blended` tinyint(1) DEFAULT 0,
  `modular_print` tinyint(1) DEFAULT 0,
  `modular_digital` tinyint(1) DEFAULT 0,
  `online` tinyint(1) DEFAULT 0,
  `homeschooling` tinyint(1) DEFAULT 0,
  `educational_tv` tinyint(1) DEFAULT 0,
  `radio_based_tv` tinyint(1) DEFAULT 0,
  `psa_birth_certificate` varchar(255) DEFAULT NULL,
  `form_138` varchar(255) DEFAULT NULL,
  `student_id_copy` varchar(255) DEFAULT NULL,
  `enrollment_type` enum('New','Transferee','Balik-Aral') DEFAULT 'New',
  `application_status` enum('Pending','Approved','Rejected','Conditionally Approved') DEFAULT 'Pending',
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `facebook_profile` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_applications`
--

INSERT INTO `student_applications` (`application_id`, `lrn`, `last_name`, `first_name`, `middle_name`, `extension_name`, `date_of_birth`, `sex`, `place_of_birth`, `religion`, `mother_tongue`, `indigenous_community`, `ip_specify`, `four_ps_beneficiary`, `four_ps_household_id`, `house_number`, `street`, `barangay`, `city_municipality`, `province`, `country`, `zip_code`, `same_as_current`, `permanent_house_number`, `permanent_street`, `permanent_barangay`, `permanent_city`, `permanent_province`, `permanent_country`, `permanent_zip_code`, `father_last_name`, `father_first_name`, `father_middle_name`, `father_contact`, `mother_last_name`, `mother_first_name`, `mother_middle_name`, `mother_contact`, `guardian_last_name`, `guardian_first_name`, `guardian_middle_name`, `guardian_contact`, `with_disability`, `disability_type`, `manifestation`, `pwd_id`, `pwd_id_number`, `last_grade_completed`, `last_school_year_completed`, `last_school_attended`, `school_id`, `blended`, `modular_print`, `modular_digital`, `online`, `homeschooling`, `educational_tv`, `radio_based_tv`, `psa_birth_certificate`, `form_138`, `student_id_copy`, `enrollment_type`, `application_status`, `email`, `contact_number`, `remarks`, `date_submitted`, `facebook_profile`, `profile_image`) VALUES
(1, '405220150089', 'Clarito', 'Nick Charles', 'Durangparang', '', '2005-08-20', 'Male', 'Cagayan De Oro', 'Christian', 'Bisaya', 'No', '', 'No', '', 'Blk 4 lot 3 Buena Oro', NULL, 'Macasandig', 'Cagayan De Oro City', 'Misamis Oriental', 'Philippines', '9000', 'Yes', 'Blk 4 lot 3 Buena Oro', NULL, 'Macasandig', 'Cagayan De Oro City', 'Misamis Oriental', 'Philippines', '9000', 'Clarito', 'Randy', 'Durangparang', '09826473264', 'Clarito', 'Maria Cristina', 'Durangparang', '09262360968', '', '', '', '', 'No', '', '', 'No', '', 'Grade 10', '2024-2025', 'Cagayan De Oro National High School', '', 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'New', 'Approved', 'nickcharlesclarito@gmail.com', '09944719534', 'hi kido', '2026-03-04 14:39:33', 'https://www.hostitsmart.com/manage/knowledgebase/388/How-to-Change-Table-Name-in-phpMyAdmin.html', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_electives`
--

CREATE TABLE `student_electives` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `offering_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `semester` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_strand`
--

CREATE TABLE `student_strand` (
  `student_strand_id` int(11) NOT NULL,
  `student_id` int(10) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` int(2) NOT NULL,
  `section_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `status` enum('Enrolled','Dropped','Pending','Withdrawn with Grades','Withdrawn','Completed') DEFAULT 'Pending',
  `requested` tinyint(1) DEFAULT 1 COMMENT '1=student requested, 0=student did not request',
  `school_year` varchar(9) NOT NULL,
  `offering_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_track`
--

CREATE TABLE `student_track` (
  `student_id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `track_id` int(11) DEFAULT NULL,
  `cluster_id` int(11) DEFAULT NULL,
  `subject_type` enum('CORE','ELECTIVE') NOT NULL DEFAULT 'CORE',
  `recommended_grade_level` varchar(5) NOT NULL DEFAULT '11/12',
  `nc_equivalent` varchar(20) DEFAULT NULL,
  `prerequisites` text DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `semester` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject_name`, `track_id`, `cluster_id`, `subject_type`, `recommended_grade_level`, `nc_equivalent`, `prerequisites`, `is_required`, `semester`) VALUES
(1, 'Oral Communication', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(2, 'Komunikasyon at Pananaliksik', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(3, 'General Mathematics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(4, 'Earth and Life Science', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(5, 'Understanding Culture, Society and Politics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(6, 'Pre-Calculus', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(7, 'Basic Calculus', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(8, 'Chemistry 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(9, 'Oral Communication', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(10, 'Komunikasyon at Pananaliksik', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(11, 'General Mathematics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(12, 'Statistics and Probability', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(13, 'Understanding Culture, Society and Politics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(14, 'Business Math', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(15, 'Fundamentals of Accountancy 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(16, 'Organization and Management', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(17, 'Oral Communication', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(18, 'Komunikasyon at Pananaliksik', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(19, 'General Mathematics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(20, 'Statistics and Probability', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(21, 'Understanding Culture, Society and Politics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(22, 'Creative Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(23, 'Disciplines and Ideas in Social Sciences', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(24, 'Introduction to Philosophy', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(25, 'Oral Communication', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(26, 'Komunikasyon at Pananaliksik', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(27, 'General Mathematics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(28, 'Statistics and Probability', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(29, 'Understanding Culture, Society and Politics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(30, 'Humanities 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(31, 'Applied Economics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(32, 'Organization and Management', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(33, 'Oral Communication', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(34, 'Komunikasyon at Pananaliksik', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(35, 'General Mathematics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(36, 'Statistics and Probability', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(37, 'Understanding Culture, Society and Politics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(38, 'Computer Systems Servicing 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(39, 'Computer Systems Servicing 2', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(40, 'Computer Systems Servicing 3', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(41, 'Oral Communication', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(42, 'Komunikasyon at Pananaliksik', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(43, 'General Mathematics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(44, 'Statistics and Probability', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(45, 'Understanding Culture, Society and Politics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(46, 'Electrical Installation 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(47, 'Electrical Installation 2', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(48, 'Electrical Installation 3', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(49, 'Oral Communication', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(50, 'Komunikasyon at Pananaliksik', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(51, 'General Mathematics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(52, 'Statistics and Probability', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(53, 'Understanding Culture, Society and Politics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(54, 'Cookery 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(55, 'Cookery 2', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(56, 'Bread and Pastry Production', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(57, 'Reading and Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(58, '21st Century Literature', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(59, 'Contemporary Philippine Arts', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(60, 'Media and Information Literacy', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(61, 'Physical Science', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(62, 'Physics 2', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(63, 'Biology 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(64, 'Chemistry 2', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(65, 'Reading and Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(66, '21st Century Literature', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(67, 'Contemporary Philippine Arts', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(68, 'Media and Information Literacy', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(69, 'Business Ethics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(70, 'Fundamentals of Accountancy 2', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(71, 'Applied Economics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(72, 'Business Finance', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(73, 'Reading and Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(74, '21st Century Literature', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(75, 'Contemporary Philippine Arts', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(76, 'Media and Information Literacy', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(77, 'Creative Nonfiction', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(78, 'Philippine Politics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(79, 'Trends and Networks', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(80, 'Disciplines in Social Science', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(81, 'Reading and Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(82, '21st Century Literature', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(83, 'Contemporary Philippine Arts', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(84, 'Media and Information Literacy', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(85, 'Creative Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(86, 'Humanities 2', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(87, 'Disaster Readiness', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(88, 'Applied Economics', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(89, 'Reading and Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(90, '21st Century Literature', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(91, 'Contemporary Philippine Arts', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(92, 'Media and Information Literacy', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(93, 'Computer Systems Servicing 4', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(94, 'CSS Project', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(95, 'Practical Research 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(96, 'Empowerment Technologies', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(97, 'Reading and Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(98, '21st Century Literature', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(99, 'Contemporary Philippine Arts', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(100, 'Media and Information Literacy', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(101, 'Electrical Installation 4', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(102, 'EIM Project', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(103, 'Practical Research 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(104, 'Empowerment Technologies', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(105, 'Reading and Writing', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(106, '21st Century Literature', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(107, 'Contemporary Philippine Arts', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(108, 'Media and Information Literacy', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(109, 'Cookery 3', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(110, 'Food and Beverage Services', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(111, 'Practical Research 1', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL),
(112, 'Empowerment Technologies', NULL, NULL, 'CORE', '11/12', NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject_new`
--

CREATE TABLE `subject_new` (
  `subject_new_id` int(11) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `track_id` int(11) DEFAULT NULL,
  `cluster_id` int(11) NOT NULL,
  `subject_type` enum('CORE','ELECTIVE') NOT NULL,
  `recommended_grade_level` varchar(5) NOT NULL,
  `nc_equivalent` varchar(30) DEFAULT NULL,
  `prerequisites` text DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_new`
--

INSERT INTO `subject_new` (`subject_new_id`, `subject_name`, `track_id`, `cluster_id`, `subject_type`, `recommended_grade_level`, `nc_equivalent`, `prerequisites`, `is_required`, `is_active`) VALUES
(1, 'Effective Communication / Mabisang Komunikasyon', NULL, 1, 'CORE', '11', NULL, 'none', 1, 1),
(2, 'General Mathematics', NULL, 1, 'CORE', '11', NULL, 'none', 1, 1),
(3, 'General Science', NULL, 1, 'CORE', '11', NULL, 'none', 1, 1),
(4, 'Life and Career Skills', NULL, 1, 'CORE', '11', NULL, 'none', 1, 1),
(5, 'Pag-aaral ng Kasaysayan at Lipunang Pilipino', NULL, 1, 'CORE', '11', NULL, 'none', 1, 1),
(6, 'Arts 1 (Creative Industries - Visual Art, Literary Art, Media Art, Applied Art, and Traditional Art)', 1, 10, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(7, 'Arts 2 (Creative Industries - Music, Dance, and Theater)', 1, 10, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(8, 'Citizenship and Civic Engagement', 1, 10, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(9, 'Contemporary Literature 1', 1, 10, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(10, 'Contemporary Literature 2', 1, 10, 'ELECTIVE', '11/12', NULL, 'Contemporary Literature 1', 0, 1),
(11, 'Creative Composition 1', 1, 10, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(12, 'Creative Composition 2', 1, 10, 'ELECTIVE', '11', NULL, 'Creative Composition 1', 0, 1),
(13, 'Filipino 1 (Wika at Komunikasyon sa Akademikong Filipino)', 1, 10, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(14, 'Filipino 2 (Filipino para sa Larang Teknikal-Propesyonal/Isports/Sining at Disenyo)', 1, 10, 'ELECTIVE', '12', NULL, 'Filipino 1', 0, 1),
(15, 'Filipino Identity Through the Arts', 1, 10, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(16, 'Introduction to Philosophy', 1, 10, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(17, 'Leadership and Management in the Arts', 1, 10, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(18, 'Malikhaing Pagsulat', 1, 10, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(19, 'Philippine Governance (Philippine Politics and Governance)', 1, 10, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(20, 'Social Sciences (Theory and Practice)', 1, 10, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(21, 'Business 1 (Basic Accounting)', 1, 11, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(22, 'Introduction to Organization and Management', 1, 11, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(23, 'Business 2 (Business Finance and Income Taxation)', 1, 11, 'ELECTIVE', '11/12', NULL, 'Basic Accounting; Organization and Management', 0, 1),
(24, 'Business 3 (Business Economics)', 1, 11, 'ELECTIVE', '11/12', NULL, 'Basic Accounting; Organization and Management', 0, 1),
(25, 'Contemporary Marketing', 1, 11, 'ELECTIVE', '11/12', NULL, 'Basic Accounting; Organization and Management', 0, 1),
(26, 'Entrepreneurship', 1, 11, 'ELECTIVE', '12', NULL, 'Basic Accounting; Organization and Management', 0, 1),
(27, 'Biology 1', 1, 12, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(28, 'Biology 2', 1, 12, 'ELECTIVE', '11', NULL, 'Biology 1', 0, 1),
(29, 'Biology 3', 1, 12, 'ELECTIVE', '12', NULL, 'Biology 1; Biology 2', 0, 1),
(30, 'Biology 4', 1, 12, 'ELECTIVE', '12', NULL, 'Biology 1; Biology 2; Biology 3', 0, 1),
(31, 'Chemistry 1', 1, 12, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(32, 'Chemistry 2', 1, 12, 'ELECTIVE', '11', NULL, 'Chemistry 1', 0, 1),
(33, 'Chemistry 3', 1, 12, 'ELECTIVE', '12', NULL, 'Chemistry 1; Chemistry 2', 0, 1),
(34, 'Chemistry 4', 1, 12, 'ELECTIVE', '12', NULL, 'Chemistry 1; Chemistry 2; Chemistry 3', 0, 1),
(35, 'Earth and Space Science 1', 1, 12, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(36, 'Earth and Space Science 2', 1, 12, 'ELECTIVE', '11', NULL, 'Earth and Space Science 1', 0, 1),
(37, 'Earth and Space Science 3', 1, 12, 'ELECTIVE', '12', NULL, 'Earth and Space Science 1; Earth and Space Science 2', 0, 1),
(38, 'Earth and Space Science 4', 1, 12, 'ELECTIVE', '12', NULL, 'Earth and Space Science 1; Earth and Space Science 2; Earth and Space Science 3', 0, 1),
(39, 'Physics 1', 1, 12, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(40, 'Physics 2', 1, 12, 'ELECTIVE', '11', NULL, 'Physics 1', 0, 1),
(41, 'Physics 3', 1, 12, 'ELECTIVE', '12', NULL, 'Physics 1; Physics 2', 0, 1),
(42, 'Physics 4', 1, 12, 'ELECTIVE', '12', NULL, 'Physics 1; Physics 2; Physics 3', 0, 1),
(43, 'Finite Mathematics 1', 1, 12, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(44, 'Finite Mathematics 2', 1, 12, 'ELECTIVE', '11/12', NULL, 'Finite Mathematics 1', 0, 1),
(45, 'Pre-calculus 1', 1, 12, 'ELECTIVE', '12', NULL, 'General Mathematics (core)', 0, 1),
(46, 'Pre-calculus 2', 1, 12, 'ELECTIVE', '12', NULL, 'Pre-calculus 1', 0, 1),
(47, 'Trigonometry 1', 1, 12, 'ELECTIVE', '12', NULL, 'General Mathematics (core)', 0, 1),
(48, 'Trigonometry 2', 1, 12, 'ELECTIVE', '12', NULL, 'Trigonometry 1', 0, 1),
(49, 'General Science 3', 1, 12, 'ELECTIVE', '12', NULL, 'General Science (core)', 0, 1),
(50, 'General Science 4', 1, 12, 'ELECTIVE', '12', NULL, 'General Science 3', 0, 1),
(51, 'Empowerment Technologies', 1, 12, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(52, 'Fundamentals in Data Analytics', 1, 12, 'ELECTIVE', '12', NULL, 'General Mathematics (core)', 0, 1),
(53, 'Database Management', 1, 12, 'ELECTIVE', '12', NULL, 'Fundamentals in Data Analytics', 0, 1),
(54, 'Human Movement 1 (Basic Anatomy in Sports and Exercise)', 1, 13, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(55, 'Human Movement 2 (Motor Skills Development)', 1, 13, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(56, 'Physical Education 1 (Fitness and Recreation)', 1, 13, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(57, 'Physical Education 2 (Sports and Dance)', 1, 13, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(58, 'Sports Activity Management', 1, 13, 'ELECTIVE', '11/12', NULL, 'none', 0, 1),
(59, 'Sports Coaching', 1, 13, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(60, 'Sports Officiating', 1, 13, 'ELECTIVE', '11', NULL, 'none', 0, 1),
(61, 'Exercise and Sports Programming', 1, 13, 'ELECTIVE', '12', NULL, 'Human Movement 1; Human Movement 2', 0, 1),
(62, 'Safety and First Aid', 1, 13, 'ELECTIVE', '12', NULL, 'none', 0, 1),
(63, 'Research Methods (80 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'none', 0, 1),
(64, 'Design and Innovation (160 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'Research Methods', 0, 1),
(65, 'Creative Production and Presentation (160 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'Any Arts Apprenticeship (if applicable)', 0, 1),
(66, 'Field Exposure (In-Campus) (160 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'cluster prerequisites', 0, 1),
(67, 'Field Exposure (Off-Campus) (320-640 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'cluster prerequisites', 0, 1),
(68, 'Arts Apprenticeship – Dance (160 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'arts prerequisites', 0, 1),
(69, 'Arts Apprenticeship – Music (160 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'arts prerequisites', 0, 1),
(70, 'Arts Apprenticeship – Theater Arts (160 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'arts prerequisites', 0, 1),
(71, 'Arts Apprenticeship – Literary Arts (160 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'arts prerequisites', 0, 1),
(72, 'Arts Apprenticeship – Visual/Media/Applied/Traditional Art (160 hrs)', 1, 14, 'ELECTIVE', '12', NULL, 'arts prerequisites', 0, 1),
(73, 'Work Immersion (320-640 hrs)', 2, 14, 'ELECTIVE', '12', NULL, 'Required for TechPro track', 1, 1),
(74, 'Aesthetic Services (Beauty Care)', 2, 20, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(75, 'Barbering Services', 2, 20, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(76, 'Caregiving (Adult Care)', 2, 20, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(77, 'Caregiving (Child Care)', 2, 20, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(78, 'Hairdressing Services', 2, 20, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(79, 'Wellness Services (Hilot/Massage)', 2, 20, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(80, 'Agricultural Crops Production', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(81, 'Agro-entrepreneurship', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(82, 'Aquaculture', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(83, 'Fish Capture Operation', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(84, 'Food Processing', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(85, 'Organic Agriculture Production', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(86, 'Poultry Production (Chicken)', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(87, 'Ruminants Production', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(88, 'Swine Production', 2, 21, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(89, 'Garments Artisanry', 2, 22, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(90, 'Handicrafts: Weaving', 2, 22, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(91, 'Driving and Automotive Servicing', 2, 23, 'ELECTIVE', '11/12', 'Driving NC II + Automotive Ser', 'none', 0, 1),
(92, 'Automotive Servicing (Electrical Repair)', 2, 23, 'ELECTIVE', '11/12', 'NC II', 'Driving and Automotive Servicing', 0, 1),
(93, 'Automotive Servicing (Engine and Chassis Repairs)', 2, 23, 'ELECTIVE', '11/12', 'NC II', 'Driving and Automotive Servicing', 0, 1),
(94, 'Motorcycle and Small Engine Servicing', 2, 23, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(95, 'Carpentry', 2, 24, 'ELECTIVE', '11/12', 'NC I/NC II', 'none', 0, 1),
(96, 'Construction Operation', 2, 24, 'ELECTIVE', '11/12', 'NC I/NC II', 'none', 0, 1),
(97, 'Manual Metal Arc Welding', 2, 24, 'ELECTIVE', '11/12', 'NC I/NC II', 'none', 0, 1),
(98, 'Technical Drafting', 2, 24, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(99, 'Animation', 2, 25, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(100, 'Illustration', 2, 25, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(101, 'Visual Graphic Design', 2, 25, 'ELECTIVE', '11/12', 'NC III', 'none', 0, 1),
(102, 'Bakery Operation', 2, 26, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(103, 'Events Management Services', 2, 26, 'ELECTIVE', '11/12', 'NC III', 'none', 0, 1),
(104, 'Food and Beverage Operation', 2, 26, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(105, 'Hotel Operation (Front Office Services)', 2, 26, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(106, 'Hotel Operation (Housekeeping Services)', 2, 26, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(107, 'Kitchen Operation', 2, 26, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(108, 'Tourism Services', 2, 26, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(109, 'Domestic Refrigeration and Air-Conditioning Servicing', 2, 27, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(110, 'Commercial Air-Conditioning Installation and Servicing', 2, 27, 'ELECTIVE', '11/12', 'NC III', 'Domestic Refrigeration and Air-Conditioning Servicing', 0, 1),
(111, 'Electrical Installation Maintenance', 2, 27, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(112, 'Electronics Product Assembly and Servicing', 2, 27, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(113, 'Mechatronics', 2, 27, 'ELECTIVE', '11/12', 'NC II', 'Electronics Product Assembly and Servicing', 0, 1),
(114, 'Photovoltaic Systems Installation', 2, 27, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(115, 'Broadband Installation', 2, 28, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(116, 'Computer Programming (Java)', 2, 28, 'ELECTIVE', '11/12', 'NC III', 'none', 0, 1),
(117, 'Computer Programming (.Net Technology)', 2, 28, 'ELECTIVE', '11/12', 'NC III', 'none', 0, 1),
(118, 'Computer Programming (Oracle Database)', 2, 28, 'ELECTIVE', '11/12', 'NC III', 'none', 0, 1),
(119, 'Computer Systems Servicing', 2, 28, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(120, 'Contact Center Services', 2, 28, 'ELECTIVE', '11/12', 'NC II', 'none', 0, 1),
(121, 'Marine Engineering at the Support Level', 2, 29, 'ELECTIVE', '11/12', 'Non-NC', 'none', 0, 1),
(122, 'Marine Transportation at the Support Level', 2, 29, 'ELECTIVE', '11/12', 'Non-NC', 'none', 0, 1),
(123, 'Ships Catering Services', 2, 29, 'ELECTIVE', '11/12', 'NC I/NC II', 'none', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject_offerings`
--

CREATE TABLE `subject_offerings` (
  `offering_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `grade_level` int(11) NOT NULL,
  `semester` tinyint(4) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `slots` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `extension_name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_advisory`
--

CREATE TABLE `teacher_advisory` (
  `teacher_advisory_id` int(11) NOT NULL,
  `teacher_id` int(10) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` int(2) NOT NULL,
  `section_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tracks`
--

CREATE TABLE `tracks` (
  `track_id` int(11) NOT NULL,
  `track_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tracks`
--

INSERT INTO `tracks` (`track_id`, `track_name`) VALUES
(1, 'Academic'),
(2, 'TechPro');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `first_login` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role_id`, `status`, `first_login`) VALUES
(19, 'admin', '$2y$10$MdaNNF77fXzwj8JVpli8U.ime3KC7mrjRWYI7VGYXPi3/bZBwBd6u', 2, 'Active', 0),
(40, '405220150089', '$2y$10$W4/qM4GxopGyaUDx5gz10.eT4TbQsZ4JcuaJY0MQH4USSo/AAT4um', 1, 'Active', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activation_settings`
--
ALTER TABLE `activation_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archived_student_strand`
--
ALTER TABLE `archived_student_strand`
  ADD PRIMARY KEY (`archive_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `strand_id` (`strand_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `clusters`
--
ALTER TABLE `clusters`
  ADD PRIMARY KEY (`cluster_id`),
  ADD UNIQUE KEY `uq_track_cluster` (`track_id`,`cluster_name`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `fk_reset_user` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `section_ibfk_1` (`strand_id`),
  ADD KEY `fk_section_track` (`track_id`),
  ADD KEY `idx_section_grade_track` (`grade_level`,`track_id`);

--
-- Indexes for table `strands`
--
ALTER TABLE `strands`
  ADD PRIMARY KEY (`strand_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `application_id` (`application_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `student_applications`
--
ALTER TABLE `student_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_contact_number` (`contact_number`);

--
-- Indexes for table `student_electives`
--
ALTER TABLE `student_electives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_student_elective` (`student_id`,`offering_id`),
  ADD KEY `offering_id` (`offering_id`);

--
-- Indexes for table `student_strand`
--
ALTER TABLE `student_strand`
  ADD PRIMARY KEY (`student_strand_id`),
  ADD KEY `student_strand_ibfk_1` (`student_id`),
  ADD KEY `student_strand_ibfk_2` (`strand_id`),
  ADD KEY `student_strand_ibfk_3` (`section_id`);

--
-- Indexes for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `uq_student_subject` (`student_id`,`subject_id`,`school_year`),
  ADD KEY `fk_subject` (`subject_id`),
  ADD KEY `fk_student_subjects_offering` (`offering_id`);

--
-- Indexes for table `student_track`
--
ALTER TABLE `student_track`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `track_id` (`track_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `idx_subject_track_cluster` (`track_id`,`cluster_id`),
  ADD KEY `fk_subject_cluster` (`cluster_id`);

--
-- Indexes for table `subject_new`
--
ALTER TABLE `subject_new`
  ADD PRIMARY KEY (`subject_new_id`),
  ADD UNIQUE KEY `uq_subject_new` (`subject_name`,`track_id`,`cluster_id`),
  ADD KEY `idx_track_cluster_grade` (`track_id`,`cluster_id`,`recommended_grade_level`),
  ADD KEY `fk_subject_new_cluster` (`cluster_id`);

--
-- Indexes for table `subject_offerings`
--
ALTER TABLE `subject_offerings`
  ADD PRIMARY KEY (`offering_id`),
  ADD UNIQUE KEY `uq_offering` (`subject_id`,`school_year`,`grade_level`,`semester`,`section_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `teacher_advisory`
--
ALTER TABLE `teacher_advisory`
  ADD PRIMARY KEY (`teacher_advisory_id`),
  ADD KEY `teacher_advisory_ibfk_1` (`teacher_id`),
  ADD KEY `teacher_advisory_ibfk_2` (`strand_id`),
  ADD KEY `teacher_advisory_ibfk_3` (`section_id`);

--
-- Indexes for table `tracks`
--
ALTER TABLE `tracks`
  ADD PRIMARY KEY (`track_id`),
  ADD UNIQUE KEY `track_name` (`track_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation_settings`
--
ALTER TABLE `activation_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `archived_student_strand`
--
ALTER TABLE `archived_student_strand`
  MODIFY `archive_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `clusters`
--
ALTER TABLE `clusters`
  MODIFY `cluster_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_applications`
--
ALTER TABLE `student_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_electives`
--
ALTER TABLE `student_electives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_strand`
--
ALTER TABLE `student_strand`
  MODIFY `student_strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=323;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `subject_new`
--
ALTER TABLE `subject_new`
  MODIFY `subject_new_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `subject_offerings`
--
ALTER TABLE `subject_offerings`
  MODIFY `offering_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `teacher_advisory`
--
ALTER TABLE `teacher_advisory`
  MODIFY `teacher_advisory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tracks`
--
ALTER TABLE `tracks`
  MODIFY `track_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `archived_student_strand`
--
ALTER TABLE `archived_student_strand`
  ADD CONSTRAINT `archived_student_strand_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `archived_student_strand_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `archived_student_strand_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `clusters`
--
ALTER TABLE `clusters`
  ADD CONSTRAINT `fk_clusters_track` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`track_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `fk_section_track` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`track_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_electives`
--
ALTER TABLE `student_electives`
  ADD CONSTRAINT `student_electives_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_electives_ibfk_2` FOREIGN KEY (`offering_id`) REFERENCES `subject_offerings` (`offering_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_strand`
--
ALTER TABLE `student_strand`
  ADD CONSTRAINT `student_strand_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `student_strand_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`),
  ADD CONSTRAINT `student_strand_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`);

--
-- Constraints for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD CONSTRAINT `fk_student_subjects_offering` FOREIGN KEY (`offering_id`) REFERENCES `subject_offerings` (`offering_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subject` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`),
  ADD CONSTRAINT `student_subjects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `student_track`
--
ALTER TABLE `student_track`
  ADD CONSTRAINT `student_track_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_track_ibfk_2` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`track_id`) ON UPDATE CASCADE;

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `fk_subject_cluster` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`cluster_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subject_track` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`track_id`);

--
-- Constraints for table `subject_new`
--
ALTER TABLE `subject_new`
  ADD CONSTRAINT `fk_subject_new_cluster` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`cluster_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subject_new_track` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`track_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `subject_offerings`
--
ALTER TABLE `subject_offerings`
  ADD CONSTRAINT `subject_offerings_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_offerings_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `fk_teachers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_advisory`
--
ALTER TABLE `teacher_advisory`
  ADD CONSTRAINT `teacher_advisory_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  ADD CONSTRAINT `teacher_advisory_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`),
  ADD CONSTRAINT `teacher_advisory_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
