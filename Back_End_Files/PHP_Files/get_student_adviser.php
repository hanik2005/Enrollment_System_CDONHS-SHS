<?php
include "../../DB_Connection/Connection.php";

/* ===============================
   GET STUDENT'S ADVISER
   This file gets the adviser information for a specific student
   Used in student profile to show who their adviser is
============================== */

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;
$school_year = isset($_GET['school_year']) ? $_GET['school_year'] : '2025-2026';

if (!$student_id) {
    echo json_encode(['success' => false, 'message' => 'Student ID required']);
    exit;
}

// Get student's current section and strand info
$studentQuery = "
    SELECT 
        ss.student_strand_id,
        ss.grade_level,
        ss.strand_id,
        ss.section_id,
        s.strand_name,
        sec.section_name,
        sec.grade_level as section_grade_level
    FROM student_strand ss
    JOIN strands s ON ss.strand_id = s.strand_id
    JOIN section sec ON ss.section_id = sec.section_id
    WHERE ss.student_id = ?
    LIMIT 1
";

$stmt = $connection->prepare($studentQuery);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$studentResult = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$studentResult) {
    echo json_encode(['success' => false, 'message' => 'Student not enrolled in any section']);
    exit;
}

$grade_level = $studentResult['grade_level'];
$strand_id = $studentResult['strand_id'];
$section_id = $studentResult['section_id'];

// First try to get adviser from teacher_advisory table
$adviserQuery = "
    SELECT 
        t.teacher_id,
        t.first_name,
        t.last_name,
        t.middle_name,
        ta.advisory_id,
        ta.school_year as advisory_school_year
    FROM teacher_advisory ta
    JOIN teachers t ON ta.teacher_id = t.teacher_id
    WHERE ta.section_id = ?
    AND ta.grade_level = ?
    AND ta.strand_id = ?
    AND ta.school_year = ?
    LIMIT 1
";

$stmt = $connection->prepare($adviserQuery);
$stmt->bind_param("iiis", $section_id, $grade_level, $strand_id, $school_year);
$stmt->execute();
$adviserResult = $stmt->get_result()->fetch_assoc();
$stmt->close();

// If no result from teacher_advisory, try section.adviser_id
if (!$adviserResult) {
    $sectionQuery = "
        SELECT 
            t.teacher_id,
            t.first_name,
            t.last_name,
            t.middle_name,
            sec.adviser_id
        FROM section sec
        JOIN teachers t ON sec.adviser_id = t.teacher_id
        WHERE sec.section_id = ?
        LIMIT 1
    ";
    
    $stmt = $connection->prepare($sectionQuery);
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $sectionResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($sectionResult && $sectionResult['adviser_id']) {
        $adviserResult = [
            'teacher_id' => $sectionResult['teacher_id'],
            'first_name' => $sectionResult['first_name'],
            'last_name' => $sectionResult['last_name'],
            'middle_name' => $sectionResult['middle_name'],
            'adviser_id' => $sectionResult['adviser_id']
        ];
    }
}

if ($adviserResult) {
    $fullName = trim($adviserResult['first_name'] . ' ' . 
        ($adviserResult['middle_name'] ? $adviserResult['middle_name'] . ' ' : '') . 
        $adviserResult['last_name']);
    
    echo json_encode([
        'success' => true,
        'adviser' => [
            'teacher_id' => $adviserResult['teacher_id'],
            'full_name' => $fullName,
            'first_name' => $adviserResult['first_name'],
            'last_name' => $adviserResult['last_name'],
            'middle_name' => $adviserResult['middle_name']
        ],
        'student_info' => $studentResult
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'No adviser assigned to this section',
        'student_info' => $studentResult
    ]);
}
