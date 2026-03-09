<?php
session_start();
include "../../DB_Connection/Connection.php";
include_once "admin_access.php";

$admin = requireSuperAdminAccess($connection, "../login.php");
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);

/* ==================== HANDLING FORM SUBMISSION ==================== */

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_adviser'])) {
    
    $teacher_id = (int)$_POST['teacher_id'];
    $section_id = (int)$_POST['section_id'];
    $grade_level = (int)$_POST['grade_level'];
    $strand_id = (int)$_POST['strand_id'];
    $school_year = $_POST['school_year'];
    
    // Check if advisory already exists for this section/strand/grade/school_year
    $checkStmt = $connection->prepare("
        SELECT advisory_id FROM teacher_advisory 
        WHERE section_id = ? AND strand_id = ? AND grade_level = ? AND school_year = ?
    ");
    $checkStmt->bind_param("iiis", $section_id, $strand_id, $grade_level, $school_year);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Update existing advisory
        $updateStmt = $connection->prepare("
            UPDATE teacher_advisory 
            SET teacher_id = ?, assigned_date = CURRENT_TIMESTAMP
            WHERE section_id = ? AND strand_id = ? AND grade_level = ? AND school_year = ?
        ");
        $updateStmt->bind_param("iiiis", $teacher_id, $section_id, $strand_id, $grade_level, $school_year);
        
        if ($updateStmt->execute()) {
            $message = 'Adviser updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error updating adviser: ' . $connection->error;
            $message_type = 'error';
        }
        $updateStmt->close();
    } else {
        // Insert new advisory
        $insertStmt = $connection->prepare("
            INSERT INTO teacher_advisory (teacher_id, section_id, grade_level, strand_id, school_year)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertStmt->bind_param("iiiis", $teacher_id, $section_id, $grade_level, $strand_id, $school_year);
        
        if ($insertStmt->execute()) {
            $message = 'Adviser assigned successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error assigning adviser: ' . $connection->error;
            $message_type = 'error';
        }
        $insertStmt->close();
    }
    $checkStmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_advisory'])) {
    $advisory_id = (int)$_POST['advisory_id'];
    
    $deleteStmt = $connection->prepare("DELETE FROM teacher_advisory WHERE advisory_id = ?");
    $deleteStmt->bind_param("i", $advisory_id);
    
    if ($deleteStmt->execute()) {
        $message = 'Adviser assignment removed successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error removing adviser: ' . $connection->error;
        $message_type = 'error';
    }
    $deleteStmt->close();
}

/* ==================== FETCH DATA FOR FILTERS ==================== */

// Get all teachers
$teachersQuery = $connection->query("
    SELECT teacher_id, first_name, last_name, middle_name 
    FROM teachers 
    ORDER BY last_name, first_name
");
$teachers = [];
while ($t = $teachersQuery->fetch_assoc()) {
    $teachers[] = $t;
}

// Get all strands
$strandsQuery = $connection->query("SELECT strand_id, strand_name FROM strands ORDER BY strand_name");
$strands = [];
while ($s = $strandsQuery->fetch_assoc()) {
    $strands[] = $s;
}

// Get all sections
$sectionsQuery = $connection->query("SELECT section_id, section_name, grade_level, strand_id FROM section ORDER BY section_name");
$sections = [];
while ($sec = $sectionsQuery->fetch_assoc()) {
    $sections[] = $sec;
}

// Get current school year (you can adjust this logic)
$current_school_year = '2025-2026';

// Fetch existing advisories
$advisoriesQuery = $connection->query("
    SELECT 
        ta.advisory_id,
        ta.teacher_id,
        ta.section_id,
        ta.grade_level,
        ta.strand_id,
        ta.school_year,
        t.first_name,
        t.last_name,
        t.middle_name,
        sec.section_name,
        s.strand_name
    FROM teacher_advisory ta
    JOIN teachers t ON ta.teacher_id = t.teacher_id
    JOIN section sec ON ta.section_id = sec.section_id
    JOIN strands s ON ta.strand_id = s.strand_id
    ORDER BY ta.grade_level, s.strand_name, sec.section_name
");
$advisories = [];
while ($adv = $advisoriesQuery->fetch_assoc()) {
    $advisories[] = $adv;
}
