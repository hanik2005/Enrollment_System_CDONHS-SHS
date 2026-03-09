<?php
session_start();

include "../../DB_Connection/Connection.php";
include_once "admin_access.php";

$admin = requireAdminAccess($connection, "../login.php");
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);

/* ========================= */
/* FILTER PARAMETERS         */
/* ========================= */
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$filter_status = isset($_GET['filter_status']) ? trim($_GET['filter_status']) : '';

/* ===============================
   GET STUDENT APPLICATIONS ONLY
   Updated query to match actual database structure
   ======================================= */
// Get Students with proper JOINs
$studentSql = "SELECT 
    sa.application_id,
    sa.lrn,
    sa.first_name,
    sa.last_name,
    sa.middle_name,
    sa.extension_name,
    sa.date_of_birth,
    sa.sex,
    sa.place_of_birth,
    sa.religion,
    sa.mother_tongue,
    sa.enrollment_type,
    sa.application_status,
    sa.email,
    sa.contact_number,
    sa.facebook_profile,
    sa.remarks,
    sa.date_submitted,
    sa.profile_image,
    CONCAT(COALESCE(addr.house_number, ''), ' ', COALESCE(addr.street, '')) as house_number_street,
    addr.barangay,
    addr.city_municipality,
    addr.province,
    docs.psa_birth_certificate,
    docs.form_138,
    docs.student_id_copy,
    CONCAT(COALESCE(fam.father_first_name, ''), ' ', COALESCE(fam.father_last_name, '')) as father_guardian_name,
    fam.father_contact,
    CONCAT(COALESCE(fam.mother_first_name, ''), ' ', COALESCE(fam.mother_last_name, '')) as mother_guardian_name,
    fam.mother_contact,
    lmod.blended,
    lmod.modular_print,
    lmod.modular_digital,
    lmod.online,
    lmod.homeschooling,
    lmod.educational_tv,
    lmod.radio_based_tv,
    lp.attended_learning_program,
    lp.learning_program_specify,
    prev.last_grade_completed,
    prev.last_school_year_completed,
    prev.last_school_attended,
    soc.indigenous_community,
    soc.ip_specify,
    soc.four_ps_beneficiary,
    soc.four_ps_household_id,
    sne.with_disability,
    sne.has_pwd_id,
    sne.pwd_id_number,
    sne.special_education_needed,
    sne.non_graded_sne,
    sne.disability_category,
    sne.disability_description,
    sne.sped_services_needed,
    sne.medical_diagnosis,
    sne.assessment_date,
    sne.assessed_by,
    'Student' as user_type
FROM student_applications sa
LEFT JOIN student_addresses addr ON sa.application_id = addr.application_id
LEFT JOIN student_documents docs ON sa.application_id = docs.application_id
LEFT JOIN student_family fam ON sa.application_id = fam.application_id
LEFT JOIN student_learning_modality lmod ON sa.application_id = lmod.application_id
LEFT JOIN student_learning_program lp ON sa.application_id = lp.application_id
LEFT JOIN student_previous_school prev ON sa.application_id = prev.application_id
LEFT JOIN student_social_info soc ON sa.application_id = soc.application_id
LEFT JOIN student_special_needs sne ON sa.application_id = sne.application_id
WHERE 1=1";

if (!empty($search_name)) {
    $studentSql .= " AND (sa.first_name LIKE '%$search_name%' OR sa.last_name LIKE '%$search_name%' OR CONCAT(sa.first_name, ' ', sa.last_name) LIKE '%$search_name%')";
}

if (!empty($filter_status)) {
    $studentSql .= " AND sa.application_status = '$filter_status'";
}

$studentSql .= " ORDER BY sa.date_submitted DESC";

$allResults = [];

$studentResult = $connection->query($studentSql);
if ($studentResult) {
    while ($row = $studentResult->fetch_assoc()) {
        $allResults[] = $row;
    }
}

// Sort by date submitted (newest first)
usort($allResults, function($a, $b) {
    return strtotime($b['date_submitted']) - strtotime($a['date_submitted']);
});

/* ==============================
   UPDATE LRN FUNCTIONALITY
   ============================== */
if (isset($_POST['update_lrn']) && isset($_POST['application_id']) && isset($_POST['new_lrn'])) {
    $app_id = intval($_POST['application_id']);
    $new_lrn = trim($_POST['new_lrn']);
    
    // Validate LRN (12 digits or empty)
    if (empty($new_lrn)) {
        // Allow empty LRN (removing LRN)
        $updateStmt = mysqli_prepare($connection, "UPDATE student_applications SET lrn = NULL WHERE application_id = ?");
        mysqli_stmt_bind_param($updateStmt, "i", $app_id);
    } elseif (preg_match('/^[0-9]{12}$/', $new_lrn)) {
        $updateStmt = mysqli_prepare($connection, "UPDATE student_applications SET lrn = ? WHERE application_id = ?");
        mysqli_stmt_bind_param($updateStmt, "si", $new_lrn, $app_id);
    }
    
    if (isset($updateStmt)) {
        if (mysqli_stmt_execute($updateStmt)) {
            $lrn_update_success = true;
        }
        mysqli_stmt_close($updateStmt);
    }
    
    // Refresh the page to show updated data
    header("Location: sensitive_information.php" . (isset($_GET['search_name']) ? "?search_name=" . urlencode($_GET['search_name']) : ""));
    exit;
}
?>
