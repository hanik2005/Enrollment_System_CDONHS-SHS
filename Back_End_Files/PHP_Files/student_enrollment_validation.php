<?php

// Helper function to validate name (allows letters, spaces, hyphens, apostrophes, and periods for abbreviations)
function validateName($name, $fieldName = 'Name') {
    $errors = [];
    
    // Trim whitespace
    $name = trim($name);
    
    // Check if empty
    if (empty($name)) {
        $errors[] = "$fieldName is required.";
        return $errors;
    }
    
    // Check minimum length (at least 2 characters)
    if (strlen($name) < 2) {
        $errors[] = "$fieldName must be at least 2 characters long.";
    }
    
    // Check maximum length (100 characters)
    if (strlen($name) > 100) {
        $errors[] = "$fieldName must not exceed 100 characters.";
    }
    
    // Check for valid characters: letters, spaces, hyphens, apostrophes, and periods
    if (!preg_match('/^[a-zA-Z][a-zA-Z \-\'.]+$/', $name)) {
        $errors[] = "$fieldName contains invalid characters. Only letters, spaces, hyphens, apostrophes, and periods are allowed.";
    }
    
    // Check for at least one letter (no numbers, symbols only)
    if (!preg_match('/[a-zA-Z]/', $name)) {
        $errors[] = "$fieldName must contain at least one letter.";
    }
    
    // Check for multiple consecutive spaces
    if (preg_match('/\s{2,}/', $name)) {
        $errors[] = "$fieldName must not contain multiple consecutive spaces.";
    }
    
    // Check for leading/trailing spaces
    if ($name !== trim($name)) {
        $errors[] = "$fieldName must not have leading or trailing spaces.";
    }
    
    return $errors;
}

