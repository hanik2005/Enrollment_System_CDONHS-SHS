-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2026 at 05:07 AM
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
-- Table structure for table `admin_audit_trail`
--

CREATE TABLE `admin_audit_trail` (
  `audit_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(120) NOT NULL,
  `entity_type` varchar(120) DEFAULT NULL,
  `entity_id` varchar(120) DEFAULT NULL,
  `description` text NOT NULL,
  `metadata` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_audit_trail`
--

INSERT INTO `admin_audit_trail` (`audit_id`, `user_id`, `action_type`, `entity_type`, `entity_id`, `description`, `metadata`, `ip_address`, `created_at`) VALUES
(1, 19, 'DOCUMENT_CORRECTION_REQUESTED', 'student_documents', '2', 'Requested correction for Form 138 (application #2)', '{\"document_field\":\"form_138\",\"document_label\":\"Form 138\",\"reason\":\"send another one\",\"student_name\":\"Nick Charles Clarito\",\"student_email\":\"nickcharlesclarito@gmail.com\",\"parent_sms\":{\"attempted\":2,\"sent\":0,\"failed\":2,\"skipped\":0,\"details\":[{\"status\":\"failed\",\"label\":\"Father\",\"number\":\"+639826473264\",\"error\":\"SMS gateway is not configured. Set SMS_ENABLED=true and SMS_API_KEY.\"},{\"status\":\"failed\",\"label\":\"Mother\",\"number\":\"+639262360968\",\"error\":\"SMS gateway is not configured. Set SMS_ENABLED=true and SMS_API_KEY.\"}]}}', '::1', '2026-03-08 05:29:14'),
(2, 19, 'DOCUMENT_DELETED', 'student_documents', '2', 'Deleted Form 138 for application #2', '{\"document_field\":\"form_138\",\"document_label\":\"Form 138\",\"deleted_filename\":\"1772942522_FORM_138_2.pdf\",\"student_name\":\"Nick Charles Clarito\"}', '::1', '2026-03-08 05:30:00'),
(3, 19, 'DOCUMENT_CORRECTION_REQUESTED', 'student_documents', '2', 'Requested correction for PSA Birth Certificate (application #2)', '{\"document_field\":\"psa_birth_certificate\",\"document_label\":\"PSA Birth Certificate\",\"reason\":\"dasdafgagag\",\"student_name\":\"Nick Charles Clarito\",\"student_email\":\"nickcharlesclarito@gmail.com\",\"parent_sms\":{\"attempted\":2,\"sent\":0,\"failed\":2,\"skipped\":0,\"details\":[{\"status\":\"failed\",\"label\":\"Father\",\"number\":\"+639944719534\",\"error\":\"SMS gateway is not configured. Set SMS_ENABLED=true and SMS_API_KEY.\"},{\"status\":\"failed\",\"label\":\"Mother\",\"number\":\"+639262360968\",\"error\":\"SMS gateway is not configured. Set SMS_ENABLED=true and SMS_API_KEY.\"}]}}', '::1', '2026-03-08 05:39:03'),
(4, 19, 'DOCUMENT_CORRECTION_REQUESTED', 'student_documents', '2', 'Requested correction for PSA Birth Certificate (application #2)', '{\"document_field\":\"psa_birth_certificate\",\"document_label\":\"PSA Birth Certificate\",\"reason\":\"this is wrong\",\"student_name\":\"Nick Charles Clarito\",\"student_email\":\"nickcharlesclarito@gmail.com\",\"parent_sms\":{\"attempted\":0,\"sent\":0,\"failed\":0,\"skipped\":1,\"details\":[{\"status\":\"skipped\",\"reason\":\"SMS gateway disabled. Configure Back_End_Files\\/PHP_Files\\/sms_config.php\"}]}}', '::1', '2026-03-08 05:46:40'),
(5, 19, 'DOCUMENT_CORRECTION_REQUESTED', 'student_documents', '2', 'Requested correction for PSA Birth Certificate (application #2)', '{\"document_field\":\"psa_birth_certificate\",\"document_label\":\"PSA Birth Certificate\",\"reason\":\"dasdggvxz\",\"student_name\":\"Nick Charles Clarito\",\"student_email\":\"nickcharlesclarito@gmail.com\"}', '::1', '2026-03-08 06:15:41'),
(6, 19, 'APPLICATION_APPROVED', 'student_applications', '6', 'Approved application #6', '{\"status\":\"Approved\",\"remarks\":\"Badgao ka???\",\"student_name\":\"Charlie Nathaniel Viador\",\"created_user_id\":101}', '::1', '2026-03-08 06:43:00'),
(7, 19, 'STUDENT_SEMESTER_PROMOTED', 'student_strand', '6', 'Approved semester promotion for student #6 to 2nd Semester', '{\"school_year\":\"2025-2026\",\"semester_from\":\"1st Semester\",\"semester_to\":\"2nd Semester\",\"approved_by\":19}', '::1', '2026-03-08 09:24:06'),
(8, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '2', 'Approved teacher student-progress recommendation #2', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to 2nd Semester.\"}', '::1', '2026-03-08 09:24:07'),
(9, 19, 'STUDENT_SEMESTER_PROMOTED', 'student_strand', '8', 'Approved semester promotion for student #8 to 2nd Semester', '{\"school_year\":\"2025-2026\",\"semester_from\":\"1st Semester\",\"semester_to\":\"2nd Semester\",\"approved_by\":19}', '::1', '2026-03-08 09:24:07'),
(10, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '1', 'Approved teacher student-progress recommendation #1', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to 2nd Semester.\"}', '::1', '2026-03-08 09:24:07'),
(11, 19, 'STUDENT_SEMESTER_PROMOTED', 'student_strand', '9', 'Approved semester promotion for student #9 to 2nd Semester', '{\"school_year\":\"2025-2026\",\"semester_from\":\"1st Semester\",\"semester_to\":\"2nd Semester\",\"approved_by\":19}', '::1', '2026-03-08 09:24:07'),
(12, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '3', 'Approved teacher student-progress recommendation #3', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to 2nd Semester.\"}', '::1', '2026-03-08 09:24:07'),
(13, 19, 'STUDENT_PROMOTED', 'students', '6', 'Approved teacher recommendation and promoted student #6 to Grade 12', '{\"teacher_recommended_status\":\"Promote to Grade 12\",\"school_year_from\":\"2025-2026\",\"school_year_to\":\"2026-2027\",\"approved_by\":19}', '::1', '2026-03-08 09:26:30'),
(14, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '11', 'Approved teacher student-progress recommendation #11', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to Grade 12.\"}', '::1', '2026-03-08 09:26:30'),
(15, 19, 'STUDENT_PROMOTED', 'students', '8', 'Approved teacher recommendation and promoted student #8 to Grade 12', '{\"teacher_recommended_status\":\"Promote to Grade 12\",\"school_year_from\":\"2025-2026\",\"school_year_to\":\"2026-2027\",\"approved_by\":19}', '::1', '2026-03-08 09:26:30'),
(16, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '10', 'Approved teacher student-progress recommendation #10', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to Grade 12.\"}', '::1', '2026-03-08 09:26:30'),
(17, 19, 'STUDENT_PROMOTED', 'students', '9', 'Approved teacher recommendation and promoted student #9 to Grade 12', '{\"teacher_recommended_status\":\"Promote to Grade 12\",\"school_year_from\":\"2025-2026\",\"school_year_to\":\"2026-2027\",\"approved_by\":19}', '::1', '2026-03-08 09:26:31'),
(18, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '12', 'Approved teacher student-progress recommendation #12', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to Grade 12.\"}', '::1', '2026-03-08 09:26:31'),
(19, 19, 'STUDENT_SEMESTER_PROMOTED', 'student_strand', '6', 'Approved semester promotion for student #6 to 2nd Semester', '{\"school_year\":\"2026-2027\",\"semester_from\":\"1st Semester\",\"semester_to\":\"2nd Semester\",\"approved_by\":19}', '::1', '2026-03-08 09:30:12'),
(20, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '14', 'Approved teacher student-progress recommendation #14', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to 2nd Semester.\"}', '::1', '2026-03-08 09:30:12'),
(21, 19, 'STUDENT_SEMESTER_PROMOTED', 'student_strand', '8', 'Approved semester promotion for student #8 to 2nd Semester', '{\"school_year\":\"2026-2027\",\"semester_from\":\"1st Semester\",\"semester_to\":\"2nd Semester\",\"approved_by\":19}', '::1', '2026-03-08 09:30:13'),
(22, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '13', 'Approved teacher student-progress recommendation #13', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to 2nd Semester.\"}', '::1', '2026-03-08 09:30:13'),
(23, 19, 'STUDENT_SEMESTER_PROMOTED', 'student_strand', '9', 'Approved semester promotion for student #9 to 2nd Semester', '{\"school_year\":\"2026-2027\",\"semester_from\":\"1st Semester\",\"semester_to\":\"2nd Semester\",\"approved_by\":19}', '::1', '2026-03-08 09:30:13'),
(24, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '15', 'Approved teacher student-progress recommendation #15', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Promoted to 2nd Semester.\"}', '::1', '2026-03-08 09:30:13'),
(25, 19, 'STUDENT_GRADUATED', 'students', '8', 'Approved teacher recommendation and marked student #8 as Graduated', '{\"teacher_recommended_status\":\"Graduate\",\"approved_by\":19}', '::1', '2026-03-08 09:31:45'),
(26, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '16', 'Approved teacher student-progress recommendation #16', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Marked as Graduated.\"}', '::1', '2026-03-08 09:31:45'),
(27, 19, 'STUDENT_GRADUATED', 'students', '6', 'Approved teacher recommendation and marked student #6 as Graduated', '{\"teacher_recommended_status\":\"Graduate\",\"approved_by\":19}', '::1', '2026-03-08 09:31:45'),
(28, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '17', 'Approved teacher student-progress recommendation #17', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Marked as Graduated.\"}', '::1', '2026-03-08 09:31:46'),
(29, 19, 'STUDENT_GRADUATED', 'students', '9', 'Approved teacher recommendation and marked student #9 as Graduated', '{\"teacher_recommended_status\":\"Graduate\",\"approved_by\":19}', '::1', '2026-03-08 09:31:46'),
(30, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '18', 'Approved teacher student-progress recommendation #18', '{\"decision\":\"Approved\",\"remark\":\"\",\"action_result\":\"Marked as Graduated.\"}', '::1', '2026-03-08 09:31:46'),
(31, 19, 'ENLISTMENT_APPROVED', 'students', '10', 'Approved enlistment for student #10', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":6,\"email\":\"clarito.nickcharles@gmail.com\",\"first_name\":\"Charlie Nathaniel\",\"last_name\":\"Viador\"}}', '::1', '2026-03-08 11:17:14'),
(32, 19, 'ENLISTMENT_APPROVED', 'students', '10', 'Approved enlistment for student #10', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":6,\"email\":\"clarito.nickcharles@gmail.com\",\"first_name\":\"Charlie Nathaniel\",\"last_name\":\"Viador\"}}', '::1', '2026-03-12 05:28:24'),
(33, 19, 'APPLICATION_REJECTED', 'student_applications', '7', 'Rejected and removed application #7', '{\"status\":\"Rejected\",\"remarks\":\"sdfsffsdjoj\",\"student_name\":\"John Clarito\"}', '::1', '2026-03-12 05:36:49'),
(34, 19, 'STUDENT_PROMOTED', 'students', '10', 'Approved teacher recommendation and promoted student #10 to Grade 12', '{\"teacher_recommended_status\":\"Promote to Grade 12\",\"school_year_from\":\"2025-2026\",\"school_year_to\":\"2026-2027\",\"approved_by\":19}', '::1', '2026-03-12 05:39:52'),
(35, 19, 'TEACHER_PROGRESS_APPROVED', 'student_promotion_status', '19', 'Approved teacher student-progress recommendation #19', '{\"decision\":\"Approved\",\"remark\":\"go\",\"action_result\":\"Promoted to Grade 12.\"}', '::1', '2026-03-12 05:39:52'),
(36, 19, 'APPLICATION_REJECTED', 'student_applications', '8', 'Rejected and removed application #8', '{\"status\":\"Rejected\",\"remarks\":\"good bye\",\"student_name\":\"John Clarito\"}', '::1', '2026-03-12 06:02:05'),
(37, 19, 'STUDENT_DATA_PURGED', 'students', NULL, 'Super Admin purged all student-related records.', '{\"deleted_counts\":{\"teacher_student_notes\":1,\"student_promotion_status\":13,\"archived_student_strand\":0,\"student_strand\":4,\"students\":4,\"student_applications\":4,\"student_users\":5},\"before_counts\":{\"student_users\":5,\"student_applications\":4,\"students\":4,\"student_strand\":4,\"archived_student_strand\":0,\"student_promotion_status\":13,\"teacher_student_notes\":1}}', '::1', '2026-03-12 12:43:37'),
(38, 19, 'STUDENT_DATA_PURGED', 'students', NULL, 'Super Admin purged all student-related records.', '{\"deleted_counts\":{\"teacher_student_notes\":0,\"student_promotion_status\":0,\"archived_student_strand\":0,\"student_strand\":0,\"students\":0,\"student_applications\":0,\"student_users\":0},\"before_counts\":{\"student_users\":0,\"student_applications\":0,\"students\":0,\"student_strand\":0,\"archived_student_strand\":0,\"student_promotion_status\":0,\"teacher_student_notes\":0}}', '::1', '2026-03-12 12:43:45'),
(39, 106, 'APPLICATION_REJECTED', 'student_applications', '12', 'Rejected and removed application #12', '{\"status\":\"Rejected\",\"remarks\":\"\",\"student_name\":\"TestTc28First TestTc28Last\"}', '::1', '2026-03-13 05:00:16'),
(40, 106, 'APPLICATION_APPROVED', 'student_applications', '13', 'Approved application #13', '{\"status\":\"Approved\",\"remarks\":\"Approved in automated test\",\"student_name\":\"TestTc29First TestTc29Last\",\"created_user_id\":110}', '::1', '2026-03-13 05:00:17'),
(41, 106, 'APPLICATION_REJECTED', 'student_applications', '14', 'Rejected and removed application #14', '{\"status\":\"Rejected\",\"remarks\":\"Incomplete requirements\",\"student_name\":\"TestTc30First TestTc30Last\"}', '::1', '2026-03-13 05:00:17'),
(42, 106, 'ENLISTMENT_APPROVED', 'students', '11', 'Approved enlistment for student #11', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":9,\"email\":\"tc26_20260313125957_doc_allowed@example.com\",\"first_name\":\"TestBaseFirst\",\"last_name\":\"TestBaseLast\"}}', '::1', '2026-03-13 05:00:22'),
(43, 106, 'DOCUMENT_DELETED', 'student_documents', '11', 'Deleted Form 138 for application #11', '{\"document_field\":\"form_138\",\"document_label\":\"Form 138\",\"deleted_filename\":\"tc35_20260313125957.pdf\",\"student_name\":\"OnlineSuccessFirst OnlineSuccessLast\"}', '::1', '2026-03-13 05:00:23'),
(44, 116, 'APPLICATION_REJECTED', 'student_applications', '18', 'Rejected and removed application #18', '{\"status\":\"Rejected\",\"remarks\":\"\",\"student_name\":\"TestTc28First TestTc28Last\"}', '::1', '2026-03-13 05:01:09'),
(45, 116, 'APPLICATION_APPROVED', 'student_applications', '19', 'Approved application #19', '{\"status\":\"Approved\",\"remarks\":\"Approved in automated test\",\"student_name\":\"TestTc29First TestTc29Last\",\"created_user_id\":120}', '::1', '2026-03-13 05:01:10'),
(46, 116, 'APPLICATION_REJECTED', 'student_applications', '20', 'Rejected and removed application #20', '{\"status\":\"Rejected\",\"remarks\":\"Incomplete requirements\",\"student_name\":\"TestTc30First TestTc30Last\"}', '::1', '2026-03-13 05:01:10'),
(47, 116, 'ENLISTMENT_APPROVED', 'students', '14', 'Approved enlistment for student #14', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":16,\"email\":\"tc26_20260313130056_doc_allowed@example.com\",\"first_name\":\"TestBaseFirst\",\"last_name\":\"TestBaseLast\"}}', '::1', '2026-03-13 05:01:15'),
(48, 126, 'APPLICATION_REJECTED', 'student_applications', '24', 'Rejected and removed application #24', '{\"status\":\"Rejected\",\"remarks\":\"\",\"student_name\":\"TestTc28First TestTc28Last\"}', '::1', '2026-03-13 05:02:17'),
(49, 126, 'APPLICATION_APPROVED', 'student_applications', '25', 'Approved application #25', '{\"status\":\"Approved\",\"remarks\":\"Approved in automated test\",\"student_name\":\"TestTc29First TestTc29Last\",\"created_user_id\":130}', '::1', '2026-03-13 05:02:18'),
(50, 126, 'APPLICATION_REJECTED', 'student_applications', '26', 'Rejected and removed application #26', '{\"status\":\"Rejected\",\"remarks\":\"Incomplete requirements\",\"student_name\":\"TestTc30First TestTc30Last\"}', '::1', '2026-03-13 05:02:18'),
(51, 126, 'ENLISTMENT_APPROVED', 'students', '17', 'Approved enlistment for student #17', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":22,\"email\":\"tc26_20260313130203_doc_allowed@example.com\",\"first_name\":\"TestBaseFirst\",\"last_name\":\"TestBaseLast\"}}', '::1', '2026-03-13 05:02:23'),
(52, 136, 'APPLICATION_REJECTED', 'student_applications', '31', 'Rejected and removed application #31', '{\"status\":\"Rejected\",\"remarks\":\"\",\"student_name\":\"TestTc28First TestTc28Last\"}', '::1', '2026-03-13 05:03:46'),
(53, 136, 'APPLICATION_APPROVED', 'student_applications', '32', 'Approved application #32', '{\"status\":\"Approved\",\"remarks\":\"Approved in automated test\",\"student_name\":\"TestTc29First TestTc29Last\",\"created_user_id\":140}', '::1', '2026-03-13 05:03:47'),
(54, 136, 'APPLICATION_REJECTED', 'student_applications', '33', 'Rejected and removed application #33', '{\"status\":\"Rejected\",\"remarks\":\"Incomplete requirements\",\"student_name\":\"TestTc30First TestTc30Last\"}', '::1', '2026-03-13 05:03:47'),
(55, 136, 'ENLISTMENT_APPROVED', 'students', '20', 'Approved enlistment for student #20', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":28,\"email\":\"tc26_20260313130328_doc_allowed@example.com\",\"first_name\":\"TestBaseFirst\",\"last_name\":\"TestBaseLast\"}}', '::1', '2026-03-13 05:03:52'),
(56, 136, 'DOCUMENT_DELETED', 'student_documents', '30', 'Deleted Form 138 for application #30', '{\"document_field\":\"form_138\",\"document_label\":\"Form 138\",\"deleted_filename\":\"tc35_20260313130328.pdf\",\"student_name\":\"OnlineISRB SuccessNJPF\"}', '::1', '2026-03-13 05:03:52'),
(57, 146, 'APPLICATION_REJECTED', 'student_applications', '38', 'Rejected and removed application #38', '{\"status\":\"Rejected\",\"remarks\":\"\",\"student_name\":\"TestTc28First TestTc28Last\"}', '::1', '2026-03-13 05:08:37'),
(58, 146, 'APPLICATION_APPROVED', 'student_applications', '39', 'Approved application #39', '{\"status\":\"Approved\",\"remarks\":\"Approved in automated test\",\"student_name\":\"TestTc29First TestTc29Last\",\"created_user_id\":150}', '::1', '2026-03-13 05:08:37'),
(59, 146, 'APPLICATION_REJECTED', 'student_applications', '40', 'Rejected and removed application #40', '{\"status\":\"Rejected\",\"remarks\":\"Incomplete requirements\",\"student_name\":\"TestTc30First TestTc30Last\"}', '::1', '2026-03-13 05:08:38'),
(60, 146, 'ENLISTMENT_APPROVED', 'students', '23', 'Approved enlistment for student #23', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":35,\"email\":\"tc26_20260313130819_doc_allowed@example.com\",\"first_name\":\"TestBaseFirst\",\"last_name\":\"TestBaseLast\"}}', '::1', '2026-03-13 05:08:42'),
(61, 146, 'DOCUMENT_DELETED', 'student_documents', '37', 'Deleted Form 138 for application #37', '{\"document_field\":\"form_138\",\"document_label\":\"Form 138\",\"deleted_filename\":\"tc35_20260313130819.pdf\",\"student_name\":\"OnlineTUYP SuccessHOKI\"}', '::1', '2026-03-13 05:08:43'),
(62, 156, 'APPLICATION_APPROVED', 'student_applications', '46', 'Approved application #46', '{\"status\":\"Approved\",\"remarks\":\"Approved in automated test\",\"student_name\":\"TestTc29First TestTc29Last\",\"created_user_id\":160}', '::1', '2026-03-13 05:23:47'),
(63, 156, 'APPLICATION_REJECTED', 'student_applications', '47', 'Rejected and removed application #47', '{\"status\":\"Rejected\",\"remarks\":\"Incomplete requirements\",\"student_name\":\"TestTc30First TestTc30Last\"}', '::1', '2026-03-13 05:23:47'),
(64, 156, 'ENLISTMENT_APPROVED', 'students', '26', 'Approved enlistment for student #26', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":42,\"email\":\"tc26_20260313132331_doc_allowed@example.com\",\"first_name\":\"TestBaseFirst\",\"last_name\":\"TestBaseLast\"}}', '::1', '2026-03-13 05:23:52'),
(65, 156, 'DOCUMENT_DELETED', 'student_documents', '44', 'Deleted Form 138 for application #44', '{\"document_field\":\"form_138\",\"document_label\":\"Form 138\",\"deleted_filename\":\"tc35_20260313132331.pdf\",\"student_name\":\"OnlineSTIR SuccessIKVN\"}', '::1', '2026-03-13 05:23:52'),
(66, 19, 'APPLICATION_STATUS_UPDATED', 'student_applications', '49', 'Updated application #49 to Pending', '{\"status\":\"Pending\",\"remarks\":\"goods\",\"student_name\":\"Andry Clarito\"}', '::1', '2026-03-14 02:26:34'),
(67, 19, 'APPLICATION_APPROVED', 'student_applications', '49', 'Approved application #49', '{\"status\":\"Approved\",\"remarks\":\"goods\",\"student_name\":\"Andry Clarito\",\"created_user_id\":163}', '::1', '2026-03-14 02:26:55'),
(68, 102, 'APPLICATION_STATUS_UPDATED', 'student_applications', '48', 'Updated application #48 to Pending', '{\"status\":\"Pending\",\"remarks\":\"\",\"student_name\":\"TestTc31First TestTc31Last\"}', '::1', '2026-03-14 13:22:38'),
(69, 102, 'ENLISTMENT_STATUS_UPDATED', 'students', '29', 'Updated enlistment status for student #29 to Pending', '{\"new_status\":\"Pending\"}', '::1', '2026-03-14 13:24:03'),
(70, 102, 'APPLICATION_APPROVED', 'student_applications', '50', 'Approved application #50', '{\"status\":\"Approved\",\"remarks\":\"\",\"student_name\":\"Gerd Clarito\",\"created_user_id\":164}', '::1', '2026-03-14 13:54:36'),
(71, 19, 'STUDENT_DATA_PURGED', 'students', NULL, 'Super Admin purged all student-related records.', '{\"deleted_counts\":{\"teacher_student_notes\":0,\"student_promotion_status\":0,\"archived_student_strand\":0,\"student_strand\":8,\"students\":20,\"student_applications\":32,\"student_users\":20},\"before_counts\":{\"student_users\":20,\"student_applications\":32,\"students\":20,\"student_strand\":8,\"archived_student_strand\":0,\"student_promotion_status\":0,\"teacher_student_notes\":0}}', '::1', '2026-03-14 14:00:45'),
(72, 19, 'STUDENT_DATA_PURGED', 'students', NULL, 'Super Admin purged all student-related records.', '{\"deleted_counts\":{\"teacher_student_notes\":0,\"student_promotion_status\":0,\"archived_student_strand\":0,\"student_strand\":0,\"students\":0,\"student_applications\":0,\"student_users\":0},\"before_counts\":{\"student_users\":0,\"student_applications\":0,\"students\":0,\"student_strand\":0,\"archived_student_strand\":0,\"student_promotion_status\":0,\"teacher_student_notes\":0}}', '::1', '2026-03-14 14:00:55'),
(73, 19, 'APPLICATION_APPROVED', 'student_applications', '52', 'Approved application #52', '{\"status\":\"Approved\",\"remarks\":\"\",\"student_name\":\"Harvey Clarito\",\"created_user_id\":165}', '::1', '2026-03-14 14:06:37'),
(74, 19, 'ENLISTMENT_APPROVED', 'students', '31', 'Approved enlistment for student #31', '{\"new_status\":\"Enlisted\",\"student\":{\"application_id\":52,\"email\":\"claritohabe@gmail.com\",\"first_name\":\"Harvey\",\"last_name\":\"Clarito\"}}', '::1', '2026-03-15 02:00:16');

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
(4, 'Registrar'),
(1, 'Student'),
(2, 'Super Admin'),
(3, 'Teacher');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `section_name` varchar(30) NOT NULL,
  `grade_level` int(11) NOT NULL,
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
  `strand_abbreviation` varchar(30) NOT NULL,
  `strand_name` varchar(255) NOT NULL,
  `track_name` varchar(120) NOT NULL DEFAULT 'Academic Track',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`strand_id`, `strand_abbreviation`, `strand_name`, `track_name`, `is_active`) VALUES
(1, 'STEM', 'Science, Technology, Engineering and Mathematics', 'Academic Track', 1),
(2, 'BE', 'Business and Entrepreneurship', 'Academic Track', 1),
(3, 'ASSH', 'Arts, Social Sciences, and Humanities', 'Academic Track', 1),
(4, 'GAS', 'General Academic Strand', 'Academic Track', 0),
(5, 'ICT', 'Information and Communication Technology', 'Technical Professional (TechPro) Track', 1),
(6, 'IA', 'Industrial Arts', 'Technical Professional (TechPro) Track', 1),
(7, 'FCS', 'Family and Consumer Science', 'Technical Professional (TechPro) Track', 1);

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
(31, 165, 52, 'Active', '2026-03-14', 'Pending', '2025-2026');

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
(52, 52, 'Street', 'Don Marcelino Street', 'Agusan', 'City of Cagayan De Oro', 'Misamis Oriental', 'Philippines', '9000', 'Yes', 'Street', 'Don Marcelino Street', 'Agusan', 'City of Cagayan De Oro', 'Misamis Oriental', 'Philippines', '9000');

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
(52, '098765432112', 'Clarito', 'Harvey', 'Durangparang', '', '2005-01-01', 'Male', 'Cagayan De Oro City', 'Catholic', 'Filipino', 'New', 'Pending', 'claritohabe@gmail.com', '09947892199', '', '2026-03-14 14:05:29', '', NULL);

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
(52, 52, NULL, NULL, NULL, NULL);

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
(52, 52, 'Clarito', 'Randy', 'Abecia', '09262360968', 'Clarito', 'Maria Cristina', 'Durangparang', '09262360968', '', '', '', '');

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
(52, 52, 0, 0, 0, 0, 0, 0, 0);

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
(52, 52, 'No', '');

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
(52, 52, 'Grade 10', '2019-2020', 'Lourdes College IBED', '');

-- --------------------------------------------------------

--
-- Table structure for table `student_promotion_status`
--

CREATE TABLE `student_promotion_status` (
  `promotion_status_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL,
  `computed_status` enum('Pending','Complete','Incomplete') NOT NULL DEFAULT 'Pending',
  `recommended_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `teacher_remarks` text DEFAULT NULL,
  `approval_status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `admin_user_id` int(11) DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(52, 52, 'No', '', 'No', '');

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
(52, 52, 'No', 'No', '', 'No', 'No', NULL, NULL, NULL, NULL, NULL, NULL);

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
(25, 31, 3, 11, '2nd Semester', '2025-2026', 17);

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
(50, 90, 'Jordan', 'Meteor', 'Cosmos', NULL),
(51, 105, 'TestTeacher', 'Runner', NULL, NULL),
(52, 112, 'New', 'Teacher', NULL, NULL),
(53, 115, 'TestTeacher', 'Runner', NULL, NULL),
(54, 122, 'New', 'Teacher', NULL, NULL),
(55, 125, 'TestTeacher', 'Runner', NULL, NULL),
(56, 132, 'New', 'Teacher', NULL, NULL),
(57, 135, 'TestTeacher', 'Runner', NULL, NULL),
(58, 142, 'New', 'Teacher', NULL, NULL),
(59, 145, 'TestTeacher', 'Runner', NULL, NULL),
(60, 152, 'New', 'Teacher', NULL, NULL),
(61, 155, 'TestTeacher', 'Runner', NULL, NULL),
(62, 162, 'New', 'Teacher', NULL, NULL);

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

--
-- Dumping data for table `teacher_advisory`
--

INSERT INTO `teacher_advisory` (`advisory_id`, `teacher_id`, `section_id`, `grade_level`, `strand_id`, `school_year`, `assigned_date`) VALUES
(1, 27, 33, 11, 5, '2025-2026', '2026-03-08 07:17:01'),
(2, 11, 37, 12, 5, '2025-2026', '2026-03-08 09:27:31'),
(3, 51, 33, 11, 5, '2025-2026', '2026-03-13 04:59:59'),
(5, 53, 33, 11, 5, '2025-2026', '2026-03-13 05:00:57'),
(7, 55, 33, 11, 5, '2025-2026', '2026-03-13 05:02:04'),
(9, 57, 33, 11, 5, '2025-2026', '2026-03-13 05:03:29'),
(11, 59, 33, 11, 5, '2025-2026', '2026-03-13 05:08:20'),
(13, 61, 33, 11, 5, '2025-2026', '2026-03-13 05:23:33');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_student_notes`
--

CREATE TABLE `teacher_student_notes` (
  `note_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `behavior_note` text DEFAULT NULL,
  `follow_up_note` text DEFAULT NULL,
  `intervention_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
(51, 'teacher011', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Active', 0),
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
(102, 'registrar', '$2y$10$eRuPGBKHXhkVctNOvLu8c.UFByrTJKUK1zqcxDY/A.s5eB7eWZROO', 4, 'Active', 0),
(105, 'tc26_20260313125957_teacher', '$2y$10$knFGM7PvWgXeZxP0f7bWFu1RzvrO.HDgl/rVdifdkthGH3kCG4JW.', 3, 'Active', 0),
(106, 'tc26_20260313125957_super', '$2y$10$HL5Y6HpBWax4IftgO5McSOtpz.ZOaWJZGoSgWq410UljUZ55CKCLe', 2, 'Active', 0),
(107, 'tc26_20260313125957_registrar', '$2y$10$KJbb/ql6VXekqhVdzWaumuZZubRllC77nM0y9Ft7bqWfYsYjK72by', 4, 'Active', 0),
(108, 'tc26_20260313125957_firstlogin', '$2y$10$qeGl.9FtJFQ.gxn2BDGUPeGLbCShcHpZd/cNmUSUVsVJr2kJDvqf6', 4, 'Active', 1),
(109, 'tc26_20260313125957_changepw', '$2y$10$8l0lE7nmhffdL2UTL07wm.3t3xhGvkSFv7STvOgnP7hd8CTsNHl9.', 4, 'Active', 1),
(111, 'tc26_20260313125957_acct_registrar', '$2y$10$SwdSQx7e6z2J28b8080uLedOxlchaUAEC9Oxy6LcHtNfh/NRB63AG', 4, 'Active', 1),
(112, 'tc26_20260313125957_acct_teacher', '$2y$10$qNyzom7xU6TUs5JTMKbpjuvLmhGDX/i3CbCEQSQLXAJLRwtaInGda', 3, 'Active', 1),
(115, 'tc26_20260313130056_teacher', '$2y$10$07z4VLqlhC6CSFf4iueAj.WMU2bDLDb6XNh0c1fM8/M8dJC118tSu', 3, 'Active', 0),
(116, 'tc26_20260313130056_super', '$2y$10$G7jeCpWrlo0OSFZeKmOy.OmQWjkTC8alynNhjvWnexNPVUvG9C1hy', 2, 'Active', 0),
(117, 'tc26_20260313130056_registrar', '$2y$10$cDrocItz9EEihaIEFVYPxu.I3bMqJr/KP41QiBy3R8n22.Xx.NWWi', 4, 'Active', 0),
(118, 'tc26_20260313130056_firstlogin', '$2y$10$25khisL8wpF9KzEcIzileu8rRxgwY9.b206k3R1jhIdPVTYDwpy1W', 4, 'Active', 1),
(119, 'tc26_20260313130056_changepw', '$2y$10$NtqqjH7RREHmQybEko3pI.dX85DI5uvKiwJvSU1TOCsYbfPljBV/a', 4, 'Active', 1),
(121, 'tc26_20260313130056_acct_registrar', '$2y$10$HlugD09E21Wd66YefUL4BuoHAHNOY6dk3cVJAnFHoVAzdHKYrSOiO', 4, 'Active', 1),
(122, 'tc26_20260313130056_acct_teacher', '$2y$10$YlB8sWiW4sXdFPDIcjBUK.NfEIjHdq4WWxyMKybUiveEOVR0zpWq6', 3, 'Active', 1),
(125, 'tc26_20260313130203_teacher', '$2y$10$hO9tOgdPX4EVWk1VFrz7T.BEhC0PGJDX9qVmFgDuZYzB62aCpRx1G', 3, 'Active', 0),
(126, 'tc26_20260313130203_super', '$2y$10$aYLIzDWVYHybsQYHofpd1OrJt8WE5GoiUlXfF/67Js13KGsQ1M85C', 2, 'Active', 0),
(127, 'tc26_20260313130203_registrar', '$2y$10$ZYV6yoKu.z8Ki7TBzJw5auqqOGEnymjFgq4RxoOoMCqfptnSl25CO', 4, 'Active', 0),
(128, 'tc26_20260313130203_firstlogin', '$2y$10$jidgrMg3XgZv2k.nTl.YleWEIuLR7y142NnmYlcbDgzHsu//wJH0m', 4, 'Active', 1),
(129, 'tc26_20260313130203_changepw', '$2y$10$EqjBK2vRxh0gsXPIvIi2zuhKiI/W7dNPN9NJa9S/nMw/B3yGqwNVe', 4, 'Active', 1),
(131, 'tc26_20260313130203_acct_registrar', '$2y$10$NEU7Cz6p6uf.CTRis9akSeNrmRsW1ucO6wThfoTWFD6Tn72dAa4vi', 4, 'Active', 1),
(132, 'tc26_20260313130203_acct_teacher', '$2y$10$LAILqcVh9v/H/Dxvhyi97edpYTg01tCYN8frbQHcPD2DGkRhoeuzm', 3, 'Active', 1),
(135, 'tc26_20260313130328_teacher', '$2y$10$IUtWSgl/9q5xRkkROWp5Mu/p7EHx0kEswvDuf9nPwE96Ii3VlbzX2', 3, 'Active', 0),
(136, 'tc26_20260313130328_super', '$2y$10$cxDgUzOBzPt5l1ccKMXgse5V6FRTr5AbfUQk9xHMX6y8Qpr8Xa/Ma', 2, 'Active', 0),
(137, 'tc26_20260313130328_registrar', '$2y$10$Xut4i0Pkw2UZ8gRqi42SXOUfNHkZwVefBd10PZJmZusQBakxUYLw6', 4, 'Active', 0),
(138, 'tc26_20260313130328_firstlogin', '$2y$10$9NA0Sd3YOykIIehcD7ng5eOEk4X43LieWIWSD6.Acx06.28wtYi4W', 4, 'Active', 1),
(139, 'tc26_20260313130328_changepw', '$2y$10$Y7P2jwUc.8agnZDCutQd2uZEu5RvC43qtJ1HyhEGNIEj3hvbCMCXm', 4, 'Active', 1),
(141, 'tc26_20260313130328_acct_registrar', '$2y$10$hY0neUboQWf3YgGaKVX/ue5UszZogYawR2uokAfokIL9P2hj7hIB2', 4, 'Active', 1),
(142, 'tc26_20260313130328_acct_teacher', '$2y$10$lSoUvA2r1uGsZ0g5ysa67OYNZbQKlKwTla7fqjzGK8gIRhMIyqt56', 3, 'Active', 1),
(145, 'tc26_20260313130819_teacher', '$2y$10$SzSUQxIw8B5151UGkNKJV.mDcqzhNFWqlB2Wf4HWQX4XugVdLzs9C', 3, 'Active', 0),
(146, 'tc26_20260313130819_super', '$2y$10$WBlL1/Py22zMt0brAhYVz.jV3IW.P88Nhd8k57PpK7gKt0PFiPYf6', 2, 'Active', 0),
(147, 'tc26_20260313130819_registrar', '$2y$10$m7W5mLjAimbgtkvchqnN9estlrxHmdRIpzmNRh6fA7W5bknOCMrgu', 4, 'Active', 0),
(148, 'tc26_20260313130819_firstlogin', '$2y$10$o56cJ.vpFCgNknTRMtNiKu4.N5Sru1v.zOx0tGytHmT7gHpbCIE4u', 4, 'Active', 1),
(149, 'tc26_20260313130819_changepw', '$2y$10$nmS6QpSWz3AaGAS7Xxmvt.izarIXZlQrvJuD354aJSuk074J.mbOm', 4, 'Active', 1),
(151, 'tc26_20260313130819_acct_registrar', '$2y$10$NKWw5.O8/kJow1AC0gi1AOFPab8hHb7xgwPurEQhVd///XzVbMH4C', 4, 'Active', 1),
(152, 'tc26_20260313130819_acct_teacher', '$2y$10$vWMSPHD48/XW2sGosY3xMeBCpGZCqpIAeLXmMbJqz2M35EqEk6zpy', 3, 'Active', 1),
(155, 'tc26_20260313132331_teacher', '$2y$10$1Rj3VNYnk0yO.xV4M1VZ6OdRn3wJu3F/HalLb9dcdeEi1JHrlAOhC', 3, 'Active', 0),
(156, 'tc26_20260313132331_super', '$2y$10$BhGcXfYCQv90WFyWnJIE7ubQT7/eyZE0pLXig.3XF4OeIzp5Kbb.S', 2, 'Active', 0),
(157, 'tc26_20260313132331_registrar', '$2y$10$W.UCy4vSULdLDc1BP48KvOl4CwnZhIZP9z2TXipcHqG1QIOWB1DVO', 4, 'Active', 0),
(158, 'tc26_20260313132331_firstlogin', '$2y$10$rr1VY12K0Fx9GjP4t/FyzeO5BqgR/vBPWoPUNhXYMVW03JrijuQia', 4, 'Active', 1),
(159, 'tc26_20260313132331_changepw', '$2y$10$xtJN3kEFIqsxOi9OMBECGe.0d3BWZqsso6ruxrgQfId.V.V2GzQp6', 4, 'Active', 1),
(161, 'tc26_20260313132331_acct_registrar', '$2y$10$RKmhtH5mrGOTf0ox3BT5YuSSPz8zRIdr6bZX0Ih07pD/nBgpMvqfe', 4, 'Active', 1),
(162, 'tc26_20260313132331_acct_teacher', '$2y$10$d.pyk.MSY3VMvLcac0/2COUpdxEpZ6WRdByPKZYRSrlz2ynxxwui.', 3, 'Active', 1),
(165, '098765432112', '$2y$10$lX5lrXvkHw0JuvpTMxXKUupyumDSFr4Esb4rnGi6OWoOCtGx/RuJ.', 1, 'Active', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_theme_preferences`
--

CREATE TABLE `user_theme_preferences` (
  `user_id` int(11) NOT NULL,
  `theme_preference` varchar(10) NOT NULL DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_theme_preferences`
--

INSERT INTO `user_theme_preferences` (`user_id`, `theme_preference`, `created_at`, `updated_at`) VALUES
(19, 'dark', '2026-03-15 03:13:12', '2026-03-15 03:54:37'),
(51, 'dark', '2026-03-15 03:21:32', '2026-03-15 03:21:32'),
(165, 'light', '2026-03-15 03:11:44', '2026-03-15 04:03:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activation_settings`
--
ALTER TABLE `activation_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_audit_trail`
--
ALTER TABLE `admin_audit_trail`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `student_promotion_status`
--
ALTER TABLE `student_promotion_status`
  ADD PRIMARY KEY (`promotion_status_id`),
  ADD UNIQUE KEY `uq_student_promotion_term` (`student_id`,`school_year`,`semester`),
  ADD KEY `idx_promotion_status_teacher` (`teacher_id`),
  ADD KEY `idx_promotion_status_admin` (`admin_user_id`),
  ADD KEY `idx_promotion_status_term` (`school_year`,`semester`),
  ADD KEY `idx_promotion_status_approval` (`approval_status`);

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
-- Indexes for table `teacher_student_notes`
--
ALTER TABLE `teacher_student_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD UNIQUE KEY `uq_teacher_student_note` (`teacher_id`,`student_id`),
  ADD KEY `idx_teacher_student_notes_teacher` (`teacher_id`),
  ADD KEY `idx_teacher_student_notes_student` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_theme_preferences`
--
ALTER TABLE `user_theme_preferences`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation_settings`
--
ALTER TABLE `activation_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_audit_trail`
--
ALTER TABLE `admin_audit_trail`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `archived_student_strand`
--
ALTER TABLE `archived_student_strand`
  MODIFY `archive_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `student_addresses`
--
ALTER TABLE `student_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_applications`
--
ALTER TABLE `student_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_documents`
--
ALTER TABLE `student_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_family`
--
ALTER TABLE `student_family`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_learning_modality`
--
ALTER TABLE `student_learning_modality`
  MODIFY `modality_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_learning_program`
--
ALTER TABLE `student_learning_program`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_previous_school`
--
ALTER TABLE `student_previous_school`
  MODIFY `prev_school_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_promotion_status`
--
ALTER TABLE `student_promotion_status`
  MODIFY `promotion_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `student_social_info`
--
ALTER TABLE `student_social_info`
  MODIFY `social_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_special_needs`
--
ALTER TABLE `student_special_needs`
  MODIFY `sne_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_strand`
--
ALTER TABLE `student_strand`
  MODIFY `student_strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `teacher_advisory`
--
ALTER TABLE `teacher_advisory`
  MODIFY `advisory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `teacher_student_notes`
--
ALTER TABLE `teacher_student_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_audit_trail`
--
ALTER TABLE `admin_audit_trail`
  ADD CONSTRAINT `admin_audit_trail_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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
-- Constraints for table `teacher_student_notes`
--
ALTER TABLE `teacher_student_notes`
  ADD CONSTRAINT `teacher_student_notes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
