<?php
session_start();

include "../../DB_Connection/Connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Website_Files/login.php");
    exit();
}

// Verify this is a student
if ($_SESSION['role_id'] != 1) {
    header("Location: ../../Website_Files/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $student_id = $_POST['student_id'] ?? null;
    $user_id = $_SESSION['user_id'];
    
    // Verify the student_id belongs to this user
    $verifyStmt = $connection->prepare("SELECT application_id FROM students WHERE student_id = ? AND user_id = ?");
    $verifyStmt->bind_param("ii", $student_id, $user_id);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    
    if ($verifyResult->num_rows !== 1) {
        header("Location: ../../Website_Files/Student_Files/profile_page.php?error=unauthorized");
        exit();
    }
    
    $student = $verifyResult->fetch_assoc();
    $application_id = $student['application_id'];
    $verifyStmt->close();
    
    // Get editable fields for student_applications
    $contact_number = trim($_POST['contact_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $facebook_profile = trim($_POST['facebook_profile'] ?? '');
    
    // Additional info fields for student_applications
    $place_of_birth = trim($_POST['place_of_birth'] ?? '');
    $religion = trim($_POST['religion'] ?? '');
    $mother_tongue = trim($_POST['mother_tongue'] ?? '');
    
    // Address fields for student_addresses
    $house_number = trim($_POST['house_number'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city_municipality = trim($_POST['city_municipality'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $country = trim($_POST['country'] ?? 'Philippines');
    
    // Father info for student_family
    $father_first_name = trim($_POST['father_first_name'] ?? '');
    $father_middle_name = trim($_POST['father_middle_name'] ?? '');
    $father_last_name = trim($_POST['father_last_name'] ?? '');
    $father_contact = trim($_POST['father_contact'] ?? '');
    
    // Mother info for student_family
    $mother_first_name = trim($_POST['mother_first_name'] ?? '');
    $mother_middle_name = trim($_POST['mother_middle_name'] ?? '');
    $mother_last_name = trim($_POST['mother_last_name'] ?? '');
    $mother_contact = trim($_POST['mother_contact'] ?? '');
    
    // Guardian info for student_family
    $guardian_first_name = trim($_POST['guardian_first_name'] ?? '');
    $guardian_middle_name = trim($_POST['guardian_middle_name'] ?? '');
    $guardian_last_name = trim($_POST['guardian_last_name'] ?? '');
    $guardian_contact = trim($_POST['guardian_contact'] ?? '');
    
    // Validation
    $errors = [];
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (!empty($contact_number) && !preg_match('/^[0-9+\-\s]+$/', $contact_number)) {
        $errors[] = "Invalid contact number format";
    }
    
    if (!empty($errors)) {
        header("Location: ../../Website_Files/Student_Files/profile_page.php?error=invalid_input");
        exit();
    }
    
    // Handle file uploads
    $uploadDir = "../../uploads/Documents/student/";
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
    $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Handle profile image upload
    $profileImageValue = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($fileExt, $imageTypes)) {
            $newFileName = time() . "_PROFILE_" . $application_id . "." . $fileExt;
            $destination = "../../uploads/Profile/student/" . $newFileName;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $profileImageValue = $newFileName;
            }
        }
    }
    
    // Handle document uploads
    $documentFields = ['psa_birth_certificate', 'form_138', 'student_id_copy'];
    $documentUpdates = [];
    $documentValues = [];
    
    foreach ($documentFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$field];
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($fileExt, $allowedTypes)) {
                continue;
            }
            
            $newFileName = time() . "_" . strtoupper(str_replace(' ', '_', $field)) . "_" . $application_id . "." . $fileExt;
            $destination = $uploadDir . $newFileName;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $documentUpdates[$field] = $newFileName;
            }
        }
    }
    
    // Start transaction for multiple table updates
    $connection->begin_transaction();
    
    try {
        // 1. Update student_applications (contact info + basic info)
        $appUpdate = "UPDATE student_applications SET contact_number = ?, email = ?, facebook_profile = ?, place_of_birth = ?, religion = ?, mother_tongue = ?";
        $appParams = [$contact_number, $email, $facebook_profile, $place_of_birth, $religion, $mother_tongue];
        $appTypes = "ssssss";
        
        // Add profile image if uploaded
        if ($profileImageValue) {
            $appUpdate .= ", profile_image = ?";
            $appParams[] = $profileImageValue;
            $appTypes .= "s";
        }
        
        $appUpdate .= " WHERE application_id = ?";
        $appParams[] = $application_id;
        $appTypes .= "i";
        
        $appStmt = $connection->prepare($appUpdate);
        $appStmt->bind_param($appTypes, ...$appParams);
        $appStmt->execute();
        $appStmt->close();
        
        // 2. Update student_addresses
        $addrUpdate = "UPDATE student_addresses SET house_number = ?, street = ?, barangay = ?, city_municipality = ?, province = ?, country = ? WHERE application_id = ?";
        $addrStmt = $connection->prepare($addrUpdate);
        $addrStmt->bind_param("ssssssi", $house_number, $street, $barangay, $city_municipality, $province, $country, $application_id);
        $addrStmt->execute();
        $addrStmt->close();
        
        // 3. Update student_family
        $familyUpdate = "UPDATE student_family SET 
            father_first_name = ?, father_middle_name = ?, father_last_name = ?, father_contact = ?,
            mother_first_name = ?, mother_middle_name = ?, mother_last_name = ?, mother_contact = ?,
            guardian_first_name = ?, guardian_middle_name = ?, guardian_last_name = ?, guardian_contact = ?
            WHERE application_id = ?";
        $familyStmt = $connection->prepare($familyUpdate);
        $familyStmt->bind_param("ssssssssssssi", 
            $father_first_name, $father_middle_name, $father_last_name, $father_contact,
            $mother_first_name, $mother_middle_name, $mother_last_name, $mother_contact,
            $guardian_first_name, $guardian_middle_name, $guardian_last_name, $guardian_contact,
            $application_id
        );
        $familyStmt->execute();
        $familyStmt->close();
        
        // 4. Update student_documents if any new files uploaded
        if (!empty($documentUpdates)) {
            $docSetParts = [];
            $docParams = [];
            $docTypes = "";
            
            foreach ($documentUpdates as $field => $value) {
                $docSetParts[] = "$field = ?";
                $docParams[] = $value;
                $docTypes .= "s";
            }
            
            $docParams[] = $application_id;
            $docTypes .= "i";
            
            $docUpdate = "UPDATE student_documents SET " . implode(", ", $docSetParts) . " WHERE application_id = ?";
            $docStmt = $connection->prepare($docUpdate);
            $docStmt->bind_param($docTypes, ...$docParams);
            $docStmt->execute();
            $docStmt->close();
        }
        
        // Commit transaction
        $connection->commit();
        
        header("Location: ../../Website_Files/Student_Files/profile_page.php?success=updated");
        exit();
        
    } catch (Exception $e) {
        $connection->rollback();
        header("Location: ../../Website_Files/Student_Files/profile_page.php?error=update_failed");
        exit();
    }
    
} else {
    header("Location: ../../Website_Files/Student_Files/profile_page.php");
    exit();
}
?>
