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
$teacher = $teacherStmt->get_result()->fetch_assoc();
$teacherStmt->close();

if (!$teacher) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/theme_preferences.php';
syncSessionThemePreference($connection, (int) $_SESSION['user_id']);

include "../../Back_End_Files/PHP_Files/get_teacher_advisory.php";

$teacherId = (int) $teacher['teacher_id'];
$profileImagePath = "../../Assets/profile_button.png";
$message = '';
$messageType = '';

function ensureTeacherStudentNotesTable(mysqli $connection): bool
{
    $sql = "
        CREATE TABLE IF NOT EXISTS teacher_student_notes (
            note_id INT(11) NOT NULL AUTO_INCREMENT,
            teacher_id INT(11) NOT NULL,
            student_id INT(11) NOT NULL,
            behavior_note TEXT DEFAULT NULL,
            follow_up_note TEXT DEFAULT NULL,
            intervention_note TEXT DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (note_id),
            UNIQUE KEY uq_teacher_student_note (teacher_id, student_id),
            KEY idx_teacher_student_notes_teacher (teacher_id),
            KEY idx_teacher_student_notes_student (student_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ";

    return $connection->query($sql) === true;
}

$notesTableReady = ensureTeacherStudentNotesTable($connection);
if (!$notesTableReady) {
    $message = "Unable to prepare advisory notes storage. " . $connection->error;
    $messageType = "error";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note']) && $notesTableReady) {
    $studentId = (int) ($_POST['student_id'] ?? 0);
    $behaviorNote = trim((string) ($_POST['behavior_note'] ?? ''));
    $followUpNote = trim((string) ($_POST['follow_up_note'] ?? ''));
    $interventionNote = trim((string) ($_POST['intervention_note'] ?? ''));

    if (empty($advisorySectionId)) {
        $message = "You cannot save notes because no advisory section is assigned.";
        $messageType = "error";
    } elseif ($studentId <= 0) {
        $message = "Invalid student record.";
        $messageType = "error";
    } else {
        $validateStmt = $connection->prepare("
            SELECT s.student_id
            FROM students s
            INNER JOIN student_strand ss ON ss.student_id = s.student_id
            WHERE s.student_id = ? AND ss.section_id = ?
            LIMIT 1
        ");
        $validateStmt->bind_param("ii", $studentId, $advisorySectionId);
        $validateStmt->execute();
        $allowedStudent = $validateStmt->get_result()->fetch_assoc();
        $validateStmt->close();

        if (!$allowedStudent) {
            $message = "You can only save notes for students in your advisory section.";
            $messageType = "error";
        } else {
            if ($behaviorNote === '' && $followUpNote === '' && $interventionNote === '') {
                $deleteStmt = $connection->prepare("
                    DELETE FROM teacher_student_notes
                    WHERE teacher_id = ? AND student_id = ?
                ");
                $deleteStmt->bind_param("ii", $teacherId, $studentId);

                if ($deleteStmt->execute()) {
                    $message = "Advisory notes cleared for selected student.";
                    $messageType = "success";
                } else {
                    $message = "Failed to clear notes. " . $connection->error;
                    $messageType = "error";
                }
                $deleteStmt->close();
            } else {
                $saveStmt = $connection->prepare("
                    INSERT INTO teacher_student_notes (teacher_id, student_id, behavior_note, follow_up_note, intervention_note)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        behavior_note = VALUES(behavior_note),
                        follow_up_note = VALUES(follow_up_note),
                        intervention_note = VALUES(intervention_note),
                        updated_at = CURRENT_TIMESTAMP
                ");
                $saveStmt->bind_param("iisss", $teacherId, $studentId, $behaviorNote, $followUpNote, $interventionNote);

                if ($saveStmt->execute()) {
                    $message = "Advisory notes saved successfully.";
                    $messageType = "success";
                } else {
                    $message = "Failed to save notes. " . $connection->error;
                    $messageType = "error";
                }
                $saveStmt->close();
            }
        }
    }
}

$notesStats = [
    'total_students' => 0,
    'students_with_notes' => 0,
];
$notesRows = [];

if (!empty($advisorySectionId) && $notesTableReady) {
    $fetchStmt = $connection->prepare("
        SELECT
            s.student_id,
            sa.lrn,
            sa.first_name,
            sa.last_name,
            sa.middle_name,
            sa.extension_name,
            ss.grade_level,
            st.strand_abbreviation,
            sec.section_name,
            s.enlistment_status,
            COALESCE(tsn.behavior_note, '') AS behavior_note,
            COALESCE(tsn.follow_up_note, '') AS follow_up_note,
            COALESCE(tsn.intervention_note, '') AS intervention_note,
            tsn.updated_at AS note_updated_at
        FROM students s
        INNER JOIN student_strand ss ON ss.student_id = s.student_id
        INNER JOIN student_applications sa ON sa.application_id = s.application_id
        LEFT JOIN strands st ON st.strand_id = ss.strand_id
        LEFT JOIN section sec ON sec.section_id = ss.section_id
        LEFT JOIN teacher_student_notes tsn ON tsn.student_id = s.student_id AND tsn.teacher_id = ?
        WHERE ss.section_id = ?
        ORDER BY sa.last_name ASC, sa.first_name ASC
    ");
    $fetchStmt->bind_param("ii", $teacherId, $advisorySectionId);
    $fetchStmt->execute();
    $fetchedRows = $fetchStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $fetchStmt->close();

    $uniqueRows = [];
    foreach ($fetchedRows as $row) {
        $uniqueRows[(int) $row['student_id']] = $row;
    }

    foreach ($uniqueRows as $row) {
        $notesStats['total_students']++;

        $fullName = trim(($row['last_name'] ?? '') . ', ' . ($row['first_name'] ?? ''));
        if (!empty($row['middle_name'])) {
            $fullName .= ' ' . strtoupper(substr((string) $row['middle_name'], 0, 1)) . '.';
        }
        if (!empty($row['extension_name'])) {
            $fullName .= ' ' . trim((string) $row['extension_name']);
        }

        $hasAnyNote = trim((string) $row['behavior_note']) !== ''
            || trim((string) $row['follow_up_note']) !== ''
            || trim((string) $row['intervention_note']) !== '';

        if ($hasAnyNote) {
            $notesStats['students_with_notes']++;
        }

        $notesRows[] = [
            'student_id' => (int) $row['student_id'],
            'full_name' => $fullName,
            'lrn' => (string) ($row['lrn'] ?? ''),
            'grade_level' => (int) ($row['grade_level'] ?? 0),
            'strand_abbreviation' => (string) ($row['strand_abbreviation'] ?? ''),
            'section_name' => (string) ($row['section_name'] ?? ''),
            'enlistment_status' => (string) ($row['enlistment_status'] ?? 'Unknown'),
            'behavior_note' => (string) $row['behavior_note'],
            'follow_up_note' => (string) $row['follow_up_note'],
            'intervention_note' => (string) $row['intervention_note'],
            'note_updated_at' => $row['note_updated_at'],
            'has_any_note' => $hasAnyNote,
        ];
    }
}
?>
