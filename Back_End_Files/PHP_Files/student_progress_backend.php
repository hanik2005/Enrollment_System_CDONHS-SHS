<?php
include "../../Back_End_Files/PHP_Files/get_teacher_advisory.php";

$progressData = [];
$successMessage = '';
$errorMessage = '';
$progressAvailableSchoolYears = [];

if (!function_exists('getCurrentSchoolYearForProgress')) {
    function getCurrentSchoolYearForProgress(): string
    {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');
        if ($currentMonth >= 8) {
            return $currentYear . '-' . ($currentYear + 1);
        }
        return ($currentYear - 1) . '-' . $currentYear;
    }
}

if (!function_exists('getCurrentSemesterForProgress')) {
    function getCurrentSemesterForProgress(): string
    {
        $month = (int) date('n');
        return ($month >= 8 && $month <= 12) ? '1st Semester' : '2nd Semester';
    }
}

if (!function_exists('ensurePromotionWorkflowTables')) {
    function ensurePromotionWorkflowTables(mysqli $connection): bool
    {
        $createPromotionStatusTable = "
            CREATE TABLE IF NOT EXISTS student_promotion_status (
                promotion_status_id INT(11) NOT NULL AUTO_INCREMENT,
                student_id INT(11) NOT NULL,
                teacher_id INT(11) NOT NULL,
                school_year VARCHAR(9) NOT NULL,
                semester ENUM('1st Semester','2nd Semester') NOT NULL,
                computed_status ENUM('Pending','Complete','Incomplete') NOT NULL DEFAULT 'Pending',
                recommended_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
                teacher_remarks TEXT DEFAULT NULL,
                approval_status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
                is_approved TINYINT(1) NOT NULL DEFAULT 0,
                admin_user_id INT(11) DEFAULT NULL,
                admin_remarks TEXT DEFAULT NULL,
                approved_at DATETIME DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (promotion_status_id),
                UNIQUE KEY uq_student_promotion_term (student_id, school_year, semester),
                KEY idx_promotion_status_teacher (teacher_id),
                KEY idx_promotion_status_admin (admin_user_id),
                KEY idx_promotion_status_term (school_year, semester),
                KEY idx_promotion_status_approval (approval_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ";

        return $connection->query($createPromotionStatusTable) === true;
    }
}

$progressCurrentSchoolYear = getCurrentSchoolYearForProgress();
$progressCurrentSemester = getCurrentSemesterForProgress();
$progressSelectedSchoolYear = isset($_GET['school_year']) ? trim((string) $_GET['school_year']) : $progressCurrentSchoolYear;
$progressSelectedSemester = isset($_GET['semester']) ? trim((string) $_GET['semester']) : $progressCurrentSemester;

if (!in_array($progressSelectedSemester, ['1st Semester', '2nd Semester'], true)) {
    $progressSelectedSemester = $progressCurrentSemester;
}
$progressTablesReady = ensurePromotionWorkflowTables($connection);

if (!$progressTablesReady) {
    $errorMessage = "Unable to prepare student progress tables: " . $connection->error;
}

if (!empty($advisorySectionId) && $progressTablesReady) {
    $yearsStmt = $connection->prepare("
        SELECT DISTINCT school_year
        FROM student_strand
        WHERE section_id = ?
        ORDER BY school_year DESC
    ");
    $yearsStmt->bind_param("i", $advisorySectionId);
    $yearsStmt->execute();
    $yearsResult = $yearsStmt->get_result();
    while ($yearRow = $yearsResult->fetch_assoc()) {
        $progressAvailableSchoolYears[] = (string) $yearRow['school_year'];
    }
    $yearsStmt->close();

    if (empty($progressAvailableSchoolYears)) {
        $progressAvailableSchoolYears[] = $progressCurrentSchoolYear;
    }

    if (!in_array($progressSelectedSchoolYear, $progressAvailableSchoolYears, true)) {
        $progressSelectedSchoolYear = $progressCurrentSchoolYear;
    }

    $progressStmt = $connection->prepare("
        SELECT
            s.student_id,
            sa.lrn,
            sa.first_name,
            sa.last_name,
            sa.middle_name,
            sa.extension_name,
            ss.grade_level,
            ss.school_year,
            ss.semester,
            st.strand_name,
            sec.section_name,
            s.enrollment_status,
            s.enlistment_status,
            COALESCE(sps.computed_status, 'Pending') AS computed_status,
            COALESCE(sps.recommended_status, 'Pending') AS recommended_status,
            COALESCE(sps.teacher_remarks, '') AS teacher_remarks,
            COALESCE(sps.is_approved, 0) AS is_approved,
            COALESCE(sps.approval_status, 'Pending') AS approval_status
        FROM students s
        INNER JOIN student_strand ss
            ON s.student_id = ss.student_id
            AND ss.section_id = ?
            AND ss.school_year = ?
            AND ss.semester = ?
        INNER JOIN student_applications sa
            ON s.application_id = sa.application_id
        INNER JOIN strands st
            ON ss.strand_id = st.strand_id
        INNER JOIN section sec
            ON ss.section_id = sec.section_id
        LEFT JOIN student_promotion_status sps
            ON sps.student_id = s.student_id
            AND sps.school_year = ss.school_year
            AND sps.semester = ss.semester
        WHERE s.enrollment_status = 'Active'
        ORDER BY sa.last_name ASC, sa.first_name ASC
    ");

    $progressStmt->bind_param("iss", $advisorySectionId, $progressSelectedSchoolYear, $progressSelectedSemester);
    $progressStmt->execute();
    $students = $progressStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $progressStmt->close();

    $uniqueStudents = [];
    foreach ($students as $student) {
        $uniqueStudents[(int) $student['student_id']] = $student;
    }

    foreach ($uniqueStudents as $student) {
        $computedStatus = (string) ($student['computed_status'] ?? 'Pending');
        if (!in_array($computedStatus, ['Pending', 'Complete', 'Incomplete'], true)) {
            $computedStatus = 'Pending';
        }
        $computedLabel = $computedStatus;

        $semester = (string) ($student['semester'] ?? $progressSelectedSemester);
        $canPromote = $semester === '2nd Semester' && $computedStatus === 'Complete';

        $student['computed_status'] = $computedStatus;
        $student['computed_status_label'] = $computedLabel;
        $student['can_promote'] = $canPromote;
        $student['is_second_semester'] = $semester === '2nd Semester';

        $progressData[] = $student;
    }
}
?>
