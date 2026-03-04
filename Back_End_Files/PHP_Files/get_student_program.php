<?php
if (!isset($_SESSION)) {
    session_start();
}

include "../../DB_Connection/Connection.php";

// Default values
$student_id = null;
$isEnlisted = false;
$isPending = false;
$isRejected = false;
$Promoted = false;
$gradeLevel = null;
$trackName = null;
$sectionName = null;

// Get student_id
$stmtStudent = $connection->prepare("
    SELECT student_id, enlistment_status 
    FROM students 
    WHERE user_id = ?
");
$stmtStudent->bind_param("i", $_SESSION['user_id']);
$stmtStudent->execute();
$resStudent = $stmtStudent->get_result();
$studentRow = $resStudent->fetch_assoc();
$stmtStudent->close();

if ($studentRow) {
    $student_id = $studentRow['student_id'];
    if ($studentRow['enlistment_status'] === 'Enlisted') {
        $isEnlisted = true;

        // Get program info from student_track (new curriculum)
        $stmtProgram = $connection->prepare("
            SELECT st.grade_level, t.track_name, sec.section_name
            FROM student_track st
            LEFT JOIN tracks t ON st.track_id = t.track_id
            LEFT JOIN section sec ON st.section_id = sec.section_id
            WHERE st.student_id = ?
            ORDER BY st.grade_level DESC
            LIMIT 1
        ");
        $stmtProgram->bind_param("i", $student_id);
        $stmtProgram->execute();
        $resProgram = $stmtProgram->get_result();
        if ($row = $resProgram->fetch_assoc()) {
            $gradeLevel = $row['grade_level'];
            $trackName = $row['track_name'];
            $sectionName = $row['section_name'];
        }
        $stmtProgram->close();
    } elseif ($studentRow['enlistment_status'] === 'Pending') {
        $isPending = true;
    } elseif ($studentRow['enlistment_status'] === 'Rejected') {
        $isRejected = true;
    } elseif ($studentRow['enlistment_status'] === 'Promoted') {
        $Promoted = true;
    }
}
