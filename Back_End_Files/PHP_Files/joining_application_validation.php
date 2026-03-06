
<?php
/* ========================= */
/* FILTER PARAMETERS         */
/* ========================= */
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';

/* ✅ GET STUDENT APPLICATIONS WITH FILTERS - JOINING MULTIPLE TABLES */
// First try with JOINs, if fails fall back to simple query
try {
    $sql = "SELECT 
        sa.application_id, sa.lrn, sa.first_name, sa.last_name, sa.middle_name, sa.extension_name,
        sa.date_of_birth, sa.sex, sa.place_of_birth, sa.religion, sa.mother_tongue,
        sa.enrollment_type, sa.application_status, sa.email, sa.contact_number, sa.facebook_profile,
        sa.profile_image, sa.date_submitted, sa.remarks,
        COALESCE(doc.psa_birth_certificate, '') as psa_birth_certificate, 
        COALESCE(doc.form_138, '') as form_138, 
        COALESCE(doc.student_id_copy, '') as student_id_copy,
        COALESCE(addr.house_number, '') as house_number, 
        COALESCE(addr.street, '') as street, 
        COALESCE(addr.barangay, '') as barangay, 
        COALESCE(addr.city_municipality, '') as city_municipality, 
        COALESCE(addr.province, '') as province,
        COALESCE(fam.father_last_name, '') as father_last_name, 
        COALESCE(fam.father_first_name, '') as father_first_name, 
        COALESCE(fam.father_middle_name, '') as father_middle_name,
        COALESCE(fam.mother_last_name, '') as mother_last_name, 
        COALESCE(fam.mother_first_name, '') as mother_first_name, 
        COALESCE(fam.mother_middle_name, '') as mother_middle_name,
        COALESCE(soc.indigenous_community, 'No') as indigenous_community, 
        COALESCE(soc.four_ps_beneficiary, 'No') as four_ps_beneficiary
    FROM student_applications sa
    LEFT JOIN student_documents doc ON sa.application_id = doc.application_id
    LEFT JOIN student_addresses addr ON sa.application_id = addr.application_id
    LEFT JOIN student_family fam ON sa.application_id = fam.application_id
    LEFT JOIN student_social_info soc ON sa.application_id = soc.application_id
    WHERE sa.application_status = 'Pending'";
    
    $result = $connection->query($sql);
    
    // If query fails, it means tables don't exist - use fallback
    if (!$result) {
        throw new Exception($connection->error);
    }
} catch (Exception $e) {
    // Fallback to simple query using only student_applications table
    $sql = "SELECT * FROM student_applications WHERE application_status = 'Pending'";
    $result = $connection->query($sql);
}

if (!empty($search_name)) {
    $sql .= " AND (sa.first_name LIKE '%$search_name%' OR sa.last_name LIKE '%$search_name%' OR CONCAT(sa.first_name, ' ', sa.last_name) LIKE '%$search_name%')";
}

$sql .= " ORDER BY sa.application_id DESC";

$result = $connection->query($sql);

if (!$result) {
    die("Query failed: " . $connection->error);
}

?>