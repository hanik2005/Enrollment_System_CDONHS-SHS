<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

$teacherStmt = $connection->prepare("
    SELECT u.user_id, t.teacher_id, t.first_name, t.last_name, t.middle_name, t.extension_name
    FROM users u
    INNER JOIN teachers t ON t.user_id = u.user_id
    WHERE u.user_id = ? AND u.role_id = 3
");
$teacherStmt->bind_param("i", $_SESSION['user_id']);
$teacherStmt->execute();
$teacherResult = $teacherStmt->get_result();
$teacher = $teacherResult->fetch_assoc();
$teacherStmt->close();

if (!$teacher) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$teacherId = (int) $teacher['teacher_id'];
$profileImagePath = "../../Assets/profile_button.png";

include "../../Back_End_Files/PHP_Files/get_teacher_advisory.php";

$summaryStats = [
    'total_students' => 0,
    'enlisted' => 0,
    'pending' => 0,
    'missing_docs' => 0,
    'promoted' => 0,
];

$summaryRows = [];

function buildStudentFullName(array $row): string
{
    $fullName = trim(($row['last_name'] ?? '') . ', ' . ($row['first_name'] ?? ''));

    if (!empty($row['middle_name'])) {
        $fullName .= ' ' . strtoupper(substr((string) $row['middle_name'], 0, 1)) . '.';
    }
    if (!empty($row['extension_name'])) {
        $fullName .= ' ' . trim((string) $row['extension_name']);
    }

    return trim($fullName);
}

if (!empty($advisorySectionId)) {
    $summaryStmt = $connection->prepare("
        SELECT
            s.student_id,
            sa.lrn,
            sa.first_name,
            sa.last_name,
            sa.middle_name,
            sa.extension_name,
            ss.grade_level,
            st.strand_name,
            sec.section_name,
            s.enrollment_status,
            s.enlistment_status,
            COALESCE(sd.psa_birth_certificate, '') AS psa_birth_certificate,
            COALESCE(sd.form_138, '') AS form_138,
            COALESCE(sd.student_id_copy, '') AS student_id_copy
        FROM students s
        INNER JOIN student_strand ss
            ON ss.student_id = s.student_id
        INNER JOIN student_applications sa
            ON sa.application_id = s.application_id
        LEFT JOIN strands st
            ON st.strand_id = ss.strand_id
        LEFT JOIN section sec
            ON sec.section_id = ss.section_id
        LEFT JOIN student_documents sd
            ON sd.application_id = sa.application_id
        WHERE ss.section_id = ?
          AND COALESCE(s.enrollment_status, '') <> 'Graduated'
          AND COALESCE(s.enlistment_status, '') <> 'Finished'
        ORDER BY sa.last_name ASC, sa.first_name ASC
    ");

    $summaryStmt->bind_param("i", $advisorySectionId);
    $summaryStmt->execute();
    $summaryResult = $summaryStmt->get_result();
    $rows = $summaryResult ? $summaryResult->fetch_all(MYSQLI_ASSOC) : [];
    $summaryStmt->close();

    $uniqueStudents = [];
    foreach ($rows as $row) {
        $uniqueStudents[(int) $row['student_id']] = $row;
    }

    foreach ($uniqueStudents as $row) {
        $summaryStats['total_students']++;

        $enlistmentStatus = trim((string) ($row['enlistment_status'] ?? ''));
        $enrollmentStatus = trim((string) ($row['enrollment_status'] ?? ''));

        if (strcasecmp($enlistmentStatus, 'Enlisted') === 0) {
            $summaryStats['enlisted']++;
        }
        if (strcasecmp($enlistmentStatus, 'Pending') === 0) {
            $summaryStats['pending']++;
        }
        if (strcasecmp($enlistmentStatus, 'Promoted') === 0) {
            $summaryStats['promoted']++;
        }

        $missingDocs = [];
        if (trim((string) $row['psa_birth_certificate']) === '') {
            $missingDocs[] = 'Birth Certificate';
        }
        if (trim((string) $row['form_138']) === '') {
            $missingDocs[] = 'Form 138';
        }
        if (trim((string) $row['student_id_copy']) === '') {
            $missingDocs[] = 'Student ID';
        }

        if (!empty($missingDocs)) {
            $summaryStats['missing_docs']++;
        }

        $summaryRows[] = [
            'student_id' => (int) $row['student_id'],
            'lrn' => (string) ($row['lrn'] ?? ''),
            'full_name' => buildStudentFullName($row),
            'grade_level' => (int) ($row['grade_level'] ?? 0),
            'strand_name' => (string) ($row['strand_name'] ?? ''),
            'section_name' => (string) ($row['section_name'] ?? ''),
            'enrollment_status' => $enrollmentStatus === '' ? 'Unknown' : $enrollmentStatus,
            'enlistment_status' => $enlistmentStatus === '' ? 'Unknown' : $enlistmentStatus,
            'missing_docs_count' => count($missingDocs),
            'missing_docs_text' => empty($missingDocs) ? 'Complete' : implode(', ', $missingDocs),
        ];
    }
}
?>
