-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2026 at 09:56 AM
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
  `strand_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `section_name`, `grade_level`, `strand_id`) VALUES
(1, 'A', 11, 1),
(2, 'B', 11, 1),
(3, 'C', 11, 1),
(4, 'D', 11, 1),
(5, 'A', 12, 1),
(6, 'B', 12, 1),
(7, 'C', 12, 1),
(8, 'D', 12, 1),
(9, 'A', 11, 2),
(10, 'B', 11, 2),
(11, 'C', 11, 2),
(12, 'D', 11, 2),
(13, 'A', 12, 2),
(14, 'B', 12, 2),
(15, 'C', 12, 2),
(16, 'D', 12, 2),
(17, 'A', 11, 3),
(18, 'B', 11, 3),
(19, 'C', 11, 3),
(20, 'D', 11, 3),
(21, 'A', 12, 3),
(22, 'B', 12, 3),
(23, 'C', 12, 3),
(24, 'D', 12, 3),
(25, 'A', 11, 4),
(26, 'B', 11, 4),
(27, 'C', 11, 4),
(28, 'D', 11, 4),
(29, 'A', 12, 4),
(30, 'B', 12, 4),
(31, 'C', 12, 4),
(32, 'D', 12, 4),
(33, 'A', 11, 5),
(34, 'B', 11, 5),
(35, 'C', 11, 5),
(36, 'D', 11, 5),
(37, 'A', 12, 5),
(38, 'B', 12, 5),
(39, 'C', 12, 5),
(40, 'D', 12, 5),
(41, 'A', 11, 6),
(42, 'B', 11, 6),
(43, 'C', 11, 6),
(44, 'D', 11, 6),
(45, 'A', 12, 6),
(46, 'B', 12, 6),
(47, 'C', 12, 6),
(48, 'D', 12, 6),
(49, 'A', 11, 7),
(50, 'B', 11, 7),
(51, 'C', 11, 7),
(52, 'D', 11, 7),
(53, 'A', 12, 7),
(54, 'B', 12, 7),
(55, 'C', 12, 7),
(56, 'D', 12, 7);

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
(17, 40, 1, 'Active', '2026-03-04', 'Enlisted', '2025-2026'),
(18, 91, 2, 'Active', '2026-03-05', 'Enlisted', '2025-2026');

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
(1, '405220150089', 'Clarito', 'Nick Charles', 'Durangparang', '', '2005-08-20', 'Male', 'Cagayan De Oro', 'Christian', 'Bisaya', 'No', '', 'No', '', 'Blk 4 lot 3 Buena Oro', NULL, 'Macasandig', 'Cagayan De Oro City', 'Misamis Oriental', 'Philippines', '9000', 'Yes', 'Blk 4 lot 3 Buena Oro', NULL, 'Macasandig', 'Cagayan De Oro City', 'Misamis Oriental', 'Philippines', '9000', 'Clarito', 'Randy', 'Durangparang', '09826473264', 'Clarito', 'Maria Cristina', 'Durangparang', '09262360968', '', '', '', '', 'No', '', '', 'No', '', 'Grade 10', '2024-2025', 'Cagayan De Oro National High School', '', 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'New', 'Approved', 'nickcharlesclarito@gmail.com', '09944719534', 'hi kido', '2026-03-04 14:39:33', 'https://www.hostitsmart.com/manage/knowledgebase/388/How-to-Change-Table-Name-in-phpMyAdmin.html', NULL),
(2, '146273816439', 'hdjkahsdfksahdksa', 'dnjhajskhdjsakhdlsa', 'dasdsahkjsdhksadha', '', '2008-10-05', 'Male', 'dhakjslshfkljafhalikfhlsa', 'hlkdhaldhlashdlsakdhaslk', 'skjadhkljahdslsahdlsad', 'Yes', 'dasfdagfsafasfsaqfas', 'No', '', 'lot 5', 'dasndjsahndlksajndls', 'djalkjslkadjsaldasdkdksa', 'Cagayan De Oro City', 'dadadad', 'Philippines', '9000', 'Yes', 'lot 5', 'dasndjsahndlksajndls', 'djalkjslkadjsaldasdkdksa', 'Cagayan De Oro City', 'dadadad', 'Philippines', '9000', 'dasdsadsasdsa', 'fakjsdasijshdsliadl', 'dnakdhkjashjdlsa', '09687288758', 'dhkasjdhkjslahdlksad', 'djnlaskdjhsladjsa', 'bnksajdhkjlasdhlskajhdk', '09767865846', '', '', '', '', 'No', '', '', 'No', '', 'Grade 10', '2024-2025', 'dkhaslkjdhsaldhsahdl', '', 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'New', 'Approved', 'nidu.clarito.coc@phinmaed.com', '09638276328', '', '2026-03-05 08:36:22', '', NULL);

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

--
-- Dumping data for table `student_strand`
--

INSERT INTO `student_strand` (`student_strand_id`, `student_id`, `strand_id`, `grade_level`, `section_id`) VALUES
(39, 17, 5, 11, 33),
(40, 18, 1, 12, 5);

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
  `extension_name` varchar(20) DEFAULT NULL,
  `advisor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `user_id`, `first_name`, `last_name`, `middle_name`, `extension_name`, `advisor_id`) VALUES
