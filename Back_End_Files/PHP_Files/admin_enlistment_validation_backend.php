<?php
include "../../DB_Connection/Connection.php";
include "mailer_details.php";

if (isset($_POST['confirm']) && isset($_POST['status'])) {
    $updatedCount = 0;
    $emailAttemptedCount = 0;
    $emailSentCount = 0;
    $emailFailedCount = 0;
    $emailSkippedCount = 0;

    foreach ($_POST['status'] as $student_id => $status) {
        $student_id = (int)$student_id;

        // Update student's enlistment status
        $stmt = $connection->prepare("
            UPDATE students 
            SET enlistment_status=? 
            WHERE student_id=?
        ");

        $stmt->bind_param("si", $status, $student_id);
        $stmt->execute();
        $stmt->close();
        $updatedCount++;

        // If admin confirms as "Enlisted", send approval email
        if ($status === 'Enlisted') {
            // Get student's email and name for notification
            $stmtGetEmail = $connection->prepare("
                SELECT sa.email, sa.first_name, sa.last_name
                FROM students s
                INNER JOIN student_applications sa ON s.application_id = sa.application_id
                WHERE s.student_id = ?
            ");
            $stmtGetEmail->bind_param("i", $student_id);
            $stmtGetEmail->execute();
            $resultEmail = $stmtGetEmail->get_result();
            $studentInfo = $resultEmail->fetch_assoc();
            $stmtGetEmail->close();

            // Send approval email notification
            if ($studentInfo && !empty($studentInfo['email'])) {
                $emailAttemptedCount++;
                try {
                    $mail->clearAllRecipients();
                    $mail->setFrom('cdonhsshsacc@gmail.com', 'CDONHS-SHS Enrollment Office');
                    $mail->addAddress($studentInfo['email']);
                    $mail->isHTML(true);
                    $mail->Subject = "Enlistment Application Approved";

                    $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; }
                            .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
                            .header { font-size: 18px; font-weight: bold; color: #28a745; margin-bottom: 15px; }
                            .status { font-weight: bold; color: #28a745; }
                            .footer { margin-top: 30px; font-size: 14px; color: #777; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>Good day {$studentInfo['first_name']} {$studentInfo['last_name']},</div>
                            
                            <p>We are pleased to inform you that your <b>enlistment application</b> has been <span class='status'>APPROVED</span>!</p>
                            
                            <p>You are now officially enlisted. Please proceed to enrollment to complete your registration.</p>
                            
                            <p>Thank you and welcome to CDONHS-SHS!<br>
                            <b>CDONHS-SHS Enrollment Office</b></p>
                            
                            <div class='footer'>&copy; " . date("Y") . " CDONHS-SHS. All rights reserved.</div>
                        </div>
                    </body>
                    </html>
                    ";

                    $mail->send();
                    $emailSentCount++;
                } catch (Exception $e) {
                    error_log("Mailer Error: " . $mail->ErrorInfo);
                    $emailFailedCount++;
                }
            } else {
                $emailSkippedCount++;
            }
        }

        // If admin rejects, delete enlistment data and send email notification
        if ($status === 'Rejected') {
            // Delete student's enlistment from student_strand table
            $stmtReject = $connection->prepare("
                DELETE FROM student_strand 
                WHERE student_id = ?
            ");
            $stmtReject->bind_param("i", $student_id);
            $stmtReject->execute();
            $stmtReject->close();

            // Get student's email and name for notification
            $stmtGetEmail = $connection->prepare("
                SELECT sa.email, sa.first_name, sa.last_name
                FROM students s
                INNER JOIN student_applications sa ON s.application_id = sa.application_id
                WHERE s.student_id = ?
            ");
            $stmtGetEmail->bind_param("i", $student_id);
            $stmtGetEmail->execute();
            $resultEmail = $stmtGetEmail->get_result();
            $studentInfo = $resultEmail->fetch_assoc();
            $stmtGetEmail->close();

            // Send rejection email notification
            if ($studentInfo && !empty($studentInfo['email'])) {
                $emailAttemptedCount++;
                try {
                    $mail->clearAllRecipients();
                    $mail->setFrom('cdonhsshsacc@gmail.com', 'CDONHS-SHS Enrollment Office');
                    $mail->addAddress($studentInfo['email']);
                    $mail->isHTML(true);
                    $mail->Subject = "Enlistment Application Rejected";

                    $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; }
                            .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
                            .header { font-size: 18px; font-weight: bold; color: #dc3545; margin-bottom: 15px; }
                            .status { font-weight: bold; color: #dc3545; }
                            .footer { margin-top: 30px; font-size: 14px; color: #777; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>Good day {$studentInfo['first_name']} {$studentInfo['last_name']},</div>
                            
                            <p>We regret to inform you that your <b>enlistment application</b> has been <span class='status'>REJECTED</span>.</p>
                            
                            <p>You may contact the school administration for more information regarding this decision.</p>
                            
                            <p>Thank you for your understanding.<br>
                            <b>CDONHS-SHS Enrollment Office</b></p>
                            
                            <div class='footer'>&copy; " . date("Y") . " CDONHS-SHS. All rights reserved.</div>
                        </div>
                    </body>
                    </html>
                    ";

                    $mail->send();
                    $emailSentCount++;
                } catch (Exception $e) {
                    error_log("Mailer Error: " . $mail->ErrorInfo);
                    $emailFailedCount++;
                }
            } else {
                $emailSkippedCount++;
            }
        }
    }

    $query = http_build_query([
        'success' => 1,
        'updated' => $updatedCount,
        'emails_attempted' => $emailAttemptedCount,
        'emails_sent' => $emailSentCount,
        'emails_failed' => $emailFailedCount,
        'emails_skipped' => $emailSkippedCount
    ]);

    header("Location: ../../Website_Files/Admin_Files/enlistment_validation_page.php?" . $query);
    exit;
}
?>
