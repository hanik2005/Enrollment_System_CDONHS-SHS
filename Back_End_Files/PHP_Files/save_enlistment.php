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
    $track_id    = isset($input['track_id']) ? (int)$input['track_id'] : null;
    $section_id  = isset($input['section_id']) ? (int)$input['section_id'] : null;
    $subjects    = isset($input['subjects']) && is_array($input['subjects']) ? $input['subjects'] : [];

    if (!$grade_level || !$track_id || !$section_id || empty($subjects)) {
        echo json_encode(['success' => false, 'message' => 'Incomplete data. Please provide grade_level, track_id, section_id, and at least one subject.']);
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

    // 2️⃣ Check current enrollment in student_track (new curriculum)
    $stmtCheck = $connection->prepare("
        SELECT track_id, grade_level, section_id
        FROM student_track
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
        // Promotion only (same track, higher grade)
        if ($current['grade_level'] < $grade_level && $current['track_id'] == $track_id) {
            $stmtUpdate = $connection->prepare("
                UPDATE student_track
                SET grade_level = ?, section_id = ?
                WHERE student_id = ? AND track_id = ?
            ");
            $stmtUpdate->bind_param("iiii", $grade_level, $section_id, $student_id, $track_id);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }
        // Track change - need to withdraw old subjects
        elseif ($current['track_id'] != $track_id) {
            // Archive old track info
            $stmtArchive = $connection->prepare("
                INSERT INTO archived_student_strand
                (student_id, strand_id, grade_level, section_id, reason)
                VALUES (?, ?, ?, ?, 'TRACK_CHANGE')
            ");
            // Use track_id as strand_id for archive compatibility
            $stmtArchive->bind_param("iiii", $student_id, $current['track_id'], $current['grade_level'], $current['section_id']);
            $stmtArchive->execute();
            $stmtArchive->close();

            // Delete old track
            $stmtDelete = $connection->prepare("
                DELETE FROM student_track
                WHERE student_id = ? AND track_id = ?
            ");
            $stmtDelete->bind_param("ii", $student_id, $current['track_id']);
            $stmtDelete->execute();
            $stmtDelete->close();

            // Insert new track
            $stmtInsert = $connection->prepare("
                INSERT INTO student_track (student_id, track_id, grade_level, section_id)
                VALUES (?, ?, ?, ?)
            ");
            $stmtInsert->bind_param("iiii", $student_id, $track_id, $grade_level, $section_id);
            $stmtInsert->execute();
            $stmtInsert->close();
            
            // 🆕 WITHDRAW OLD SUBJECTS
            // Get old subjects that are Enrolled or Pending
            $stmtOldSubj = $connection->prepare("
                SELECT subject_id
                FROM student_subjects
                WHERE student_id = ? 
                AND status IN ('Enrolled', 'Pending')
            ");
            $stmtOldSubj->bind_param("i", $student_id);
            $stmtOldSubj->execute();
            $resultOldSubj = $stmtOldSubj->get_result();
            $oldSubjects = $resultOldSubj->fetch_all(MYSQLI_ASSOC);
            $stmtOldSubj->close();
            
            // For each old subject, check if there are grades
            foreach ($oldSubjects as $oldSubj) {
                $old_subject_id = $oldSubj['subject_id'];
                
                // Check if there are grades for this subject
                $stmtCheckGrades = $connection->prepare("
                    SELECT COUNT(*) as grade_count
                    FROM grade_entry
                    WHERE student_id = ? AND subject_id = ?
                ");
                $stmtCheckGrades->bind_param("ii", $student_id, $old_subject_id);
                $stmtCheckGrades->execute();
                $gradeResult = $stmtCheckGrades->get_result()->fetch_assoc();
                $stmtCheckGrades->close();
                
                // Set status based on whether grades exist
                if ($gradeResult['grade_count'] > 0) {
                    $newStatus = 'Withdrawn with Grades';
                } else {
                    $newStatus = 'Withdrawn';
                }
                
                // Update the subject status
                $stmtUpdateStatus = $connection->prepare("
                    UPDATE student_subjects
                    SET status = ?
                    WHERE student_id = ? AND subject_id = ?
                ");
                $stmtUpdateStatus->bind_param("sii", $newStatus, $student_id, $old_subject_id);
                $stmtUpdateStatus->execute();
                $stmtUpdateStatus->close();
            }
        }
        // Already enrolled in same grade & track
        else {
            // Just update section
            $stmtUpdate = $connection->prepare("
                UPDATE student_track
                SET section_id = ?
                WHERE student_id = ? AND track_id = ?
            ");
            $stmtUpdate->bind_param("iii", $section_id, $student_id, $track_id);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }
    } else {
        // First enrollment - insert into student_track
        $stmtInsert = $connection->prepare("
            INSERT INTO student_track (student_id, track_id, grade_level, section_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmtInsert->bind_param("iiii", $student_id, $track_id, $grade_level, $section_id);
        $stmtInsert->execute();
        $stmtInsert->close();
    }

    // 3️⃣ Insert/update subjects with Pending status (using subject_new_id from new curriculum)
    $stmtSubj = $connection->prepare("
        INSERT INTO student_subjects (student_id, subject_id, status, requested, school_year)
        VALUES (?, ?, 'Pending', ?, ?)
        ON DUPLICATE KEY UPDATE 
            status = 'Pending',
            requested = VALUES(requested),
            school_year = VALUES(school_year)
    ");
    
    if (!$stmtSubj) {
        echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $connection->error]);
        exit;
    }
    
    foreach ($subjects as $subject) {
        // Handle both old format (simple ID) and new format (object with subject_id and requested)
        if (is_array($subject)) {
            $subject_id = (int)$subject['subject_id'];
            $requested = (int)$subject['requested'];
        } else {
            // Old format: just a subject ID (assume requested = 1)
            $subject_id = (int)$subject;
            $requested = 1;
        }
        
        // Only insert if requested (checked)
        if ($requested) {
            $stmtSubj->bind_param("iiis", $student_id, $subject_id, $requested, $school_year);
            $stmtSubj->execute();
        }
    }
    $stmtSubj->close();

    // 4️⃣ Update enlistment_status
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
