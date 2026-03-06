-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 08:12 PM
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
  `activation_status` tinyint(1) DEFAULT 0
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
  `grade_level` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `adviser_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `section_name`, `grade_level`, `strand_id`, `adviser_id`) VALUES
(1, 'A', 11, 1, NULL),
(2, 'B', 11, 1, NULL),
(3, 'C', 11, 1, NULL),
(4, 'D', 11, 1, NULL),
(5, 'A', 12, 1, NULL),
(6, 'B', 12, 1, NULL),
(7, 'C', 12, 1, NULL),
(8, 'D', 12, 1, NULL),
(9, 'A', 11, 2, NULL),
(10, 'B', 11, 2, NULL),
(11, 'C', 11, 2, NULL),
(12, 'D', 11, 2, NULL),
(13, 'A', 12, 2, NULL),
(14, 'B', 12, 2, NULL),
(15, 'C', 12, 2, NULL),
(16, 'D', 12, 2, NULL),
(17, 'A', 11, 3, NULL),
(18, 'B', 11, 3, NULL),
(19, 'C', 11, 3, NULL),
(20, 'D', 11, 3, NULL),
(21, 'A', 12, 3, NULL),
(22, 'B', 12, 3, NULL),
(23, 'C', 12, 3, NULL),
(24, 'D', 12, 3, NULL),
(25, 'A', 11, 4, NULL),
(26, 'B', 11, 4, NULL),
(27, 'C', 11, 4, NULL),
(28, 'D', 11, 4, NULL),
(29, 'A', 12, 4, NULL),
(30, 'B', 12, 4, NULL),
(31, 'C', 12, 4, NULL),
(32, 'D', 12, 4, NULL),
(33, 'A', 11, 5, NULL),
(34, 'B', 11, 5, NULL),
(35, 'C', 11, 5, NULL),
(36, 'D', 11, 5, NULL),
(37, 'A', 12, 5, NULL),
(38, 'B', 12, 5, NULL),
(39, 'C', 12, 5, NULL),
(40, 'D', 12, 5, NULL),
(41, 'A', 11, 6, NULL),
(42, 'B', 11, 6, NULL),
(43, 'C', 11, 6, NULL),
(44, 'D', 11, 6, NULL),
(45, 'A', 12, 6, NULL),
(46, 'B', 12, 6, NULL),
(47, 'C', 12, 6, NULL),
(48, 'D', 12, 6, NULL),
(49, 'A', 11, 7, NULL),
(50, 'B', 11, 7, NULL),
(51, 'C', 11, 7, NULL),
(52, 'D', 11, 7, NULL),
(53, 'A', 12, 7, NULL),
(54, 'B', 12, 7, NULL),
(55, 'C', 12, 7, NULL),
(56, 'D', 12, 7, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `strand_id` int(11) NOT NULL,
  `strand_abbreviation` varchar(30) NOT NULL,
  `strand_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`strand_id`, `strand_abbreviation`, `strand_name`) VALUES
(1, 'STEM', 'Science, Technology, Engineering and Mathematics'),
(2, 'ABM', 'Accountancy, Business and Management'),
(3, 'HUMSS', 'Humanities and Social Sciences'),
(4, 'GAS', 'General Academic Strand'),
(5, 'TVL-ICT', 'Technical-Vocational-Livelihood - Information and Communications Technology'),
(6, 'TVL-EIM', 'Technical-Vocational-Livelihood - Electrical Installation and Maintenance'),
(7, 'TVL-HE', 'Technical-Vocational-Livelihood - Home Economics');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `enrollment_status` enum('Active','Inactive','Graduated','Transferred') DEFAULT 'Active',
  `date_enrolled` date DEFAULT curdate(),
  `enlistment_status` enum('Not Enlisted','Pending','Enlisted','Rejected','Promoted','Finished') DEFAULT 'Not Enlisted',
  `school_year` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `application_id`, `enrollment_status`, `date_enrolled`, `enlistment_status`, `school_year`) VALUES
(6, 97, 2, 'Active', '2026-03-05', 'Enlisted', '2025-2026'),
(8, 99, 3, 'Active', '2026-03-06', 'Enlisted', '2025-2026'),
(9, 100, 5, 'Active', '2026-03-06', 'Enlisted', '2025-2026');

-- --------------------------------------------------------

--
-- Table structure for table `student_addresses`
--

