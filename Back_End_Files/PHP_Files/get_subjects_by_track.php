<?php
/**
 * Get Subjects by Track (New Curriculum)
 * Returns subjects filtered by grade level and track using subject_new table
 */

session_start();
include '../../DB_Connection/Connection.php';

$grade_level = $_GET['grade_level'] ?? '';
$track_id    = $_GET['track_id'] ?? '';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

if (!$grade_level || !$track_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing grade level or track',
        'debug' => [
            'user_id' => $user_id,
            'grade_level' => $grade_level,
            'track_id' => $track_id
        ]
    ]);
    exit;
}

// Get current school year
$syQuery = $connection->query("SELECT school_year FROM students WHERE user_id = $user_id LIMIT 1");
$syRow = $syQuery->fetch_assoc();
$current_sy = $syRow['school_year'] ?? date('Y') . '-' . (date('Y') + 1);

// Get student's enrolled subjects (already selected)
$enrolledSubjects = [];
$enrolledQuery = $connection->prepare("
    SELECT subject_id FROM student_subjects 
    WHERE student_id = (SELECT student_id FROM students WHERE user_id = ?) 
    AND status IN ('Enrolled', 'Pending')
");
$enrolledQuery->bind_param("i", $user_id);
$enrolledQuery->execute();
$enrolledResult = $enrolledQuery->get_result();
while ($row = $enrolledResult->fetch_assoc()) {
    $enrolledSubjects[] = $row['subject_id'];
}

// Get subjects from subject_new (new curriculum)
// Core subjects: track_id is NULL
// Track-specific: track_id matches the selected track
$stmt = $connection->prepare("
    SELECT 
        sn.subject_new_id,
        sn.subject_name,
        sn.subject_type,
        sn.cluster_id,
        c.cluster_name,
        sn.is_required,
        sn.nc_equivalent
    FROM subject_new sn
    LEFT JOIN clusters c ON sn.cluster_id = c.cluster_id
    WHERE (sn.track_id = ? OR sn.track_id IS NULL)
    AND (sn.recommended_grade_level = ? OR sn.recommended_grade_level = '11/12')
    AND sn.is_active = 1
    ORDER BY 
        CASE WHEN sn.subject_type = 'CORE' THEN 0 ELSE 1 END,
        c.cluster_name,
        sn.subject_name
");

$stmt->bind_param("is", $track_id, $grade_level);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
$currentCluster = '';

while ($row = $result->fetch_assoc()) {
    $isEnrolled = in_array($row['subject_new_id'], $enrolledSubjects);
    
    $subjects[] = [
        'subject_id' => $row['subject_new_id'],
        'subject_name' => $row['subject_name'],
        'subject_type' => $row['subject_type'],
        'cluster_id' => $row['cluster_id'],
        'cluster_name' => $row['cluster_name'] ?? 'Core',
        'is_required' => $row['is_required'],
        'nc_equivalent' => $row['nc_equivalent'],
        'enrolled' => $isEnrolled
    ];
}

echo json_encode([
    'success' => true,
    'subjects' => $subjects,
    'meta' => [
        'grade_level' => $grade_level,
        'track_id' => $track_id,
        'current_sy' => $current_sy,
        'enrolled_count' => count($enrolledSubjects)
    ]
]);
