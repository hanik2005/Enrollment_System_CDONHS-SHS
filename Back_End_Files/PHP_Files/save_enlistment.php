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
    $strand_id   = isset($input['strand_id']) ? (int)$input['strand_id'] : null;
    $section_id  = isset($input['section_id']) ? (int)$input['section_id'] : null;

    if (!$grade_level || !$strand_id || !$section_id) {
        echo json_encode(['success' => false, 'message' => 'Incomplete data. Please provide grade_level, strand_id, and section_id.']);
        exit;
    }

    $userID = $_SESSION['user_id'];

    // Enlistment status
    $enlistment_status = 'Pending';

    // Start transaction
    $connection->begin_transaction();

    // 1️⃣ Find or create student and get their school_year
    $stmtStudent = $connection->prepare("
        SELECT student_id, school_year FROM students WHERE user_id = ?
    ");
    $stmtStudent->bind_param("i", $userID);
    $stmtStudent->execute();
    $resStudent = $stmtStudent->get_result();
    $studentRow = $resStudent->fetch_assoc();
    $stmtStudent->close();

    if ($studentRow) {
        $student_id = $studentRow['student_id'];
        $school_year = $studentRow['school_year'];
    } else {
        // Insert new student - calculate school year for new students
        // Philippine school year runs from August to May/June
        $currentMonth = (int)date('n');
        $currentYear = (int)date('Y');
        
        if ($currentMonth >= 8) {
            $school_year = $currentYear . '-' . ($currentYear + 1);
        } else {
            $school_year = ($currentYear - 1) . '-' . $currentYear;
        }
        
        $stmtInsertStudent = $connection->prepare("
            INSERT INTO students (enlistment_status, school_year) VALUES (?, ?)
        ");
        $stmtInsertStudent->bind_param("ss", $enlistment_status, $school_year);
        $stmtInsertStudent->execute();
        $student_id = $stmtInsertStudent->insert_id;
        $stmtInsertStudent->close();
    }

    // 2️⃣ Check current enrollment in student_strand
    $stmtCheck = $connection->prepare("
        SELECT strand_id, grade_level, section_id
        FROM student_strand
        WHERE student_id = ?
        ORDER BY grade_level DESC
        LIMIT 1
    ");
    $stmtCheck->bind_param("i", $student_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    $current = $resCheck->fetch_assoc();
    $stmtCheck->close();

    if ($current) {
        // Student already has enlistment - UPDATE the record
        $stmtUpdate = $connection->prepare("
            UPDATE student_strand
            SET strand_id = ?, grade_level = ?, section_id = ?
            WHERE student_id = ?
        ");
        $stmtUpdate->bind_param("iiii", $strand_id, $grade_level, $section_id, $student_id);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    } else {
        // First enrollment - INSERT into student_strand
        $stmtInsert = $connection->prepare("
            INSERT INTO student_strand (student_id, strand_id, grade_level, section_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmtInsert->bind_param("iiii", $student_id, $strand_id, $grade_level, $section_id);
        $stmtInsert->execute();
        $stmtInsert->close();
    }

    // Commit transaction
    $stmtStatus = $connection->prepare("
        UPDATE students
        SET enlistment_status = ?
        WHERE student_id = ?
    ");
    $stmtStatus->bind_param("si", $enlistment_status, $student_id);
    $stmtStatus->execute();
    $stmtStatus->close();

    // Commit transaction
    $connection->commit();

    echo json_encode([
        'success' => true,
        'status' => $enlistment_status,
        'message' => 'Enlistment submitted successfully!'
    ]);

} catch (Exception $e) {
    $connection->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
