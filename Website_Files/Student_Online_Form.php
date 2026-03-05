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
    <title>Online Enrollment</title>
    <link rel="icon" href="../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../Design/Online_Form_Design.css">
</head>
<body>

<form action="../Back_End_Files/PHP_Files/student_enrollment_backend.php" 
      method="POST" enctype="multipart/form-data">

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

        <div>
            <label>Enrollment Type</label>
            <select name="enrollmentType" required>
                <option value="">Select</option>
                <option value="New">New Student</option>
                <option value="Transferee">Transferee</option>
                <option value="Balik-Aral">Balik-Aral (Returning)</option>
            </select>
        </div>

    </div>

    <hr>

    <!-- STUDENT INFORMATION -->
    <h2>STUDENT PERSONAL INFORMATION</h2>

    <div class="form-grid">

        <div>
            <label>Learner Reference Number (LRN) - Optional</label>
            <input type="text" name="lrn" maxlength="12" pattern="[0-9]{12}" placeholder="Example: 123456789012" title="Enter 12-digit LRN (numbers only)">
        </div>

        <div>
            <label>Last Name / Surname</label>
            <input type="text" name="lastName" required>
        </div>

        <div>
            <label>First Name</label>
            <input type="text" name="firstName" required>
        </div>

        <div>
            <label>Middle Name</label>
            <input type="text" name="middleName">
        </div>

        <div>
            <label>Extension Name (Jr., Sr., III)</label>
            <input type="text" name="extensionName">
        </div>

        <div>
            <label>Date of Birth</label>
            <input type="date" name="dateOfBirth" required>
        </div>

        <div>
            <label>Place of Birth</label>
            <input type="text" name="placeOfBirth">
        </div>

        <div>
            <label>Sex</label>
            <select name="sex" required>
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div>
            <label>Civil Status</label>
            <select name="civilStatus">
                <option value="">Select</option>
                <option>Single</option>
                <option>Married</option>
                <option>Widowed</option>
            </select>
        </div>

        <div>
            <label>Religion</label>
            <input type="text" name="religion">
        </div>

        <div>
            <label>Mother Tongue</label>
            <input type="text" name="motherTongue">
        </div>

        <div>
            <label>Contact Number</label>
            <input type="tel" name="contactNumber" pattern="^09[0-9]{9}$" maxlength="11" minlength="11" placeholder="Example: 09123456789" title="Enter 11-digit mobile number starting with 09" required>
        </div>

        <div>
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>Facebook Profile URL (Optional)</label>
            <input type="url" name="facebookProfile">
        </div>

    </div>

    <!-- INDIGENOUS COMMUNITY & 4PS -->
    <h2>INDIGENOUS GROUP & 4PS INFORMATION</h2>

    <div class="form-grid">

        <div>
            <label>Member of Indigenous Community?</label>
            <select name="indigenousCommunity" id="indigenousCommunity" onchange="toggleIpSpecify()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div id="ipSpecifyDiv" style="display: none;">
            <label>Specify Indigenous Group</label>
            <input type="text" name="ipSpecify">
        </div>

        <div>
            <label>4PS Beneficiary?</label>
            <select name="fourPsBeneficiary" id="fourPsBeneficiary" onchange="toggleFourPsId()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div id="fourPsIdDiv" style="display: none;">
            <label>4PS Household ID</label>
            <input type="text" name="fourPsHouseholdId">
        </div>

    </div>

    <!-- DISABILITY INFORMATION -->
    <h2>DISABILITY INFORMATION (If Applicable)</h2>

    <div class="form-grid">

        <div>
            <label>With Disability?</label>
            <select name="withDisability" id="withDisability" onchange="toggleDisabilityFields()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div id="disabilityTypeDiv" style="display: none;">
            <label>Type of Disability</label>
            <input type="text" name="disabilityType">
        </div>

        <div id="manifestationDiv" style="display: none;">
            <label>Manifestation / Details</label>
            <input type="text" name="manifestation">
        </div>

        <div id="pwdIdDiv" style="display: none;">
            <label>Have PWD ID?</label>
            <select name="pwdId">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

        <div id="pwdIdNumberDiv" style="display: none;">
            <label>PWD ID Number</label>
            <input type="text" name="pwdIdNumber">
        </div>

    </div>

    <script>
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
            var select = document.querySelector('select[name="pwdId"]');
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
    </script>

    <hr>

    <!-- CURRENT ADDRESS -->
    <h2>CURRENT ADDRESS</h2>

    <div class="form-grid">

        <div>
            <label>House No.</label>
            <input type="text" name="house_number">
        </div>

        <div>
            <label>Street</label>
            <input type="text" name="street">
        </div>

        <div>
            <label>Barangay</label>
            <input type="text" name="barangay" required>
        </div>

        <div>
            <label>City / Municipality</label>
            <input type="text" name="city_municipality" required>
        </div>

        <div>
            <label>Province</label>
            <input type="text" name="province" required>
        </div>

        <div>
            <label>Country</label>
            <input type="text" name="country" value="Philippines">
        </div>

        <div>
            <label>Zip Code</label>
            <input type="text" name="zip_code">
        </div>

    </div>

    <!-- PERMANENT ADDRESS -->
    <h2>PERMANENT ADDRESS</h2>

    <div class="form-grid">

        <div>
            <label>Same as Current Address?</label>
            <select name="sameAsCurrent" id="sameAsCurrent" onchange="togglePermanentAddress()">
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>

    </div>

    <div id="permanentAddressDiv" style="display: none;">
        <div class="form-grid">

            <div>
                <label>Permanent House No.</label>
                <input type="text" name="permanent_house_number">
            </div>

            <div>
                <label>Permanent Street</label>
                <input type="text" name="permanent_street">
            </div>

            <div>
                <label>Permanent Barangay</label>
                <input type="text" name="permanent_barangay">
            </div>

            <div>
                <label>Permanent City / Municipality</label>
                <input type="text" name="permanent_city">
            </div>

            <div>
                <label>Permanent Province</label>
                <input type="text" name="permanent_province">
            </div>

            <div>
                <label>Permanent Country</label>
                <input type="text" name="permanentCountry" value="Philippines">
            </div>

            <div>
                <label>Permanent Zip Code</label>
                <input type="text" name="permanentZipCode">
            </div>

        </div>
    </div>

    <script>
        function togglePermanentAddress() {
            var select = document.getElementById('sameAsCurrent');
            var div = document.getElementById('permanentAddressDiv');
            div.style.display = select.value === 'No' ? 'block' : 'none';
        }
    </script>

    <hr>

    <!-- FATHER INFORMATION -->
    <h2>FATHER'S INFORMATION</h2>

    <div class="form-grid">

        <div>
            <label>Father's Last Name</label>
            <input type="text" name="fatherLastName">
        </div>

        <div>
            <label>Father's First Name</label>
            <input type="text" name="fatherFirstName">
        </div>

        <div>
            <label>Father's Middle Name</label>
            <input type="text" name="fatherMiddleName">
        </div>

        <div>
            <label>Father's Contact Number</label>
            <input type="tel" name="fatherContact" pattern="^09[0-9]{9}$" maxlength="11" minlength="11" placeholder="Example: 09123456789" title="Enter 11-digit mobile number starting with 09">
        </div>

    </div>

    <!-- MOTHER INFORMATION -->
    <h2>MOTHER'S INFORMATION</h2>

    <div class="form-grid">

        <div>
            <label>Mother's Last Name</label>
            <input type="text" name="motherLastName">
        </div>

        <div>
            <label>Mother's First Name</label>
            <input type="text" name="motherFirstName">
        </div>

        <div>
            <label>Mother's Middle Name</label>
            <input type="text" name="motherMiddleName">
        </div>

        <div>
            <label>Mother's Contact Number</label>
            <input type="tel" name="motherContact" pattern="^09[0-9]{9}$" maxlength="11" minlength="11" placeholder="Example: 09123456789" title="Enter 11-digit mobile number starting with 09">
        </div>

    </div>

    <!-- GUARDIAN INFORMATION (Optional - if different from parents) -->
    <h2>GUARDIAN INFORMATION</h2>
    <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Only fill this if guardian is different from father or mother</p>
    
    <div class="form-grid">

        <div>
            <label>Has Guardian (different from parents)?</label>
            <select name="hasGuardian" id="hasGuardian" onchange="toggleGuardianFields()">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>

    </div>

    <div id="guardianFields" style="display: none;">
    <div class="form-grid">

        <div>
            <label>Guardian's Last Name</label>
            <input type="text" name="guardianLastName">
        </div>

        <div>
            <label>Guardian's First Name</label>
            <input type="text" name="guardianFirstName">
        </div>

        <div>
            <label>Guardian's Middle Name</label>
            <input type="text" name="guardianMiddleName">
        </div>

        <div>
            <label>Guardian's Contact Number</label>
            <input type="tel" name="guardianContact" pattern="^09[0-9]{9}$" maxlength="11" minlength="11" placeholder="Example: 09123456789" title="Enter 11-digit mobile number starting with 09">
        </div>

    </div>
    </div>

    <hr>

    <!-- PREVIOUS SCHOOL INFORMATION -->
    <h2>PREVIOUS SCHOOL INFORMATION</h2>

    <div class="form-grid">

        <div>
            <label>Last School Attended</label>
            <input type="text" name="lastSchoolAttended" required>
        </div>

        <div>
            <label>School ID (If available)</label>
            <input type="text" name="schoolId">
        </div>

        <div>
            <label>Last Grade Completed</label>
            <select name="lastGradeCompleted">
                <option value="">Select</option>
                <option value="Grade 10">Grade 10</option>
                <option value="Grade 11">Grade 11</option>
                <option value="Grade 12">Grade 12</option>
            </select>
        </div>

        <div>
            <label>Last School Year Completed</label>
            <select name="lastSchoolYearCompleted">
                <option value="">Select</option>
                <option value="2024-2025">2024-2025</option>
                <option value="2025-2026">2025-2026</option>
            </select>
        </div>

    </div>

    <!-- LEARNING MODALITY -->
    <h2>PREFERRED LEARNING MODALITY</h2>

    <div class="form-grid">

        <div>
            <label>Blended Learning</label>
            <select name="blended">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div>
            <label>Modular (Print)</label>
            <select name="modularPrint">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div>
            <label>Modular (Digital)</label>
            <select name="modularDigital">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div>
            <label>Online</label>
            <select name="online">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div>
            <label>Homeschooling</label>
            <select name="homeschooling">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div>
            <label>Educational TV</label>
            <select name="educationalTv">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div>
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
    <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Please upload available documents. These can be submitted later if not available now.</p>

    <div class="form-grid">

        <div>
            <label>PSA Birth Certificate (Optional)</label>
            <input type="file" name="psaBirthCertificate" accept=".pdf,.jpg,.png">
        </div>

        <div>
            <label>Form 138 / Report Card (Optional)</label>
            <input type="file" name="form138" accept=".pdf,.jpg,.png">
        </div>

        <div>
            <label>Student ID (Optional - if available)</label>
            <input type="file" name="studentIdCopy" accept=".pdf,.jpg,.png">
        </div>

    </div>

    <button type="submit">SUBMIT ENROLLMENT</button>

</form>

</body>
</html>
