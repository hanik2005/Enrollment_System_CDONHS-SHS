<!DOCTYPE html>
<?php
// Check if Student Enrollment is enabled
include_once '../Back_End_Files/PHP_Files/check_activation.php';
if (!isFeatureEnabled('Student Enrollment')) {
    header("Location: access_denied.php?feature=Student Enrollment");
    exit;
}

// Build dynamic school year options (current school year and previous years)
$currentYear = (int) date('Y');
$currentMonth = (int) date('n');
$currentSchoolYearStart = ($currentMonth >= 6) ? $currentYear : ($currentYear - 1);
$schoolYearOptions = [];

for ($i = 0; $i < 10; $i++) {
    $startYear = $currentSchoolYearStart - $i;
    $schoolYearOptions[] = $startYear . '-' . ($startYear + 1);
}
?>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Enrollment</title>
    <link rel="icon" href="../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../Design/Online_Form_Design.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Theme Toggle Button -->
    <button class="theme-toggle light" id="themeToggle" aria-label="Toggle dark/light mode" type="button">
        <!-- Sun icon (shown in light mode) -->
        <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0 .39-.39.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/>
        </svg>
        <!-- Moon icon (shown in dark mode) -->
        <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/>
        </svg>
    </button>
    
    <script>
        // Theme Toggle Functionality
        (function() {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;
            
            // Check for saved theme preference or default to light
            const savedTheme = localStorage.getItem('enrollmentTheme') || 'light';
            html.setAttribute('data-theme', savedTheme);
            themeToggle.classList.remove('light', 'dark');
            themeToggle.classList.add(savedTheme);
            
            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                // Save preference
                localStorage.setItem('enrollmentTheme', newTheme);
                
                // Apply theme
                html.setAttribute('data-theme', newTheme);
                themeToggle.classList.remove('light', 'dark');
                themeToggle.classList.add(newTheme);
            });
        })();
    </script>
</head>
<body>

<!-- Back to Guest Home Button -->
<a href="guest_page.php" class="back-button">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
    </svg>
    <span>Back to Home</span>
</a>

