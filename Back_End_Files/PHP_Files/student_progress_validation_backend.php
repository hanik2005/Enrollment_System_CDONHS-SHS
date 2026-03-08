<?php
session_start();

include "../../DB_Connection/Connection.php";
include_once "audit_trail_helper.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Website_Files/login.php");
    exit;
}

$adminUserId = (int) $_SESSION['user_id'];
$adminStmt = $connection->prepare("
    SELECT user_id
    FROM users
    WHERE user_id = ? AND role_id = 2
");
$adminStmt->bind_param("i", $adminUserId);
$adminStmt->execute();
$admin = $adminStmt->get_result()->fetch_assoc();
$adminStmt->close();

if (!$admin) {
    header("Location: ../../Website_Files/login.php");
    exit;
}

function getCurrentSchoolYearForValidation(): string
{
    $month = (int) date('n');
    $year = (int) date('Y');
    if ($month >= 8) {
        return $year . '-' . ($year + 1);
    }
    return ($year - 1) . '-' . $year;
}

function getCurrentSemesterForValidation(): string
{
    $month = (int) date('n');
    return ($month >= 8 && $month <= 12) ? '1st Semester' : '2nd Semester';
}

function getNextSchoolYearValue(string $schoolYear): string
{
    if (preg_match('/^(\d{4})-(\d{4})$/', $schoolYear, $m)) {
        $start = (int) $m[1] + 1;
        $end = (int) $m[2] + 1;
        return $start . '-' . $end;
    }
    $year = (int) date('Y');
    return $year . '-' . ($year + 1);
}

function ensureProgressWorkflowTables(mysqli $connection): bool
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

function applyPromotionOrGraduation(mysqli $connection, array $record, int $adminUserId): string
{
    $studentId = (int) $record['student_id'];
    $gradeLevel = (int) $record['grade_level'];
    $schoolYear = (string) $record['school_year'];
    $recommendedStatus = (string) $record['recommended_status'];
    $semester = (string) $record['semester'];
    $computedStatus = (string) $record['computed_status'];

    if ($semester === '1st Semester') {
        if (
            $computedStatus === 'Complete' &&
            ($recommendedStatus === 'Promote to 2nd Semester' || $recommendedStatus === 'Complete')
        ) {
            $checkSecondSem = $connection->prepare("
                SELECT student_strand_id
                FROM student_strand
                WHERE student_id = ? AND school_year = ? AND semester = '2nd Semester'
                LIMIT 1
            ");
            $checkSecondSem->bind_param("is", $studentId, $schoolYear);
            $checkSecondSem->execute();
            $secondSemExisting = $checkSecondSem->get_result()->fetch_assoc();
            $checkSecondSem->close();

            if ($secondSemExisting) {
                return 'Already in 2nd Semester (no duplicate transition applied).';
            }

            $updateToSecondSem = $connection->prepare("
                UPDATE student_strand
                SET semester = '2nd Semester'
                WHERE student_id = ? AND school_year = ? AND semester = '1st Semester'
            ");
            $updateToSecondSem->bind_param("is", $studentId, $schoolYear);
            $updateToSecondSem->execute();
            $affected = $updateToSecondSem->affected_rows;
            $updateToSecondSem->close();

            if ($affected > 0) {
                logAdminAudit(
                    $connection,
                    'STUDENT_SEMESTER_PROMOTED',
                    'student_strand',
                    (string) $studentId,
                    "Approved semester promotion for student #{$studentId} to 2nd Semester",
                    [
                        'school_year' => $schoolYear,
                        'semester_from' => '1st Semester',
                        'semester_to' => '2nd Semester',
                        'approved_by' => $adminUserId
                    ],
                    $adminUserId
                );

                return 'Promoted to 2nd Semester.';
            }

            return 'Semester promotion failed: no 1st Semester strand record found.';
        }

        if ($recommendedStatus === 'Incomplete' || $computedStatus === 'Incomplete') {
            return 'Marked as Incomplete (no semester promotion).';
        }

        return 'No semester transition applied.';
    }

    if ($semester !== '2nd Semester' || $computedStatus !== 'Complete') {
        return 'No transition applied (non-final term or incomplete status).';
    }

    if ($recommendedStatus === 'Promote to Grade 12' && $gradeLevel === 11) {
        $currentStmt = $connection->prepare("
            SELECT strand_id, section_id, grade_level
            FROM student_strand
            WHERE student_id = ? AND school_year = ? AND semester = '2nd Semester'
            LIMIT 1
        ");
        $currentStmt->bind_param("is", $studentId, $schoolYear);
        $currentStmt->execute();
        $currentInfo = $currentStmt->get_result()->fetch_assoc();
        $currentStmt->close();

        if (!$currentInfo) {
            return 'Promotion failed: no current strand record found.';
        }

        $strandId = (int) $currentInfo['strand_id'];

        $sectionStmt = $connection->prepare("
            SELECT s.section_id
            FROM section s
            WHERE s.grade_level = 12 AND s.strand_id = ?
            ORDER BY s.section_name ASC
            LIMIT 1
        ");
        $sectionStmt->bind_param("i", $strandId);
        $sectionStmt->execute();
        $sectionInfo = $sectionStmt->get_result()->fetch_assoc();
        $sectionStmt->close();

        if (!$sectionInfo) {
            return 'Promotion failed: no Grade 12 section found for strand.';
        }

        $nextSchoolYear = getNextSchoolYearValue($schoolYear);
        $newSectionId = (int) $sectionInfo['section_id'];

        $updateStrand = $connection->prepare("
            UPDATE student_strand
            SET grade_level = 12, section_id = ?, semester = '1st Semester', school_year = ?
            WHERE student_id = ? AND school_year = ? AND semester = '2nd Semester'
        ");
        $updateStrand->bind_param("isis", $newSectionId, $nextSchoolYear, $studentId, $schoolYear);
        $updateStrand->execute();
        $updateStrand->close();

        $updateStudent = $connection->prepare("
            UPDATE students
            SET enlistment_status = 'Promoted', school_year = ?
            WHERE student_id = ?
        ");
        $updateStudent->bind_param("si", $nextSchoolYear, $studentId);
        $updateStudent->execute();
        $updateStudent->close();

        logAdminAudit(
            $connection,
            'STUDENT_PROMOTED',
            'students',
            (string) $studentId,
            "Approved teacher recommendation and promoted student #{$studentId} to Grade 12",
            [
                'teacher_recommended_status' => $recommendedStatus,
                'school_year_from' => $schoolYear,
                'school_year_to' => $nextSchoolYear,
                'approved_by' => $adminUserId
            ],
            $adminUserId
        );

        return 'Promoted to Grade 12.';
    }

    if ($recommendedStatus === 'Graduate' && $gradeLevel === 12) {
        $updateStudent = $connection->prepare("
            UPDATE students
            SET enrollment_status = 'Graduated', enlistment_status = 'Finished'
            WHERE student_id = ?
        ");
        $updateStudent->bind_param("i", $studentId);
        $updateStudent->execute();
        $updateStudent->close();

        logAdminAudit(
            $connection,
            'STUDENT_GRADUATED',
            'students',
            (string) $studentId,
            "Approved teacher recommendation and marked student #{$studentId} as Graduated",
            [
                'teacher_recommended_status' => $recommendedStatus,
                'approved_by' => $adminUserId
            ],
            $adminUserId
        );

        return 'Marked as Graduated.';
    }

    if ($recommendedStatus === 'Incomplete') {
        return 'Marked as Incomplete (no promotion).';
    }

    return 'No promotion action applied.';
}

$tablesReady = ensureProgressWorkflowTables($connection);

if (isset($_POST['confirm_validation']) && $tablesReady) {
    $selectedRecords = array_map('intval', $_POST['selected_records'] ?? []);
    $decisions = $_POST['decision'] ?? [];
    $adminRemarks = $_POST['admin_remarks'] ?? [];

    $approvedCount = 0;
    $rejectedCount = 0;
    $actionsApplied = 0;

    foreach ($selectedRecords as $recordId) {
        if ($recordId <= 0) {
            continue;
        }

        $decision = $decisions[$recordId] ?? 'Pending';
        $remark = trim((string) ($adminRemarks[$recordId] ?? ''));

        if (!in_array($decision, ['Approved', 'Rejected'], true)) {
            continue;
        }

        $recordStmt = $connection->prepare("
            SELECT
                sps.promotion_status_id,
                sps.student_id,
                sps.school_year,
                sps.semester,
                sps.computed_status,
                sps.recommended_status,
                ss.grade_level
            FROM student_promotion_status sps
            LEFT JOIN student_strand ss
                ON ss.student_id = sps.student_id
                AND ss.school_year = sps.school_year
                AND ss.semester = sps.semester
            WHERE sps.promotion_status_id = ?
            LIMIT 1
        ");
        $recordStmt->bind_param("i", $recordId);
        $recordStmt->execute();
        $record = $recordStmt->get_result()->fetch_assoc();
        $recordStmt->close();

        if (!$record) {
            continue;
        }

        $approvalFlag = ($decision === 'Approved') ? 1 : 0;
        $approvalStatus = $decision;
        $approvedAt = ($decision === 'Approved') ? date('Y-m-d H:i:s') : null;

        $updateStmt = $connection->prepare("
            UPDATE student_promotion_status
            SET approval_status = ?, is_approved = ?, admin_user_id = ?, admin_remarks = ?, approved_at = ?, updated_at = CURRENT_TIMESTAMP
            WHERE promotion_status_id = ?
        ");
        $updateStmt->bind_param("siissi", $approvalStatus, $approvalFlag, $adminUserId, $remark, $approvedAt, $recordId);
        $updateStmt->execute();
        $updateStmt->close();

        if ($decision === 'Approved') {
            $approvedCount++;
            $actionResult = applyPromotionOrGraduation($connection, $record, $adminUserId);
            if (in_array($actionResult, ['Promoted to 2nd Semester.', 'Promoted to Grade 12.', 'Marked as Graduated.'], true)) {
                $actionsApplied++;
            }

            logAdminAudit(
                $connection,
                'TEACHER_PROGRESS_APPROVED',
                'student_promotion_status',
                (string) $recordId,
                "Approved teacher student-progress recommendation #{$recordId}",
                [
                    'decision' => $decision,
                    'remark' => $remark,
                    'action_result' => $actionResult
                ],
                $adminUserId
            );
        } else {
            $rejectedCount++;
            logAdminAudit(
                $connection,
                'TEACHER_PROGRESS_REJECTED',
                'student_promotion_status',
                (string) $recordId,
                "Rejected teacher student-progress recommendation #{$recordId}",
                [
                    'decision' => $decision,
                    'remark' => $remark
                ],
                $adminUserId
            );
        }
    }

    $query = http_build_query([
        'success' => 1,
        'approved' => $approvedCount,
        'rejected' => $rejectedCount,
        'actions' => $actionsApplied
    ]);
    header("Location: ../../Website_Files/Admin_Files/student_progress_validation_page.php?" . $query);
    exit;
}

$searchName = isset($_GET['search_name']) ? trim((string) $_GET['search_name']) : '';
$schoolYearFilter = isset($_GET['school_year']) ? trim((string) $_GET['school_year']) : getCurrentSchoolYearForValidation();
$semesterFilter = isset($_GET['semester']) ? trim((string) $_GET['semester']) : getCurrentSemesterForValidation();
$approvalFilter = isset($_GET['approval_status']) ? trim((string) $_GET['approval_status']) : 'Pending';

$filterSql = "
    SELECT
        sps.promotion_status_id,
        sps.student_id,
        sps.school_year,
        sps.semester,
        sps.computed_status,
        sps.recommended_status,
        sps.teacher_remarks,
        sps.approval_status,
        sps.admin_remarks,
        sps.updated_at,
        sa.first_name,
        sa.last_name,
        sa.lrn,
        ss.grade_level,
        st.strand_name,
        sec.section_name,
        t.first_name AS teacher_first_name,
        t.last_name AS teacher_last_name
    FROM student_promotion_status sps
    INNER JOIN students s ON s.student_id = sps.student_id
    INNER JOIN student_applications sa ON sa.application_id = s.application_id
    LEFT JOIN student_strand ss
        ON ss.student_id = sps.student_id
        AND ss.school_year = sps.school_year
        AND ss.semester = sps.semester
    LEFT JOIN strands st ON st.strand_id = ss.strand_id
    LEFT JOIN section sec ON sec.section_id = ss.section_id
    LEFT JOIN teachers t ON t.teacher_id = sps.teacher_id
    WHERE 1 = 1
";

$params = [];
$types = '';

if ($searchName !== '') {
    $filterSql .= " AND (sa.first_name LIKE ? OR sa.last_name LIKE ? OR CONCAT(sa.first_name, ' ', sa.last_name) LIKE ?)";
    $searchTerm = '%' . $searchName . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

if ($schoolYearFilter !== '') {
    $filterSql .= " AND sps.school_year = ?";
    $params[] = $schoolYearFilter;
    $types .= 's';
}

if (in_array($semesterFilter, ['1st Semester', '2nd Semester'], true)) {
    $filterSql .= " AND sps.semester = ?";
    $params[] = $semesterFilter;
    $types .= 's';
}

if (in_array($approvalFilter, ['Pending', 'Approved', 'Rejected'], true)) {
    $filterSql .= " AND sps.approval_status = ?";
    $params[] = $approvalFilter;
    $types .= 's';
}

$filterSql .= " ORDER BY sps.updated_at DESC, sa.last_name ASC";

$recordsStmt = $connection->prepare($filterSql);
if (!empty($params)) {
    $recordsStmt->bind_param($types, ...$params);
}
$recordsStmt->execute();
$records = $recordsStmt->get_result();

$schoolYears = [];
$yearsResult = $connection->query("SELECT DISTINCT school_year FROM student_promotion_status ORDER BY school_year DESC");
if ($yearsResult) {
    while ($yearRow = $yearsResult->fetch_assoc()) {
        $schoolYears[] = $yearRow['school_year'];
    }
}

if (empty($schoolYears)) {
    $schoolYears[] = getCurrentSchoolYearForValidation();
}
?>