function validateStudentEnrollment($connection, $data) {

    $errors = [];

    // ===============================
    // VALIDATE REQUIRED FIELDS
    // ===============================
    if (empty($data['enrollmentType'] ?? '')) {
        $errors[] = "Enrollment Type is required.";
    }

    // ===============================
    // VALIDATE NAME FIELDS
    // ===============================
    $nameFields = [
        'firstName' => 'First Name',
        'lastName' => 'Last Name',
        'middleName' => 'Middle Name',
        'extensionName' => 'Extension Name',
        'fatherFirstName' => "Father's First Name",
        'motherFirstName' => "Mother's First Name"
    ];
    
    foreach ($nameFields as $field => $displayName) {
        if (isset($data[$field]) && !empty($data[$field])) {
            $nameErrors = validateName($data[$field], $displayName);
            $errors = array_merge($errors, $nameErrors);
        }
    }

    // Validate father's last name
    if (isset($data['fatherLastName']) && !empty($data['fatherLastName'])) {
        $nameErrors = validateName($data['fatherLastName'], "Father's Last Name");
        $errors = array_merge($errors, $nameErrors);
    }

    // Validate mother's last name
    if (isset($data['motherLastName']) && !empty($data['motherLastName'])) {
        $nameErrors = validateName($data['motherLastName'], "Mother's Last Name");
        $errors = array_merge($errors, $nameErrors);
    }

    // Validate guardian names if provided
    if (isset($data['guardianFirstName']) && !empty($data['guardianFirstName'])) {
        $nameErrors = validateName($data['guardianFirstName'], "Guardian's First Name");
        $errors = array_merge($errors, $nameErrors);
    }

    if (isset($data['guardianLastName']) && !empty($data['guardianLastName'])) {
        $nameErrors = validateName($data['guardianLastName'], "Guardian's Last Name");
        $errors = array_merge($errors, $nameErrors);
    }

    // ===============================
    // VALIDATE LRN (Required, numbers only)
    // ===============================
    if (empty($data['lrn'] ?? '')) {
        $errors[] = "LRN is required.";
    } else {
        $lrn = $data['lrn'];
        
        // Check if LRN contains only numbers
        if (!preg_match('/^[0-9]+$/', $lrn)) {
            $errors[] = "LRN must contain numbers only.";
        }
        
        // Check exact length (12 digits for Philippine LRN)
        if (strlen($lrn) !== 12) {
            $errors[] = "LRN must be exactly 12 digits.";
        }
    }

    // ===============================
    // CHECK DUPLICATE NAME (Same first, last, middle, extension)
    // ===============================
    if (isset($data['firstName'], $data['lastName'])) {
        $firstName = trim($data['firstName']);
        $lastName = trim($data['lastName']);
        $middleName = isset($data['middleName']) ? trim($data['middleName']) : null;
        $extensionName = isset($data['extensionName']) ? trim($data['extensionName']) : null;
        
        // Check for exact match in student_applications
        $checkDuplicateName = $connection->prepare(
            "SELECT application_id, first_name, last_name 
             FROM student_applications 
             WHERE LOWER(first_name) = LOWER(?) 
               AND LOWER(last_name) = LOWER(?)
               AND (LOWER(middle_name) = LOWER(?) OR (middle_name IS NULL AND ? IS NULL))
               AND (LOWER(extension_name) = LOWER(?) OR (extension_name IS NULL AND ? IS NULL))"
        );
        $checkDuplicateName->bind_param("ssssss", $firstName, $lastName, $middleName, $middleName, $extensionName, $extensionName);
        $checkDuplicateName->execute();
        $checkDuplicateName->store_result();

        if ($checkDuplicateName->num_rows > 0) {
            $errors[] = "A student with the same name (first name, last name, middle name, and extension name) already exists in the system.";
        }
    }

    // ===============================
    // VALIDATE CONTACT NUMBERS
    // ===============================
    if (isset($data['contactNumber'])) {
        $contactNumber = preg_replace('/[^0-9]/', '', $data['contactNumber']);
        
        // Check if it's exactly 11 digits and starts with 09
        if (!preg_match('/^09[0-9]{9}$/', $contactNumber)) {
            $errors[] = "Contact number must be an 11-digit Philippine mobile number (e.g., 09123456789).";
        } else {
            // Update data with normalized number
            $data['contactNumber'] = $contactNumber;
        }
    }

    // ===============================
    // VALIDATE FATHER CONTACT NUMBER
    // ===============================
    if (isset($data['fatherContact']) && !empty($data['fatherContact'])) {
        $fatherContact = preg_replace('/[^0-9]/', '', $data['fatherContact']);
        if (!empty($fatherContact) && !preg_match('/^09[0-9]{9}$/', $fatherContact)) {
            $errors[] = "Father's contact must be an 11-digit Philippine mobile number.";
        }
    }

    // ===============================
    // VALIDATE MOTHER CONTACT NUMBER
    // ===============================
    if (isset($data['motherContact']) && !empty($data['motherContact'])) {
        $motherContact = preg_replace('/[^0-9]/', '', $data['motherContact']);
        if (!empty($motherContact) && !preg_match('/^09[0-9]{9}$/', $motherContact)) {
            $errors[] = "Mother's contact must be an 11-digit Philippine mobile number.";
        }
    }

    // ===============================
    // VALIDATE GUARDIAN CONTACT NUMBER
    // ===============================
    if (isset($data['guardianContact']) && !empty($data['guardianContact'])) {
        $guardianContact = preg_replace('/[^0-9]/', '', $data['guardianContact']);
        if (!empty($guardianContact) && !preg_match('/^09[0-9]{9}$/', $guardianContact)) {
            $errors[] = "Guardian's contact must be an 11-digit Philippine mobile number.";
        }
    }

    // ===============================
    // CHECK DUPLICATE LRN
    // ===============================
    if (isset($data['lrn']) && !empty($data['lrn'])) {
        $checkLRN = $connection->prepare(
            "SELECT application_id 
             FROM student_applications 
             WHERE lrn = ?"
        );
        $checkLRN->bind_param("s", $data['lrn']);
        $checkLRN->execute();
        $checkLRN->store_result();

        if ($checkLRN->num_rows > 0) {
            $errors[] = "LRN already exists in the system.";
        }
    }

    // ===============================
    // VALIDATE EMAIL FORMAT
    // ===============================
    if (isset($data['email'])) {
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        
        // Check valid email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        } else {
            // Check for valid domain (basic check)
            if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                $errors[] = "Please enter a valid email address with a proper domain.";
            }
            $data['email'] = $email;
        }
    }

    // ===============================
    // CHECK DUPLICATE EMAIL (across both tables)
    // ===============================
    if (isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        // Check in student_applications
        $checkStudentEmail = $connection->prepare(
            "SELECT application_id 
             FROM student_applications 
             WHERE email = ?"
        );
        
        if ($checkStudentEmail) {
            $checkStudentEmail->bind_param("s", $data['email']);
            $checkStudentEmail->execute();
            $checkStudentEmail->store_result();

            if ($checkStudentEmail->num_rows > 0) {
                $errors[] = "Email already exists in the system (used by another student or teacher).";
            }
            $checkStudentEmail->close();
        }
    }

    // ===============================
    // CHECK DUPLICATE CONTACT NUMBER (across both tables)
    // ===============================
    if (isset($data['contactNumber'])) {
        // Check in student_applications
        $checkStudentContact = $connection->prepare(
            "SELECT application_id 
             FROM student_applications 
             WHERE contact_number = ?"
        );
        
        if ($checkStudentContact) {
            $contactNum = preg_replace('/[^0-9]/', '', $data['contactNumber']);
            $checkStudentContact->bind_param("s", $contactNum);
            $checkStudentContact->execute();
            $checkStudentContact->store_result();


            if ($checkStudentContact->num_rows > 0) {
                $errors[] = "Contact number already exists in the system.";
            }
            $checkStudentContact->close();
        }
    }

    // ===============================
    // VALIDATE DATE OF BIRTH
    // ===============================
    if (isset($data['dateOfBirth']) && !empty($data['dateOfBirth'])) {
        $dob = new DateTime($data['dateOfBirth']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        
        // Check if date is valid
        if ($dob > $today) {
            $errors[] = "Date of birth cannot be in the future.";
        }
        
        // Check if age is reasonable (at least 10 years old and not over 100)
        if ($age < 10 || $age > 100) {
            $errors[] = "Please enter a valid date of birth.";
        }
    }

    // ===============================
    // VALIDATE ADDRESS FIELDS
    // ===============================
    if (empty($data['barangay'] ?? '')) {
        $errors[] = "Barangay is required.";
    }

    if (empty($data['city_municipality'] ?? '')) {
        $errors[] = "City/Municipality is required.";
    }

    if (empty($data['province'] ?? '')) {
        $errors[] = "Province is required.";
    }

    // ===============================
    // VALIDATE SCHOOL INFORMATION
    // ===============================
    if (empty($data['lastSchoolAttended'] ?? '')) {
        $errors[] = "Last School Attended is required.";
    }

    return $errors;
}
