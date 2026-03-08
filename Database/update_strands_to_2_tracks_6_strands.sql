-- Run this once on your existing database.
-- It updates strands to:
-- Academic Track: STEM, ASSH, BE
-- Technical Professional (TechPro) Track: ICT, FCS, IA

ALTER TABLE `strands`
  ADD COLUMN IF NOT EXISTS `track_name` varchar(120) NOT NULL DEFAULT 'Academic Track' AFTER `strand_name`,
  ADD COLUMN IF NOT EXISTS `is_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `track_name`;

UPDATE `strands`
SET
  `strand_abbreviation` = 'STEM',
  `strand_name` = 'Science, Technology, Engineering and Mathematics',
  `track_name` = 'Academic Track',
  `is_active` = 1
WHERE `strand_id` = 1;

UPDATE `strands`
SET
  `strand_abbreviation` = 'BE',
  `strand_name` = 'Business and Entrepreneurship',
  `track_name` = 'Academic Track',
  `is_active` = 1
WHERE `strand_id` = 2;

UPDATE `strands`
SET
  `strand_abbreviation` = 'ASSH',
  `strand_name` = 'Arts, Social Sciences, and Humanities',
  `track_name` = 'Academic Track',
  `is_active` = 1
WHERE `strand_id` = 3;

UPDATE `strands`
SET
  `strand_abbreviation` = 'GAS',
  `strand_name` = 'General Academic Strand',
  `track_name` = 'Academic Track',
  `is_active` = 0
WHERE `strand_id` = 4;

UPDATE `strands`
SET
  `strand_abbreviation` = 'ICT',
  `strand_name` = 'Information and Communication Technology',
  `track_name` = 'Technical Professional (TechPro) Track',
  `is_active` = 1
WHERE `strand_id` = 5;

UPDATE `strands`
SET
  `strand_abbreviation` = 'IA',
  `strand_name` = 'Industrial Arts',
  `track_name` = 'Technical Professional (TechPro) Track',
  `is_active` = 1
WHERE `strand_id` = 6;

UPDATE `strands`
SET
  `strand_abbreviation` = 'FCS',
  `strand_name` = 'Family and Consumer Science',
  `track_name` = 'Technical Professional (TechPro) Track',
  `is_active` = 1
WHERE `strand_id` = 7;
