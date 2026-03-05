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
    
    // Get editable fields from database schema
    $contact_number = trim($_POST['contact_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $facebook_profile = trim($_POST['facebook_profile'] ?? '');
    
    // Address fields
    $house_number = trim($_POST['house_number'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city_municipality = trim($_POST['city_municipality'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $country = trim($_POST['country'] ?? 'Philippines');
    
    // Additional info fields
    $place_of_birth = trim($_POST['place_of_birth'] ?? '');
    $religion = trim($_POST['religion'] ?? '');
    $mother_tongue = trim($_POST['mother_tongue'] ?? '');
    
    // Father info
    $father_first_name = trim($_POST['father_first_name'] ?? '');
    $father_middle_name = trim($_POST['father_middle_name'] ?? '');
    $father_last_name = trim($_POST['father_last_name'] ?? '');
    $father_contact = trim($_POST['father_contact'] ?? '');
    
    // Mother info
    $mother_first_name = trim($_POST['mother_first_name'] ?? '');
    $mother_middle_name = trim($_POST['mother_middle_name'] ?? '');
    $mother_last_name = trim($_POST['mother_last_name'] ?? '');
    $mother_contact = trim($_POST['mother_contact'] ?? '');
    
    // Guardian info
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
    $documentFields = ['psa_birth_certificate', 'form_138', 'student_id_copy'];
    $documentUpdates = [];
    $documentValues = [];
    $documentTypes = "";
    $profileImageUpdate = "";
    $profileImageValue = null;
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate image type
        if (in_array($fileExt, $imageTypes)) {
            // Generate unique filename
            $newFileName = time() . "_PROFILE_" . $application_id . "." . $fileExt;
            $destination = "../../uploads/Profile/student/" . $newFileName;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $profileImageUpdate = "profile_image = ?";
                $profileImageValue = $newFileName;
            }
        }
    }
    
    foreach ($documentFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$field];
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate file type
            if (!in_array($fileExt, $allowedTypes)) {
                continue;
            }
            
            // Generate unique filename
            $timestamp = time();
            $newFileName = $timestamp . "_" . strtoupper(str_replace(' ', '_', $field)) . "_" . $application_id . "." . $fileExt;
            $destination = $uploadDir . $newFileName;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $documentUpdates[] = "$field = ?";
                $documentValues[] = $newFileName;
                $documentTypes .= "s";
            }
        }
    }
    
    // Build the update query with database column names
    $updateQuery = "
        UPDATE student_applications 
        SET contact_number = ?, 
            email = ?, 
            facebook_profile = ?, 
            house_number = ?,
            street = ?,
            barangay = ?, 
            city_municipality = ?, 
            province = ?,
            country = ?,
            place_of_birth = ?,
            religion = ?,
            mother_tongue = ?,
            father_first_name = ?,
            father_middle_name = ?,
            father_last_name = ?,
            father_contact = ?,
            mother_first_name = ?,
            mother_middle_name = ?,
            mother_last_name = ?,
            mother_contact = ?,
            guardian_first_name = ?,
            guardian_middle_name = ?,
            guardian_last_name = ?,
            guardian_contact = ?
    ";
    
    // Add profile image update if uploaded
    if (!empty($profileImageUpdate)) {
        $updateQuery .= ", " . $profileImageUpdate;
    }
    
    // Add document updates if any
    if (!empty($documentUpdates)) {
        $updateQuery .= ", " . implode(", ", $documentUpdates);
    }
    
    $updateQuery .= " WHERE application_id = ?";
    
    $updateStmt = $connection->prepare($updateQuery);
    
    // Build parameters array
    $params = [
        $contact_number, 
        $email, 
        $facebook_profile, 
        $house_number,
        $street,
        $barangay, 
        $city_municipality, 
        $province,
        $country,
        $place_of_birth,
        $religion,
        $mother_tongue,
        $father_first_name,
        $father_middle_name,
        $father_last_name,
        $father_contact,
        $mother_first_name,
        $mother_middle_name,
        $mother_last_name,
        $mother_contact,
        $guardian_first_name,
        $guardian_middle_name,
        $guardian_last_name,
        $guardian_contact
    ];
    
    // Add profile image value if uploaded
    if (!empty($profileImageValue)) {
        $params[] = $profileImageValue;
    }
    
    // Add document values
    foreach ($documentValues as $docValue) {
        $params[] = $docValue;
    }
    
    // Add application_id
    $params[] = $application_id;
    
    // Build types string (24 string params + profile image + documents + int)
    $types = "ssssssssssssssssssssssss";
    if (!empty($profileImageValue)) {
        $types .= "s";
    }
    $types .= $documentTypes . "i";
    
    // Bind parameters dynamically
    $updateStmt->bind_param($types, ...$params);
    
    if ($updateStmt->execute()) {
        $updateStmt->close();
        header("Location: ../../Website_Files/Student_Files/profile_page.php?success=updated");
        exit();
    } else {
        $updateStmt->close();
        header("Location: ../../Website_Files/Student_Files/profile_page.php?error=update_failed");
        exit();
    }
}

$connection->close();
?>
