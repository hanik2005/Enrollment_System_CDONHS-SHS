-- =============================================================================
-- CDONHS SHS Enrollment System - Teachers and Sections Data
-- Simplified: Only 8 teachers, sections by track only (no strand)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- First, insert additional users for 8 teachers
-- Password is hashed 'password' (for demo purposes)
-- Using existing users 41-48 for teachers
-- -----------------------------------------------------------------------------

-- Usersuser_id 41 already exist (-48 with role_id 3), skip user insertion

-- -----------------------------------------------------------------------------
-- Insert 8 Teachers into the teachers table
-- Each teacher has a unique advisor_id
-- Using user_ids 41-48 to match existing users
-- -----------------------------------------------------------------------------

INSERT INTO `teachers` (`teacher_id`, `user_id`, `first_name`, `last_name`, `middle_name`, `extension_name`, `advisor_id`) VALUES
(1, 41, 'Maria', 'Santos', 'Cruz', NULL, 1),
(2, 42, 'John', 'Lim', 'Bautista', NULL, 2),
(3, 43, 'Elizabeth', 'Garcia', 'Mendoza', NULL, 3),
(4, 44, 'Michael', 'Rodriguez', 'Torres', NULL, 4),
(5, 45, 'Catherine', 'Bautista', 'Reyes', NULL, 5),
(6, 46, 'David', 'Cruz', 'Aquino', NULL, 6),
(7, 47, 'Patricia', 'Mendoza', 'Del Rosario', NULL, 7),
(8, 48, 'James', 'Reyes', 'San Jose', NULL, 8);

-- -----------------------------------------------------------------------------
-- Insert Sections into the section table (Simplified - no strand_id)
-- Section table columns: section_id, section_name, grade_level, track_id, adviser_id, school_year
-- Each section has an adviser_id (references the teacher's advisor_id)
-- School Year: 2025-2026
-- 
-- Initial sections for both tracks (Academic and TVL)
-- Section naming: First 4 sections are A, B, C, D for initial setup
-- If more sections needed, they will be auto-created as E, F, G, etc.
-- -----------------------------------------------------------------------------

INSERT INTO `section` (`section_id`, `section_name`, `grade_level`, `track_id`, `adviser_id`, `school_year`) VALUES
-- Academic Track (track_id = 1) - Grade 11
(1, 'A', 11, 1, 1, '2025-2026'),
(2, 'B', 11, 1, 2, '2025-2026'),
(3, 'C', 11, 1, 3, '2025-2026'),
(4, 'D', 11, 1, 4, '2025-2026'),
-- Academic Track - Grade 12
(5, 'A', 12, 1, 5, '2025-2026'),
(6, 'B', 12, 1, 6, '2025-2026'),
(7, 'C', 12, 1, 7, '2025-2026'),
(8, 'D', 12, 1, 8, '2025-2026'),
-- TVL Track (track_id = 2) - Grade 11
(9, 'A', 11, 2, 1, '2025-2026'),
(10, 'B', 11, 2, 2, '2025-2026'),
(11, 'C', 11, 2, 3, '2025-2026'),
(12, 'D', 11, 2, 4, '2025-2026'),
-- TVL Track - Grade 12
(13, 'A', 12, 2, 5, '2025-2026'),
(14, 'B', 12, 2, 6, '2025-2026'),
(15, 'C', 12, 2, 7, '2025-2026'),
(16, 'D', 12, 2, 8, '2025-2026');
