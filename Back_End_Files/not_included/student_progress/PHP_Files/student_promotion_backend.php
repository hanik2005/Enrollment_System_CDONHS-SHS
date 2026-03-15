<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($connection)) {
    include "../../DB_Connection/Connection.php";
}

if (!isset($advisorySectionId)) {
    include "../../Back_End_Files/PHP_Files/get_teacher_advisory.php";
}

$message = '';
$message_type = '';

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
        $createPromotionTable = "
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
                UNIQUE KEY uq_student_promotion_term (student_id, school_year, semester)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ";

        return $connection->query($createPromotionTable) === true;
    }
}

if (!function_exists('normalizeSemesterRecommendation')) {
    function normalizeSemesterRecommendation(string $semester, string $computedStatus, int $gradeLevel, string $requestedStatus): string
    {
        if ($semester === '1st Semester') {
            if ($computedStatus === 'Complete') {
                return 'Promote to 2nd Semester';
            }
            if ($computedStatus === 'Incomplete') {
                return 'Incomplete';
            }
            return 'Pending';
        }

        if ($computedStatus !== 'Complete') {
            return 'Incomplete';
        }

        if ($gradeLevel === 11 && $requestedStatus === 'Promote to Grade 12') {
            return 'Promote to Grade 12';
        }

        if ($gradeLevel === 12 && $requestedStatus === 'Graduate') {
            return 'Graduate';
        }

        return 'Pending';
    }
}

if (!function_exists('normalizeManualComputedStatus')) {
    function normalizeManualComputedStatus(string $value): string
    {
        if ($value === 'Complete') {
            return 'Complete';
        }
        if ($value === 'Incomplete') {
            return 'Incomplete';
        }
        return 'Pending';
    }
}