CREATE TABLE `student_addresses` (
  `address_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `house_number` varchar(50) DEFAULT NULL,
  `street` varchar(150) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city_municipality` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Philippines',
  `zip_code` varchar(10) DEFAULT NULL,
  `same_as_current` enum('Yes','No') DEFAULT 'Yes',
  `permanent_house_number` varchar(50) DEFAULT NULL,
  `permanent_street` varchar(150) DEFAULT NULL,
  `permanent_barangay` varchar(100) DEFAULT NULL,
  `permanent_city` varchar(100) DEFAULT NULL,
  `permanent_province` varchar(100) DEFAULT NULL,
  `permanent_country` varchar(100) DEFAULT NULL,
  `permanent_zip_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_addresses`
--

INSERT INTO `student_addresses` (`address_id`, `application_id`, `house_number`, `street`, `barangay`, `city_municipality`, `province`, `country`, `zip_code`, `same_as_current`, `permanent_house_number`, `permanent_street`, `permanent_barangay`, `permanent_city`, `permanent_province`, `permanent_country`, `permanent_zip_code`) VALUES
(2, 2, 'Blk 4 Lot 3', 'Buena Oro', 'Barangay 15', 'Cagayan de Oro City', 'Misamis Oriental', 'Philippines', '9000', 'Yes', 'Blk 4 Lot 3', 'Buena Oro', 'Barangay 15', 'Cagayan de Oro City', 'Misamis Oriental', 'Philippines', '9000'),
(3, 3, 'Blk 4 Lot 3', 'Buena Oro', 'Macasandig', '104305000', '104300000', 'Philippines', '9000', 'Yes', 'Blk 4 Lot 3', 'Buena Oro', 'Macasandig', '104305000', '104300000', 'Philippines', '9000'),
(5, 5, 'Blk 4 Lot 3', 'Buena Oro', 'Macasandig', 'City of Cagayan De Oro', 'Misamis Oriental', 'Philippines', '9000', 'Yes', 'Blk 4 Lot 3', 'Buena Oro', 'Macasandig', 'City of Cagayan De Oro', 'Misamis Oriental', 'Philippines', '9000');

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

INSERT INTO `student_applications` (`application_id`, `lrn`, `last_name`, `first_name`, `middle_name`, `extension_name`, `date_of_birth`, `sex`, `place_of_birth`, `religion`, `mother_tongue`, `enrollment_type`, `application_status`, `email`, `contact_number`, `remarks`, `date_submitted`, `facebook_profile`, `profile_image`) VALUES
(2, '405220150089', 'Clarito', 'Nick Charles', 'Durangparang', '', '2005-08-20', 'Male', 'Cagayan De Oro', 'Catholic', 'Bisaya', 'Balik-Aral', 'Approved', 'nickcharlesclarito@gmail.com', '09944718764', 'dasgasga', '2026-03-05 20:59:38', 'https://www.hostitsmart.com/manage/knowledgebase/388/How-to-Change-Table-Name-in-phpMyAdmin.html', NULL),
(3, '123892477492', 'Clarito', 'Andry', 'Durangparang', '', '2010-08-20', 'Male', 'Cagayan De Oro', 'Catholic', 'Bisaya', 'New', 'Approved', 'nidu.clarito.coc@phinmaed.com', '09315510501', 'kdaslgajpgas', '2026-03-06 05:01:42', 'https://www.hostitsmart.com/manage/knowledgebase/388/How-to-Change-Table-Name-in-phpMyAdmin.html', NULL),
(5, '182785932075', 'Japlag', 'Jason Jay', 'Dumang', '', '2006-10-17', 'Male', 'Cagayan De Oro', 'Catholic', 'Bisaya', 'New', 'Approved', 'nickhoyo2005@gmail.com', '09782357252', 'dasfvxdshsherrg', '2026-03-06 19:04:49', 'https://www.hostitsmart.com/manage/knowledgebase/388/How-to-Change-Table-Name-in-phpMyAdmin.html', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

CREATE TABLE `student_documents` (
  `document_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `psa_birth_certificate` varchar(255) DEFAULT NULL,
  `psa_birth_certificate_no` varchar(30) DEFAULT NULL,
  `form_138` varchar(255) DEFAULT NULL,
  `student_id_copy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_documents`
--

INSERT INTO `student_documents` (`document_id`, `application_id`, `psa_birth_certificate`, `psa_birth_certificate_no`, `form_138`, `student_id_copy`) VALUES
(2, 2, NULL, NULL, NULL, NULL),
(3, 3, NULL, NULL, NULL, NULL),
(5, 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_family`
--

CREATE TABLE `student_family` (
  `family_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
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
  `guardian_contact` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_family`
--

INSERT INTO `student_family` (`family_id`, `application_id`, `father_last_name`, `father_first_name`, `father_middle_name`, `father_contact`, `mother_last_name`, `mother_first_name`, `mother_middle_name`, `mother_contact`, `guardian_last_name`, `guardian_first_name`, `guardian_middle_name`, `guardian_contact`) VALUES
(2, 2, 'Clarito', 'Randy', 'Durangparang', '09826473264', 'Clarito', 'Maria Cristina', 'Durangparang', '09262360968', '', '', '', ''),
(3, 3, 'Clarito', 'Randy', 'Durangparang', '09826473264', 'Clarito', 'Maria Cristina', 'Durangparang', '09262360968', '', '', '', ''),
(5, 5, 'Clarito', 'Randy', 'Durangparang', '09826473264', 'Clarito', 'Maria Cristina', 'Durangparang', '09262360968', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `student_learning_modality`
--

CREATE TABLE `student_learning_modality` (
  `modality_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `blended` tinyint(1) DEFAULT 0,
  `modular_print` tinyint(1) DEFAULT 0,
  `modular_digital` tinyint(1) DEFAULT 0,
  `online` tinyint(1) DEFAULT 0,
  `homeschooling` tinyint(1) DEFAULT 0,
  `educational_tv` tinyint(1) DEFAULT 0,
  `radio_based_tv` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_learning_modality`
--

INSERT INTO `student_learning_modality` (`modality_id`, `application_id`, `blended`, `modular_print`, `modular_digital`, `online`, `homeschooling`, `educational_tv`, `radio_based_tv`) VALUES
(2, 2, 0, 0, 0, 0, 0, 0, 0),
(3, 3, 0, 0, 0, 0, 0, 0, 0),
(5, 5, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_learning_program`
--

CREATE TABLE `student_learning_program` (
  `program_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `attended_learning_program` enum('Yes','No') DEFAULT 'No',
  `learning_program_specify` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_learning_program`
--

INSERT INTO `student_learning_program` (`program_id`, `application_id`, `attended_learning_program`, `learning_program_specify`) VALUES
(2, 2, 'No', ''),
(3, 3, 'No', ''),
(5, 5, 'Yes', 'Home Schooling');

-- --------------------------------------------------------

--
-- Table structure for table `student_previous_school`
--

CREATE TABLE `student_previous_school` (
  `prev_school_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `last_grade_completed` varchar(50) DEFAULT NULL,
  `last_school_year_completed` varchar(9) DEFAULT NULL,
  `last_school_attended` varchar(255) DEFAULT NULL,
  `school_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_previous_school`
--

INSERT INTO `student_previous_school` (`prev_school_id`, `application_id`, `last_grade_completed`, `last_school_year_completed`, `last_school_attended`, `school_id`) VALUES
(2, 2, 'Grade 10', '2024-2025', 'Cagayan De Oro National High School', ''),
(3, 3, 'Grade 10', '2024-2025', 'Cagayan De Oro National High School', '304111'),
(5, 5, 'Grade 10', '2024-2025', 'Cagayan De Oro National High School', '304111');

-- --------------------------------------------------------

--
-- Table structure for table `student_social_info`
--

CREATE TABLE `student_social_info` (
  `social_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `indigenous_community` enum('Yes','No') DEFAULT 'No',
  `ip_specify` varchar(150) DEFAULT NULL,
  `four_ps_beneficiary` enum('Yes','No') DEFAULT 'No',
  `four_ps_household_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_social_info`
--

INSERT INTO `student_social_info` (`social_id`, `application_id`, `indigenous_community`, `ip_specify`, `four_ps_beneficiary`, `four_ps_household_id`) VALUES
(2, 2, 'No', '', 'No', ''),
(3, 3, 'No', '', 'No', ''),
(5, 5, 'No', '', 'No', '');

-- --------------------------------------------------------

--
-- Table structure for table `student_special_needs`
--

CREATE TABLE `student_special_needs` (
  `sne_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `with_disability` enum('Yes','No') DEFAULT 'No',
  `has_pwd_id` enum('Yes','No') DEFAULT 'No',
  `pwd_id_number` varchar(50) DEFAULT NULL,
  `special_education_needed` enum('Yes','No') DEFAULT 'No',
  `non_graded_sne` enum('Yes','No') DEFAULT 'No',
  `disability_category` varchar(100) DEFAULT NULL,
  `disability_description` text DEFAULT NULL,
  `sped_services_needed` text DEFAULT NULL,
  `medical_diagnosis` varchar(255) DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `assessed_by` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_special_needs`
--

INSERT INTO `student_special_needs` (`sne_id`, `application_id`, `with_disability`, `has_pwd_id`, `pwd_id_number`, `special_education_needed`, `non_graded_sne`, `disability_category`, `disability_description`, `sped_services_needed`, `medical_diagnosis`, `assessment_date`, `assessed_by`) VALUES
(2, 2, 'No', 'No', '', 'No', 'No', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 3, 'No', 'No', '', 'No', 'No', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 5, 'No', 'No', '', 'No', 'No', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_strand`
--

CREATE TABLE `student_strand` (
  `student_strand_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` int(11) NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `section_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_strand`
--

INSERT INTO `student_strand` (`student_strand_id`, `student_id`, `strand_id`, `grade_level`, `semester`, `school_year`, `section_id`) VALUES
(1, 6, 5, 11, '2nd Semester', '2025-2026', 33),
(2, 8, 5, 11, '2nd Semester', '2025-2026', 33),
(3, 9, 5, 11, '2nd Semester', '2025-2026', 33);

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

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `user_id`, `first_name`, `last_name`, `middle_name`, `extension_name`) VALUES
(1, 41, 'Maria', 'Santos', 'Cruz', NULL),
(2, 42, 'John', 'Lim', 'Bautista', NULL),
(3, 43, 'Elizabeth', 'Garcia', 'Mendoza', NULL),
(4, 44, 'Michael', 'Rodriguez', 'Torres', NULL),
(5, 45, 'Catherine', 'Bautista', 'Reyes', NULL),
(6, 46, 'David', 'Cruz', 'Aquino', NULL),
(7, 47, 'Patricia', 'Mendoza', 'Del Rosario', NULL),
(8, 48, 'James', 'Reyes', 'San Jose', NULL),
(9, 49, 'Jennifer', 'Torres', 'Lopez', NULL),
(10, 50, 'Robert', 'Flores', 'Villanueva', NULL),
(11, 51, 'Michelle', 'Aquino', 'Diaz', NULL),
(12, 52, 'William', 'Dela Cruz', 'Ramos', NULL),
(13, 53, 'Sarah', 'Ramos', 'Santos', NULL),
(14, 54, 'Daniel', 'Villanueva', 'Castro', NULL),
(15, 55, 'Mary', 'Castro', 'Morales', NULL),
(16, 56, 'Christopher', 'Morales', 'Gonzales', NULL),
(17, 57, 'Jessica', 'Gonzales', 'Perez', NULL),
(18, 58, 'Anthony', 'Perez', 'Sanchez', NULL),
(19, 59, 'Amanda', 'Sanchez', 'Rivera', NULL),
(20, 60, 'Kevin', 'Rivera', 'Bermudez', NULL),
(21, 61, 'Stephanie', 'Bermudez', 'Jimenez', NULL),
(22, 62, 'Joseph', 'Jimenez', 'Villar', NULL),
(23, 63, 'Nicole', 'Villar', 'Mabini', NULL),
(24, 64, 'Mark', 'Mabini', 'Bonifacio', NULL),
(25, 65, 'Laura', 'Bonifacio', 'Rizal', NULL),
(26, 66, 'Paul', 'Rizal', 'Aguinaldo', NULL),
(27, 67, 'Rachel', 'Aguinaldo', 'Marcos', NULL),
(28, 68, 'Steven', 'Marcos', 'Enrile', NULL),
(29, 69, 'Melissa', 'Enrile', 'Cory', NULL),
(30, 70, 'Andrew', 'Cory', 'Noynoy', NULL),
(31, 71, 'Kimberly', 'Noynoy', 'PNoy', NULL),
(32, 72, 'Jonathan', 'PNoy', 'Duplex', NULL),
(33, 73, 'Christina', 'Duplex', 'Earth', NULL),
(34, 74, 'Brian', 'Earth', 'Mars', NULL),
(35, 75, 'Samantha', 'Mars', 'Jupiter', NULL),
(36, 76, 'Justin', 'Jupiter', 'Saturn', NULL),
(37, 77, 'Ashley', 'Saturn', 'Neptune', NULL),
(38, 78, 'Tyler', 'Neptune', 'Pluto', NULL),
(39, 79, 'Brittany', 'Pluto', 'Venus', NULL),
(40, 80, 'Brandon', 'Venus', 'Mercury', NULL),
(41, 81, 'Victoria', 'Mercury', 'Uranus', NULL),
(42, 82, 'Gregory', 'Uranus', 'Sun', NULL),
(43, 83, 'Natalie', 'Sun', 'Moon', NULL),
(44, 84, 'Eric', 'Moon', 'Star', NULL),
(45, 85, 'Gabrielle', 'Star', 'Galaxy', NULL),
(46, 86, 'Jason', 'Galaxy', 'Nebula', NULL),
(47, 87, 'Alexis', 'Nebula', 'Comet', NULL),
(48, 88, 'Nathan', 'Comet', 'Asteroid', NULL),
(49, 89, 'Madison', 'Asteroid', 'Meteor', NULL),
(50, 90, 'Jordan', 'Meteor', 'Cosmos', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_advisory`
--

CREATE TABLE `teacher_advisory` (
  `advisory_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `grade_level` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `assigned_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(67, 'teacher027', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 0),
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
(91, '146273816439', '$2y$10$JfdtFxxr8ov1gFjTpKWUWOYpIiTCo2KIC8qTrobIbYEPxrL1t6nCG', 1, 'Active', 0),
(97, '405220150089', '$2y$10$k/VQwXg7RWJVmCl48AzxTuityJNyxLNYXkuRevdPeklf6V.JmX/Fe', 1, 'Active', 0),
(99, '123892477492', '$2y$10$omOeBaVL1PKGuycXxrmZwugqpb6L1ePvCjHEuRR4hCFkzk7W/U11q', 1, 'Active', 0),
(100, '182785932075', '$2y$10$a1tlQWF.EXwNDnAWkigLteT9cRsmGhwsK59G033xnbOiWvZioLTBm', 1, 'Active', 0);

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
  ADD KEY `user_id` (`user_id`);

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
  ADD KEY `strand_id` (`strand_id`);

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
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `application_id` (`application_id`);

--
-- Indexes for table `student_addresses`
--
ALTER TABLE `student_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `student_applications`
--
ALTER TABLE `student_applications`
  ADD PRIMARY KEY (`application_id`);

--
-- Indexes for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `student_family`
--
ALTER TABLE `student_family`
  ADD PRIMARY KEY (`family_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `student_learning_modality`
--
ALTER TABLE `student_learning_modality`
  ADD PRIMARY KEY (`modality_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `student_learning_program`
--
ALTER TABLE `student_learning_program`
  ADD PRIMARY KEY (`program_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `student_previous_school`
--
ALTER TABLE `student_previous_school`
  ADD PRIMARY KEY (`prev_school_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `student_social_info`
--
ALTER TABLE `student_social_info`
  ADD PRIMARY KEY (`social_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `student_special_needs`
--
ALTER TABLE `student_special_needs`
  ADD PRIMARY KEY (`sne_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `student_strand`
--
ALTER TABLE `student_strand`
  ADD PRIMARY KEY (`student_strand_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `strand_id` (`strand_id`),
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
  ADD PRIMARY KEY (`advisory_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `strand_id` (`strand_id`);

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
  MODIFY `archive_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student_addresses`
--
ALTER TABLE `student_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_applications`
--
ALTER TABLE `student_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_documents`
--
ALTER TABLE `student_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_family`
--
ALTER TABLE `student_family`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_learning_modality`
--
ALTER TABLE `student_learning_modality`
  MODIFY `modality_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_learning_program`
--
ALTER TABLE `student_learning_program`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_previous_school`
--
ALTER TABLE `student_previous_school`
  MODIFY `prev_school_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_social_info`
--
ALTER TABLE `student_social_info`
  MODIFY `social_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_special_needs`
--
ALTER TABLE `student_special_needs`
  MODIFY `sne_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_strand`
--
ALTER TABLE `student_strand`
  MODIFY `student_strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `teacher_advisory`
--
ALTER TABLE `teacher_advisory`
  MODIFY `advisory_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `archived_student_strand`
--
ALTER TABLE `archived_student_strand`
  ADD CONSTRAINT `archived_student_strand_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `archived_student_strand_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_addresses`
--
ALTER TABLE `student_addresses`
  ADD CONSTRAINT `student_addresses_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD CONSTRAINT `student_documents_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_family`
--
ALTER TABLE `student_family`
  ADD CONSTRAINT `student_family_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_learning_modality`
--
ALTER TABLE `student_learning_modality`
  ADD CONSTRAINT `student_learning_modality_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_learning_program`
--
ALTER TABLE `student_learning_program`
  ADD CONSTRAINT `student_learning_program_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_previous_school`
--
ALTER TABLE `student_previous_school`
  ADD CONSTRAINT `student_previous_school_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_social_info`
--
ALTER TABLE `student_social_info`
  ADD CONSTRAINT `student_social_info_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_special_needs`
--
ALTER TABLE `student_special_needs`
  ADD CONSTRAINT `student_special_needs_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `student_applications` (`application_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_advisory`
--
ALTER TABLE `teacher_advisory`
  ADD CONSTRAINT `teacher_advisory_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  ADD CONSTRAINT `teacher_advisory_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`),
  ADD CONSTRAINT `teacher_advisory_ibfk_3` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
