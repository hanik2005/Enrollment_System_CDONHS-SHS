<?php
// Disable all error display - only log errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start(); // start output buffering
header('Content-Type: application/json');

include "../../DB_Connection/Connection.php";
include "mailer_details.php";
include_once "audit_trail_helper.php";
include_once "admin_access.php";

if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$admin = requireAdminAccess($connection, '../../Website_Files/login.php');
$adminUserId = (int) $admin['user_id'];

if (!$admin) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Super admin or registrar access required']);
    exit;
}

// read JSON from frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['updates'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$updates = $data['updates'];

$updatedCount = 0;
$errorCount = 0;

foreach ($updates as $item) {
    if (!isset($item['application_id'], $item['status'])) {
        $errorCount++;
        continue;
    }
    
    $applicationId = (int)$item['application_id'];
    $status = trim((string) $item['status']);
    $remarks = trim((string) ($item['remarks'] ?? ""));

    if ($status === 'Rejected' && $remarks === '') {
        $errorCount++;
        error_log("Validation Error: remarks required for rejected application_id: " . $applicationId);
        continue;
    }
    
    // Get student info first (before any update)
    $stmtGetStudent = $connection->prepare("SELECT first_name, last_name, email, lrn FROM student_applications WHERE application_id = ?");
    $stmtGetStudent->bind_param("i", $applicationId);
    $stmtGetStudent->execute();
    $resultStudent = $stmtGetStudent->get_result();
    $studentInfo = $resultStudent->fetch_assoc();
    $stmtGetStudent->close();
    
    if (!$studentInfo) {
        $errorCount++;
        error_log("Student not found for application_id: " . $applicationId);
        continue;
    }
    
    // Check if email exists
    if (empty($studentInfo['email'])) {
        error_log("No email found for student application_id: " . $applicationId);
    }
    
    // Handle Rejected status
    if ($status === 'Rejected') {
        // Send rejection email only if email exists
        if (!empty($studentInfo['email'])) {
            try {
                $mail->setFrom('cdonhsshsacc@gmail.com', 'CDONHS-SHS Admin');
                $mail->addAddress($studentInfo['email']);
            $mail->isHTML(true);
            
            $mail->Subject = "Application Rejected - CDONHS-SHS";
            $remarksHtml = !empty($remarks) ? "<p><strong>Reason:</strong> {$remarks}</p>" : "";
            $mail->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; }
                        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
                        .header { font-size: 18px; font-weight: bold; color: #dc3545; margin-bottom: 15px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>Good day {$studentInfo['first_name']} {$studentInfo['last_name']},</div>
                        <p>We regret to inform you that your application to CDONHS-SHS has been <strong>REJECTED</strong>.</p>
                        {$remarksHtml}
                        <p>Thank you for your interest in CDONHS-SHS.</p>
                        <p><br><b>CDONHS-SHS Admin</b></p>
                    </div>
                </body>
                </html>
            ";
            
            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
        
        $mail->clearAddresses();
        } // End if email exists

        // Delete the application and all related data from normalized tables
        $connection->begin_transaction();
        try {
            // Delete from related tables first (due to foreign key constraints)
            $tables = [
                'student_addresses',
                'student_family', 
                'student_learning_modality',
                'student_documents',
                'student_social_info',
                'student_special_needs',
                'student_previous_school',
                'student_learning_program'
            ];
            
            foreach ($tables as $table) {
                $deleteStmt = $connection->prepare("DELETE FROM {$table} WHERE application_id = ?");
                $deleteStmt->bind_param("i", $applicationId);
                $deleteStmt->execute();
                $deleteStmt->close();
            }
            
            // Finally delete from main table
            $stmtDelete = $connection->prepare("DELETE FROM student_applications WHERE application_id = ?");
            $stmtDelete->bind_param("i", $applicationId);
            $stmtDelete->execute();
            $stmtDelete->close();
            
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            error_log("Delete Error: " . $e->getMessage());
            $errorCount++;
            continue;
        }

        logAdminAudit(
            $connection,
            'APPLICATION_REJECTED',
            'student_applications',
            (string) $applicationId,
            "Rejected and removed application #{$applicationId}",
            [
                'status' => $status,
                'remarks' => $remarks,
                'student_name' => trim($studentInfo['first_name'] . ' ' . $studentInfo['last_name']),
            ],
            $adminUserId
        );
        
        $updatedCount++;
        continue;
    }
    
    // Handle Approved status - Create user and student records
    if ($status === 'Approved') {
        $connection->begin_transaction();
        
        try {
            // Get Student role_id
            $roleResult = $connection->query("SELECT role_id FROM roles WHERE role_name = 'Student'");
            $role_id = $roleResult ? $roleResult->fetch_assoc()['role_id'] : 1;
            
            // Create login credentials - username is LRN
            $username = $studentInfo['lrn'];
            $defaultPassword = substr($studentInfo['lrn'], -6);
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
            
            // 1. INSERT INTO USERS FIRST
            $stmtUser = $connection->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
            $stmtUser->bind_param("ssi", $username, $hashedPassword, $role_id);
            $stmtUser->execute();
            $userId = $connection->insert_id;
            $stmtUser->close();
            
            // Get current school year
            $currentMonth = date('n');
            $currentYear = date('Y');
            if ($currentMonth >= 8) {
                $school_year = $currentYear . '-' . ($currentYear + 1);
            } else {
                $school_year = ($currentYear - 1) . '-' . $currentYear;
            }
            
            // 2. INSERT INTO STUDENTS (enlistment_status = Not Enlisted)
            $enrollmentStatus = "Active";
            $enlistmentStatus = "Not Enlisted";
            $dateEnrolled = date("Y-m-d");
            $stmtStudent = $connection->prepare("INSERT INTO students (user_id, application_id, enrollment_status, date_enrolled, enlistment_status, school_year) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtStudent->bind_param("iissss", $userId, $applicationId, $enrollmentStatus, $dateEnrolled, $enlistmentStatus, $school_year);
            $stmtStudent->execute();
            $stmtStudent->close();
            
            // 3. UPDATE student_applications status
            $stmtUpdate = $connection->prepare("UPDATE student_applications SET application_status = ?, remarks = ? WHERE application_id = ?");
            $stmtUpdate->bind_param("ssi", $status, $remarks, $applicationId);
            $stmtUpdate->execute();
            $stmtUpdate->close();
            
            $connection->commit();
            
            // Send approval email with credentials only if email exists
            if (!empty($studentInfo['email'])) {
                try {
                    $mail->setFrom('cdonhsshsacc@gmail.com', 'CDONHS-SHS Admin');
                    $mail->addAddress($studentInfo['email']);
                $mail->isHTML(true);
                
                $mail->Subject = "Application Approved - CDONHS-SHS";
                $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; }
                            .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
                            .header { font-size: 18px; font-weight: bold; color: #28a745; margin-bottom: 15px; }
                            .credentials { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>Good day {$studentInfo['first_name']} {$studentInfo['last_name']},</div>
                            <p>Congratulations! Your application to CDONHS-SHS has been <strong>APPROVED</strong>.</p>
                            <p>You may now proceed with the enrollment process.</p>
                            <div class='credentials'>
                                <p><strong>Username:</strong> {$username}</p>
                                <p><strong>Temporary Password:</strong> {$defaultPassword}</p>
                            </div>
                            <p>Please change your password after logging in.</p>
                            <p>Thank you,<br><b>CDONHS-SHS Admin</b></p>
                        </div>
                    </body>
                    </html>
                ";
                
                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
            }
            
            $mail->clearAddresses();
            } // End if email exists

            logAdminAudit(
                $connection,
                'APPLICATION_APPROVED',
                'student_applications',
                (string) $applicationId,
                "Approved application #{$applicationId}",
                [
                    'status' => $status,
                    'remarks' => $remarks,
                    'student_name' => trim($studentInfo['first_name'] . ' ' . $studentInfo['last_name']),
                    'created_user_id' => $userId,
                ],
                $adminUserId
            );
            
        } catch (Exception $e) {
            $connection->rollback();
            error_log("Transaction Error: " . $e->getMessage());
            $errorCount++;
            continue;
        }
        
        $updatedCount++;
        continue;
    }
    
    // Handle other statuses (Pending, etc.) - just update status
    $stmt = $connection->prepare("UPDATE student_applications SET application_status = ?, remarks = ? WHERE application_id = ?");
    $stmt->bind_param("ssi", $status, $remarks, $applicationId);
    $stmt->execute();
    $stmt->close();
    
    // Send email notification for Pending status only if email exists
    if (!empty($studentInfo['email'])) {
        try {
            $mail->setFrom('cdonhsshsacc@gmail.com', 'CDONHS-SHS Admin');
            $mail->addAddress($studentInfo['email']);
        $mail->isHTML(true);
        
        $mail->Subject = "Application Status Update - CDONHS-SHS";
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; }
                    .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
                    .header { font-size: 18px; font-weight: bold; color: #ffc107; margin-bottom: 15px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>Good day {$studentInfo['first_name']} {$studentInfo['last_name']},</div>
                    <p>Your application status has been updated to <strong>{$status}</strong>.</p>
                    <p>Thank you,<br><b>CDONHS-SHS Admin</b></p>
                </div>
            </body>
            </html>
        ";
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
    
    $mail->clearAddresses();
    } // End if email exists for pending

    logAdminAudit(
        $connection,
        'APPLICATION_STATUS_UPDATED',
        'student_applications',
        (string) $applicationId,
        "Updated application #{$applicationId} to {$status}",
        [
            'status' => $status,
            'remarks' => $remarks,
            'student_name' => trim($studentInfo['first_name'] . ' ' . $studentInfo['last_name']),
        ],
        $adminUserId
    );
    
    $updatedCount++;
}

ob_end_clean();

if ($updatedCount === 0 && $errorCount > 0) {
    $success = false;
    $message = 'No updates applied. Remarks are required for rejected applications.';
} elseif ($errorCount > 0) {
    $success = true;
    $message = "Applications updated with some validation errors. Total updated: {$updatedCount}";
} else {
    $success = true;
    $message = "Applications updated successfully. Total: {$updatedCount}";
}

echo json_encode([
    'success' => $success,
    'message' => $message,
    'debug' => [
        'updated' => $updatedCount,
        'errors' => $errorCount
    ]
]);
exit;
