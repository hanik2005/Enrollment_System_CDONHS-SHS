<?php
session_start();

// Disable HTML errors, log them instead
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

include "../../DB_Connection/Connection.php";

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $grade_level = isset($input['grade_level']) ? (int)$input['grade_level'] : null;
    $strand_id = isset($input['strand_id']) ? (int)$input['strand_id'] : null;
    $section_id = isset($input['section_id']) ? (int)$input['section_id'] : null;

    if (!$grade_level || !$strand_id || !$section_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Incomplete data. Please provide grade_level, strand_id, and section_id.'
        ]);
        exit;
    }

    $userID = (int)$_SESSION['user_id'];
    $enlistment_status = 'Pending';

    // Determine semester and school year from current date.
    $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $currentMonth = (int)$now->format('n');
    $currentYear = (int)$now->format('Y');

    if ($currentMonth >= 8) {
        $semester = '1st Semester';
        $school_year = $currentYear . '-' . ($currentYear + 1);
    } else {
        $semester = '2nd Semester';
        $school_year = ($currentYear - 1) . '-' . $currentYear;
    }

    $connection->begin_transaction();

    // Resolve student record from logged-in user.
    $stmtStudent = $connection->prepare(
        "SELECT student_id, enrollment_status FROM students WHERE user_id = ? LIMIT 1"
    );
    $stmtStudent->bind_param("i", $userID);
    $stmtStudent->execute();
    $resStudent = $stmtStudent->get_result();
    $studentRow = $resStudent->fetch_assoc();
    $stmtStudent->close();

    if (!$studentRow) {
        throw new Exception('Student record not found for this user.');
    }

    $student_id = (int)$studentRow['student_id'];

    if (($studentRow['enrollment_status'] ?? null) === 'Graduated') {
        throw new Exception('Graduated students cannot be enlisted again.');
    }

    // Validate section belongs to selected strand and grade.
    $stmtValidateSection = $connection->prepare(
        "SELECT section_id FROM section WHERE section_id = ? AND grade_level = ? AND strand_id = ? LIMIT 1"
    );
    $stmtValidateSection->bind_param("iii", $section_id, $grade_level, $strand_id);
    $stmtValidateSection->execute();
    $resValidateSection = $stmtValidateSection->get_result();
    $isValidSection = $resValidateSection->fetch_assoc();
    $stmtValidateSection->close();

    if (!$isValidSection) {
        throw new Exception('Invalid section for selected grade level and strand.');
    }

    // Upsert term-specific student strand assignment.
    $stmtCheck = $connection->prepare(
        "SELECT student_strand_id FROM student_strand WHERE student_id = ? AND semester = ? AND school_year = ? LIMIT 1"
    );
    $stmtCheck->bind_param("iss", $student_id, $semester, $school_year);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    $existingRow = $resCheck->fetch_assoc();
    $stmtCheck->close();

    if ($existingRow) {
        $student_strand_id = (int)$existingRow['student_strand_id'];

        $stmtUpdate = $connection->prepare(
            "UPDATE student_strand SET strand_id = ?, grade_level = ?, section_id = ? WHERE student_strand_id = ?"
        );
        $stmtUpdate->bind_param("iiii", $strand_id, $grade_level, $section_id, $student_strand_id);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    } else {
        $stmtInsert = $connection->prepare(
            "INSERT INTO student_strand (student_id, strand_id, grade_level, semester, school_year, section_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmtInsert->bind_param("iiissi", $student_id, $strand_id, $grade_level, $semester, $school_year, $section_id);
        $stmtInsert->execute();
        $stmtInsert->close();
    }

    // Keep student record aligned with current enlistment term.
    $stmtStatus = $connection->prepare(
        "UPDATE students SET enlistment_status = ?, school_year = ? WHERE student_id = ?"
    );
    $stmtStatus->bind_param("ssi", $enlistment_status, $school_year, $student_id);
    $stmtStatus->execute();
    $stmtStatus->close();

    $connection->commit();

    echo json_encode([
        'success' => true,
        'status' => $enlistment_status,
        'semester' => $semester,
        'school_year' => $school_year,
        'message' => 'Enlistment submitted successfully!'
    ]);
} catch (Exception $e) {
    $connection->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
