<?php
/**
 * Get Sections by Track
 * Returns sections filtered by grade level
 * Note: Section now only has grade_level and school_year (no track_id or strand_id)
 */

include '../../DB_Connection/Connection.php';

$grade_level = $_GET['grade_level'] ?? '';
$track_id    = $_GET['track_id'] ?? '';

// Get current school year
$current_sy = date("Y") . "-" . (date("Y") + 1);

if ($grade_level) {
    try {
        // Get all sections for this grade level, ordered alphabetically
        $stmt = $connection->prepare("
            SELECT section_id, section_name, grade_level
            FROM section
            WHERE grade_level = ? AND school_year = ?
            ORDER BY section_name
        ");

        $stmt->bind_param("is", $grade_level, $current_sy);
        $stmt->execute();
        $result = $stmt->get_result();

        $sections = [];
        
        // Get student count for each section
        while ($row = $result->fetch_assoc()) {
            // Count students in this section (from student_strand table)
            $countStmt = $connection->prepare("
                SELECT COUNT(*) as student_count
                FROM student_strand
                WHERE section_id = ?
            ");
            $countStmt->bind_param("i", $row['section_id']);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $countRow = $countResult->fetch_assoc();
            
            $row['student_count'] = $countRow['student_count'] ?? 0;
            $sections[] = $row;
        }

        echo json_encode($sections);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([]);
}
