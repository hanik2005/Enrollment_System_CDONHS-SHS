<?php require_once __DIR__ . '/../../Back_End_Files/PHP_Files/student_profile_page_data.php'; ?>

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
<body <?php echo renderThemeBodyAttributes('student-profile-page'); ?>>

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
    $profileHeaderImagePath,
    $fullName,
    (string) ($profile['lrn'] ?? ''),
    isset($strandInfo['grade_level']) ? (string) $strandInfo['grade_level'] : null,
    $strandInfo['strand_abbreviation'] ?? null,
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

            <div class="profile-page-bar">
                <div class="profile-page-heading">
                    <span class="profile-page-kicker">Student Workspace</span>
                    <h1>My Profile</h1>
                    <p>Review your student record, update editable information, and keep your school documents complete.</p>
                </div>
                <div class="profile-buttons profile-buttons-top">
                    <button type="button" class="btn btn-edit" id="editBtn" onclick="toggleEdit()">Edit Profile</button>
                    <button type="submit" class="btn btn-save" id="saveBtn" style="display: none;">Save Changes</button>
                    <button type="button" class="btn btn-cancel" id="cancelBtn" style="display: none;" onclick="cancelEdit()">Cancel</button>
                    <a href="home.php" class="btn btn-secondary">Back to Home</a>
                </div>
            </div>

            <div class="profile-summary-card">
                <div class="profile-summary-main">
                    <div class="profile-image-container">
                        <img src="<?php echo $profileFormImagePath; ?>" alt="Profile Image" class="profile-image" id="profileImagePreview">
                        <label for="profile_image" class="profile-image-upload" title="Click to change profile image">
                            <span aria-hidden="true">+</span>
                        </label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" class="profile-image-input" onchange="previewImage(this)">
                    </div>
                    <div class="profile-summary-copy">
                        <span class="profile-summary-tag">Student Overview</span>
                        <h2><?php echo htmlspecialchars($fullName); ?></h2>
                        <p>Student record and profile photo</p>
                        <div class="profile-header-chips">
                            <span class="profile-header-chip">Username: <?php echo htmlspecialchars($profile['username'] ?? 'Student'); ?></span>
                            <span class="profile-header-chip">Enlistment: <?php echo htmlspecialchars($profile['enlistment_status'] ?? 'Not set'); ?></span>
                            <span class="profile-header-chip">Enrolled: <?php echo !empty($profile['date_enrolled']) ? htmlspecialchars(date('F j, Y', strtotime($profile['date_enrolled']))) : 'Not recorded'; ?></span>
                        </div>
                    </div>
                </div>
                <div class="profile-summary-stats">
                    <div class="profile-summary-stat">
                        <span>Status</span>
                        <strong><?php echo htmlspecialchars($profile['enrollment_status']); ?></strong>
                    </div>
                    <div class="profile-summary-stat">
                        <span>LRN</span>
                        <strong><?php echo htmlspecialchars($profile['lrn'] ?? 'Not available'); ?></strong>
                    </div>
                    <div class="profile-summary-stat profile-summary-stat-wide">
                        <span>Current Class</span>
                        <strong><?php echo htmlspecialchars($studentProgramDetail); ?></strong>
                    </div>
                </div>
            </div>

            <div class="academic-focus-card profile-assignment-card">
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
                <div class="profile-section profile-section-personal">
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
                <div class="profile-section profile-section-contact full-width">
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
                <div class="profile-section profile-section-address full-width">
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
                <div class="profile-section profile-section-academic full-width">
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
                <div class="profile-section profile-section-guardian full-width">
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
                <div class="profile-section profile-section-documents full-width">
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

            <p class="profile-action-note">Use Edit Profile to unlock editable fields and upload missing documents before saving your changes.</p>
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

