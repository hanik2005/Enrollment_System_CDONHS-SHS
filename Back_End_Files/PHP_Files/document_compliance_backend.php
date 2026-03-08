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
    SELECT user_id
    FROM users
    WHERE user_id = ?
    AND role_id = 2
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$adminResult = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($adminResult);

if (!$admin) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

/* ========================= */
/* FILTER PARAMETERS         */
/* ========================= */
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$filter_application_status = isset($_GET['application_status']) ? trim($_GET['application_status']) : '';
$filter_compliance = isset($_GET['compliance_status']) ? trim($_GET['compliance_status']) : '';
$filter_grade_level = isset($_GET['grade_level']) ? trim($_GET['grade_level']) : '';
$filter_strand_id = isset($_GET['strand_id']) ? trim($_GET['strand_id']) : '';

$validApplicationStatuses = ['Pending', 'Approved', 'Rejected', 'Conditionally Approved'];
$validComplianceStatuses = ['Compliant', 'Missing'];
$validGradeLevels = ['11', '12'];

/* ========================= */
/* STRANDS FOR FILTER        */
/* ========================= */
$strands = [];
$strandResult = $connection->query("
    SELECT strand_id, strand_name
    FROM strands
    ORDER BY strand_name ASC
");
if ($strandResult) {
    while ($row = $strandResult->fetch_assoc()) {
        $strands[] = $row;
    }
}

/* ========================= */
/* CHECK FORM 137 COLUMN     */
/* ========================= */
$hasForm137Column = false;
$columnCheck = $connection->query("SHOW COLUMNS FROM student_documents LIKE 'form_137'");
if ($columnCheck && $columnCheck->num_rows > 0) {
    $hasForm137Column = true;
}

$form137Select = $hasForm137Column
    ? "COALESCE(sd.form_137, '') AS form_137"
    : "'' AS form_137";

/* ========================= */
/* MAIN QUERY                */
/* ========================= */
$sql = "
    SELECT
        sa.application_id,
        sa.lrn,
        sa.first_name,
        sa.last_name,
        sa.middle_name,
        sa.extension_name,
        sa.email,
        sa.enrollment_type,
        sa.application_status,
        s.student_id,
        s.enrollment_status,
        s.enlistment_status,
        ss.grade_level,
        st.strand_name,
        sec.section_name,
        COALESCE(sd.psa_birth_certificate, '') AS psa_birth_certificate,
        COALESCE(sd.form_138, '') AS form_138,
        COALESCE(sd.student_id_copy, '') AS student_id_copy,
        $form137Select
    FROM student_applications sa
    LEFT JOIN student_documents sd ON sa.application_id = sd.application_id
    LEFT JOIN students s ON sa.application_id = s.application_id
    LEFT JOIN student_strand ss ON s.student_id = ss.student_id
    LEFT JOIN strands st ON ss.strand_id = st.strand_id
    LEFT JOIN section sec ON ss.section_id = sec.section_id
    WHERE 1 = 1
";

$params = [];
$types = "";

if (!empty($search_name)) {
    $searchLike = "%" . $search_name . "%";
    $sql .= " AND (
        sa.first_name LIKE ?
        OR sa.last_name LIKE ?
        OR CONCAT(sa.first_name, ' ', sa.last_name) LIKE ?
    )";
    $types .= "sss";
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
}

if (in_array($filter_application_status, $validApplicationStatuses, true)) {
    $sql .= " AND sa.application_status = ?";
    $types .= "s";
    $params[] = $filter_application_status;
}

if (in_array($filter_grade_level, $validGradeLevels, true)) {
    $sql .= " AND ss.grade_level = ?";
    $types .= "i";
    $params[] = (int) $filter_grade_level;
}

if (ctype_digit($filter_strand_id) && (int) $filter_strand_id > 0) {
    $sql .= " AND st.strand_id = ?";
    $types .= "i";
    $params[] = (int) $filter_strand_id;
}

$sql .= " ORDER BY sa.date_submitted DESC, sa.last_name ASC, sa.first_name ASC";

$stmt = $connection->prepare($sql);
if (!$stmt) {
    die("Failed to prepare compliance query: " . $connection->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$rawRows = [];
while ($row = $result->fetch_assoc()) {
    $rawRows[] = $row;
}
$stmt->close();

// Avoid duplicate application rows when a student has multiple strand records.
$uniqueByApplication = [];
foreach ($rawRows as $row) {
    $appId = (int) $row['application_id'];
    if (!isset($uniqueByApplication[$appId])) {
        $uniqueByApplication[$appId] = $row;
    }
}
$rawRows = array_values($uniqueByApplication);

/* ========================= */
/* COMPLIANCE PROCESSING     */
/* ========================= */
$complianceRows = [];
$summary = [
    'total_records' => 0,
    'compliant_records' => 0,
    'missing_records' => 0,
    'missing_psa_birth_certificate' => 0,
    'missing_form_138' => 0,
    'missing_form_137' => 0,
    'missing_student_id_copy' => 0,
];

foreach ($rawRows as $row) {
    $birthCertificateSubmitted = !empty(trim((string) $row['psa_birth_certificate']));
    $form138Submitted = !empty(trim((string) $row['form_138']));
    $form137Submitted = !empty(trim((string) $row['form_137']));
    $studentIdSubmitted = !empty(trim((string) $row['student_id_copy']));

    $missingDocs = [];
    if (!$birthCertificateSubmitted) {
        $missingDocs[] = 'PSA Birth Certificate';
        $summary['missing_psa_birth_certificate']++;
    }

    if (!$form138Submitted) {
        $missingDocs[] = 'Form 138';
        $summary['missing_form_138']++;
    }

    if ($hasForm137Column) {
        if (!$form137Submitted) {
            $missingDocs[] = 'Form 137';
            $summary['missing_form_137']++;
        }
        $row['form_137_status'] = $form137Submitted ? 'Submitted' : 'Missing';
    } else {
        $row['form_137_status'] = 'Not Configured';
    }

    if (!$studentIdSubmitted) {
        $missingDocs[] = 'Student ID Copy';
        $summary['missing_student_id_copy']++;
    }

    $row['psa_birth_certificate_status'] = $birthCertificateSubmitted ? 'Submitted' : 'Missing';
    $row['form_138_status'] = $form138Submitted ? 'Submitted' : 'Missing';
    $row['student_id_copy_status'] = $studentIdSubmitted ? 'Submitted' : 'Missing';
    $row['missing_docs_text'] = empty($missingDocs) ? 'None' : implode(', ', $missingDocs);
    $row['compliance_status'] = empty($missingDocs) ? 'Compliant' : 'Missing';

    if (in_array($filter_compliance, $validComplianceStatuses, true) && $row['compliance_status'] !== $filter_compliance) {
        continue;
    }

    $complianceRows[] = $row;
    $summary['total_records']++;
    if ($row['compliance_status'] === 'Compliant') {
        $summary['compliant_records']++;
    } else {
        $summary['missing_records']++;
    }
}

$summary['records_before_compliance_filter'] = count($rawRows);
$summary['has_form_137_column'] = $hasForm137Column;
?>
