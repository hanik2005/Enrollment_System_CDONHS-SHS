<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/DB_Connection/Connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/mailer_details.php';
include "student_enrollment_validation.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Enrollment Details
    $enrollmentType = $_POST['enrollmentType'] ?? 'New';

    // Student Personal Information
    $lrn = $_POST['lrn'] ?? null;
    $lastName = $_POST['lastName'] ?? null;
    $firstName = $_POST['firstName'] ?? null;
    $middleName = $_POST['middleName'] ?? null;
    $extensionName = $_POST['extensionName'] ?? null;
    $dateOfBirth = $_POST['dateOfBirth'] ?? null;
    $placeOfBirth = $_POST['placeOfBirth'] ?? null;
    $sex = $_POST['sex'] ?? null;
    $religion = $_POST['religion'] ?? null;
    $motherTongue = $_POST['motherTongue'] ?? null;
    $contactNumber = $_POST['contactNumber'] ?? null;
    $email = $_POST['email'] ?? null;
    $facebookProfile = $_POST['facebookProfile'] ?? null;

    // Indigenous / 4PS
    $indigenousCommunity = $_POST['indigenousCommunity'] ?? 'No';
    $ipSpecify = $_POST['ipSpecify'] ?? null;
    $fourPsBeneficiary = $_POST['fourPsBeneficiary'] ?? 'No';
    $fourPsHouseholdId = $_POST['fourPsHouseholdId'] ?? null;

    // Disability
    $withDisability = $_POST['withDisability'] ?? 'No';
    $pwdId = $_POST['pwdId'] ?? 'No';
    $pwdIdNumber = $_POST['pwdIdNumber'] ?? null;

    // Address
    $house_number = $_POST['house_number'] ?? null;
    $street = $_POST['street'] ?? null;
    $barangay = $_POST['barangay'] ?? null;
    $city_municipality = $_POST['city_municipality'] ?? null;
    $province = $_POST['province'] ?? null;
    $country = $_POST['country'] ?? 'Philippines';
    $zip_code = $_POST['zip_code'] ?? null;

    // Permanent Address
    $sameAsCurrent = $_POST['sameAsCurrent'] ?? 'Yes';

    $permanent_house_number = $_POST['permanent_house_number'] ?? null;
    $permanent_street = $_POST['permanent_street'] ?? null;
    $permanent_barangay = $_POST['permanent_barangay'] ?? null;
    $permanent_city = $_POST['permanent_city'] ?? null;
    $permanent_province = $_POST['permanent_province'] ?? null;
    $permanent_country = $_POST['permanent_country'] ?? 'Philippines';
    $permanent_zip_code = $_POST['permanent_zip_code'] ?? null;

    if ($sameAsCurrent === 'Yes') {
        $permanent_house_number = $house_number;
        $permanent_street = $street;
        $permanent_barangay = $barangay;
        $permanent_city = $city_municipality;
        $permanent_province = $province;
        $permanent_country = $country;
        $permanent_zip_code = $zip_code;
    }

    // Parents
    $fatherLastName = $_POST['fatherLastName'] ?? null;
    $fatherFirstName = $_POST['fatherFirstName'] ?? null;
    $fatherMiddleName = $_POST['fatherMiddleName'] ?? null;
    $fatherContact = $_POST['fatherContact'] ?? null;

    $motherLastName = $_POST['motherLastName'] ?? null;
    $motherFirstName = $_POST['motherFirstName'] ?? null;
    $motherMiddleName = $_POST['motherMiddleName'] ?? null;
    $motherContact = $_POST['motherContact'] ?? null;

    $guardianLastName = $_POST['guardianLastName'] ?? null;
    $guardianFirstName = $_POST['guardianFirstName'] ?? null;
    $guardianMiddleName = $_POST['guardianMiddleName'] ?? null;
    $guardianContact = $_POST['guardianContact'] ?? null;

    // Previous School
    $lastSchoolAttended = $_POST['lastSchoolAttended'] ?? null;
    $schoolId = $_POST['schoolId'] ?? null;
    $lastGradeCompleted = $_POST['lastGradeCompleted'] ?? null;
    $lastSchoolYearCompleted = $_POST['lastSchoolYearCompleted'] ?? null;

    // Learning Program
    $attendedLearningProgram = $_POST['attendedLearningProgram'] ?? 'No';
    $learningProgramSpecify = $_POST['learningProgramSpecify'] ?? null;

    // Learning Modality
    $blended = isset($_POST['blended']) && $_POST['blended'] == 1 ? 1 : 0;
    $modularPrint = isset($_POST['modularPrint']) && $_POST['modularPrint'] == 1 ? 1 : 0;
    $modularDigital = isset($_POST['modularDigital']) && $_POST['modularDigital'] == 1 ? 1 : 0;
    $online = isset($_POST['online']) && $_POST['online'] == 1 ? 1 : 0;
    $homeschooling = isset($_POST['homeschooling']) && $_POST['homeschooling'] == 1 ? 1 : 0;
    $educationalTv = isset($_POST['educationalTv']) && $_POST['educationalTv'] == 1 ? 1 : 0;
    $radioBasedTv = isset($_POST['radioBasedTv']) && $_POST['radioBasedTv'] == 1 ? 1 : 0;

    // Normalize phone numbers
    if($contactNumber) $contactNumber = preg_replace('/[^0-9]/', '', $contactNumber);
    if($fatherContact) $fatherContact = preg_replace('/[^0-9]/', '', $fatherContact);
    if($motherContact) $motherContact = preg_replace('/[^0-9]/', '', $motherContact);
    if($guardianContact) $guardianContact = preg_replace('/[^0-9]/', '', $guardianContact);

    // ===============================
    // VALIDATION
    // ===============================

    $data = [
        'enrollmentType'=>$enrollmentType,
        'firstName'=>$firstName,
        'lastName'=>$lastName,
        'lrn'=>$lrn,
        'email'=>$email,
        'contactNumber'=>$contactNumber,
        'barangay'=>$barangay,
        'city_municipality'=>$city_municipality,
        'province'=>$province,
        'lastSchoolAttended'=>$lastSchoolAttended,
        'dateOfBirth'=>$dateOfBirth
    ];

    $errors = validateStudentEnrollment($connection,$data);

    if(!empty($errors)){
        echo "<script>alert('".implode("\\n",$errors)."');window.history.back();</script>";
        exit;
    }

    // ===============================
    // FILE UPLOAD
    // ===============================

    $uploadDir = $_SERVER['DOCUMENT_ROOT']."/SMS_CDONHS-SHS_WEBSITE/uploads/Documents/student/";

    if(!is_dir($uploadDir)){
        mkdir($uploadDir,0777,true);
    }

    function uploadOptionalFile($inputName,$prefix,$uploadDir){

        if(!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== 0){
            return null;
        }

        $fileName = time()."_".$prefix."_".basename($_FILES[$inputName]['name']);
        $target = $uploadDir.$fileName;

        if(move_uploaded_file($_FILES[$inputName]['tmp_name'],$target)){
            return $fileName;
        }

        return null;
    }

    $psaBirthCertificate = uploadOptionalFile("psaBirthCertificate","PSA",$uploadDir);
    $form138 = uploadOptionalFile("form138","FORM138",$uploadDir);
    $studentIdCopy = uploadOptionalFile("studentIdCopy","STUDENTID",$uploadDir);

    // ===============================
    // DATABASE INSERT - Multiple Tables
    // ===============================
    
    // Start transaction for data integrity
    $connection->begin_transaction();
    
    try {
        // 1. Insert into student_applications (only core personal fields)
        $applicationStatus = 'Pending';
        $sql = "INSERT INTO student_applications(
            lrn, last_name, first_name, middle_name, extension_name,
            date_of_birth, sex, place_of_birth, religion, mother_tongue,
            enrollment_type, application_status,
            email, contact_number, facebook_profile
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssssssssssss", 
            $lrn, $lastName, $firstName, $middleName, $extensionName,
            $dateOfBirth, $sex, $placeOfBirth, $religion, $motherTongue,
            $enrollmentType, $applicationStatus,
            $email, $contactNumber, $facebookProfile
        );
        $stmt->execute();
        $applicationId = $connection->insert_id;
        $stmt->close();

        // 2. Insert into student_addresses
        $addrSql = "INSERT INTO student_addresses(
            application_id, house_number, street, barangay, city_municipality, 
            province, country, zip_code, same_as_current,
            permanent_house_number, permanent_street, permanent_barangay,
            permanent_city, permanent_province, permanent_country, permanent_zip_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $addrStmt = $connection->prepare($addrSql);
        $addrStmt->bind_param("isssssssssssssss",
            $applicationId, $house_number, $street, $barangay, $city_municipality,
            $province, $country, $zip_code, $sameAsCurrent,
            $permanent_house_number, $permanent_street, $permanent_barangay,
            $permanent_city, $permanent_province, $permanent_country, $permanent_zip_code
        );
        $addrStmt->execute();
        $addrStmt->close();

        // 3. Insert into student_family
        $familySql = "INSERT INTO student_family(
            application_id, father_last_name, father_first_name, father_middle_name, father_contact,
            mother_last_name, mother_first_name, mother_middle_name, mother_contact,
            guardian_last_name, guardian_first_name, guardian_middle_name, guardian_contact
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $familyStmt = $connection->prepare($familySql);
        $familyStmt->bind_param("issssssssssss",
            $applicationId,
            $fatherLastName, $fatherFirstName, $fatherMiddleName, $fatherContact,
            $motherLastName, $motherFirstName, $motherMiddleName, $motherContact,
            $guardianLastName, $guardianFirstName, $guardianMiddleName, $guardianContact
        );
        $familyStmt->execute();
        $familyStmt->close();

        // 4. Insert into student_learning_modality
        $modalitySql = "INSERT INTO student_learning_modality(
            application_id, blended, modular_print, modular_digital, online, homeschooling, educational_tv, radio_based_tv
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $modalityStmt = $connection->prepare($modalitySql);
        $modalityStmt->bind_param("iiiiiiii",
            $applicationId, $blended, $modularPrint, $modularDigital, $online, $homeschooling, $educationalTv, $radioBasedTv
        );
        $modalityStmt->execute();
        $modalityStmt->close();

        // 5. Insert into student_documents
        $docSql = "INSERT INTO student_documents(
            application_id, psa_birth_certificate, form_138, student_id_copy
        ) VALUES (?, ?, ?, ?)";
        
        $docStmt = $connection->prepare($docSql);
        $docStmt->bind_param("isss",
            $applicationId, $psaBirthCertificate, $form138, $studentIdCopy
        );
        $docStmt->execute();
        $docStmt->close();

        // 6. Insert into student_social_info
        $socialSql = "INSERT INTO student_social_info(
            application_id, indigenous_community, ip_specify, four_ps_beneficiary, four_ps_household_id
        ) VALUES (?, ?, ?, ?, ?)";
        
        $socialStmt = $connection->prepare($socialSql);
        $socialStmt->bind_param("issss",
            $applicationId, $indigenousCommunity, $ipSpecify, $fourPsBeneficiary, $fourPsHouseholdId
        );
        $socialStmt->execute();
        $socialStmt->close();

        // 7. Insert into student_special_needs
        $sneSql = "INSERT INTO student_special_needs(
            application_id, with_disability, has_pwd_id, pwd_id_number
        ) VALUES (?, ?, ?, ?)";
        
        $sneStmt = $connection->prepare($sneSql);
        $sneStmt->bind_param("isss",
            $applicationId, $withDisability, $pwdId, $pwdIdNumber
        );
        $sneStmt->execute();
        $sneStmt->close();

        // 8. Insert into student_learning_program
        $learnProgramSql = "INSERT INTO student_learning_program(
            application_id, attended_learning_program, learning_program_specify
        ) VALUES (?, ?, ?)";
        
        $learnProgramStmt = $connection->prepare($learnProgramSql);
        $learnProgramStmt->bind_param("iss",
            $applicationId, $attendedLearningProgram, $learningProgramSpecify
        );
        $learnProgramStmt->execute();
        $learnProgramStmt->close();

        // 9. Insert into student_previous_school
        $prevSql = "INSERT INTO student_previous_school(
            application_id, last_school_attended, school_id, last_grade_completed, last_school_year_completed
        ) VALUES (?, ?, ?, ?, ?)";
        
        $prevStmt = $connection->prepare($prevSql);
        $prevStmt->bind_param("issss",
            $applicationId, $lastSchoolAttended, $schoolId, $lastGradeCompleted, $lastSchoolYearCompleted
        );
        $prevStmt->execute();
        $prevStmt->close();

        // Commit transaction
        $connection->commit();

        // Send email confirmation
        $gradeLevel = $_POST['gradeLevel'] ?? '';
        $schoolYear = date("Y")."-".(date("Y")+1);

        try{
            $mail->setFrom('cdonhsshsacc@gmail.com','CDONHS-SHS Enrollment Office');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "CDONHS-SHS Enrollment Application Submitted";

            $mail->Body = "
            <h3>Good day $firstName $lastName</h3>
            <p>Your enrollment application has been <b>submitted successfully</b>.</p>
            <p>Status: <b>PENDING</b></p>
            <p>Grade Level: $gradeLevel</p>
            <p>School Year: $schoolYear</p>
            <br>
            <p>Thank you</p>
            <b>CDONHS-SHS Enrollment Office</b>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email failed: " . $mail->ErrorInfo);
        }

        echo "<script>
            alert('Enrollment application submitted successfully! Please check your email for confirmation.');
            window.location.href = '../../Website_Files/thank_you.php';
        </script>";
        exit;

    } catch (Exception $e) {
        $connection->rollback();
        echo "<script>alert('Error submitting application: " . addslashes($e->getMessage()) . "');window.history.back();</script>";
        exit;
    }

} else {
    header("Location: ../../Website_Files/Student_Online_Form.php");
    exit;
}
?>