<form action="../Back_End_Files/PHP_Files/student_enrollment_backend.php" 
      method="POST" enctype="multipart/form-data" id="enrollmentForm" novalidate>

    <!-- HEADER -->
    <div class="form-header">
        <img src="../Assets/LOGO.png" alt="School Logo">
        <div class="header-text">
            <h3>Republic of the Philippines</h3>
            <h3>Department of Education</h3>
            <h2 id="TITLE">CAGAYAN DE ORO NATIONAL HIGH SCHOOL - SENIOR HIGH</h2>
            <h1>ONLINE ENROLLMENT FORM</h1>
        </div>
    </div>

    <hr>

    <!-- SCHOOL YEAR AND GRADE LEVEL -->
    <h2>ENROLLMENT DETAILS</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Enrollment Type <span class="required">*</span></label>
            <select name="enrollmentType" required data-validate="required">
                <option value="">Select</option>
                <option value="New">New Student</option>
                <option value="Transferee">Transferee</option>
                <option value="Balik-Aral">Balik-Aral (Returning)</option>
            </select>
            <span class="error-message"></span>
        </div>

    </div>

    <hr>

    <!-- STUDENT INFORMATION -->
    <h2>STUDENT PERSONAL INFORMATION</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Learner Reference Number (LRN) <span class="required">*</span></label>
            <input type="text" name="lrn" required maxlength="12" pattern="[0-9]{12}" placeholder="Example: 123456789012" title="Enter 12-digit LRN (numbers only)" data-validate="required|lrn">
            <span class="error-message"></span>
            <span class="hint">12-digit number</span>
        </div>

        <div class="form-group">
            <label>Last Name / Surname <span class="required">*</span></label>
            <input type="text" name="lastName" required data-validate="required|alpha|capitalization" placeholder="Enter last name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>First Name <span class="required">*</span></label>
            <input type="text" name="firstName" required data-validate="required|alpha|capitalization" placeholder="Enter first name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Middle Name</label>
            <input type="text" name="middleName" data-validate="alpha|capitalization" placeholder="Enter middle name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Extension Name (Jr., Sr., III)</label>
            <input type="text" name="extensionName" data-validate="extension" placeholder="e.g., Jr., Sr., III">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Date of Birth <span class="required">*</span></label>
            <input type="date" name="dateOfBirth" required data-validate="required|date" placeholder="Select date">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Place of Birth</label>
            <input type="text" name="placeOfBirth" data-validate="capitalization" placeholder="Enter place of birth">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Sex <span class="required">*</span></label>
            <select name="sex" required data-validate="required">
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Civil Status</label>
            <select name="civilStatus" data-validate="select">
                <option value="">Select</option>
                <option>Single</option>
                <option>Married</option>
                <option>Widowed</option>
            </select>
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Religion</label>
            <div class="dropdown-with-other">
                <select name="religionSelect" id="religionSelect" onchange="handleDropdownWithOther(this, 'religionInput')">
                    <option value="">Select Religion</option>
                    <option value="Catholic">Catholic</option>
                    <option value="Christian">Christian</option>
                    <option value="Islam">Islam</option>
                    <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                    <option value="Buddhism">Buddhism</option>
                    <option value="Hinduism">Hinduism</option>
                    <option value="Atheist">Atheist</option>
                    <option value="Other">Other (Specify)</option>
                </select>
                <input type="text" name="religion" id="religionInput" placeholder="Specify religion" style="display: none;" data-validate="capitalization">
            </div>
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Mother Tongue</label>
            <input type="text" name="motherTongue" data-validate="capitalization" placeholder="Enter mother tongue">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Contact Number <span class="required">*</span></label>
            <input type="tel" name="contactNumber" pattern="^09[0-9]{9}$" maxlength="11" minlength="11" placeholder="Example: 09123456789" title="Enter 11-digit mobile number starting with 09" required data-validate="required|phone">
            <span class="error-message"></span>
            <span class="hint">11 digits starting with 09</span>
        </div>

        <div class="form-group">
            <label>Email Address <span class="required">*</span></label>
            <input type="email" name="email" required data-validate="required|email" placeholder="example@email.com">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Facebook Profile URL (Optional)</label>
            <input type="url" name="facebookProfile" data-validate="url" placeholder="https://facebook.com/username">
            <span class="error-message"></span>
        </div>

    </div>

    <!-- INDIGENOUS COMMUNITY & 4PS -->
    <h2>INDIGENOUS GROUP & 4PS INFORMATION</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Member of Indigenous Community?</label>
            <select name="indigenousCommunity" id="indigenousCommunity" onchange="toggleIpSpecify()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div class="form-group" id="ipSpecifyDiv" style="display: none;">
            <label>Specify Indigenous Group</label>
            <input type="text" name="ipSpecify" placeholder="Specify indigenous group">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>4PS Beneficiary?</label>
            <select name="fourPsBeneficiary" id="fourPsBeneficiary" onchange="toggleFourPsId()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div class="form-group" id="fourPsIdDiv" style="display: none;">
            <label>4PS Household ID</label>
            <input type="text" name="fourPsHouseholdId" placeholder="Enter 4PS Household ID">
            <span class="error-message"></span>
        </div>

    </div>

    <!-- DISABILITY INFORMATION -->
    <h2>DISABILITY INFORMATION (If Applicable)</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>With Disability?</label>
            <select name="withDisability" id="withDisability" onchange="toggleDisabilityFields()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div class="form-group" id="disabilityTypeDiv" style="display: none;">
            <label>Type of Disability</label>
            <input type="text" name="disabilityType" placeholder="Specify disability type">
            <span class="error-message"></span>
        </div>

        <div class="form-group" id="manifestationDiv" style="display: none;">
            <label>Manifestation / Details</label>
            <input type="text" name="manifestation" placeholder="Describe manifestation">
            <span class="error-message"></span>
        </div>

        <div class="form-group" id="pwdIdDiv" style="display: none;">
            <label>Have PWD ID?</label>
            <select name="pwdId" id="pwdId" onchange="togglePwdIdNumber()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div class="form-group" id="pwdIdNumberDiv" style="display: none;">
            <label>PWD ID Number</label>
            <input type="text" name="pwdIdNumber" placeholder="Enter PWD ID Number">
            <span class="error-message"></span>
        </div>

    </div>

    <hr>

    <!-- CURRENT ADDRESS -->
    <h2>CURRENT ADDRESS</h2>
    <p class="form-hint">Please select in order: Province first, then City/Municipality, then Barangay. If loading fails, choose Other (Specify) and type it manually.</p>

    <div class="form-grid">

        
        <div class="form-group">
            <label>Country</label>
            <input type="text" name="country" value="Philippines" readonly>
            <span class="error-message"></span>
        </div>
        
        <div class="form-group">
            <label>Province <span class="required">*</span></label>
            <div class="dropdown-with-other">
                <select name="provinceSelect" id="provinceSelect" onchange="handleDropdownWithOther(this, 'provinceInput')">
                    <option value="">Select Province</option>
                    <option value="Other">Other (Specify)</option>
                </select>
                <input type="text" name="province" id="provinceInput" placeholder="Specify province" style="display: none;" data-validate="required|capitalization">
            </div>
            <span class="error-message"></span>
        </div>

         <div class="form-group">
            <label>City / Municipality <span class="required">*</span></label>
            <div class="dropdown-with-other">
                <select name="citySelect" id="citySelect" onchange="handleDropdownWithOther(this, 'cityInput')">
                    <option value="">Select City/Municipality</option>
                    <option value="Other">Other (Specify)</option>
                </select>
                <input type="text" name="city_municipality" id="cityInput" placeholder="Specify city/municipality" style="display: none;" data-validate="required|capitalization">
            </div>
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Barangay <span class="required">*</span></label>
            <div class="dropdown-with-other">
                <select name="barangaySelect" id="barangaySelect" onchange="handleDropdownWithOther(this, 'barangayInput')">
                    <option value="">Select Barangay</option>
                    <option value="Other">Other (Specify)</option>
                </select>
                <input type="text" name="barangay" id="barangayInput" placeholder="Specify barangay" style="display: none;" data-validate="required|capitalization">
            </div>
            <span class="error-message"></span>
        </div>

        

        <div class="form-group">
            <label>Street</label>
            <input type="text" name="street" data-validate="capitalization" placeholder="Enter street name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>House No.</label>
            <input type="text" name="house_number" data-validate="capitalization" placeholder="Enter house number">
            <span class="error-message"></span>
        </div>


        <div class="form-group">
            <label>Zip Code</label>
            <input type="text" name="zip_code" data-validate="zip" placeholder="Enter zip code">
            <span class="error-message"></span>
        </div>

    </div>

    <!-- PERMANENT ADDRESS -->
    <h2>PERMANENT ADDRESS</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Same as Current Address?</label>
            <select name="sameAsCurrent" id="sameAsCurrent" onchange="togglePermanentAddress()">
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>

    </div>

    <div id="permanentAddressDiv" style="display: none;">
        <div class="form-grid">

            <div class="form-group">
                <label>Permanent House No.</label>
                <input type="text" name="permanent_house_number" data-validate="capitalization" placeholder="Enter house number">
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label>Permanent Street</label>
                <input type="text" name="permanent_street" data-validate="capitalization" placeholder="Enter street name">
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label>Permanent Barangay</label>
                <input type="text" name="permanent_barangay" data-validate="capitalization" placeholder="Enter barangay">
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label>Permanent City / Municipality</label>
                <input type="text" name="permanent_city" data-validate="capitalization" placeholder="Enter city/municipality">
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label>Permanent Province</label>
                <input type="text" name="permanent_province" data-validate="capitalization" placeholder="Enter province">
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label>Permanent Country</label>
                <input type="text" name="permanentCountry" value="Philippines">
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label>Permanent Zip Code</label>
                <input type="text" name="permanentZipCode" placeholder="Enter zip code">
                <span class="error-message"></span>
            </div>

        </div>
    </div>

    <hr>

    <!-- FATHER INFORMATION -->
    <h2>FATHER'S INFORMATION</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Father's Last Name</label>
            <input type="text" name="fatherLastName" data-validate="alpha|capitalization" placeholder="Enter father's last name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Father's First Name</label>
            <input type="text" name="fatherFirstName" data-validate="alpha|capitalization" placeholder="Enter father's first name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Father's Middle Name</label>
            <input type="text" name="fatherMiddleName" data-validate="alpha|capitalization" placeholder="Enter father's middle name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Father's Contact Number</label>
            <input type="tel" name="fatherContact" pattern="^09[0-9]{9}$" maxlength="11" minlength="11" placeholder="Example: 09123456789" title="Enter 11-digit mobile number starting with 09" data-validate="phone">
            <span class="error-message"></span>
            <span class="hint">11 digits starting with 09</span>
        </div>

    </div>

    <!-- MOTHER INFORMATION -->
    <h2>MOTHER'S INFORMATION</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Mother's Last Name</label>
            <input type="text" name="motherLastName" data-validate="alpha|capitalization" placeholder="Enter mother's last name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Mother's First Name</label>
            <input type="text" name="motherFirstName" data-validate="alpha|capitalization" placeholder="Enter mother's first name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Mother's Middle Name</label>
            <input type="text" name="motherMiddleName" data-validate="alpha|capitalization" placeholder="Enter mother's middle name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Mother's Contact Number</label>
            <input type="tel" name="motherContact" pattern="^09[0-9]{9}$" maxlength="11" minlength="11" placeholder="Example: 09123456789" title="Enter 11-digit mobile number starting with 09" data-validate="phone">
            <span class="error-message"></span>
            <span class="hint">11 digits starting with 09</span>
        </div>

    </div>

    <!-- GUARDIAN INFORMATION (Optional - if different from parents) -->
    <h2>GUARDIAN INFORMATION</h2>
    <p class="form-hint">Only fill this if guardian is different from father or mother</p>
    
    <div class="form-grid">

        <div class="form-group">
            <label>Has Guardian (different from parents)?</label>
            <select name="hasGuardian" id="hasGuardian" onchange="toggleGuardianFields()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

    </div>

    <div id="guardianFields" style="display: none;">
    <div class="form-grid">

        <div class="form-group">
            <label>Guardian's Last Name</label>
            <input type="text" name="guardianLastName" data-validate="alpha|capitalization" placeholder="Enter guardian's last name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Guardian's First Name</label>
            <input type="text" name="guardianFirstName" data-validate="alpha|capitalization" placeholder="Enter guardian's first name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Guardian's Middle Name</label>
            <input type="text" name="guardianMiddleName" data-validate="alpha|capitalization" placeholder="Enter guardian's middle name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Guardian's Contact Number</label>
            <input type="tel" name="guardianContact" pattern="^09[0-9]{9}$" maxlength="11" minlength="11" placeholder="Example: 09123456789" title="Enter 11-digit mobile number starting with 09" data-validate="phone">
            <span class="error-message"></span>
            <span class="hint">11 digits starting with 09</span>
        </div>

    </div>
    </div>

    <hr>

    <!-- PREVIOUS SCHOOL INFORMATION -->
    <h2>PREVIOUS SCHOOL INFORMATION</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Last School Attended <span class="required">*</span></label>
            <input type="text" name="lastSchoolAttended" required data-validate="required" placeholder="Enter school name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>School ID (If available)</label>
            <input type="text" name="schoolId" placeholder="Enter school ID">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Last Grade Completed</label>
            <select name="lastGradeCompleted" data-validate="select">
                <option value="">Select</option>
                <option value="Grade 10">Grade 10</option>
                <option value="Grade 11">Grade 11</option>
                <option value="Grade 12">Grade 12</option>
            </select>
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Last School Year Completed</label>
            <select name="lastSchoolYearCompleted" data-validate="select">
                <option value="">Select</option>
                <?php foreach ($schoolYearOptions as $schoolYear): ?>
                    <option value="<?php echo htmlspecialchars($schoolYear); ?>">
                        <?php echo htmlspecialchars($schoolYear); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="hint">Recent 10 school years are listed automatically.</span>
            <span class="error-message"></span>
        </div>

    </div>

    <!-- LEARNING PROGRAM -->
    <h2>LEARNING PROGRAM (If previously attended an alternative learning system)</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Have you attended a Learning Program?</label>
            <select name="attendedLearningProgram" id="attendedLearningProgram" onchange="toggleLearningProgram()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div class="form-group" id="learningProgramSpecifyGroup" style="display: none;">
            <label>Please Specify</label>
            <input type="text" name="learningProgramSpecify" placeholder="e.g., ALS, Home Schooling">
            <span class="error-message"></span>
        </div>

    </div>

    <!-- LEARNING MODALITY -->
    <h2>If the school will implement other distance learning modalities aside from face to face instructions, what would you prefer for you as a student or your child?</h2>

    <div class="form-grid">

        <div class="form-group">
            <label>Blended Learning</label>
            <select name="blended">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label>Modular (Print)</label>
            <select name="modularPrint">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label>Modular (Digital)</label>
            <select name="modularDigital">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label>Online</label>
            <select name="online">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label>Homeschooling</label>
            <select name="homeschooling">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label>Educational TV</label>
            <select name="educationalTv">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label>Radio-Based Teaching</label>
            <select name="radioBasedTv">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

    </div>

    <hr>

    <!-- REQUIREMENTS -->
    <h2>ENROLLMENT REQUIREMENTS</h2>
    <p class="form-hint">Please upload available documents. These can be submitted later if not available now.</p>

    <div class="form-grid">

        <div class="form-group">
            <label>PSA Birth Certificate (Optional)</label>
            <input type="file" name="psaBirthCertificate" accept=".pdf,.jpg,.png" class="file-input">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Form 137/138 or Latest Report Card (e.g., Grade 10 or Grade 11 completion) (Optional)</label>
            <input type="file" name="form138" accept=".pdf,.jpg,.png" class="file-input">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Student ID (Optional - if available)</label>
            <input type="file" name="studentIdCopy" accept=".pdf,.jpg,.png" class="file-input">
            <span class="error-message"></span>
        </div>

    </div>

    <div class="form-actions">
        <button type="submit" class="submit-btn">SUBMIT ENROLLMENT</button>
    </div>

</form>
<script src="../Back_End_Files/JSCRIPT_Files/address_loader.js?v=20260307"></script>
<script src="../Back_End_Files/JSCRIPT_Files/form_validation.js"></script>

</body>
</html>
