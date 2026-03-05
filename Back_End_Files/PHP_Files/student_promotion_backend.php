<?php
// Note: session_start() is already called in student_progress_page.php
// DO NOT call it again here

// Include mailer setup (same as other backend files)
require_once $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/mailer_details.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

/* ========================= */
/* VERIFY TEACHER SESSION      */
/* ========================= */
$stmt = mysqli_prepare($connection, "
    SELECT teacher_id 
    FROM teachers 
    WHERE user_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$teacher = mysqli_fetch_assoc($result);

if (!$teacher) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$teacher_id = $teacher['teacher_id'];

/* ==================== GET TEACHER'S ADVISORY INFO ==================== */

// Get teacher's advisory grade level from teacher_advisory table
$advisoryGradeLevel = null;
$advisorySectionId = null;

$getAdvisory = $connection->prepare("
    SELECT grade_level, section_id
    FROM teacher_advisory 
    WHERE teacher_id = ?
    LIMIT 1
");

if ($getAdvisory) {
    $getAdvisory->bind_param("i", $teacher_id);
    $getAdvisory->execute();
    $advisoryResult = $getAdvisory->get_result();
    if ($advisory = $advisoryResult->fetch_assoc()) {
        $advisoryGradeLevel = $advisory['grade_level'];
        $advisorySectionId = $advisory['section_id'];
    }
}

// If not found in teacher_advisory, check section table for adviser_id
if ($advisoryGradeLevel === null) {
    $checkSection = $connection->prepare("
        SELECT grade_level, section_id 
        FROM section 
        WHERE adviser_id = ?
        LIMIT 1
    ");
    
    if ($checkSection) {
        $checkSection->bind_param("i", $teacher_id);
        $checkSection->execute();
        $sectionResult = $checkSection->get_result();
        if ($section = $sectionResult->fetch_assoc()) {
            $advisoryGradeLevel = $section['grade_level'];
            $advisorySectionId = $section['section_id'];
        }
    }
}

/* ==================== EMAIL FUNCTION ==================== */

function sendPromotionEmail($student_email, $student_name, $action_type, $teacher_remarks = '') {
    global $mail;
    
    try {
        // Reset mail object for new email
        $mail->ClearAllRecipients();
        $mail->clearAttachments();
        $mail->clearReplyTos();
        
        $mail->setFrom('cdonhsshsacc@gmail.com', 'CDONHS-SHS Enrollment Office');
        $mail->addAddress($student_email, $student_name);
        
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        if ($action_type === 'promote') {
            $mail->Subject = 'Promotion Notice - Grade 12';
            $mail->Body = "
                <h2>Dear {$student_name},</h2>
                <p>Greetings from Cagayan De Oro National High School - Senior High School!</p>
                <p>We are pleased to inform you that you have been <strong>promoted to Grade 12</strong>.</p>
                " . (!empty($teacher_remarks) ? "<p><strong>Remarks from your adviser:</strong> {$teacher_remarks}</p>" : "") . "
                <p>Please proceed to the registrar's office for your enrollment details for the next school year.</p>
                <p>Congratulations and keep up the good work!</p>
                <br>
                <p>Best regards,<br>CDONHS-SHS Administration</p>
            ";
        } elseif ($action_type === 'graduate') {
            $mail->Subject = 'Graduation Notice - CDONHS-SHS';
            $mail->Body = "
                <h2>Dear {$student_name},</h2>
                <p>Congratulations from Cagayan De Oro National High School - Senior High School!</p>
                <p>We are proud to inform you that you have been recommended for <strong>GRADUATION</strong>.</p>
                " . (!empty($teacher_remarks) ? "<p><strong>Remarks from your adviser:</strong> {$teacher_remarks}</p>" : "") . "
                <p>Please proceed to the registrar's office for your graduation requirements and clearance.</p>
                <p>Congratulations on your successful completion of Senior High School!</p>
                <br>
                <p>Best regards,<br>CDONHS-SHS Administration</p>
            ";
        } elseif ($action_type === 'retain') {
            $mail->Subject = 'Academic Standing Notice - CDONHS-SHS';
            $mail->Body = "
                <h2>Dear {$student_name},</h2>
                <p>Greetings from Cagayan De Oro National High School - Senior High School.</p>
                <p>We would like to inform you that you have been marked as <strong>Retained</strong> for the current school year.</p>
                " . (!empty($teacher_remarks) ? "<p><strong>Remarks from your adviser:</strong> {$teacher_remarks}</p>" : "") . "
                <p>Please visit the registrar's office to discuss your academic plan and available options.</p>
                <p>We are here to support you in your academic journey.</p>
                <br>
                <p>Best regards,<br>CDONHS-SHS Administration</p>
            ";
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/* ==================== HANDLING FORM SUBMISSION ==================== */

$message = '';
$message_type = '';

// Get current school year (matching student_update_remarks.php format)
$currentMonth = date('n');
$currentYear = date('Y');
if ($currentMonth >= 8) {
    $current_school_year = $currentYear . '-' . ($currentYear + 1);
} else {
    $current_school_year = ($currentYear - 1) . '-' . $currentYear;
}

// Handle Save All Students
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_all_students'])) {
    
    $students = $_POST['students'] ?? [];
    $updated_count = 0;
    $emailed_count = 0;
    
    // Debug: log received data
    error_log("=== SAVE ALL STUDENTS ===");
    error_log("Students data: " . print_r($students, true));
    
    if (!empty($students) && is_array($students)) {
        foreach ($students as $student_id => $data) {
            if (!is_array($data)) continue;
            
            $student_id = (int)$student_id;
            if ($student_id <= 0) continue;
            
            $recommended_status = $data['recommended_status'] ?? 'Pending';
            $teacher_remarks = trim($data['teacher_remarks'] ?? '');
            $current_grade_level = (int)($data['current_grade_level'] ?? 11);
            
            // Skip if status is Pending
            if ($recommended_status === 'Pending') {
                continue;
            }
            
            // Get student email from student_applications table (users table has no email column)
            $emailStmt = $connection->prepare("
                SELECT sa.first_name, sa.last_name, sa.email
                FROM students s
                INNER JOIN student_applications sa ON s.application_id = sa.application_id
                WHERE s.student_id = ?
            ");
            
            $student_email = '';
            $student_name = '';
            
            if ($emailStmt) {
                $emailStmt->bind_param("i", $student_id);
                $emailStmt->execute();
                $emailResult = $emailStmt->get_result();
                if ($emailData = $emailResult->fetch_assoc()) {
                    // Get email from student_applications
                    $student_email = $emailData['email'] ?? '';
                    $student_name = ($emailData['first_name'] ?? '') . ' ' . ($emailData['last_name'] ?? '');
                    
                    error_log("Found email for student $student_id: $student_email (from student_applications)");
                } else {
                    error_log("No email data found for student $student_id");
                }
            }
            
            // If still no email, log error
            if (empty($student_email)) {
                error_log("WARNING: Empty email for student $student_id - email will not be sent");
            }
            
            // Process based on status
            if ($recommended_status === 'Promote to Grade 12') {
                // Get student's current strand
                $strandStmt = $connection->prepare("
                    SELECT strand_id FROM student_strand WHERE student_id = ?
                ");
                $strandStmt->bind_param("i", $student_id);
                $strandStmt->execute();
                $strandResult = $strandStmt->get_result();
                $strandData = $strandResult->fetch_assoc();
                $strand_id = $strandData['strand_id'] ?? null;
                
                $new_section_id = null;
                
                if ($strand_id) {
                    // Find available section in Grade 12 with same strand (max 50 students)
                    // Get sections ordered alphabetically (A, B, C, etc.)
                    $sectionStmt = $connection->prepare("
                        SELECT s.section_id, s.section_name,
                               (SELECT COUNT(*) FROM student_strand ss 
                                WHERE ss.section_id = s.section_id AND ss.grade_level = 12) as student_count
                        FROM section s
                        WHERE s.grade_level = 12 AND s.strand_id = ?
                        ORDER BY s.section_name
                    ");
                    
                    $sectionStmt->bind_param("i", $strand_id);
                    $sectionStmt->execute();
                    $sectionResult = $sectionStmt->get_result();
                    
                    while ($section = $sectionResult->fetch_assoc()) {
                        if ($section['student_count'] < 50) {
                            $new_section_id = $section['section_id'];
                            break;
                        }
                    }
                }
                
                // Update grade_level, section_id and school_year in student_strand
                if ($new_section_id) {
                    $updateStmt = $connection->prepare("
                        UPDATE student_strand SET grade_level = 12, section_id = ? WHERE student_id = ?
                    ");
                    $updateStmt->bind_param("ii", $new_section_id, $student_id);
                } else {
                    $updateStmt = $connection->prepare("
                        UPDATE student_strand SET grade_level = 12 WHERE student_id = ?
                    ");
                    $updateStmt->bind_param("i", $student_id);
                }
                
                if ($updateStmt) {
                    $updateStmt->execute();
                    $updated_count++;
                    
                    // Also update school_year in students table
                    $updateSchoolYear = $connection->prepare("UPDATE students SET school_year = ? WHERE student_id = ?");
                    $updateSchoolYear->bind_param("si", $current_school_year, $student_id);
                    $updateSchoolYear->execute();
                    
                    // Send email
                    if (!empty($student_email)) {
                        sendPromotionEmail($student_email, $student_name, 'promote', $teacher_remarks);
                        $emailed_count++;
                    }
                }
                
            } elseif ($recommended_status === 'Graduate') {
                // Update enrollment_status to Graduated, enlistment_status to Promoted, and school_year
                $updateStmt = $connection->prepare("
                    UPDATE students 
                    SET enrollment_status = 'Graduated', enlistment_status = 'Promoted', school_year = ?
                    WHERE student_id = ?
                ");
                $updateStmt->bind_param("si", $current_school_year, $student_id);
                $updateStmt->execute();
                $updated_count++;
                
                // Send email
                if (!empty($student_email)) {
                    sendPromotionEmail($student_email, $student_name, 'graduate', $teacher_remarks);
                    $emailed_count++;
                }
                
            } elseif ($recommended_status === 'Retained') {
                // For retained: send email and update school_year
                $updateSchoolYear = $connection->prepare("UPDATE students SET school_year = ? WHERE student_id = ?");
                $updateSchoolYear->bind_param("si", $current_school_year, $student_id);
                $updateSchoolYear->execute();
                
                if (!empty($student_email)) {
                    sendPromotionEmail($student_email, $student_name, 'retain', $teacher_remarks);
                    $emailed_count++;
                }
                
                // Still count as updated (remarks saved)
                $updated_count++;
            }
        }
        
        if ($updated_count > 0) {
            $message = "Successfully updated $updated_count student(s)";
            if ($emailed_count > 0) {
                $message .= " and sent $emailed_count email notification(s)";
            }
            $message .= "!";
            $message_type = 'success';
        } else {
            $message = "No changes to save.";
            $message_type = 'error';
        }
    } else {
        $message = "No student data to save.";
        $message_type = 'error';
    }
}

// Handle bulk update with checkbox
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_update_promotion'])) {
    $selected_students = $_POST['selected_students'] ?? [];
    $bulk_status = $_POST['bulk_status'] ?? 'Pending';
    $bulk_remarks = $_POST['bulk_remarks'] ?? '';
    
    $updated_count = 0;
    $emailed_count = 0;
    
    // Debug: Log received data
    error_log("Bulk update - Status: " . $bulk_status);
    error_log("Selected students count: " . count($selected_students));
    
    if (!empty($selected_students) && is_array($selected_students)) {
        foreach ($selected_students as $student_id) {
            $student_id = (int)$student_id;
            if ($student_id <= 0) continue;
            
            // Get student email from student_applications table (users table has no email column)
            $emailStmt = $connection->prepare("
                SELECT sa.first_name, sa.last_name, sa.email
                FROM students s
                INNER JOIN student_applications sa ON s.application_id = sa.application_id
                WHERE s.student_id = ?
            ");
            
            $student_email = '';
            $student_name = '';
            
            if ($emailStmt) {
                $emailStmt->bind_param("i", $student_id);
                $emailStmt->execute();
                $emailResult = $emailStmt->get_result();
                if ($emailData = $emailResult->fetch_assoc()) {
                    // Get email from student_applications
                    $student_email = $emailData['email'] ?? '';
                    $student_name = ($emailData['first_name'] ?? '') . ' ' . ($emailData['last_name'] ?? '');
                }
            }
            
            // Process based on status
            if ($bulk_status === 'Promote to Grade 12') {
                // Get student's current strand
                $strandStmt = $connection->prepare("
                    SELECT strand_id FROM student_strand WHERE student_id = ?
                ");
                $strandStmt->bind_param("i", $student_id);
                $strandStmt->execute();
                $strandResult = $strandStmt->get_result();
                $strandData = $strandResult->fetch_assoc();
                $strand_id = $strandData['strand_id'] ?? null;
                
                $new_section_id = null;
                
                if ($strand_id) {
                    // Find available section in Grade 12 with same strand (max 50 students)
                    $sectionStmt = $connection->prepare("
                        SELECT s.section_id, s.section_name,
                               (SELECT COUNT(*) FROM student_strand ss 
                                WHERE ss.section_id = s.section_id AND ss.grade_level = 12) as student_count
                        FROM section s
                        WHERE s.grade_level = 12 AND s.strand_id = ?
                        ORDER BY s.section_name
                    ");
                    
                    $sectionStmt->bind_param("i", $strand_id);
                    $sectionStmt->execute();
                    $sectionResult = $sectionStmt->get_result();
                    
                    while ($section = $sectionResult->fetch_assoc()) {
                        if ($section['student_count'] < 50) {
                            $new_section_id = $section['section_id'];
                            break;
                        }
                    }
                }
                
                // Update grade_level, section_id and school_year in student_strand (bulk)
                if ($new_section_id) {
                    $updateStmt = $connection->prepare("
                        UPDATE student_strand SET grade_level = 12, section_id = ? WHERE student_id = ?
                    ");
                    $updateStmt->bind_param("ii", $new_section_id, $student_id);
                } else {
                    $updateStmt = $connection->prepare("
                        UPDATE student_strand SET grade_level = 12 WHERE student_id = ?
                    ");
                    $updateStmt->bind_param("i", $student_id);
                }
                
                if ($updateStmt) {
                    $updateStmt->execute();
                    $updated_count++;
                    
                    // Also update school_year in students table
                    $updateSchoolYear = $connection->prepare("UPDATE students SET school_year = ? WHERE student_id = ?");
                    $updateSchoolYear->bind_param("si", $current_school_year, $student_id);
                    $updateSchoolYear->execute();
                    
                    if (!empty($student_email)) {
                        sendPromotionEmail($student_email, $student_name, 'promote', $bulk_remarks);
                        $emailed_count++;
                    }
                }
                
            } elseif ($bulk_status === 'Graduate') {
                $updateStmt = $connection->prepare("
                    UPDATE students 
                    SET enrollment_status = 'Graduated', enlistment_status = 'Promoted', school_year = ?
                    WHERE student_id = ?
                ");
                $updateStmt->bind_param("si", $current_school_year, $student_id);
                $updateStmt->execute();
                $updated_count++;
                
                if (!empty($student_email)) {
                    sendPromotionEmail($student_email, $student_name, 'graduate', $bulk_remarks);
                    $emailed_count++;
                }
                
            } elseif ($bulk_status === 'Retained') {
                // For retained: send email and update school_year (bulk)
                $updateSchoolYear = $connection->prepare("UPDATE students SET school_year = ? WHERE student_id = ?");
                $updateSchoolYear->bind_param("si", $current_school_year, $student_id);
                $updateSchoolYear->execute();
                
                if (!empty($student_email)) {
                    sendPromotionEmail($student_email, $student_name, 'retain', $bulk_remarks);
                    $emailed_count++;
                }
                $updated_count++;
            }
        }
        
        if ($updated_count > 0) {
            $message = "Updated $updated_count student(s)";
            if ($emailed_count > 0) {
                $message .= " and sent $emailed_count email notification(s)";
            }
            $message .= " successfully!";
            $message_type = 'success';
        } else {
            $message = "Please select a valid status.";
            $message_type = 'error';
        }
    } else {
        $message = "Please select at least one student.";
        $message_type = 'error';
    }
}
?>
