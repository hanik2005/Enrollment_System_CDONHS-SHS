<?php
session_start();

include "../../DB_Connection/Connection.php";
include_once "mailer_details.php";
include_once "audit_trail_helper.php";
include_once "admin_access.php";

$admin = requireAdminAccess($connection, "../login.php");
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);

$allowedDocuments = [
    'psa_birth_certificate' => 'PSA Birth Certificate',
    'form_138' => 'Form 138',
    'student_id_copy' => 'Student ID Copy',
];

function setDocumentFlash(string $type, string $message): void
{
    $_SESSION['document_correction_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function redirectToDocumentCorrectionPage(?int $applicationId = null): void
{
    $location = "../../Website_Files/Admin_Files/document_correction_page.php";
    if (!empty($applicationId) && $applicationId > 0) {
        $location .= "?application_id=" . (int) $applicationId;
    }
    header("Location: " . $location);
    exit;
}

/* ========================= */
/* HANDLE ACTIONS            */
/* ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim($_POST['action'] ?? '');
    $applicationId = (int) ($_POST['application_id'] ?? 0);
    $documentField = trim($_POST['document_field'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if ($applicationId <= 0 || !isset($allowedDocuments[$documentField])) {
        setDocumentFlash('error', 'Invalid request. Please try again.');
        redirectToDocumentCorrectionPage($applicationId);
    }

    $documentLabel = $allowedDocuments[$documentField];
    $studentQuery = "
        SELECT
            sa.first_name,
            sa.last_name,
            sa.email,
            COALESCE(sd.$documentField, '') AS document_file
        FROM student_applications sa
        LEFT JOIN student_documents sd ON sd.application_id = sa.application_id
        WHERE sa.application_id = ?
        LIMIT 1
    ";

    $studentStmt = $connection->prepare($studentQuery);
    $studentStmt->bind_param("i", $applicationId);
    $studentStmt->execute();
    $studentData = $studentStmt->get_result()->fetch_assoc();
    $studentStmt->close();

    if (!$studentData) {
        setDocumentFlash('error', 'Student record not found.');
        redirectToDocumentCorrectionPage($applicationId);
    }

    $fullName = trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['last_name'] ?? ''));
    $studentEmail = trim($studentData['email'] ?? '');
    $documentFile = trim($studentData['document_file'] ?? '');

    if ($action === 'send_correction_email') {
        if ($documentFile === '') {
            setDocumentFlash('error', "Cannot send correction email. {$documentLabel} is not uploaded.");
            redirectToDocumentCorrectionPage($applicationId);
        }

        if ($studentEmail === '') {
            setDocumentFlash('error', "Cannot send correction email to {$fullName}. Student email is missing.");
            redirectToDocumentCorrectionPage($applicationId);
        }

        if ($reason === '') {
            setDocumentFlash('error', 'Please provide a reason for the document correction request.');
            redirectToDocumentCorrectionPage($applicationId);
        }

        try {
            $mail->clearAllRecipients();
            $mail->setFrom('cdonhsshsacc@gmail.com', 'CDONHS-SHS Enrollment Office');
            $mail->addAddress($studentEmail, $fullName);
            $mail->isHTML(true);
            $mail->Subject = "Document Correction Required - {$documentLabel}";

            $safeReason = nl2br(htmlspecialchars($reason, ENT_QUOTES, 'UTF-8'));
            $mail->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; }
                        .container { max-width: 620px; margin: auto; border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: #f8fafc; }
                        .header { font-size: 18px; font-weight: bold; color: #b91c1c; margin-bottom: 12px; }
                        .doc-name { font-weight: bold; color: #1f2937; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>Document Correction Notice</div>
                        <p>Good day {$fullName},</p>
                        <p>Your submitted document <span class='doc-name'>{$documentLabel}</span> needs correction or re-upload.</p>
                        <p><strong>Admin remarks:</strong><br>{$safeReason}</p>
                        <p>Please log in to your student account and upload the corrected document as soon as possible.</p>
                        <p>Thank you.<br><strong>CDONHS-SHS Enrollment Office</strong></p>
                    </div>
                </body>
                </html>
            ";

            $mail->send();

            logAdminAudit(
                $connection,
                'DOCUMENT_CORRECTION_REQUESTED',
                'student_documents',
                (string) $applicationId,
                "Requested correction for {$documentLabel} (application #{$applicationId})",
                [
                    'document_field' => $documentField,
                    'document_label' => $documentLabel,
                    'reason' => $reason,
                    'student_name' => $fullName,
                    'student_email' => $studentEmail,
                ],
                $user_id
            );
            setDocumentFlash('success', "Correction email sent to {$fullName} for {$documentLabel}.");
        } catch (Exception $e) {
            error_log("Document correction email error: " . $mail->ErrorInfo);
            setDocumentFlash('error', 'Failed to send correction email. Check mail configuration.');
        }

        redirectToDocumentCorrectionPage($applicationId);
    }

    if ($action === 'delete_document') {
        if ($documentFile === '') {
            setDocumentFlash('error', "{$documentLabel} is already empty for {$fullName}.");
            redirectToDocumentCorrectionPage($applicationId);
        }

        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/uploads/Documents/student/' . $documentFile;

        if (file_exists($uploadPath) && !@unlink($uploadPath)) {
            setDocumentFlash('error', "Failed to delete file from server for {$documentLabel}.");
            redirectToDocumentCorrectionPage($applicationId);
        }

        $updateStmt = $connection->prepare("UPDATE student_documents SET $documentField = NULL WHERE application_id = ?");
        $updateStmt->bind_param("i", $applicationId);
        $updateStmt->execute();
        $updateStmt->close();

        logAdminAudit(
            $connection,
            'DOCUMENT_DELETED',
            'student_documents',
            (string) $applicationId,
            "Deleted {$documentLabel} for application #{$applicationId}",
            [
                'document_field' => $documentField,
                'document_label' => $documentLabel,
                'deleted_filename' => $documentFile,
                'student_name' => $fullName,
            ],
            $user_id
        );

        setDocumentFlash('success', "{$documentLabel} has been deleted for {$fullName}.");
        redirectToDocumentCorrectionPage($applicationId);
    }

    setDocumentFlash('error', 'Unknown action.');
    redirectToDocumentCorrectionPage($applicationId);
}

/* ========================= */
/* FLASH MESSAGE             */
/* ========================= */
$flashMessage = $_SESSION['document_correction_flash'] ?? null;
unset($_SESSION['document_correction_flash']);

/* ========================= */
/* FILTERS + TABLE DATA      */
/* ========================= */
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$filter_status = isset($_GET['filter_status']) ? trim($_GET['filter_status']) : '';
$filter_application_id = isset($_GET['application_id']) ? (int) $_GET['application_id'] : 0;

$validStatuses = ['Pending', 'Approved', 'Rejected', 'Conditionally Approved'];

$listSql = "
    SELECT
        sa.application_id,
        sa.first_name,
        sa.last_name,
        sa.middle_name,
        sa.extension_name,
        sa.email,
        sa.application_status,
        COALESCE(sd.psa_birth_certificate, '') AS psa_birth_certificate,
        COALESCE(sd.form_138, '') AS form_138,
        COALESCE(sd.student_id_copy, '') AS student_id_copy
    FROM student_applications sa
    LEFT JOIN student_documents sd ON sd.application_id = sa.application_id
    WHERE 1 = 1
";

$params = [];
$types = '';

if ($filter_application_id > 0) {
    $listSql .= " AND sa.application_id = ?";
    $types .= 'i';
    $params[] = $filter_application_id;
}

if ($search_name !== '') {
    $searchLike = '%' . $search_name . '%';
    $listSql .= " AND (
        sa.first_name LIKE ?
        OR sa.last_name LIKE ?
        OR CONCAT(sa.first_name, ' ', sa.last_name) LIKE ?
    )";
    $types .= 'sss';
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
}

if (in_array($filter_status, $validStatuses, true)) {
    $listSql .= " AND sa.application_status = ?";
    $types .= 's';
    $params[] = $filter_status;
}

$listSql .= " ORDER BY sa.date_submitted DESC, sa.last_name ASC, sa.first_name ASC";

$listStmt = $connection->prepare($listSql);
if (!empty($params)) {
    $listStmt->bind_param($types, ...$params);
}
$listStmt->execute();
$documentsResult = $listStmt->get_result();

$documentRows = [];
while ($row = $documentsResult->fetch_assoc()) {
    $documentRows[] = $row;
}
$listStmt->close();
?>