if (!function_exists('isAdvisoryStudentInTerm')) {
    function isAdvisoryStudentInTerm(mysqli $connection, int $studentId, int $sectionId, string $schoolYear, string $semester): ?array
    {
        $validateStmt = $connection->prepare("
            SELECT ss.grade_level
            FROM student_strand ss
            WHERE ss.student_id = ? AND ss.section_id = ? AND ss.school_year = ? AND ss.semester = ?
            LIMIT 1
        ");
        $validateStmt->bind_param("iiss", $studentId, $sectionId, $schoolYear, $semester);
        $validateStmt->execute();
        $row = $validateStmt->get_result()->fetch_assoc();
        $validateStmt->close();
        return $row ?: null;
    }
}

$teacherStmt = $connection->prepare("
    SELECT teacher_id
    FROM teachers
    WHERE user_id = ?
    LIMIT 1
");
$teacherStmt->bind_param("i", $_SESSION['user_id']);
$teacherStmt->execute();
$teacherData = $teacherStmt->get_result()->fetch_assoc();
$teacherStmt->close();
$teacher_id = (int) ($teacherData['teacher_id'] ?? 0);

$currentSchoolYear = getCurrentSchoolYearForProgress();
$currentSemester = getCurrentSemesterForProgress();
$activeSchoolYear = isset($_POST['selected_school_year']) ? trim((string) $_POST['selected_school_year']) : $currentSchoolYear;
$activeSemester = isset($_POST['selected_semester']) ? trim((string) $_POST['selected_semester']) : $currentSemester;

if (!preg_match('/^\d{4}-\d{4}$/', $activeSchoolYear)) {
    $activeSchoolYear = $currentSchoolYear;
}
if (!in_array($activeSemester, ['1st Semester', '2nd Semester'], true)) {
    $activeSemester = $currentSemester;
}

$tablesReady = ensurePromotionWorkflowTables($connection);

if (!$tablesReady) {
    $message = "Unable to prepare student progress workflow tables: " . $connection->error;
    $message_type = 'error';
}

if ((isset($_POST['save_selected_students']) || isset($_POST['bulk_update_promotion'])) && empty($advisorySectionId)) {
    $message = "No advisory section assigned. Cannot save recommendations.";
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_selected_students']) && $tablesReady && !empty($advisorySectionId)) {
    $students = $_POST['students'] ?? [];
    $selectedStudents = array_map('intval', $_POST['selected_students'] ?? []);
    $selectedStudents = array_values(array_filter($selectedStudents, function ($studentId) {
        return $studentId > 0;
    }));
    $savedCount = 0;

    if (!empty($students) && is_array($students) && !empty($selectedStudents)) {
        foreach ($selectedStudents as $studentId) {
            $data = $students[$studentId] ?? null;
            $studentId = (int) $studentId;
            if ($studentId <= 0 || !is_array($data)) {
                continue;
            }

            $advisoryRow = isAdvisoryStudentInTerm($connection, $studentId, (int) $advisorySectionId, $activeSchoolYear, $activeSemester);
            if (!$advisoryRow) {
                continue;
            }

            $gradeLevel = (int) ($advisoryRow['grade_level'] ?? 0);
            $computedStatus = normalizeManualComputedStatus((string) ($data['computed_status'] ?? 'Pending'));
            $requestedStatus = trim((string) ($data['recommended_status'] ?? 'Pending'));
            $teacherRemarks = trim((string) ($data['teacher_remarks'] ?? ''));

            $finalRecommendation = normalizeSemesterRecommendation(
                $activeSemester,
                $computedStatus,
                $gradeLevel,
                $requestedStatus
            );

            $saveStmt = $connection->prepare("
                INSERT INTO student_promotion_status
                    (student_id, teacher_id, school_year, semester, computed_status, recommended_status, teacher_remarks, approval_status, is_approved, admin_user_id, admin_remarks, approved_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', 0, NULL, NULL, NULL)
                ON DUPLICATE KEY UPDATE
                    teacher_id = VALUES(teacher_id),
                    computed_status = VALUES(computed_status),
                    recommended_status = VALUES(recommended_status),
                    teacher_remarks = VALUES(teacher_remarks),
                    approval_status = 'Pending',
                    is_approved = 0,
                    admin_user_id = NULL,
                    admin_remarks = NULL,
                    approved_at = NULL,
                    updated_at = CURRENT_TIMESTAMP
            ");
            $saveStmt->bind_param(
                "iisssss",
                $studentId,
                $teacher_id,
                $activeSchoolYear,
                $activeSemester,
                $computedStatus,
                $finalRecommendation,
                $teacherRemarks
            );

            if ($saveStmt->execute()) {
                $savedCount++;
            }
            $saveStmt->close();
        }
    }

    if ($savedCount > 0) {
        $message = "Saved {$savedCount} student recommendation(s) for {$activeSemester} ({$activeSchoolYear}). Waiting for admin validation.";
        $message_type = 'success';
    } elseif (empty($selectedStudents)) {
        $message = "No students were selected. Select at least one student to save.";
        $message_type = 'error';
    } else {
        $message = "No recommendations were saved. Check semester rules.";
        $message_type = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_update_promotion']) && $tablesReady && !empty($advisorySectionId)) {
    $selectedStudents = $_POST['selected_students'] ?? [];
    $bulkStatus = trim((string) ($_POST['bulk_status'] ?? 'Pending'));
    $bulkRemarks = trim((string) ($_POST['bulk_remarks'] ?? ''));
    $savedCount = 0;

    foreach ($selectedStudents as $studentIdRaw) {
        $studentId = (int) $studentIdRaw;
        if ($studentId <= 0) {
            continue;
        }

        $advisoryRow = isAdvisoryStudentInTerm($connection, $studentId, (int) $advisorySectionId, $activeSchoolYear, $activeSemester);
        if (!$advisoryRow) {
            continue;
        }

        $gradeLevel = (int) ($advisoryRow['grade_level'] ?? 0);
        if ($activeSemester === '1st Semester') {
            $computedStatus = normalizeManualComputedStatus($bulkStatus);
        } else {
            if ($bulkStatus === 'Promote to Grade 12' || $bulkStatus === 'Graduate') {
                $computedStatus = 'Complete';
            } elseif ($bulkStatus === 'Incomplete') {
                $computedStatus = 'Incomplete';
            } else {
                $computedStatus = 'Pending';
            }
        }
        $finalRecommendation = normalizeSemesterRecommendation(
            $activeSemester,
            $computedStatus,
            $gradeLevel,
            $bulkStatus
        );

        $saveStmt = $connection->prepare("
            INSERT INTO student_promotion_status
                (student_id, teacher_id, school_year, semester, computed_status, recommended_status, teacher_remarks, approval_status, is_approved, admin_user_id, admin_remarks, approved_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', 0, NULL, NULL, NULL)
            ON DUPLICATE KEY UPDATE
                teacher_id = VALUES(teacher_id),
                computed_status = VALUES(computed_status),
                recommended_status = VALUES(recommended_status),
                teacher_remarks = VALUES(teacher_remarks),
                approval_status = 'Pending',
                is_approved = 0,
                admin_user_id = NULL,
                admin_remarks = NULL,
                approved_at = NULL,
                updated_at = CURRENT_TIMESTAMP
        ");
        $saveStmt->bind_param(
            "iisssss",
            $studentId,
            $teacher_id,
            $activeSchoolYear,
            $activeSemester,
            $computedStatus,
            $finalRecommendation,
            $bulkRemarks
        );

        if ($saveStmt->execute()) {
            $savedCount++;
        }
        $saveStmt->close();
    }

    if ($savedCount > 0) {
        $message = "Saved {$savedCount} bulk recommendation(s) for {$activeSemester} ({$activeSchoolYear}). Waiting for admin validation.";
        $message_type = 'success';
    } else {
        $message = "No students were updated. Select students first.";
        $message_type = 'error';
    }
}
?>
