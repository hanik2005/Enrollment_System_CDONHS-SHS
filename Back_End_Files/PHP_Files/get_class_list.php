<?php
include "../../DB_Connection/Connection.php";

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized access");
}

$students = [];
$strandName = '';
$sectionName = '';

if (empty($advisorySectionId)) {
    return;
}

/*
 * Fetch latest strand record per student for the advisory section,
 * and include active students even if enlistment_status is not exactly "Enlisted"
 * (for example: Promoted).
 */
$stmt = $connection->prepare("
    SELECT
        s.student_id,
        sa.last_name,
        sa.first_name,
        sa.lrn,
        sa.sex,
        ss.grade_level,
        ss.strand_id,
        ss.section_id,
        s.enlistment_status,
        st.strand_name,
        sec.section_name
    FROM students s
    INNER JOIN (
        SELECT MAX(student_strand_id) AS latest_student_strand_id
        FROM student_strand
        WHERE section_id = ?
        GROUP BY student_id
    ) latest_ss
        ON 1 = 1
    INNER JOIN student_strand ss
        ON ss.student_strand_id = latest_ss.latest_student_strand_id
        AND ss.student_id = s.student_id
    INNER JOIN student_applications sa
        ON s.application_id = sa.application_id
    LEFT JOIN strands st
        ON st.strand_id = ss.strand_id
    LEFT JOIN section sec
        ON sec.section_id = ss.section_id
    WHERE ss.section_id = ?
      AND COALESCE(s.enrollment_status, '') <> 'Graduated'
      AND COALESCE(s.enlistment_status, '') <> 'Finished'
    ORDER BY sa.last_name ASC, sa.first_name ASC
");

$stmt->bind_param("ii", $advisorySectionId, $advisorySectionId);
$stmt->execute();
$result = $stmt->get_result();

$uniqueStudents = [];
while ($row = $result->fetch_assoc()) {
    $uniqueStudents[(int) $row['student_id']] = $row;
}

foreach ($uniqueStudents as $row) {
    $students[] = $row;
    if ($strandName === '' && !empty($row['strand_name'])) {
        $strandName = (string) $row['strand_name'];
        $sectionName = (string) $row['section_name'];
    }
}

$stmt->close();
?>
