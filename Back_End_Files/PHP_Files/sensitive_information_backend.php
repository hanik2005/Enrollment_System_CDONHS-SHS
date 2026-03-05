<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

/* ========================= */
/* VERIFY ADMIN SESSION      */
/* ========================= */
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($connection, "
    SELECT * FROM users 
    WHERE user_id = ?
    AND role_id = 2
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

/* ========================= */
/* FILTER PARAMETERS         */
/* ========================= */
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$filter_user_type = isset($_GET['filter_user_type']) ? trim($_GET['filter_user_type']) : '';
$filter_status = isset($_GET['filter_status']) ? trim($_GET['filter_status']) : '';

/* ===============================
   GET STUDENTS AND TEACHERS APPLICATIONS
======================================= */
$results = [];

// Get Students
$studentSql = "SELECT 
    sa.application_id,
    sa.first_name,
    sa.last_name,
    sa.middle_name,
    sa.extension_name,
    sa.date_of_birth,
    sa.sex,
    sa.civil_status,
    sa.house_number_street,
    sa.barangay,
    sa.city_municipality,
    sa.province,
    sa.contact_number,
    sa.email,
    sa.facebook_profile,
    sa.current_school,
    sa.school_classification,
    sa.enrollment_type,
    sa.year_graduated,
    sa.father_guardian_name,
    sa.father_guardian_contact,
    sa.mother_guardian_name,
    sa.mother_guardian_contact,
    sa.psa_birth_certificate,
    sa.form_138,
    sa.student_id_copy,
    sa.profile_image,
    sa.application_status,
    sa.remarks,
    sa.date_submitted,
    'Student' as user_type
FROM student_applications sa
WHERE 1=1";

if (!empty($search_name)) {
    $studentSql .= " AND (sa.first_name LIKE '%$search_name%' OR sa.last_name LIKE '%$search_name%' OR CONCAT(sa.first_name, ' ', sa.last_name) LIKE '%$search_name%')";
}

if (!empty($filter_status)) {
    $studentSql .= " AND sa.application_status = '$filter_status'";
}

$studentSql .= " ORDER BY sa.date_submitted DESC";

// Get Teachers (using teachers table)
$teacherSql = "SELECT 
    t.teacher_id as application_id,
    t.first_name,
    t.last_name,
    t.middle_name,
    t.extension_name,
    t.advisor_id,
    u.username,
    u.status as application_status,
    u.first_login as date_submitted,
    'Teacher' as user_type
FROM teachers t
JOIN users u ON t.user_id = u.user_id
WHERE u.role_id = 3";

if (!empty($search_name)) {
    $teacherSql .= " AND (t.first_name LIKE '%$search_name%' OR t.last_name LIKE '%$search_name%' OR CONCAT(t.first_name, ' ', t.last_name) LIKE '%$search_name%')";
}

if (!empty($filter_status)) {
    $teacherSql .= " AND u.status = '$filter_status'";
}

$teacherSql .= " ORDER BY t.last_name, t.first_name";

$allResults = [];

if (empty($filter_user_type) || $filter_user_type === 'Student') {
    $studentResult = $connection->query($studentSql);
    if ($studentResult) {
        while ($row = $studentResult->fetch_assoc()) {
            $allResults[] = $row;
        }
    }
}

if (empty($filter_user_type) || $filter_user_type === 'Teacher') {
    $teacherResult = $connection->query($teacherSql);
    if ($teacherResult) {
        while ($row = $teacherResult->fetch_assoc()) {
            $allResults[] = $row;
        }
    }
}

// Sort by date submitted (newest first)
usort($allResults, function($a, $b) {
    return strtotime($b['date_submitted']) - strtotime($a['date_submitted']);
});
?>
