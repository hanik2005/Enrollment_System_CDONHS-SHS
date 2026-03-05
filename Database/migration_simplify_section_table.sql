-- First delete all rows (slower but works with FK constraints)
DELETE FROM `section`;

-- Then insert new data
INSERT INTO `section` (`section_id`, `section_name`, `grade_level`, `school_year`) VALUES
(1, 'Academic - A', 11, '2025-2026'),
(2, 'Academic - B', 11, '2025-2026'),
(3, 'Academic - C', 11, '2025-2026'),
(4, 'Academic - A', 12, '2025-2026'),
(5, 'Academic - B', 12, '2025-2026'),
(6, 'Academic - C', 12, '2025-2026'),
(7, 'TechPro - A', 11, '2025-2026'),
(8, 'TechPro - B', 11, '2025-2026'),
(9, 'TechPro - A', 12, '2025-2026'),
(10, 'TechPro - B', 12, '2025-2026');

-- Reset auto_increment
ALTER TABLE `section` AUTO_INCREMENT = 11;

-- Add columns to student_track
ALTER TABLE `student_track` ADD COLUMN `grade_level` INT DEFAULT NULL;
ALTER TABLE `student_track` ADD COLUMN `section_id` INT DEFAULT NULL;
