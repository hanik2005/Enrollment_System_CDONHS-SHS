<!DOCTYPE html>
<?php
// Check if Student Enrollment is enabled
include_once '../Back_End_Files/PHP_Files/check_activation.php';
if (!isFeatureEnabled('Student Enrollment')) {
    header("Location: access_denied.php?feature=Student Enrollment");
    exit;
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Enrollment</title>
    <link rel="icon" href="../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../Design/Online_Form_Design.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<form action="../Back_End_Files/PHP_Files/student_enrollment_backend.php" 
      method="POST" enctype="multipart/form-data" id="enrollmentForm" novalidate>

    <!-- HEADER -->
    <div class="form-header">
        <img src="../Assets/LOGO.png" alt="School Logo">
        <div class="header-text">
            <h3>Republic of the Philippines</h3>
            <h3>Department of Education</h3>
            <h2>CAGAYAN DE ORO NATIONAL HIGH SCHOOL - SENIOR HIGH</h2>
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
            <label>Learner Reference Number (LRN) - Optional</label>
            <input type="text" name="lrn" maxlength="12" pattern="[0-9]{12}" placeholder="Example: 123456789012" title="Enter 12-digit LRN (numbers only)" data-validate="lrn">
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

    <div class="form-grid">

        <div class="form-group">
            <label>House No.</label>
            <input type="text" name="house_number" data-validate="capitalization" placeholder="Enter house number">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Street</label>
            <input type="text" name="street" data-validate="capitalization" placeholder="Enter street name">
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Barangay <span class="required">*</span></label>
            <div class="dropdown-with-other">
                <select name="barangaySelect" id="barangaySelect" onchange="handleDropdownWithOther(this, 'barangayInput')">
                    <option value="">Select Barangay</option>
                    <option value="Barangay 1">Barangay 1</option>
                    <option value="Barangay 2">Barangay 2</option>
                    <option value="Barangay 3">Barangay 3</option>
                    <option value="Barangay 4">Barangay 4</option>
                    <option value="Barangay 5">Barangay 5</option>
                    <option value="Barangay 6">Barangay 6</option>
                    <option value="Barangay 7">Barangay 7</option>
                    <option value="Barangay 8">Barangay 8</option>
                    <option value="Barangay 9">Barangay 9</option>
                    <option value="Barangay 10">Barangay 10</option>
                    <option value="Barangay 11">Barangay 11</option>
                    <option value="Barangay 12">Barangay 12</option>
                    <option value="Barangay 13">Barangay 13</option>
                    <option value="Barangay 14">Barangay 14</option>
                    <option value="Barangay 15">Barangay 15</option>
                    <option value="Barangay 16">Barangay 16</option>
                    <option value="Barangay 17">Barangay 17</option>
                    <option value="Barangay 18">Barangay 18</option>
                    <option value="Barangay 19">Barangay 19</option>
                    <option value="Barangay 20">Barangay 20</option>
                    <option value="Barangay 21">Barangay 21</option>
                    <option value="Barangay 22">Barangay 22</option>
                    <option value="Barangay 23">Barangay 23</option>
                    <option value="Barangay 24">Barangay 24</option>
                    <option value="Barangay 25">Barangay 25</option>
                    <option value="Barangay 26">Barangay 26</option>
                    <option value="Barangay 27">Barangay 27</option>
                    <option value="Barangay 28">Barangay 28</option>
                    <option value="Barangay 29">Barangay 29</option>
                    <option value="Barangay 30">Barangay 30</option>
                    <option value="Barangay 31">Barangay 31</option>
                    <option value="Barangay 32">Barangay 32</option>
                    <option value="Barangay 33">Barangay 33</option>
                    <option value="Barangay 34">Barangay 34</option>
                    <option value="Barangay 35">Barangay 35</option>
                    <option value="Other">Other (Specify)</option>
                </select>
                <input type="text" name="barangay" id="barangayInput" placeholder="Specify barangay" style="display: none;" data-validate="required|capitalization">
            </div>
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>City / Municipality <span class="required">*</span></label>
            <div class="dropdown-with-other">
                <select name="citySelect" id="citySelect" onchange="handleDropdownWithOther(this, 'cityInput')">
                    <option value="">Select City/Municipality</option>
                    <option value="Cagayan de Oro City">Cagayan de Oro City</option>
                    <option value="Iligan City">Iligan City</option>
                    <option value="Marawi City">Marawi City</option>
                    <option value="Oroquieta City">Oroquieta City</option>
                    <option value="Ozamis City">Ozamis City</option>
                    <option value="Tangub City">Tangub City</option>
                    <option value="Gingoog City">Gingoog City</option>
                    <option value="Butuan City">Butuan City</option>
                    <option value="Cabadbaran City">Cabadbaran City</option>
                    <option value="Bayugan City">Bayugan City</option>
                    <option value="Other">Other (Specify)</option>
                </select>
                <input type="text" name="city_municipality" id="cityInput" placeholder="Specify city/municipality" style="display: none;" data-validate="required|capitalization">
            </div>
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Province <span class="required">*</span></label>
            <div class="dropdown-with-other">
                <select name="provinceSelect" id="provinceSelect" onchange="handleDropdownWithOther(this, 'provinceInput')">
                    <option value="">Select Province</option>
                    <option value="Misamis Oriental">Misamis Oriental</option>
                    <option value="Misamis Occidental">Misamis Occidental</option>
                    <option value="Lanao del Norte">Lanao del Norte</option>
                    <option value="Lanao del Sur">Lanao del Sur</option>
                    <option value="Maguindanao">Maguindanao</option>
                    <option value="Sultan Kudarat">Sultan Kudarat</option>
                    <option value="Cotabato">Cotabato</option>
                    <option value="South Cotabato">South Cotabato</option>
                    <option value="Saranggani">Saranggani</option>
                    <option value="Agusan del Norte">Agusan del Norte</option>
                    <option value="Agusan del Sur">Agusan del Sur</option>
                    <option value="Surigao del Norte">Surigao del Norte</option>
                    <option value="Surigao del Sur">Surigao del Sur</option>
                    <option value="Dinagat Islands">Dinagat Islands</option>
                    <option value="Bukidnon">Bukidnon</option>
                    <option value="Camiguin">Camiguin</option>
                    <option value="Other">Other (Specify)</option>
                </select>
                <input type="text" name="province" id="provinceInput" placeholder="Specify province" style="display: none;" data-validate="required|capitalization">
            </div>
            <span class="error-message"></span>
        </div>

        <div class="form-group">
            <label>Country</label>
            <input type="text" name="country" value="Philippines" readonly>
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
                <option value="2024-2025">2024-2025</option>
                <option value="2025-2026">2025-2026</option>
            </select>
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
            <label>Grade 10 Completion of Form 138 / Report Card (Optional)</label>
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

<script>
    // Handle dropdown with "Other" option
    function handleDropdownWithOther(selectElement, inputId) {
        const inputField = document.getElementById(inputId);
        if (selectElement.value === 'Other') {
            inputField.style.display = 'block';
            inputField.required = true;
            inputField.focus();
        } else if (selectElement.value !== '') {
            inputField.style.display = 'none';
            inputField.required = false;
            inputField.value = selectElement.value;
        } else {
            inputField.style.display = 'none';
            inputField.required = false;
            inputField.value = '';
        }
    }

    // Toggle functions for conditional fields
    function toggleIpSpecify() {
        var select = document.getElementById('indigenousCommunity');
        var div = document.getElementById('ipSpecifyDiv');
        div.style.display = select.value === 'Yes' ? 'block' : 'none';
    }

    function toggleFourPsId() {
        var select = document.getElementById('fourPsBeneficiary');
        var div = document.getElementById('fourPsIdDiv');
        div.style.display = select.value === 'Yes' ? 'block' : 'none';
    }

    function toggleDisabilityFields() {
        var select = document.getElementById('withDisability');
        var typeDiv = document.getElementById('disabilityTypeDiv');
        var manifestationDiv = document.getElementById('manifestationDiv');
        var pwdIdDiv = document.getElementById('pwdIdDiv');
        var pwdIdNumberDiv = document.getElementById('pwdIdNumberDiv');
        
        if (select.value === 'Yes') {
            typeDiv.style.display = 'block';
            manifestationDiv.style.display = 'block';
            pwdIdDiv.style.display = 'block';
            togglePwdIdNumber();
        } else {
            typeDiv.style.display = 'none';
            manifestationDiv.style.display = 'none';
            pwdIdDiv.style.display = 'none';
            pwdIdNumberDiv.style.display = 'none';
        }
    }

    function togglePwdIdNumber() {
        var select = document.getElementById('pwdId');
        var div = document.getElementById('pwdIdNumberDiv');
        if (select) {
            div.style.display = select.value === 'Yes' ? 'block' : 'none';
        }
    }

    function toggleGuardianFields() {
        var select = document.getElementById('hasGuardian');
        var div = document.getElementById('guardianFields');
        div.style.display = select.value === 'Yes' ? 'block' : 'none';
    }

    function togglePermanentAddress() {
        var select = document.getElementById('sameAsCurrent');
        var div = document.getElementById('permanentAddressDiv');
        div.style.display = select.value === 'No' ? 'block' : 'none';
    }

    function toggleLearningProgram() {
        var select = document.getElementById('attendedLearningProgram');
        var group = document.getElementById('learningProgramSpecifyGroup');
        if (select && group) {
            group.style.display = select.value === 'Yes' ? 'block' : 'none';
        }
    }

    // Real-time validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('enrollmentForm');
        const inputs = form.querySelectorAll('input, select');

        // Validation patterns and messages
        const validationRules = {
            required: {
                validate: (value) => value.trim() !== '',
                message: 'This field is required'
            },
            email: {
                validate: (value) => {
                    if (!value) return true;
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(value);
                },
                message: 'Please enter a valid email address'
            },
            phone: {
                validate: (value) => {
                    if (!value) return true;
                    const phoneRegex = /^09[0-9]{9}$/;
                    return phoneRegex.test(value);
                },
                message: 'Please enter a valid 11-digit mobile number (e.g., 09123456789)'
            },
            lrn: {
                validate: (value) => {
                    if (!value) return true;
                    const lrnRegex = /^[0-9]{12}$/;
                    return lrnRegex.test(value);
                },
                message: 'LRN must be exactly 12 digits'
            },
            alpha: {
                validate: (value) => {
                    if (!value) return true;
                    const alphaRegex = /^[a-zA-Z\s\-'.ñÑ]+$/;
                    return alphaRegex.test(value);
                },
                message: 'Please enter only letters, spaces, hyphens, and apostrophes'
            },
            // New capitalization validation - checks if first letter of each word is uppercase
            capitalization: {
                validate: (value) => {
                    if (!value) return true;
                    // Check if first letter of each word is capitalized
                    const words = value.trim().split(/\s+/);
                    for (const word of words) {
                        if (word.length > 0 && word[0] !== word[0].toUpperCase()) {
                            return false;
                        }
                    }
                    return true;
                },
                message: 'Please use proper capitalization (e.g., Juan Dela Cruz)'
            },
            extension: {
                validate: (value) => {
                    if (!value) return true;
                    const extRegex = /^(Jr\.?|Sr\.?|II|III|IV|V)$/i;
                    return extRegex.test(value);
                },
                message: 'Valid extensions: Jr., Sr., II, III, IV, V'
            },
            date: {
                validate: (value) => {
                    if (!value) return true;
                    const date = new Date(value);
                    const now = new Date();
                    const minDate = new Date();
                    minDate.setFullYear(minDate.getFullYear() - 25);
                    return date <= now && date >= minDate;
                },
                message: 'Please enter a valid date (within the last 25 years)'
            },
            zip: {
                validate: (value) => {
                    if (!value) return true;
                    const zipRegex = /^[0-9]{4}$/;
                    return zipRegex.test(value);
                },
                message: 'Please enter a valid 4-digit zip code'
            },
            url: {
                validate: (value) => {
                    if (!value) return true;
                    try {
                        new URL(value);
                        return true;
                    } catch {
                        return false;
                    }
                },
                message: 'Please enter a valid URL (e.g., https://facebook.com/username)'
            },
            select: {
                validate: (value) => {
                    // For select elements, empty string means not selected
                    return value !== '';
                },
                message: 'Please select an option'
            }
        };

        // Auto-capitalize function for name fields
        function capitalizeFirstLetter(str) {
            if (!str) return '';
            return str.replace(/\b\w/g, char => char.toUpperCase());
        }

        // Add auto-capitalize to text inputs on blur
        const textInputs = form.querySelectorAll('input[type="text"]');
        textInputs.forEach(input => {
            // Skip LRN (should remain numeric)
            if (input.name === 'lrn') {
                return;
            }
            
            // Apply capitalization on blur
            input.addEventListener('blur', function() {
                if (this.value) {
                    this.value = capitalizeFirstLetter(this.value.trim());
                }
            });
            
            // Also apply on change for dropdown "Other" inputs
            input.addEventListener('change', function() {
                if (this.value) {
                    this.value = capitalizeFirstLetter(this.value.trim());
                }
            });
        });

        // Also capitalize dropdown select values when changed
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // If user selects "Other", the input field will be handled by the text input listener above
            });
        });

        // Validate single field
        function validateField(input) {
            const formGroup = input.closest('.form-group');
            if (!formGroup) return true;

            const errorSpan = formGroup.querySelector('.error-message');
            const validateAttr = input.dataset.validate;
            
            if (!validateAttr) return true;

            const rules = validateAttr.split('|');
            let isValid = true;
            let errorMessage = '';

            for (const rule of rules) {
                if (validationRules[rule]) {
                    const result = validationRules[rule].validate(input.value);
                    if (!result) {
                        isValid = false;
                        errorMessage = validationRules[rule].message;
                        break;
                    }
                }
            }

            // Handle dropdown-with-other wrapper
            const dropdownWrapper = input.closest('.dropdown-with-other');
            if (dropdownWrapper) {
                const selectElement = dropdownWrapper.querySelector('select');
                if (selectElement && selectElement.value === 'Other') {
                    // Check the text input instead
                    return validateField(input);
                } else if (selectElement && selectElement.value !== '' && selectElement.value !== 'Other') {
                    // Pre-selected value is valid
                    if (errorSpan) {
                        errorSpan.textContent = '';
                        formGroup.classList.remove('invalid');
                        input.classList.remove('invalid');
                    }
                    return true;
                }
            }

            if (errorSpan) {
                errorSpan.textContent = isValid ? '' : errorMessage;
            }
            
            if (isValid) {
                formGroup.classList.remove('invalid');
                input.classList.remove('invalid');
            } else {
                formGroup.classList.add('invalid');
                input.classList.add('invalid');
            }

            return isValid;
        }

        // Add event listeners for real-time validation
        inputs.forEach(input => {
            // Validate on blur
            input.addEventListener('blur', function() {
                validateField(this);
            });

            // Validate on input for immediate feedback (optional)
            input.addEventListener('input', function() {
                const formGroup = this.closest('.form-group');
                if (formGroup && formGroup.classList.contains('invalid')) {
                    validateField(this);
                }
            });
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate all inputs
            inputs.forEach(input => {
                // Skip hidden inputs in dropdown-with-other when select is active
                const dropdownWrapper = input.closest('.dropdown-with-other');
                if (dropdownWrapper) {
                    const selectElement = dropdownWrapper.querySelector('select');
                    if (selectElement && selectElement.value !== 'Other' && input.id.includes('Input')) {
                        return;
                    }
                }
                
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            // Check for at least one learning modality selected
            const learningMods = ['blended', 'modularPrint', 'modularDigital', 'online', 'homeschooling', 'educationalTv', 'radioBasedTv'];
            let hasModality = false;
            learningMods.forEach(mod => {
                const select = form.querySelector(`[name="${mod}"]`);
                if (select && select.value === '1') {
                    hasModality = true;
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                alert('Please correct the errors in the form before submitting.');
            }
        });

        // Auto-format phone numbers
        const phoneInputs = form.querySelectorAll('input[data-validate*="phone"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                e.target.value = value;
            });
        });

        // Auto-format LRN
        const lrnInput = form.querySelector('input[name="lrn"]');
        if (lrnInput) {
            lrnInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 12) value = value.slice(0, 12);
                e.target.value = value;
            });
        }
    });
</script>

</body>
</html>
