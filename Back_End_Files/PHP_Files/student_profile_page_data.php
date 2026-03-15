<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../DB_Connection/Connection.php';
require_once __DIR__ . '/portal_ui_helper.php';

$stmt = $connection->prepare("
    SELECT s.student_id, s.enrollment_status, s.date_enrolled, s.enlistment_status,
           sa.first_name, sa.last_name, sa.middle_name, sa.extension_name,
           sa.lrn, sa.date_of_birth, sa.sex, sa.place_of_birth, sa.religion, sa.mother_tongue,
           sa.email, sa.contact_number, sa.facebook_profile, sa.enrollment_type,
           sa.profile_image,
           u.username, u.status,
           addr.house_number, addr.street, addr.barangay, addr.city_municipality, addr.province, addr.country, addr.zip_code,
           addr.permanent_house_number, addr.permanent_street, addr.permanent_barangay,
           addr.permanent_city, addr.permanent_province, addr.permanent_country, addr.permanent_zip_code,
           fam.father_last_name, fam.father_first_name, fam.father_middle_name, fam.father_contact,
           fam.mother_last_name, fam.mother_first_name, fam.mother_middle_name, fam.mother_contact,
           fam.guardian_last_name, fam.guardian_first_name, fam.guardian_middle_name, fam.guardian_contact,
           soc.indigenous_community, soc.ip_specify, soc.four_ps_beneficiary, soc.four_ps_household_id,
           doc.psa_birth_certificate, doc.form_138, doc.student_id_copy,
           prev.last_school_attended, prev.last_grade_completed, prev.last_school_year_completed,
           sne.with_disability, sne.has_pwd_id, sne.pwd_id_number
    FROM students s
    INNER JOIN users u ON s.user_id = u.user_id
    INNER JOIN student_applications sa ON s.application_id = sa.application_id
    LEFT JOIN student_addresses addr ON sa.application_id = addr.application_id
    LEFT JOIN student_family fam ON sa.application_id = fam.application_id
    LEFT JOIN student_social_info soc ON sa.application_id = soc.application_id
    LEFT JOIN student_documents doc ON sa.application_id = doc.application_id
    LEFT JOIN student_previous_school prev ON sa.application_id = prev.application_id
    LEFT JOIN student_special_needs sne ON sa.application_id = sne.application_id
    WHERE s.user_id = ?
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

if (!$profile) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

syncSessionThemePreference($connection, (int) $_SESSION['user_id']);

$strandInfo = null;
$stmt = $connection->prepare("
    SELECT st.strand_name, st.strand_abbreviation, sec.section_name, ss.grade_level
    FROM student_strand ss
    INNER JOIN strands st ON ss.strand_id = st.strand_id
    INNER JOIN section sec ON ss.section_id = sec.section_id
    WHERE ss.student_id = ?
    ORDER BY ss.grade_level DESC
    LIMIT 1
");
$stmt->bind_param("i", $profile['student_id']);
$stmt->execute();
$strandResult = $stmt->get_result();
if ($strandResult->num_rows > 0) {
    $strandInfo = $strandResult->fetch_assoc();
}
$stmt->close();

$adviserInfo = null;
$adviserStmt = $connection->prepare("
    SELECT
        t.first_name as adviser_first_name,
        t.last_name as adviser_last_name,
        t.middle_name as adviser_middle_name,
        t.extension_name as adviser_extension
    FROM student_strand ss
    INNER JOIN section sec ON ss.section_id = sec.section_id
    LEFT JOIN teacher_advisory ta ON sec.section_id = ta.section_id
    LEFT JOIN teachers t ON ta.teacher_id = t.teacher_id
    WHERE ss.student_id = ?
");
$adviserStmt->bind_param("i", $profile['student_id']);
$adviserStmt->execute();
$adviserResult = $adviserStmt->get_result();
if ($adviserResult->num_rows > 0) {
    $adviserInfo = $adviserResult->fetch_assoc();
}
$adviserStmt->close();

$adviserName = "No adviser assigned yet";
if ($adviserInfo && !empty($adviserInfo['adviser_first_name'])) {
    $adviserName = $adviserInfo['adviser_first_name'];
    if (!empty($adviserInfo['adviser_middle_name'])) {
        $adviserName .= ' ' . substr($adviserInfo['adviser_middle_name'], 0, 1) . '.';
    }
    $adviserName .= ' ' . $adviserInfo['adviser_last_name'];
    if (!empty($adviserInfo['adviser_extension'])) {
        $adviserName .= ' ' . $adviserInfo['adviser_extension'];
    }
}

$message = "";
$messageType = "";
if (isset($_GET['success'])) {
    $messageType = "success";
    $message = "Profile updated successfully!";
}
if (isset($_GET['error'])) {
    $messageType = "error";
    switch ($_GET['error']) {
        case 'update_failed':
            $message = "Failed to update profile. Please try again.";
            break;
        case 'invalid_input':
            $message = "Invalid input detected.";
            break;
        case 'image_upload_failed':
            $message = "Profile image upload failed. Please try again with a smaller image file.";
            break;
        case 'image_invalid_type':
            $message = "Profile image must be JPG, JPEG, PNG, or GIF.";
            break;
        case 'unauthorized':
            $message = "You are not allowed to update this profile.";
            break;
        default:
            $message = "An error occurred.";
    }
}

$fullName = formatPortalPersonName(
    $profile['first_name'] ?? null,
    $profile['middle_name'] ?? null,
    $profile['last_name'] ?? null,
    $profile['extension_name'] ?? null,
    $profile['username'] ?? 'Student User'
);

$profileHeaderImagePath = !empty($profile['profile_image'])
    ? "../../uploads/Profile/student/" . htmlspecialchars($profile['profile_image'])
    : "../../Assets/profile_button.png";

$profileFormImagePath = !empty($profile['profile_image'])
    ? "../../uploads/Profile/student/" . htmlspecialchars($profile['profile_image'])
    : "../../Assets/default.png";

require __DIR__ . '/get_student_program.php';

$studentProgramDetail = 'Current Class: ';
if (($profile['enrollment_status'] ?? '') === 'Graduated') {
    $studentProgramDetail = 'Status: Already graduated and cannot be enlisted again';
} elseif ($isPending) {
    $studentProgramDetail = "Pending Enlistment";
} elseif ($isRejected) {
    $studentProgramDetail = "Rejected Enlistment";
} elseif ($strandInfo) {
    $studentProgramDetail .= 'Grade ' . $strandInfo['grade_level'] . ' - ' . $strandInfo['strand_abbreviation'] . ' - ' . $strandInfo['section_name'];
} else {
    $studentProgramDetail .= 'Not assigned yet';
}

$studentMenuLinks = '<a href="home.php">Home</a>';
if (($profile['enrollment_status'] ?? '') !== 'Graduated'
    && ($profile['enlistment_status'] ?? '') !== 'Enlisted'
    && ($profile['enlistment_status'] ?? '') !== 'Pending'
    && ($profile['enlistment_status'] ?? '') !== 'Promoted') {
    $studentMenuLinks .= '<a href="student_enlistment.php">Enlistment</a>';
} elseif (($profile['enlistment_status'] ?? '') === 'Pending') {
    $studentMenuLinks .= '<span class="menu-link-disabled">Pending Enlistment</span>';
} elseif (($profile['enrollment_status'] ?? '') === 'Graduated') {
    $studentMenuLinks .= '<span class="menu-link-disabled">Already Graduated</span>';
} else {
    $studentMenuLinks .= '<span class="menu-link-disabled">Already Enlisted</span>';
}
$studentMenuLinks .= '<a href="settings.php">Settings</a>';
$studentMenuLinks .= '<a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>';
