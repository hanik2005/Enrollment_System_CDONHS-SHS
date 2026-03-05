<?php
include "../../DB_Connection/Connection.php";
/* ===============================
   GET TEACHER ADVISORY
   This file gets the advisory information for a teacher
============================== */

if (!isset($_SESSION['user_id'])) {
    $advisoryText = "No Advisory Assigned";
    $advisorySectionId = null;
    $advisoryGradeLevel = null;
    $advisoryStrandId = null;
    return;
}

$getTeacher = $connection->prepare("
    SELECT teacher_id
    FROM teachers t
    WHERE t.user_id = ?
");

$getTeacher->bind_param("i", $_SESSION['user_id']);
$getTeacher->execute();
$teacherResult = $getTeacher->get_result()->fetch_assoc();

$advisoryText = "No Advisory Assigned";
$advisorySectionId = null;
$advisoryGradeLevel = null;
$advisoryStrandId = null;

if ($teacherResult) {

    $teacher_id = $teacherResult['teacher_id'];

    // First, try to get from teacher_advisory table
    $getAdvisory = $connection->prepare("
        SELECT 
            ta.grade_level,
            ta.strand_id,
            ta.section_id,
            s.strand_name,
            sec.section_name
        FROM teacher_advisory ta
        JOIN strands s ON ta.strand_id = s.strand_id
        JOIN section sec ON ta.section_id = sec.section_id
        WHERE ta.teacher_id = ?
        LIMIT 1
    ");

    $getAdvisory->bind_param("i", $teacher_id);
    $getAdvisory->execute();
    $advisory = $getAdvisory->get_result()->fetch_assoc();

    if ($advisory) {
        $advisoryText =
            "Grade " . $advisory['grade_level'] .
            " - " . $advisory['strand_name'] .
            " - " . $advisory['section_name'];
        $advisorySectionId = $advisory['section_id'];
        $advisoryGradeLevel = $advisory['grade_level'];
        $advisoryStrandId = $advisory['strand_id'];
    } else {
        // Fallback: Check if there's a section with this teacher as adviser
        // Note: This requires the section table to have adviser_id column
        $getAdvisoryFallback = $connection->prepare("
            SELECT 
                sec.grade_level,
                sec.strand_id,
                sec.section_id,
                s.strand_name,
                sec.section_name
            FROM section sec
            JOIN strands s ON sec.strand_id = s.strand_id
            WHERE sec.adviser_id = ?
            LIMIT 1
        ");
        
        $getAdvisoryFallback->bind_param("i", $teacher_id);
        $getAdvisoryFallback->execute();
        $advisoryFallback = $getAdvisoryFallback->get_result()->fetch_assoc();
        
        if ($advisoryFallback) {
            $advisoryText =
                "Grade " . $advisoryFallback['grade_level'] .
                " - " . $advisoryFallback['strand_name'] .
                " - " . $advisoryFallback['section_name'];
            $advisorySectionId = $advisoryFallback['section_id'];
            $advisoryGradeLevel = $advisoryFallback['grade_level'];
            $advisoryStrandId = $advisoryFallback['strand_id'];
        }
    }
}

?>