(1, 41, 'Maria', 'Santos', 'Cruz', NULL, 1),
(2, 42, 'John', 'Lim', 'Bautista', NULL, 2),
(3, 43, 'Elizabeth', 'Garcia', 'Mendoza', NULL, 3),
(4, 44, 'Michael', 'Rodriguez', 'Torres', NULL, 4),
(5, 45, 'Catherine', 'Bautista', 'Reyes', NULL, 5),
(6, 46, 'David', 'Cruz', 'Aquino', NULL, 6),
(7, 47, 'Patricia', 'Mendoza', 'Del Rosario', NULL, 7),
(8, 48, 'James', 'Reyes', 'San Jose', NULL, 8),
(9, 49, 'Jennifer', 'Torres', 'Lopez', NULL, 9),
(10, 50, 'Robert', 'Flores', 'Villanueva', NULL, 10),
(11, 51, 'Michelle', 'Aquino', 'Diaz', NULL, 11),
(12, 52, 'William', 'Dela Cruz', 'Ramos', NULL, 12),
(13, 53, 'Sarah', 'Ramos', 'Santos', NULL, 13),
(14, 54, 'Daniel', 'Villanueva', 'Castro', NULL, 14),
(15, 55, 'Mary', 'Castro', 'Morales', NULL, 15),
(16, 56, 'Christopher', 'Morales', 'Gonzales', NULL, 16),
(17, 57, 'Jessica', 'Gonzales', 'Perez', NULL, 17),
(18, 58, 'Anthony', 'Perez', 'Sanchez', NULL, 18),
(19, 59, 'Amanda', 'Sanchez', 'Rivera', NULL, 19),
(20, 60, 'Kevin', 'Rivera', 'Bermudez', NULL, 20),
(21, 61, 'Stephanie', 'Bermudez', 'Jimenez', NULL, 21),
(22, 62, 'Joseph', 'Jimenez', 'Villar', NULL, 22),
(23, 63, 'Nicole', 'Villar', 'Mabini', NULL, 23),
(24, 64, 'Mark', 'Mabini', 'Bonifacio', NULL, 24),
(25, 65, 'Laura', 'Bonifacio', 'Rizal', NULL, 25),
(26, 66, 'Paul', 'Rizal', 'Aguinaldo', NULL, 26),
(27, 67, 'Rachel', 'Aguinaldo', 'Marcos', NULL, 27),
(28, 68, 'Steven', 'Marcos', 'Enrile', NULL, 28),
(29, 69, 'Melissa', 'Enrile', 'Cory', NULL, 29),
(30, 70, 'Andrew', 'Cory', 'Noynoy', NULL, 30),
(31, 71, 'Kimberly', 'Noynoy', 'PNoy', NULL, 31),
(32, 72, 'Jonathan', 'PNoy', 'Duplex', NULL, 32),
(33, 73, 'Christina', 'Duplex', 'Earth', NULL, 33),
(34, 74, 'Brian', 'Earth', 'Mars', NULL, 34),
(35, 75, 'Samantha', 'Mars', 'Jupiter', NULL, 35),
(36, 76, 'Justin', 'Jupiter', 'Saturn', NULL, 36),
(37, 77, 'Ashley', 'Saturn', 'Neptune', NULL, 37),
(38, 78, 'Tyler', 'Neptune', 'Pluto', NULL, 38),
(39, 79, 'Brittany', 'Pluto', 'Venus', NULL, 39),
(40, 80, 'Brandon', 'Venus', 'Mercury', NULL, 40),
(41, 81, 'Victoria', 'Mercury', 'Uranus', NULL, 41),
(42, 82, 'Gregory', 'Uranus', 'Sun', NULL, 42),
(43, 83, 'Natalie', 'Sun', 'Moon', NULL, 43),
(44, 84, 'Eric', 'Moon', 'Star', NULL, 44),
(45, 85, 'Gabrielle', 'Star', 'Galaxy', NULL, 45),
(46, 86, 'Jason', 'Galaxy', 'Nebula', NULL, 46),
(47, 87, 'Alexis', 'Nebula', 'Comet', NULL, 47),
(48, 88, 'Nathan', 'Comet', 'Asteroid', NULL, 48),
(49, 89, 'Madison', 'Asteroid', 'Meteor', NULL, 49),
(50, 90, 'Jordan', 'Meteor', 'Cosmos', NULL, 50);

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
(40, '405220150089', '$2y$10$W4/qM4GxopGyaUDx5gz10.eT4TbQsZ4JcuaJY0MQH4USSo/AAT4um', 1, 'Active', 0),
(41, 'teacher001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 0),
(42, 'teacher002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 0),
(43, 'teacher003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 0),
(44, 'teacher004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 0),
(45, 'teacher005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(46, 'teacher006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(47, 'teacher007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(48, 'teacher008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(49, 'teacher009', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(50, 'teacher010', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(51, 'teacher011', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(52, 'teacher012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(53, 'teacher013', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(54, 'teacher014', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(55, 'teacher015', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(56, 'teacher016', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(57, 'teacher017', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(58, 'teacher018', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(59, 'teacher019', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(60, 'teacher020', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(61, 'teacher021', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(62, 'teacher022', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 0),
(63, 'teacher023', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 0),
(64, 'teacher024', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(65, 'teacher025', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(66, 'teacher026', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(67, 'teacher027', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(68, 'teacher028', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(69, 'teacher029', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(70, 'teacher030', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(71, 'teacher031', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(72, 'teacher032', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(73, 'teacher033', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(74, 'teacher034', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(75, 'teacher035', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(76, 'teacher036', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(77, 'teacher037', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(78, 'teacher038', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(79, 'teacher039', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(80, 'teacher040', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(81, 'teacher041', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(82, 'teacher042', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(83, 'teacher043', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(84, 'teacher044', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(85, 'teacher045', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(86, 'teacher046', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(87, 'teacher047', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(88, 'teacher048', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(89, 'teacher049', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(90, 'teacher050', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 1),
(91, '146273816439', '$2y$10$JfdtFxxr8ov1gFjTpKWUWOYpIiTCo2KIC8qTrobIbYEPxrL1t6nCG', 1, 'Active', 0);

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
  ADD KEY `strand_id` (`strand_id`);

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
  ADD KEY `section_ibfk_1` (`strand_id`);

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
-- Indexes for table `student_strand`
--
ALTER TABLE `student_strand`
  ADD PRIMARY KEY (`student_strand_id`),
  ADD KEY `student_strand_ibfk_1` (`student_id`),
  ADD KEY `student_strand_ibfk_2` (`strand_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `advisor_id` (`advisor_id`);

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
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `student_applications`
--
ALTER TABLE `student_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_strand`
--
ALTER TABLE `student_strand`
  MODIFY `student_strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `archived_student_strand`
--
ALTER TABLE `archived_student_strand`
  ADD CONSTRAINT `archived_student_strand_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `archived_student_strand_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_strand`
--
ALTER TABLE `student_strand`
  ADD CONSTRAINT `student_strand_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `student_strand_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`),
  ADD CONSTRAINT `student_strand_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `fk_teachers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
