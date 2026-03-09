<?php
session_start();

include "../../DB_Connection/Connection.php";
include_once "admin_access.php";

$admin = requireAdminAccess($connection, "../login.php");
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);

function getCurrentSchoolYear()
{
    $month = (int) date('n');
    $year = (int) date('Y');
    if ($month >= 6) {
        return $year . '-' . ($year + 1);
    }
    return ($year - 1) . '-' . $year;
}

function runSingleCount(mysqli $connection, string $sql, string $types = "", array $params = []): int
{
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        return 0;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return (int) ($row['total'] ?? 0);
}

function runGroupedQuery(mysqli $connection, string $sql, string $types = "", array $params = []): array
{
    $rows = [];
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        return $rows;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    $stmt->close();

    return $rows;
}

/* ========================= */
/* SCHOOL YEAR FILTER        */
/* ========================= */
$availableSchoolYears = [];
$schoolYearResult = $connection->query("
    SELECT school_year FROM students WHERE school_year IS NOT NULL AND school_year <> ''
    UNION
    SELECT school_year FROM student_strand WHERE school_year IS NOT NULL AND school_year <> ''
    ORDER BY school_year DESC
");

if ($schoolYearResult) {
    while ($row = $schoolYearResult->fetch_assoc()) {
        $availableSchoolYears[] = $row['school_year'];
    }
}

if (empty($availableSchoolYears)) {
    $availableSchoolYears[] = getCurrentSchoolYear();
}

$selectedSchoolYear = isset($_GET['school_year']) ? trim($_GET['school_year']) : getCurrentSchoolYear();
if ($selectedSchoolYear !== 'All' && !in_array($selectedSchoolYear, $availableSchoolYears, true)) {
    $selectedSchoolYear = getCurrentSchoolYear();
}

$yearFilterSql = "";
$yearFilterTypes = "";
$yearFilterParams = [];

if ($selectedSchoolYear !== 'All') {
    $yearFilterSql = " AND s.school_year = ?";
    $yearFilterTypes = "s";
    $yearFilterParams = [$selectedSchoolYear];
}

/* ========================= */
/* SUMMARY METRICS           */
/* ========================= */
$totalEnrolled = runSingleCount(
    $connection,
    "SELECT COUNT(*) AS total FROM students s WHERE s.enrollment_status = 'Active' $yearFilterSql",
    $yearFilterTypes,
    $yearFilterParams
);

$pendingApplications = runSingleCount(
    $connection,
    "SELECT COUNT(*) AS total FROM student_applications WHERE application_status = 'Pending'"
);

$pendingEnlistment = runSingleCount(
    $connection,
    "SELECT COUNT(*) AS total FROM students s WHERE s.enlistment_status = 'Pending' $yearFilterSql",
    $yearFilterTypes,
    $yearFilterParams
);

$promotedCount = runSingleCount(
    $connection,
    "SELECT COUNT(*) AS total FROM students s WHERE s.enlistment_status = 'Promoted' $yearFilterSql",
    $yearFilterTypes,
    $yearFilterParams
);

$graduatedCount = runSingleCount(
    $connection,
    "SELECT COUNT(*) AS total FROM students s WHERE s.enrollment_status = 'Graduated' $yearFilterSql",
    $yearFilterTypes,
    $yearFilterParams
);

/* ========================= */
/* ENROLLED COUNTS           */
/* ========================= */
$countsByGrade = runGroupedQuery(
    $connection,
    "
    SELECT
        COALESCE(ss.grade_level, 0) AS grade_level,
        COUNT(DISTINCT s.student_id) AS total
    FROM students s
    LEFT JOIN student_strand ss ON ss.student_id = s.student_id
    WHERE s.enrollment_status = 'Active'
    $yearFilterSql
    GROUP BY COALESCE(ss.grade_level, 0)
    ORDER BY grade_level ASC
    ",
    $yearFilterTypes,
    $yearFilterParams
);

$countsByStrand = runGroupedQuery(
    $connection,
    "
    SELECT
        COALESCE(st.strand_name, 'Unassigned') AS strand_name,
        COUNT(DISTINCT s.student_id) AS total
    FROM students s
    LEFT JOIN student_strand ss ON ss.student_id = s.student_id
    LEFT JOIN strands st ON st.strand_id = ss.strand_id
    WHERE s.enrollment_status = 'Active'
    $yearFilterSql
    GROUP BY COALESCE(st.strand_name, 'Unassigned')
    ORDER BY total DESC, strand_name ASC
    ",
    $yearFilterTypes,
    $yearFilterParams
);

$countsBySection = runGroupedQuery(
    $connection,
    "
    SELECT
        COALESCE(sec.section_name, 'Unassigned') AS section_name,
        COALESCE(sec.grade_level, 0) AS grade_level,
        COALESCE(st.strand_abbreviation, 'N/A') AS strand_abbreviation,
        COUNT(DISTINCT s.student_id) AS total
    FROM students s
    LEFT JOIN student_strand ss ON ss.student_id = s.student_id
    LEFT JOIN section sec ON sec.section_id = ss.section_id
    LEFT JOIN strands st ON st.strand_id = ss.strand_id
    WHERE s.enrollment_status = 'Active'
    $yearFilterSql
    GROUP BY COALESCE(sec.section_name, 'Unassigned'), COALESCE(sec.grade_level, 0), COALESCE(st.strand_abbreviation, 'N/A')
    ORDER BY total DESC, grade_level ASC, section_name ASC
    ",
    $yearFilterTypes,
    $yearFilterParams
);

/* ========================= */
/* APPROVAL BREAKDOWNS       */
/* ========================= */
$applicationStatusBreakdown = runGroupedQuery(
    $connection,
    "
    SELECT
        application_status,
        COUNT(*) AS total
    FROM student_applications
    GROUP BY application_status
    ORDER BY total DESC, application_status ASC
    "
);

$enlistmentStatusBreakdown = runGroupedQuery(
    $connection,
    "
    SELECT
        s.enlistment_status,
        COUNT(*) AS total
    FROM students s
    WHERE 1 = 1
    $yearFilterSql
    GROUP BY s.enlistment_status
    ORDER BY total DESC, s.enlistment_status ASC
    ",
    $yearFilterTypes,
    $yearFilterParams
);
?>
