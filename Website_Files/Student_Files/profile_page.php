<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/portal_ui_helper.php";

// Verify student session and get profile data
// Now joining multiple tables for normalized database structure
$stmt = $connection->prepare("
    SELECT s.student_id, s.enrollment_status, s.date_enrolled, s.enlistment_status,
           sa.first_name, sa.last_name, sa.middle_name, sa.extension_name,
           sa.lrn, sa.date_of_birth, sa.sex, sa.place_of_birth, sa.religion, sa.mother_tongue,
           sa.email, sa.contact_number, sa.facebook_profile, sa.enrollment_type,
           sa.profile_image,
           u.username, u.status,
           -- Address from student_addresses
           addr.house_number, addr.street, addr.barangay, addr.city_municipality, addr.province, addr.country, addr.zip_code,
           addr.permanent_house_number, addr.permanent_street, addr.permanent_barangay, 
           addr.permanent_city, addr.permanent_province, addr.permanent_country, addr.permanent_zip_code,
           -- Family from student_family
           fam.father_last_name, fam.father_first_name, fam.father_middle_name, fam.father_contact,
           fam.mother_last_name, fam.mother_first_name, fam.mother_middle_name, fam.mother_contact,
           fam.guardian_last_name, fam.guardian_first_name, fam.guardian_middle_name, fam.guardian_contact,
           -- Social info from student_social_info
           soc.indigenous_community, soc.ip_specify, soc.four_ps_beneficiary, soc.four_ps_household_id,
           -- Documents from student_documents
           doc.psa_birth_certificate, doc.form_138, doc.student_id_copy,
           -- Previous school from student_previous_school
           prev.last_school_attended, prev.last_grade_completed, prev.last_school_year_completed,
           -- Special needs from student_special_needs
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

if (!$profile) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Get strand and section info
$strandInfo = null;
$stmt = $connection->prepare("
    SELECT st.strand_name, sec.section_name, ss.grade_level
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

// Get adviser info for the student
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

// Handle success/error messages
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

// Format address
$address = ($profile['house_number'] ?? '') . " " . ($profile['street'] ?? '') . ", " . ($profile['barangay'] ?? '') . ", " . 
           ($profile['city_municipality'] ?? '') . ", " . ($profile['province'] ?? '');

// Set profile image path for header
$profileImagePath = !empty($profile['profile_image']) 
    ? "../../uploads/Profile/student/" . htmlspecialchars($profile['profile_image']) 
    : "../../Assets/profile_button.png";

include "../../Back_End_Files/PHP_Files/get_student_program.php";

$studentProgramDetail = 'Current Class: ';
if (($profile['enrollment_status'] ?? '') === 'Graduated') {
    $studentProgramDetail = 'Status: Already graduated and cannot be enlisted again';
}elseif ($isPending) {
     $studentProgramDetail  = "Pending Enlistment";
} elseif ($isRejected) {
    $studentProgramDetail = "Rejected Enlistment"; 
} elseif ($strandInfo) {
    $studentProgramDetail .= 'Grade ' . $strandInfo['grade_level'] . ' - ' . $strandInfo['strand_name'] . ' - ' . $strandInfo['section_name'];
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
$studentMenuLinks .= '<a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Student | CDONHS-SHS</title>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/profile_page_design.css">
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body class="student-profile-page">

<!-- Header -->
<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>
    <?php echo renderPortalHeaderBanner('Student Portal', 'Student Profile', $studentProgramDetail); ?>
    <div class="right">
        <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="student-profile-menu">
            <span class="menu-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
            <span class="menu-label">Menu</span>
        </button>
    </div>
</div>

<?php echo renderStudentMenuOverlay(
    'student-profile-menu',
    $profileImagePath,
    $fullName,
    (string) ($profile['lrn'] ?? ''),
    isset($strandInfo['grade_level']) ? (string) $strandInfo['grade_level'] : null,
    $strandInfo['strand_name'] ?? null,
    $strandInfo['section_name'] ?? null,
    $studentMenuLinks
); ?>

<!-- Profile Content -->
<div class="profile-container">
    <div class="profile-box">
        
        <?php if (!empty($message)): ?>
            <div class="message message-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

          <!-- Profile Form -->
        <form action="../../Back_End_Files/PHP_Files/student_profile_backend.php" method="POST" id="profileForm" enctype="multipart/form-data">
            <input type="hidden" name="student_id" value="<?php echo $profile['student_id']; ?>">
            
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-image-container">
                    <?php 
                    $profileImagePath = !empty($profile['profile_image']) 
                        ? "../../uploads/Profile/student/" . htmlspecialchars($profile['profile_image']) 
                        : "../../Assets/default.png"; 
                    ?>
                    <img src="<?php echo $profileImagePath; ?>" alt="Profile Image" class="profile-image" id="profileImagePreview">
                    <label for="profile_image" class="profile-image-upload" title="Click to change profile image">
                        <span aria-hidden="true">+</span>
                    </label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="profile-image-input" onchange="previewImage(this)">
                </div>
                <div class="profile-header-info">
                    <h2><?php echo htmlspecialchars($fullName); ?></h2>
                    <p>Student record and profile photo</p>
                </div>
            </div>

        <div class="student-profile-hero">
            <div class="student-profile-hero-copy">
                <span class="student-profile-hero-tag">Student Profile Center</span>
                <h1>Profile Overview</h1>
                <p>Keep your contact details, family records, and uploaded documents updated so your school information stays complete and accurate.</p>
            </div>
            <div class="student-profile-hero-meta">
                <div class="student-profile-hero-card">
                    <span>Status</span>
                    <strong><?php echo htmlspecialchars($profile['enrollment_status']); ?></strong>
                </div>
                <div class="student-profile-hero-card">
                    <span>LRN</span>
                    <strong><?php echo htmlspecialchars($profile['lrn'] ?? 'Not available'); ?></strong>
                </div>
            </div>
        </div>

      

            <div class="academic-focus-card">
                <div class="academic-focus-header">
                    Current Class Assignment
                </div>
                <div class="academic-focus-grid">
                    <div class="focus-item">
                        <span class="focus-label">Grade Level</span>
                        <span class="focus-value"><?php echo htmlspecialchars($strandInfo['grade_level'] ?? 'Not assigned'); ?></span>
                    </div>
                    <div class="focus-item">
                        <span class="focus-label">Strand</span>
                        <span class="focus-value"><?php echo htmlspecialchars($strandInfo['strand_name'] ?? 'Not assigned'); ?></span>
                    </div>
                    <div class="focus-item focus-item-primary">
                        <span class="focus-label">Section</span>
                        <span class="focus-value"><?php echo htmlspecialchars($strandInfo['section_name'] ?? 'Not assigned yet'); ?></span>
                    </div>
                    <div class="focus-item focus-item-primary">
                        <span class="focus-label">Adviser</span>
                        <span class="focus-value"><?php echo htmlspecialchars($adviserName); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="profile-content view-mode" id="profileContent">
                
                <!-- Personal Information -->
                <div class="profile-section">
                    <h3>Personal Information</h3>
                    
                    <div class="profile-field">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($profile['first_name']); ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($profile['last_name']); ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" value="<?php echo htmlspecialchars($profile['middle_name'] ?? ''); ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>Extension Name</label>
                        <input type="text" name="extension_name" value="<?php echo htmlspecialchars($profile['extension_name'] ?? ''); ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" value="<?php echo $profile['date_of_birth'] ?? ''; ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>Sex</label>
                        <input type="text" value="<?php echo ucfirst($profile['sex']); ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>Place of Birth</label>
                        <input type="text" name="place_of_birth" value="<?php echo htmlspecialchars($profile['place_of_birth'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Religion</label>
                        <input type="text" name="religion" value="<?php echo htmlspecialchars($profile['religion'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Mother Tongue</label>
                        <input type="text" name="mother_tongue" value="<?php echo htmlspecialchars($profile['mother_tongue'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Indigenous Community</label>
                        <input type="text" value="<?php echo htmlspecialchars($profile['indigenous_community'] ?? 'No'); ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>4Ps Beneficiary</label>
                        <input type="text" value="<?php echo htmlspecialchars($profile['four_ps_beneficiary'] ?? 'No'); ?>" disabled>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="profile-section">
                    <h3>Contact Information</h3>
                    
                    <div class="profile-field">
                        <label>Contact Number</label>
                        <input type="tel" name="contact_number" value="<?php echo htmlspecialchars($profile['contact_number'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Facebook Profile</label>
                        <input type="text" name="facebook_profile" value="<?php echo htmlspecialchars($profile['facebook_profile'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Address -->
                <div class="profile-section full-width">
                    <h3>Address Information</h3>
                    <div class="address-grid">
                        <div class="profile-field">
                            <label>House No.</label>
                            <input type="text" name="house_number" value="<?php echo htmlspecialchars($profile['house_number'] ?? ''); ?>">
                        </div>
                        
                        <div class="profile-field">
                            <label>Street</label>
                            <input type="text" name="street" value="<?php echo htmlspecialchars($profile['street'] ?? ''); ?>">
                        </div>
                        
                        <div class="profile-field">
                            <label>Barangay</label>
                            <input type="text" name="barangay" value="<?php echo htmlspecialchars($profile['barangay'] ?? ''); ?>">
                        </div>
                        
                        <div class="profile-field">
                            <label>City / Municipality</label>
                            <input type="text" name="city_municipality" value="<?php echo htmlspecialchars($profile['city_municipality'] ?? ''); ?>">
                        </div>
                        
                        <div class="profile-field">
                            <label>Province</label>
                            <input type="text" name="province" value="<?php echo htmlspecialchars($profile['province'] ?? ''); ?>">
                        </div>
                        
                        <div class="profile-field">
                            <label>Country</label>
                            <input type="text" name="country" value="<?php echo htmlspecialchars($profile['country'] ?? 'Philippines'); ?>">
                        </div>
                    </div>
                </div>

                <!-- Academic Information (Read-only) -->
                <div class="profile-section">
                    <h3>Academic Information</h3>
                    
                    <div class="profile-field">
                        <label>Enrollment Type</label>
                        <input type="text" value="<?php echo htmlspecialchars($profile['enrollment_type'] ?? ''); ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>Previous School</label>
                        <input type="text" value="<?php echo htmlspecialchars($profile['last_school_attended'] ?? ''); ?>" disabled>
                    </div>
                    
                    <div class="profile-field">
                        <label>School Year Completed</label>
                        <input type="text" value="<?php echo htmlspecialchars($profile['last_school_year_completed'] ?? ''); ?>" disabled>
                    </div>
                    
                </div>

                <!-- Guardian Information -->
                <div class="profile-section">
                    <h3>Guardian Information</h3>
                    
                    <div class="profile-field">
                        <label>Father's Name</label>
                        <input type="text" name="father_first_name" value="<?php echo htmlspecialchars($profile['father_first_name'] ?? ''); ?>">
                        <input type="text" name="father_middle_name" value="<?php echo htmlspecialchars($profile['father_middle_name'] ?? ''); ?>">
                        <input type="text" name="father_last_name" value="<?php echo htmlspecialchars($profile['father_last_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Father's Contact</label>
                        <input type="tel" name="father_contact" value="<?php echo htmlspecialchars($profile['father_contact'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Mother's Name</label>
                        <input type="text" name="mother_first_name" value="<?php echo htmlspecialchars($profile['mother_first_name'] ?? ''); ?>">
                        <input type="text" name="mother_middle_name" value="<?php echo htmlspecialchars($profile['mother_middle_name'] ?? ''); ?>">
                        <input type="text" name="mother_last_name" value="<?php echo htmlspecialchars($profile['mother_last_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Mother's Contact</label>
                        <input type="tel" name="mother_contact" value="<?php echo htmlspecialchars($profile['mother_contact'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Guardian's Name</label>
                        <input type="text" name="guardian_first_name" value="<?php echo htmlspecialchars($profile['guardian_first_name'] ?? ''); ?>">
                        <input type="text" name="guardian_middle_name" value="<?php echo htmlspecialchars($profile['guardian_middle_name'] ?? ''); ?>">
                        <input type="text" name="guardian_last_name" value="<?php echo htmlspecialchars($profile['guardian_last_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="profile-field">
                        <label>Guardian's Contact</label>
                        <input type="tel" name="guardian_contact" value="<?php echo htmlspecialchars($profile['guardian_contact'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Documents -->
                <div class="profile-section full-width">
                    <h3>Uploaded Documents</h3>
                    <p class="section-description">
                        Upload your documents if not yet submitted. Click on a document to view/download.
                    </p>
                    <div class="documents-grid">
                        
                        <!-- PSA Birth Certificate -->
                        <div class="document-item">
                            <div class="document-icon">
                                <img src="../../Assets/pdf.png" alt="PDF">
                            </div>
                            <div class="document-info">
                                <strong>PSA Birth Certificate</strong>
                                <?php if (!empty($profile['psa_birth_certificate'])): ?>
                                    <span class="document-status uploaded">&#10003; Uploaded</span>
                                    <a href="../../uploads/Documents/student/<?php echo htmlspecialchars($profile['psa_birth_certificate']); ?>" target="_blank" class="btn-view">View</a>
                                <?php else: ?>
                                    <span class="document-status not-uploaded">&#10007; Not Uploaded</span>
                                    <input type="file" name="psa_birth_certificate" accept=".pdf,.jpg,.jpeg,.png" class="document-upload">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Form 138 -->
                        <div class="document-item">
                            <div class="document-icon">
                                <img src="../../Assets/pdf.png" alt="PDF">
                            </div>
                            <div class="document-info">
                                <strong>Form 138 (Report Card)</strong>
                                <?php if (!empty($profile['form_138'])): ?>
                                    <span class="document-status uploaded">&#10003; Uploaded</span>
                                    <a href="../../uploads/Documents/student/<?php echo htmlspecialchars($profile['form_138']); ?>" target="_blank" class="btn-view">View</a>
                                <?php else: ?>
                                    <span class="document-status not-uploaded">&#10007; Not Uploaded</span>
                                    <input type="file" name="form_138" accept=".pdf,.jpg,.jpeg,.png" class="document-upload">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Student ID Copy -->
                        <div class="document-item">
                            <div class="document-icon">
                                <img src="../../Assets/pdf.png" alt="PDF">
                            </div>
                            <div class="document-info">
                                <strong>Student ID Copy</strong>
                                <?php if (!empty($profile['student_id_copy'])): ?>
                                    <span class="document-status uploaded">&#10003; Uploaded</span>
                                    <a href="../../uploads/Documents/student/<?php echo htmlspecialchars($profile['student_id_copy']); ?>" target="_blank" class="btn-view">View</a>
                                <?php else: ?>
                                    <span class="document-status not-uploaded">&#10007; Not Uploaded</span>
                                    <input type="file" name="student_id_copy" accept=".pdf,.jpg,.jpeg,.png" class="document-upload">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>

            <!-- Buttons -->
            <div class="profile-buttons">
                <button type="button" class="btn btn-edit" id="editBtn" onclick="toggleEdit()">Edit Profile</button>
                <button type="submit" class="btn btn-save" id="saveBtn" style="display: none;">Save Changes</button>
                <button type="button" class="btn btn-cancel" id="cancelBtn" style="display: none;" onclick="cancelEdit()">Cancel</button>
                <a href="home.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </form>

    </div>
</div>

<!-- Footer -->
<div class="footer">
    &copy; 2026 Cagayan De Oro National High School - Senior High School
</div>

<script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
<script src="../../Back_End_Files/JSCRIPT_Files/student_profile_function.js"></script>

</body>
</html>

